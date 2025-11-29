<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class Cart extends Model
{
    use HasFactory;

    /**
     * user_id: Người mua (Buyer)
     * seller_id: Người bán (Vendor/Shop)
     */
    protected $fillable = ['user_id', 'seller_id'];

    // --- QUAN HỆ VỚI NGƯỜI MUA ---
    public function user(): BelongsTo
    {
        // Quan hệ với User (người mua)
        return $this->belongsTo(User::class, 'user_id');
    }

    // --- QUAN HỆ VỚI NGƯỜI BÁN ---
    public function seller(): BelongsTo
    {
        // Quan hệ với User (người bán - vì bảng Product dùng user_id làm seller_id)
        // Đây là cách Eloquent hiểu ai là người bán của nhóm giỏ hàng này.
        return $this->belongsTo(User::class, 'seller_id');
    }

    // --- QUAN HỆ VỚI CÁC MỤC TRONG GIỎ HÀNG ---
    public function items(): HasMany
    {
        return $this->hasMany(CartItem::class, 'cart_id');
    }
    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }
    // Phương thức tính tổng giá (Tạm thời. Cần Product Price từ CartItem)
    public function getTotalPrice(): float
    {
        return $this->items->sum(function ($item) {
            if ($item->is_selected == 1) {
                return floatval($item->product->price ?? 0) * $item->quantity;
            }
            return 0;
        });
    }
    public function scopeForUserWithDetails(Builder $query, int $userId): void
    {
        $query->where('user_id', $userId)
            ->with('cartItems.product');
    }
    public function scopeActiveUserWithDetails(Builder $query, int $userId): Builder
    {
        return $query->where('user_id', $userId)
            ->with('cartItems.product');
    }
    public function isAvailableForCheckout(): bool
    {
        return $this->exists && $this->cartItems()->exists();
    }
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
    public function scopeGetSelectedItemsByBuyer(Builder $query, int $buyerId): Builder
    {
        return $query
            ->where('user_id', $buyerId)
            ->whereHas('cartItems', function (Builder $itemQuery) {
                $itemQuery->where('is_selected', 1);
            })
            ->with(['cartItems' => function ($itemQuery) {
                $itemQuery->where('is_selected', 1)
                    ->with('product');
            }]);
    }
    public static function cleanupEmptyCarts(int $buyerId): int
    {
        return self::query()
            ->where('user_id', $buyerId)
            ->doesntHave('cartItems')
            ->delete();
    }
}
