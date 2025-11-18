<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCartRequest;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Services\CartService;
use App\Exceptions\ConflictException;
use Illuminate\Http\JsonResponse;

class CartController extends Controller
{
    protected $cartService;
    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }
public function addItem(StoreCartRequest $request)
{
    $productId = $request->input('product_id');
    $quantity = $request->input('quantity', 1);
    $user = Auth::user();

    // Authentication check should ideally be handled by middleware (auth:sanctum), 
    // but keeping your explicit check here for context.
    if (!$user) {
        return response()->json(['message' => 'Người dùng chưa đăng nhập.'], 401); 
    }
    
    // Bắt đầu Transaction
    DB::beginTransaction();

    try {
        $product = Product::find($productId);
        
        // 1. Ràng buộc: Người bán không thể tự mua sản phẩm của mình
        // Giữ nguyên logic Authorization ở đây, nhưng đơn giản hóa response
        try {
            $this->authorize('buySelf', $product);
        } catch (\Exception $e) {
            DB::rollBack(); // Rollback transaction nếu có lỗi Authorization
            return response()->json([
                'success' => false,
                'message' => "Bạn không thể thêm sản phẩm của chính mình"
            ], 403); // Hoặc 403 Forbidden/Unauthorized
        }

        // Loại bỏ check stock cũ, để logic đó cho Service và Transaction lock

        // --- XỬ LÝ LOGIC CHÍNH: Gọi Service Layer ---
        // Service sẽ ném ra ConflictException nếu stock không đủ
        $cartItem = $this->cartService->handleAddItem($user, $productId, $quantity);
    
        
        // Commit transaction nếu tất cả thành công
        DB::commit(); 
        
        return response()->json([
            'message' => 'Sản phẩm đã được thêm vào giỏ hàng thành công!',
            'cart_item' => $cartItem
        ], 200);

    } catch (ConflictException $e) { // <-- Bắt lỗi ConflictException cụ thể
        DB::rollBack(); // Rollback do Conflict
        // Trả về HTTP 409 Conflict với message từ Service
        return response()->json(['message' => $e->getMessage()], 409); 

    } catch (\Throwable $e) { // <-- Bắt các lỗi khác (ví dụ: Product not found, DB errors, etc.)
        DB::rollBack(); 
        Log::error('Lỗi khi thêm sản phẩm vào giỏ hàng: ' . $e->getMessage());

        // Trả về Internal Server Error 500
        return response()->json(['message' => 'Đã có lỗi xảy ra, vui lòng thử lại sau.'], 500); 
    }
}
    public function deleteItem(int $cartItemId)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Người dùng chưa đăng nhập.'], 401);
        }
        // Lấy CartItem để kiểm tra quyền và truyền vào Service
        $cartItem = CartItem::find($cartItemId);

        if (!$cartItem) {
            // Lỗi 4. Sản phẩm đã bị xóa trước đó (hoặc không tồn tại)
            return response()->json(['message' => 'Mục giỏ hàng không tồn tại.'], 404);
        }

        // --- 1. SỬ DỤNG POLICY: Đảm bảo CartItem này thuộc về User hiện tại ---
        // Controller gọi Policy để ủy quyền.

        try {
            // Giả sử Policy cho CartItem có tên là 'delete'
            $this->authorize('delete', $cartItem); 
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return response()->json(['message' => 'Bạn không có quyền xóa mục giỏ hàng này.'], 403);
        }

        // --- 2. GỌI SERVICE XỬ LÝ LOGIC XÓA ---
        try {
            $this->cartService->handleDeleteItem($cartItem);

            // Xử lý hoàn tất
            return response()->json(['message' => 'Sản phẩm đã được xóa khỏi giỏ hàng thành công.'], 200);

        } catch (\Throwable $e) {
            Log::error('Lỗi khi xóa sản phẩm khỏi giỏ hàng: ' . $e->getMessage());
            // Lỗi 4. Lỗi DB/timeout
            return response()->json(['message' => 'Không thể xóa sản phẩm, vui lòng thử lại.'], 500);
        }
    }
    public function index(CartService $cartService): JsonResponse
    {
        // Ràng buộc 10: Bảo mật - Lấy user hiện tại
        $user = Auth::user();
        if (!$user) {
            // Controller chỉ chịu trách nhiệm xác thực cơ bản
            return response()->json(['message' => 'Người dùng chưa đăng nhập.'], 401);
        }
        
        // 3. Gọi Service: Controller chuyển giao nhiệm vụ xử lý nghiệp vụ phức tạp cho Service
        $cartData = $cartService->getFormattedCartData($user);
        // die('alo');

        // Controller chỉ trả về kết quả
        return response()->json([
            'message' => 'Lấy dữ liệu giỏ hàng thành công.',
            'data' => $cartData,
        ]);
    }

    /**
     * Phương thức giả lập lấy danh sách phương thức vận chuyển cho một shop
     * Ràng buộc 3
     */
    private function getShippingMethods(int $sellerId): array
    {
        // Giả định truy vấn từ bảng shipping_methods
        return [
            ['id' => 1, 'name' => 'Giao hàng Tiêu chuẩn', 'fee' => 25000],
            ['id' => 2, 'name' => 'Giao hàng Nhanh (Hỏa tốc)', 'fee' => 40000],
        ];
    }

    /**
     * Phương thức giả lập tính phí vận chuyển mặc định (có thể dựa trên location/shop)
     * Ràng buộc 5
     */
    private function calculateShippingFee(int $sellerId): float
    {
        // Hiện tại trả về phí mặc định của phương thức Tiêu chuẩn (25000)
        return 25000.00;
    }
}
