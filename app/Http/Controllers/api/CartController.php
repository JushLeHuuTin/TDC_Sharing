<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class CartController extends Controller
{
    /**
     * Thêm một sản phẩm vào giỏ hàng của người dùng đang đăng nhập.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function addItem(Request $request)
    {
        // --- Ràng buộc đầu vào ---
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|integer|exists:products,id',
            'quantity' => 'sometimes|integer|min:1', // `sometimes` nghĩa là chỉ validate nếu có, không bắt buộc
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422); // Unprocessable Entity
        }

        $productId = $request->input('product_id');
        $quantity = $request->input('quantity', 1); // Mặc định số lượng là 1 nếu không truyền
        $user = Auth::user();

        // --- Bắt đầu khối xử lý có Transaction để đảm bảo toàn vẹn dữ liệu ---
        DB::beginTransaction();
        try {
            $product = Product::find($productId);

            // --- XỬ LÝ CÁC RÀNG BUỘC LOGIC ---

            // 1. Ràng buộc: Người bán không thể tự mua sản phẩm của mình
            if ($product->user_id === $user->id) {
                DB::rollBack();
                return response()->json(['message' => 'Bạn không thể thêm sản phẩm của chính mình vào giỏ hàng.'], 403); // Forbidden
            }

            // 2. Ràng buộc: Kiểm tra sản phẩm có còn hàng không (stock > 0)
            if ($product->stocks < $quantity) {
                DB::rollBack();
                // Thông báo lỗi theo yêu cầu của bạn
                return response()->json(['message' => 'Sản phẩm đã hết hàng hoặc không đủ số lượng.'], 409); // Conflict
            }

            // --- XỬ LÝ LOGIC CHÍNH ---

            // Tìm hoặc tạo mới giỏ hàng cho người dùng
            $cart = Cart::firstOrCreate(['user_id' => $user->id]);

            // Kiểm tra xem sản phẩm này đã có trong giỏ hàng chưa
            $cartItem = CartItem::where('cart_id', $cart->id)
                ->where('product_id', $product->id)
                ->first();

            if ($cartItem) {
                // Nếu đã có, cập nhật số lượng
                $newQuantity = $cartItem->quantity + $quantity;
                // Kiểm tra lại số lượng tồn kho với tổng số lượng mới
                if ($product->stocks < $newQuantity) {
                    DB::rollBack();
                    return response()->json(['message' => 'Số lượng trong giỏ hàng vượt quá số lượng tồn kho.'], 409);
                }
                $cartItem->quantity = $newQuantity;
                $cartItem->save();
            } else {
                // Nếu chưa có, tạo mới cart_item
                CartItem::create([
                    'cart_id' => $cart->id,
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'price' => $product->price, // Lưu lại giá tại thời điểm thêm vào giỏ
                    'added_at' => now()
                ]);
            }

            DB::commit(); // Hoàn tất giao dịch nếu mọi thứ thành công

            return response()->json(['message' => 'Sản phẩm đã được thêm vào giỏ hàng thành công!'], 200);

        } catch (\Exception $e) {
            DB::rollBack(); // Hoàn tác lại tất cả thay đổi nếu có lỗi xảy ra
            Log::error('Lỗi khi thêm sản phẩm vào giỏ hàng: ' . $e->getMessage()); // Ghi log lỗi để debug

            // Thông báo lỗi chung theo yêu cầu của bạn
            return response()->json(['message' => 'Đã có lỗi xảy ra, vui lòng thử lại sau.'], 500); // Internal Server Error
        }
    }
    public function index(): JsonResponse
    {
        // Ràng buộc 10: Bảo mật - Lấy user hiện tại
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Người dùng chưa đăng nhập.'], 401);
        }

        // Lấy tất cả Cart Items của user hiện tại với Product và Seller
        $cartItems = CartItem::with([
            'product' => function ($query) {
                $query->select('id', 'name', 'price', 'user_id'); // chỉ lấy các cột cần thiết
            },
            'product.seller' => function ($query) {
                $query->select('id', 'full_name'); // Lấy thông tin người bán
            }
        ])
        ->where('user_id', $user->id)
        ->get();

        if ($cartItems->isEmpty()) {
            // Ràng buộc 1: Nếu không có sản phẩm
            return response()->json(['message' => 'Giỏ hàng trống.'], 200);
        }

        // Bước 1: Group sản phẩm theo người bán (Shop) - Ràng buộc 1
        $groupedItems = $cartItems->groupBy('product.user_id');

        // Khởi tạo các biến tổng toàn bộ giỏ hàng
        $overallSubtotal = 0;
        $overallShippingFee = 0;
        $overallDiscount = 0;
        
        $shops = [];

        // Bước 2: Duyệt qua từng nhóm Shop để tính toán
        foreach ($groupedItems as $sellerId => $items) {
            $shopSubtotal = 0;
            $shopItems = [];
            
            // Lấy thông tin Seller (Shop)
            $seller = $items->first()->product->seller;

            foreach ($items as $item) {
                // Ràng buộc 2: Tính subtotal (tạm tính) cho từng sản phẩm
                $price = $item->product->price ?? 0;
                $quantity = $item->quantity > 0 ? $item->quantity : 1;
                $subtotal = $price * $quantity;

                // Cập nhật tổng shop và tổng toàn bộ
                $shopSubtotal += $subtotal;
                $overallSubtotal += $subtotal;

                $shopItems[] = [
                    'cart_item_id' => $item->id,
                    'product_id' => $item->product_id,
                    'product_name' => $item->product->name,
                    'price' => $price,
                    'quantity' => $quantity,
                    'subtotal' => $subtotal,
                    // Ràng buộc 2: Xử lý lỗi giá/số lượng (đã xử lý logic)
                    'error' => ($item->product->price === null) ? 'Liên hệ giá' : null,
                ];
            }

            // Ràng buộc 5: Giả định tính Phí vận chuyển (Mặc định là 0, frontend sẽ chọn)
            // LƯU Ý: Logic thực tế cần tính phí dựa trên địa chỉ người mua, khối lượng/kích thước và phương thức.
            $shippingFee = $this->calculateShippingFee($sellerId); // Giả định hàm tính phí
            $overallShippingFee += $shippingFee;

            // Ràng buộc 6: Giả định chiết khấu (Discount)
            $discount = 0; // Áp dụng voucher ở bước sau (Checkout)

            $shopTotal = $shopSubtotal + $shippingFee - $discount;

            $shops[] = [
                // Ràng buộc 1: Thông tin người bán
                'seller_id' => $sellerId,
                'shop_name' => $seller->full_name,
                
                // Ràng buộc 2: Danh sách sản phẩm
                'items' => $shopItems,

                // Ràng buộc 4, 5, 6: Tổng tiền theo shop
                'shop_subtotal' => $shopSubtotal, // Tạm tính (Ràng buộc 4)
                'shipping_fee' => $shippingFee,   // Phí vận chuyển (Ràng buộc 5)
                'discount' => $discount,          // Giảm giá (Ràng buộc 6)
                'shop_total' => $shopTotal,       // Tổng đơn hàng (Ràng buộc 6)
                
                // Ràng buộc 3: Phương thức vận chuyển (Mặc định)
                'shipping_methods' => $this->getShippingMethods($sellerId),
                'selected_shipping_method_id' => 1, // Mặc định chọn 1
            ];
        }

        // Ràng buộc 8: Thẻ tổng giỏ hàng (Toàn bộ)
        $overallTotal = $overallSubtotal + $overallShippingFee - $overallDiscount;

        return response()->json([
            'message' => 'Lấy dữ liệu giỏ hàng thành công.',
            'data' => [
                'shops' => $shops,
                'overall_summary' => [
                    'subtotal' => $overallSubtotal,
                    'shipping_fee' => $overallShippingFee,
                    'discount' => $overallDiscount,
                    'total' => $overallTotal,
                ],
                // Ràng buộc 9: Nút Tiến hành đặt hàng (Frontend)
                'is_cart_ready_for_checkout' => $overallSubtotal > 0,
            ]
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