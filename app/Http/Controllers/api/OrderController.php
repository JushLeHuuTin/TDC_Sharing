<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProcessOrderRequest;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Cart;
use App\Models\Product;
use App\Models\Transaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;


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
        $cart = Cart::forUserWithDetails($buyerId)->first();
        $voucherId = 112;
        // die("ti");

        if (!$cart || $cart->cartItems->isEmpty()) {
            return response()->json(['message' => 'Giỏ hàng của bạn đang trống.'], 400);
        }

        // Bắt đầu giao dịch DB để đảm bảo tính toàn vẹn
        DB::beginTransaction();

        try {

            // 1. Kiểm tra tồn kho và lấy dữ liệu chi tiết đơn hàng
            $cartItemsData = $this->processCartItems($cart);
            $totalAmount = $this->calculateTotal($cartItemsData);

            // TODO: Áp dụng Voucher tại đây nếu 'voucher_code' có trong request
            $finalTotal = $totalAmount;
            // 2. Tạo Đơn hàng chính
            // die("$finalTotal");
            // die("$request->payment_method");
            $order = Order::create([
            // [KHẮC PHỤC LỖI THIẾU CỘT] user_id là BẮT BUỘC theo Schema
    'user_id'         => $buyerId, 
    'buyer_id'        => $buyerId,
    // [KHẮC PHỤC LỖI THIẾU CỘT] order_id là BẮT BUỘC và UNIQUE
    "order_id"        => "ORD-" . strtoupper(substr(uniqid(), -10)), // Tạo ID ngẫu nhiên, DUY NHẤT
    
    // [KHẮC PHỤC LỖI ÁNH XẠ] delivery_address_id trong Request -> address_id trong DB
    'address_id'      => $request->delivery_address_id,
    
    // [KHẮC PHỤC LỖI ÁNH XẠ] voucher_code trong Request -> voucher_id trong DB
    'voucher_id'      => $voucherId, 
    
    'total_amount'    => $finalTotal, 
    // Giả định discount_amount là 50000 (nếu có voucher)
    'discount_amount' => $voucherId ? 50000.00 : 0.00,
    // Final amount = Total - Discount (ví dụ: 100000.00)
    'final_amount'    => $finalTotal - ($voucherId ? 50000.00 : 0.00), 
    
    'status'          => 'pending',
    'payment_method'  => $request->payment_method,
            ]);

            // 3. Tạo Chi tiết Đơn hàng và Cập nhật tồn kho
            $this->createOrderItemsAndUpdateStock($order->id, $cartItemsData);

            // 4. Tạo bản ghi Giao dịch
            Transaction::create([
                'order_id' => $order->id,
                'payment_method' => $request->payment_method,
                'transaction_code' => $request->transaction_id,
                'amount' => $finalTotal,
                'status' => 'completed',
            ]);

            // 5. Xóa Giỏ hàng
            $cart->delete();
            
            // 6. Trả về Response thành công
            DB::commit();

            $order = Order::with(['buyer', 'items.seller'])->find($order->id);
            return response()->json([
                'message' => 'Tạo đơn hàng thành công!',
                'order_id' => $order->id,
                'order_details' => $order,
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Lỗi tạo đơn hàng cho User {$buyerId}: " . $e->getMessage());

            return response()->json([
                'message' => 'Tạo đơn hàng thất bại, vui lòng thử lại.',
            ], 500);
        }
    }

    /**
     * API Hiển thị chi tiết đơn hàng (Bảo mật).
     *
     * @param int $id ID đơn hàng
     * @return JsonResponse
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
     * Rút gọn: Kiểm tra tồn kho và tạo mảng dữ liệu chi tiết đơn hàng.
     *
     * @param Cart $cart
     * @return array
     */
    private function processCartItems(Cart $cart): array
    {
        $orderItemsData = [];

        foreach ($cart->cartItems as $cartItem) {
            $product = $cartItem->product;

            // Kiểm tra stock
            if ($product->stocks < $cartItem->quantity) {
                // Ném ra exception để tự động rollBack transaction và thông báo lỗi
                throw new \Exception("Sản phẩm '{$product->title}' chỉ còn {$product->stocks} đơn vị. Vui lòng cập nhật giỏ hàng.");
            }

            $itemPrice = $product->price * $cartItem->quantity;

            $orderItemsData[] = [
                'product_id' => $product->id,
                'seller_id' => $product->user_id,
                'quantity' => $cartItem->quantity,
                'price' => $product->price,
                'sub_total' => $itemPrice,
            ];
        }

        return $orderItemsData;
    }

    /**
     * Rút gọn: Tính tổng tiền.
     *
     * @param array $items
     * @return float
     */
    private function calculateTotal(array $items): float
    {
        return array_sum(array_column($items, 'sub_total'));
    }

    /**
     * Rút gọn: Lưu Order Items và cập nhật tồn kho.
     *
     * @param int $orderId
     * @param array $orderItemsData
     * @return void
     */
    private function createOrderItemsAndUpdateStock(int $orderId, array $orderItemsData): void
    {
        // Thêm order_id vào tất cả các mục
        $itemsToInsert = array_map(function ($item) use ($orderId) {
            $item['order_id'] = $orderId;
            return $item;
        }, $orderItemsData);

        // Chèn hàng loạt vào order_items
        OrderItem::insert($itemsToInsert);

        // Cập nhật tồn kho (từng mục)
        foreach ($orderItemsData as $item) {
            Product::where('id', $item['product_id'])->decrement('stocks', $item['quantity']);
        }
    }
}
