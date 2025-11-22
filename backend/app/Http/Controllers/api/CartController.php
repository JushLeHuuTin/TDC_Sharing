<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCartRequest;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Services\CartService;
use App\Exceptions\ConflictException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\Cart;
use Throwable;

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

        // 1. Authentication check (Middleware is better, but this works)
        if (!$user) {
            return response()->json(['message' => 'Người dùng chưa đăng nhập.'], 401);
        }

        // Bắt đầu Transaction
        DB::beginTransaction();

        try {
            $product = Product::find($productId);

            // **DANGER: Double-check logic, Product lookup without lockForUpdate**
            // The service layer should handle product finding with the lock
            // To prevent bugs, keep product finding inside the transaction block 
            // in the service layer, as you already did in handleAddItem.

            // 1. Ràng buộc: Người bán không thể tự mua sản phẩm của mình
            try {
                // Note: If $product is null, authorize will fail here.
                // But we trust the Service handles null product now.
                $this->authorize('buySelf', $product);
            } catch (Throwable $e) { // Catch Throwable to handle AuthorizationException
                DB::rollBack();
                // Assuming the AuthorizationException message is sufficient for the client
                return response()->json([
                    'success' => false,
                    'message' => "Bạn không thể thêm sản phẩm của chính mình"
                ], 403);
            }

            // --- XỬ LÝ LOGIC CHÍNH: Gọi Service Layer ---
            // Service handles product lock, stock check, and item creation/update.
            $cartItem = $this->cartService->handleAddItem($user, $productId, $quantity);


            // Commit transaction nếu tất cả thành công
            DB::commit();

            return response()->json([
                'message' => 'Sản phẩm đã được thêm vào giỏ hàng thành công!',
                'cart_item' => $cartItem
            ], 200);
        } catch (ConflictException $e) {
            // 2. Bắt lỗi ConflictException (stock limit)
            DB::rollBack();
            // Trả về HTTP 409 Conflict với message từ Service
            return response()->json(['message' => $e->getMessage()], 409);
        } catch (Throwable $e) {
            // 3. Bắt các lỗi khác (ví dụ: Product not found, database errors, etc.)
            DB::rollBack();

            // Cải thiện log message và response cho lỗi chung
            $errorMessage = $e->getMessage() === 'Product not found.'
                ? 'Sản phẩm không tồn tại.'
                : 'Đã có lỗi xảy ra, vui lòng thử lại sau.';

            Log::error('Lỗi khi thêm sản phẩm vào giỏ hàng: ' . $e->getMessage(), ['exception' => $e]);

            // Trả về Internal Server Error 500
            return response()->json(['message' => $errorMessage], 500);
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
    public function toggleItemSelection(Request $request, CartItem $cartItem)
    {
        // Giả sử logic chính nằm trong Service Layer
        try {
            DB::beginTransaction();
            $isSelected = $request->boolean('is_selected', false);
            $this->authorize('update', $cartItem);
            // 2. Gọi Service để thực hiện toggle và lấy toàn bộ dữ liệu giỏ hàng mới
            $newCartData = $this->cartService->handleToggleItem($cartItem, $isSelected);

            DB::commit();

            return response()->json([
                'message' => 'Cập nhật trạng thái chọn sản phẩm thành công.',
                'data' => $newCartData // Trả về toàn bộ dữ liệu giỏ hàng mới
            ], 200);
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            DB::rollBack();
            return response()->json(['message' => 'Không có quyền thao tác trên sản phẩm này.'], 403);
        } catch (Throwable $e) {
            DB::rollBack();
            Log::error('Lỗi khi toggle item giỏ hàng: ' . $e->getMessage(), ['cart_item_id' => $cartItem->id]);
            return response()->json(['message' => 'Đã có lỗi xảy ra, vui lòng thử lại sau.'], 500);
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
    public function updateItemQuantity(Request $request, CartItem $cartItem)
    {
        $newQuantity = $request->input('quantity');
        if (!is_numeric($newQuantity) || $newQuantity < 1) {
            return response()->json(['message' => 'Số lượng không hợp lệ.'], 400);
        }
        try {
            DB::beginTransaction();
            $this->authorize('update', $cartItem);
            $newCartData = $this->cartService->handleUpdateQuantity($cartItem, (int)$newQuantity);
            DB::commit();

            return response()->json([
                'message' => 'Cập nhật số lượng sản phẩm thành công.',
                'data' => $newCartData
            ], 200);
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            DB::rollBack();
            return response()->json(['message' => 'Không có quyền thao tác trên sản phẩm này.'], 403);
        } catch (\App\Exceptions\ConflictException $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 409);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Lỗi khi cập nhật số lượng giỏ hàng: ' . $e->getMessage());
            return response()->json(['message' => 'Đã có lỗi hệ thống xảy ra.'], 500);
        }
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
    public function destroy(Request $request): JsonResponse
    {
        $buyerId = Auth::id();
        CartItem::query()
            ->whereHas('cart', function ($query) use ($buyerId) {
                $query->where('user_id', $buyerId);
            })
            ->delete();
        Cart::query()
            ->where('user_id', $buyerId)
            ->delete();

        return response()->json(['message' => 'Giỏ hàng đã được xóa hoàn toàn.'], 200);
    }
}
