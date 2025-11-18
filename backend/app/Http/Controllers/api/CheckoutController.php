<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Models\CartItem;
use App\Models\PaymentMethod;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;

class CheckoutController extends Controller
{
    /**
     * API Lấy dữ liệu hiển thị trang Thanh toán (Checkout)
     * Ràng buộc 1-6, 9
     */
    public function index(): JsonResponse
    {
        // Ràng buộc 9: Bảo mật - Kiểm tra và lấy user hiện tại
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Bạn cần đăng nhập để tiến hành thanh toán.'], 401);
        }

        // --- Ràng buộc 1 & 2: Thông tin giao hàng ---
        $defaultAddress = $this->getDefaultShippingAddress($user->id);
        
        if (!$defaultAddress) {
            return response()->json([
                'message' => 'Chưa có địa chỉ giao hàng. Vui lòng thêm mới.',
                'code' => 'NO_ADDRESS'
            ], 400);
        }

        // --- Ràng buộc 3: Phương thức thanh toán ---
        $paymentMethods = $this->getPaymentMethods();

        // --- Ràng buộc 4, 5, 6: Chi tiết đơn hàng và tính toán tổng ---
        $cartData = $this->calculateOrderDetails($user->id);
        
        if (empty($cartData['shops'])) {
            return response()->json(['message' => 'Giỏ hàng của bạn đang trống. Vui lòng thêm sản phẩm.'], 400);
        }

        return response()->json([
            'message' => 'Lấy dữ liệu Checkout thành công.',
            'data' => [
                // Ràng buộc 1: Địa chỉ giao hàng
                'shipping_address' => $defaultAddress, 
                // Ràng buộc 3: Phương thức thanh toán
                'payment_methods' => $paymentMethods, 
                // Ràng buộc 4, 5, 6: Chi tiết và tổng tiền
                'order_details' => $cartData['shops'],
                'overall_summary' => $cartData['overall_summary'],
            ]
        ], 200);
    }
    
    /**
     * Lấy địa chỉ giao hàng mặc định của người dùng
     * Ràng buộc 1
     */
    private function getDefaultShippingAddress(int $userId): ?array
    {
        $address = Address::where('user_id', $userId)
                          ->where('is_default', true)
                          ->first();
                          
        // Nếu không có địa chỉ mặc định, lấy địa chỉ đầu tiên
        if (!$address) {
            $address = Address::where('user_id', $userId)->first();
        }

        return $address ? $address->only(['id', 'full_name', 'phone', 'address_line_1', 'city']) : null;
    }

    /**
     * Lấy danh sách phương thức thanh toán
     * Ràng buộc 3
     */
    private function getPaymentMethods(): array
    {
        // Giả sử có bảng PaymentMethod hoặc hardcode
        return PaymentMethod::where('is_active', true)->get(['id', 'name', 'code'])->toArray();
    }
    
    /**
     * Tái tính toán chi tiết đơn hàng từ giỏ hàng (tương tự CartController)
     * Ràng buộc 4, 5, 6
     */
    private function calculateOrderDetails(int $userId): array
    {
        // Lấy dữ liệu Giỏ hàng đã được xử lý (Sử dụng lại logic từ CartController)
        $cartItems = CartItem::with(['product:id,name,price,user_id', 'product.seller:id,full_name'])
                             ->where('user_id', $userId)
                             ->get();

        if ($cartItems->isEmpty()) {
            return ['shops' => [], 'overall_summary' => []];
        }

        // --- LOGIC TÍNH TOÁN (Giả lập) ---
        $overallSubtotal = 0;
        $overallShippingFee = 0;
        $overallDiscount = 0;
        $shops = [];

        $groupedItems = $cartItems->groupBy('product.user_id');

        foreach ($groupedItems as $sellerId => $items) {
            $shopSubtotal = $items->sum(function ($item) {
                return ($item->product->price ?? 0) * ($item->quantity > 0 ? $item->quantity : 1);
            });
            
            // Phí vận chuyển và Giảm giá (Ràng buộc 5)
            $shippingFee = 30000; // Giả lập phí cố định cho Checkout
            $discount = 0;        // Giảm giá (Nếu voucher đã được áp dụng ở bước trước, sẽ load ở đây)

            $shopTotal = $shopSubtotal + $shippingFee - $discount;
            
            $overallSubtotal += $shopSubtotal;
            $overallShippingFee += $shippingFee;
            $overallDiscount += $discount;

            $shops[] = [
                'seller_id' => $sellerId,
                'shop_name' => $items->first()->product->seller->full_name,
                'items_count' => $items->count(),
                'shop_subtotal' => $shopSubtotal,
                'shipping_fee' => $shippingFee,
                'discount' => $discount,
                'shop_total' => $shopTotal,
            ];
        }

        $overallTotal = $overallSubtotal + $overallShippingFee - $overallDiscount;
        
        return [
            'shops' => $shops,
            'overall_summary' => [
                'subtotal' => $overallSubtotal,
                'shipping_fee' => $overallShippingFee,
                'discount' => $overallDiscount,
                'total' => $overallTotal, // Ràng buộc 6
            ]
        ];
    }
}