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
use App\Exceptions\ForbiddenException;
use App\Exceptions\NotFoundException;

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
    public function handleAddItem(User $user, int $productId, int $quantity): void
    {
        DB::beginTransaction();
        try {
            // Lấy sản phẩm và lock hàng (để tránh race condition khi kiểm tra tồn kho)
            $product = Product::lockForUpdate()->find($productId);

            if (!$product) {
                // Controller đã kiểm tra exists:products,id nhưng vẫn nên có kiểm tra cuối
                throw new \Exception("Product not found.");
            }

            // 1. Kiểm tra Policy: (Controller đã authorize, nhưng ta vẫn ném exception cho an toàn)
            // Nếu kiểm tra Policy đã được chuyển lên Controller bằng $this->authorize('buySelf', $product), 
            // thì không cần kiểm tra lại logic này tại đây.
            
            $sellerId = $product->user_id;

            // 2. Kiểm tra tồn kho lần đầu
            if ($product->stocks < $quantity) {
                throw new ConflictException('Sản phẩm đã hết hàng hoặc không đủ số lượng.');
            }

            // 3. Tìm hoặc tạo mới nhóm giỏ hàng (Cart Group) cho C2C
            $cart = Cart::firstOrCreate([
                'user_id' => $user->id,
                'seller_id' => $sellerId,
            ]);

            // 4. Kiểm tra và cập nhật Cart Item (Sử dụng phương thức của Model)
            $cartItem = CartItem::findItemInCart($cart->id, $productId); 

            if ($cartItem) {
                // Nếu đã có, cập nhật số lượng
                $newQuantity = $cartItem->quantity + $quantity;
                
                // Kiểm tra lại số lượng tồn kho với tổng số lượng mới
                if ($product->stocks < $newQuantity) {
                    throw new ConflictException('Số lượng trong giỏ hàng vượt quá số lượng tồn kho.');
                }
                
                $cartItem->quantity = $newQuantity;
                $cartItem->price = $product->price; // Cập nhật lại giá
                $cartItem->save();
            } else {
                // Nếu chưa có, tạo mới cart_item
                CartItem::create([
                    'cart_id' => $cart->id,
                    'product_id' => $productId,
                    'quantity' => $quantity,
                    'price' => $product->price, // Lưu lại giá tại thời điểm thêm vào giỏ
                    'added_at' => now(),
                ]);
            }

            DB::commit(); // Hoàn tất giao dịch
        } catch (\Throwable $e) {
            DB::rollBack(); // Hoàn tác nếu có lỗi
            throw $e; // Ném lại ngoại lệ để Controller xử lý
        }
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
