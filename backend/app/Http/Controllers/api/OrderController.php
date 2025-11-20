<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProcessOrderRequest;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Cart;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\Voucher;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use SebastianBergmann\Environment\Console;
use Str;

class OrderController extends Controller
{
    /**
     * API Tạo Đơn hàng sau khi thanh toán thành công (Checkout).
     *
     * @param ProcessOrderRequest $request
     * @return JsonResponse
     */
    public function store(ProcessOrderRequest $request): JsonResponse
    {
        $buyerId = Auth::id();
        $voucherCode = $request->voucher_code;
        // 1. Lấy Cart: Sử dụng Local Scope của Model Cart
        $cart = Cart::activeUserWithDetails($buyerId)->first();
        if (!$cart || !$cart->isAvailableForCheckout()) {
            return response()->json(['message' => 'Giỏ hàng của bạn đang trống.'], 400);
        }

        // 2. Lấy Voucher ID: Sử dụng Local Scope của Model Voucher
        $voucherId = Voucher::IdFromCode($voucherCode)->value('id');
        // Lưu ý: Validation Form Request đã kiểm tra existence, nhưng không tìm ra ID.

        // 3. Tính toán các giá trị tài chính (Giả định hàm này nằm trong service hoặc trait)
        $calculation = $this->calculateOrderTotals($cart, $voucherId);
        // 4. Chuẩn bị dữ liệu Order
        $orderData = [
            'user_id'           => $buyerId,
            'address_id'        => $request->address_id,
            'payment_method'    => $request->payment_method,
            'voucher_id'        => $voucherId, // Đã có ID
            'status'            => 'pending',
            'total_amount'      => $calculation['total_amount'],
        ];
        DB::beginTransaction();
        try {
            // A. TẠO ĐƠN HÀNG (Order)
            $order = Order::create($orderData);
            die('a');
            // B. TẠO CHI TIẾT ĐƠN HÀNG (Order_Items) & CẬP NHẬT KHO
            $this->createOrderItemsAndUpdateStock($order, $cart, $request->address_id);

            // C. XÓA GIỎ HÀNG
            $cart->delete();

            DB::commit();
            $order->load('user.addresses', 'items.product.user');
            $seller = $order->seller;
            $buyerAddress = $order->address;
            return response()->json([
                'success' => true,
                'message' => 'Thanh toán thành công! Đơn hàng của bạn đã được xác nhận.',
                'data' => [
                    'order_id' => $order->order_id, // ORD2024001234
                    'order_time' => $order->created_at->format('H:i:s d/m/Y'),
                    'status' => 'Chờ người bán xác nhận',

                    'buyer_info' => [
                        // Lấy thông tin người mua (User Model)
                        'full_name' => $order->user->full_name,
                        'email' => $order->user->email,
                        'phone' => $buyerAddress->phone, // Lấy số điện thoại từ địa chỉ nhận
                    ],

                    'seller_info' => [
                        // Lấy thông tin người bán (Seller của item đầu tiên)
                        'full_name' => $seller->full_name ?? 'N/A',
                        'email' => $seller->email ?? 'N/A',
                        'rating' => $seller->average_rating ?? '4.8 (127 đánh giá)', // Giả định
                        'contact' => [
                            'phone' => $seller->phone ?? '0912345678', // Giả định
                            'zalo' => $seller->phone ?? '0912345678', // Giả định
                        ]
                    ],
                ]
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Lỗi tạo đơn hàng cho User {$buyerId}: " . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Đặt hàng thất bại, vui lòng thử lại.',
            ], 500);
        }
    }
    private function calculateOrderTotals(Cart $cart, ?int $voucherId): array
    {
        $totalAmount = 0;
        foreach ($cart->cartItems as $item) {
            // Giả định product đã được load sẵn nhờ scope ActiveUserWithDetails
            $totalAmount += $item->product->price * $item->quantity;
        }

        $discountAmount = 0.00;

        if ($voucherId) {
            $discountAmount = $totalAmount * 0.10;
        }

        $finalAmount = $totalAmount - $discountAmount;

        // $shippingFee = $this->calculateShippingFee();
        // $finalAmount += $shippingFee;
        return [
            'total_amount'      => $finalAmount,
            'voucher_id'        => $voucherId,
        ];
    }

    /**
     * API Hiển thị chi tiết đơn hàng (Bảo mật).
     */
    public function show(int $id): JsonResponse
    {
        $order = Order::with(['buyer', 'items.seller'])->findOrFail($id);

        // 7. Bảo mật: Sử dụng OrderPolicy để kiểm tra quyền 'view'
        $this->authorize('view', $order);

        return response()->json([
            'order' => $order,
        ]);
    }

    /**
     * Rút gọn: Lưu Order Items và cập nhật tồn kho.
     *
     * @param int $orderId
     * @param array $orderItemsData
     * @return void
     */
    private function createOrderItemsAndUpdateStock(Order $order, Cart $cart, int $addressId): void
    {
        die('alo');
        $orderItems = [];
        foreach ($cart->cartItems as $cartItem) {
            $product = $cartItem->product;

            if ($product->stocks < $cartItem->quantity) {
                // Đảm bảo rollback transaction ở tầng cao hơn (hàm store)
                throw new \Exception("Sản phẩm '{$product->title}' chỉ còn {$product->stocks} sản phẩm.");
            }

            // Tạo mảng dữ liệu cho OrderItem (sử dụng insert mass insert để tối ưu)
            $orderItems[] = [
                'order_id'      => $order->id,
                'product_id'    => $product->id,
                'price'         => $product->price,
                'quantity'      => $cartItem->quantity,
                'subtotal'         => $product->price * $cartItem->quantity,
                'created_at'    => now(),
                'updated_at'    => now(),
            ];
            // Cập nhật tồn kho (Sử dụng Model để tương tác với DB)
            // $product->decrement('stocks', $cartItem->quantity);
        }
        // die("");
        // die("$product->id");
        // Thực hiện mass insert (tạo nhiều OrderItem cùng lúc)
        OrderItem::insert($orderItems);
    }
}
