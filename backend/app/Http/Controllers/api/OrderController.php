<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProcessOrderRequest;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\Voucher;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection;
use App\Services\MomoPaymentService;
use Str;

use function Laravel\Prompts\alert;

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
        $addressId = $request->address_id;
        $paymentMethod = $request->payment_method;
        $createdOrders = [];
        $totalPaymentAmount = 0.00;
        $selectedCarts = Cart::getSelectedItemsByBuyer($buyerId)->get();
        $selectedCartItems = $selectedCarts->pluck('cartItems')->flatten(1);
        if ($selectedCartItems->isEmpty()) {
            return response()->json(['message' => 'Vui lòng chọn sản phẩm để thanh toán.'], 400);
        }

        // 2. Nhóm các Cart Items theo Seller ID (product_user_id)
        $groupedCartItems = $selectedCartItems->groupBy('product.user_id');

        // 3. Lấy Voucher ID (áp dụng cho toàn bộ giao dịch, nếu có)
        if (empty($voucherCode)) {
            $voucherId = null;
        } else {
            $voucherId = Voucher::IdFromCode($voucherCode)->value('id') ?? null;
        }
        DB::beginTransaction();
        try {
            foreach ($groupedCartItems as $sellerId => $cartItems) {
                $calculation = $this->calculateOrderTotals($cartItems, $voucherId, $paymentMethod);
                $appliedVoucherId = ($calculation['discount_amount'] > 0) ? $voucherId : null;
                // b. Chuẩn bị dữ liệu Order cho Seller hiện tại
                $orderData = [
                    'user_id' => $buyerId,
                    'seller_id' => $sellerId,
                    'address_id' => $addressId,
                    'payment_method' => $paymentMethod,
                    'voucher_id' => $appliedVoucherId,
                    'status' => 'pending',
                    'total_amount' => $calculation['total_amount'],
                ];

                // c. TẠO ĐƠN HÀNG (Order)
                $order = Order::create($orderData);

                // d. TẠO CHI TIẾT ĐƠN HÀNG (Order_Items) & CẬP NHẬT KHO
                $this->createOrderItemsAndUpdateStock($order, $cartItems, $addressId);

                // e. LƯU LẠI ĐƠN HÀNG ĐÃ TẠO
                $createdOrders[] = $order;
                $totalPaymentAmount += $calculation['total_amount'];
            }
            CartItem::deleteSelectedItems($buyerId);
            Cart::cleanupEmptyCarts($buyerId);
            if ($paymentMethod === 'momo') {
                // Gọi service Momo để tạo yêu cầu thanh toán
                $momoData = $this->createMomoPayment($createdOrders, $totalPaymentAmount, $addressId);

                // Commit Transaction để lưu Orders trạng thái 'pending'
                DB::commit();

                // Trả về URL chuyển hướng cho Frontend
                return response()->json([
                    'success' => true,
                    'message' => 'Đang chuyển hướng thanh toán Momo...',
                    'redirect_url' => $momoData['payUrl'],
                    'data' => [
                        'order_ids' => collect($createdOrders)->pluck('id'),
                    ],
                ], 200);
            }
            DB::commit();

            // 6. Trả về phản hồi: Thông báo đã tạo N đơn hàng thành công
            return response()->json([
                'success' => true,
                'message' => "Thanh toán công! Đã tạo " . count($createdOrders) . " đơn hàng.",
                'data' => [
                    // Trả về danh sách Order ID hoặc thông tin tóm tắt
                    'order_ids' => collect($createdOrders)->pluck('id'),
                ]
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            // Ghi log lỗi chi tiết hơn     
            Log::error("Lỗi tạo đơn hàng Multi-Seller cho User {$buyerId}: " . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Đặt hàng thất bại, vui lòng thử lại.',
                'error_detail' => $e->getMessage()
            ], 500);
        }
    }
    // Trong OrderController.php

    protected function createMomoPayment(array $createdOrders, float $totalAmount, int $addressId): array
    {
        $momoService = new MomoPaymentService();
        $orderIds = collect($createdOrders)->pluck('id')->toArray(); // Lấy mảng IDs
        $orderIdsString = implode(',', $orderIds); // Chuỗi IDs cho orderInfo

        // Cần URL returnUrl và ipnUrl hợp lệ
        $baseUrl = 'https://loise-unpirated-pseudoangelically.ngrok-free.dev';

        $returnUrl = $baseUrl . '/checkout/momo/return';
        $notifyUrl = $baseUrl . '/api/momo/ipn';

        // 1. Gọi Service tạo yêu cầu thanh toán
        $response = $momoService->createPaymentUrl(
            $totalAmount,
            "Thanh toan don hang #{$orderIdsString}",
            $returnUrl,
            $notifyUrl
        );

        // 2. LƯU THÔNG TIN GIAO DỊCH TẠM THỜI (QUAN TRỌNG)
        // Lưu các Order IDs liên quan và Mã Momo Order ID để dùng trong Callback
        $momoService->storeTransactionData(
            $response['orderId'], // Mã Order ID từ Momo
            $response['requestId'], // Mã Request ID từ Momo
            $orderIds,              // Mảng các Order ID của ứng dụng
            $totalAmount,
            'momo'
        );

        return $response; // Trả về payUrl
    }
    protected function calculateOrderTotals(Collection $cartItems, ?int $voucherId, string $paymentMethod): array
    {
        $subtotal = 0.00;

        // 1. TÍNH SUBTOTAL
        foreach ($cartItems as $item) {
            $subtotal += $item->product->price * $item->quantity;
        }

        $discountAmount = 0.00;

        // 2. TÍNH DISCOUNT & KIỂM TRA VOUCHER HỢP LỆ
        if ($voucherId) {
            // Sử dụng query builder và Scope IsActive để đảm bảo voucher hợp lệ
            $voucher = Voucher::isActive()->find($voucherId);
            if ($voucher) {
                // Kiểm tra giá trị tối thiểu
                if ($subtotal >= $voucher->min_purchase) {
                    if ($voucher->discount_type === 'percentage') {
                        $discount = $subtotal * ($voucher->discount_value / 100.00);
                        $discountAmount = $discount;
                        // Nếu có cột max_discount, áp dụng: min($discount, $voucher->max_discount)
                    } elseif ($voucher->discount_type === 'fixed') {
                        $discountAmount = $voucher->discount_value;
                    }

                    $discountAmount = min($discountAmount, $subtotal);
                }
            }
        }

        $shippingFee = 0.00; // Giả định
        if ($subtotal > 500000) {
            $shippingFee = 0; // Miễn phí vận chuyển
        } else {
            $shippingFee = 20000;
        }
        $finalAmount = $subtotal - $discountAmount + $shippingFee;

        return [
            'sub_total'      => round($subtotal, 2),
            'discount_amount' => round($discountAmount, 2),
            'total_amount'   => round($finalAmount, 2),
            'voucher_id'     => $voucherId,
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
    protected function createOrderItemsAndUpdateStock(Order $order, Collection $cartItems, int $addressId): void
    {
        $orderItemsData = [];
        $productsToUpdate = collect();

        foreach ($cartItems as $item) {
            $product = $item->product; 
            $quantity = $item->quantity;
            // 1. Kiểm tra tồn kho trước khi trừ
            if ($product->stocks < $quantity) {
                throw new \Exception("Sản phẩm '{$product->name}' không đủ tồn kho (còn {$product->stocks}).");
            }

            // Chuẩn bị dữ liệu cho Order Item
            $orderItemsData[] = [
                'product_id' => $product->id,
                'price' => $product->price, // Lưu giá tại thời điểm checkout
                'quantity' => $quantity,
                'subtotal' => $product->price * $quantity,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            // Chuẩn bị cập nhật tồn kho (để tránh nhiều lần query)
            $productsToUpdate->push([
                'id' => $product->id,
                'new_stock' => $product->stocks - $quantity,
            ]);
        }

        // 2. TẠO CHI TIẾT ĐƠN HÀNG (Order Items)
        // Sử dụng quan hệ để tự động gán order_id
        $order->items()->createMany($orderItemsData);

        // 3. CẬP NHẬT KHO HÀNG (Sử dụng Transaction an toàn)
        // Tối ưu hóa bằng cách dùng DB::update hoặc sử dụng Model::whereIn + update
        foreach ($productsToUpdate as $updateData) {
            Product::where('id', $updateData['id'])
                ->update(['stocks' => $updateData['new_stock']]);
        }
    }
}
