<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use App\Exceptions\ConflictException;
use Illuminate\Database\Eloquent\Builder;

class CartService
{
    public function getFormattedCartData(User $user): array
    {
        // 1. Giao tiếp với Model: Service gọi Model để lấy dữ liệu thô
        $carts = $this->fetchCartsWithRelations($user);

        // 2. Khởi tạo các biến tổng quát cho toàn bộ giỏ hàng
        $shops = [];
        $overallSubtotal = 0;
        $overallShippingFee = 0;
        $overallDiscount = 0;
        $overallTotal = 0;

        // Bắt đầu vòng lặp để xử lý từng nhóm giỏ hàng (từng Shop)
        foreach ($carts as $cart) {
            // 3. Xử lý Logic Nghiệp vụ: Tính toán tổng tiền, phí vận chuyển (logic phức tạp)
            $shopCalculations = $this->calculateShopSummary($cart);

            // Cập nhật tổng quát
            $overallSubtotal += $shopCalculations['subtotal'];
            $overallShippingFee += $shopCalculations['shipping_fee'];
            $overallDiscount += $shopCalculations['discount'];
            $overallTotal += $shopCalculations['total'];

            // 4. Định dạng dữ liệu cho Frontend (Đây cũng là trách nhiệm của Service)
            $shops[] = $this->formatShopData($cart, $shopCalculations);
        }

        return [
            'shops' => $shops,
            'overall_summary' => [
                'subtotal' => $overallSubtotal,
                'shipping_fee' => $overallShippingFee,
                'discount' => $overallDiscount,
                'total' => $overallTotal,
            ],
            'is_cart_ready_for_checkout' => $overallSubtotal > 0,
        ];
    }
    protected function fetchCartsWithRelations(User $user): Collection
    {
        return Cart::with([
            'seller' => function ($query) {
                $query->select('id', 'full_name');
            },
            'items.product' => function ($query) {
                $query->select('id', 'title', 'price', 'user_id');
            },
            'items.product.featuredImage' => function ($query) {
                $query->select('product_id', 'image');
            }
        ])
            ->where('user_id', $user->id)
            ->get();
    }
    protected function calculateShopSummary(Cart $cart): array
    {
        // 1. Tính tổng phụ (Subtotal) - Dựa vào phương thức Model
        $shopSubtotal = $cart->getTotalPrice();

        // 2. Tính Phí Vận chuyển (LOGIC GIẢ ĐỊNH - Vận chuyển là nghiệp vụ phức tạp)
        // Đây là lý do chính cần Service: xử lý logic ngoài luồng DB.
        $shopShippingFee = $shopSubtotal > 100000 ? 10000 : 20000;

        $shopDiscount = 0; // Giả định: Áp dụng voucher/giảm giá cấp Shop
        $shopTotal = $shopSubtotal + $shopShippingFee - $shopDiscount;

        return [
            'subtotal' => $shopSubtotal,
            'shipping_fee' => $shopShippingFee,
            'discount' => $shopDiscount,
            'total' => $shopTotal,
        ];
    }

    protected function formatShopData(Cart $cart, array $summary): array
    {
        return [
            'seller_id' => $cart->seller_id,
            'shop_name' => $cart->seller->full_name,
            // Dữ liệu items của shop
            'items' => $cart->items->map(function ($item) {
                $imageUrl = optional($item->product->featuredImage->first())->image ?? null;
                return [
                    'cart_item_id' => $item->id,
                    'product_id' => $item->product_id,
                    'title' => $item->product->title,
                    'price' => $item->product->price,
                    'quantity' => $item->quantity,
                    'image_url' => $imageUrl,
                    'subtotal' => ($item->product->price ?? 0) * $item->quantity,
                    // Ràng buộc 8: Có thể chọn item để checkout
                    'is_selected' => true,
                ];
            })->toArray(),

            // Tổng kết của Shop này
            'summary' => [
                'subtotal' => $summary['subtotal'],
                'shipping_fee' => $summary['shipping_fee'],
                'discount' => $summary['discount'],
                'total' => $summary['total'],
            ],
        ];
    }
    public function handleAddItem(User $user, int $productId, int $quantity): CartItem
    {
        // 1. Lấy sản phẩm và lock hàng (để tránh race condition khi kiểm tra tồn kho)
        // Sẽ tự động bị rollback nếu có lỗi nhờ Transaction bên ngoài (Controller)
        $product = Product::lockForUpdate()->find($productId);

        if (!$product) {
            // Sử dụng NotFoundException nếu cần, nhưng Exception cơ bản cũng được
            throw new \Exception("Product not found.", 404);
        }

        // 2. Lấy Seller ID (vẫn nên giữ)
        $sellerId = $product->user_id;

        // 3. Tìm Cart Item hiện có
        $cartItem = CartItem::query()
            ->whereHas('cart', function (Builder $query) use ($user, $sellerId) {
                $query->where('user_id', $user->id)->where('seller_id', $sellerId);
            })
            ->where('product_id', $productId)
            ->first();

        $newQuantity = $quantity;

        if ($cartItem) {
            // Nếu đã có, tính toán số lượng mới
            $newQuantity = $cartItem->quantity + $quantity;
        }

        // 4. Kiểm tra lại stock với tổng số lượng mới (Đây là logic quan trọng)
        if ($product->stocks < $newQuantity) {
            throw new ConflictException('Số lượng trong giỏ hàng vượt quá số lượng tồn kho.');
        }

        // 5. Tìm hoặc tạo mới Cart Group
        $cart = Cart::firstOrCreate([
            'user_id' => $user->id,
            'seller_id' => $sellerId,
        ]);

        // 6. Cập nhật/Tạo mới Cart Item
        if ($cartItem) {
            $cartItem->update([
                'quantity' => $newQuantity,
                'price' => $product->price, // Cập nhật lại giá
            ]);
        } else {
            $cartItem = CartItem::create([
                'cart_id' => $cart->id,
                'product_id' => $productId,
                'quantity' => $quantity,
                'price' => $product->price,
                'added_at' => now(),
            ]);
        }

        // Trả về CartItem đã được tải thông tin product
        return $cartItem->load('product');
    }
    public function handleDeleteItem(CartItem $cartItem): void
    {
        DB::beginTransaction();
        try {
            // 1. Thực hiện xóa 
            $cartItem->delete();

            // 2. Kiểm tra nếu Cart (nhóm giỏ hàng) này không còn CartItem nào nữa thì xóa luôn Cart đó
            if ($cartItem->cart->items()->count() === 0) {
                $cartItem->cart->delete();
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
