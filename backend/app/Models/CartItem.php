<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @property int $id
 * @property string $cart_item_id
 * @property int $cart_id
 * @property int $product_id
 * @property int $quantity
 * @property numeric $price
 * @property string|null $note
 * @property string|null $added_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Cart $cart
 * @property-read \App\Models\Product $product
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CartItem newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CartItem newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CartItem query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CartItem whereAddedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CartItem whereCartId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CartItem whereCartItemId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CartItem whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CartItem whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CartItem whereNote($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CartItem wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CartItem whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CartItem whereQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CartItem whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class CartItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'cart_item_id',
        'cart_id',
        'product_id',
        'quantity',
        'is_selected',
        'price',
        'note',
        'added_at'
    ];

    protected $casts = [
        'price' => 'decimal:2',
    ];

    public function cart()
    {
        return $this->belongsTo(Cart::class);
    }

    public static function findItemInCart(int $cartId, int $productId): ?self
    {
        return static::where('cart_id', $cartId)
            ->where('product_id', $productId)
            ->first();
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function getSubtotal()
    {
        return $this->price * $this->quantity;
    }
    public static function deleteSelectedItems(int $buyerId): int
    {
        // Bắt đầu truy vấn trên bảng cart_items
        $deletedCount = self::query()
            ->where('is_selected', 1)
            ->whereHas('cart', function (Builder $query) use ($buyerId) {
                $query->where('user_id', $buyerId);
            })
            ->delete();

        return $deletedCount;
    }
}
