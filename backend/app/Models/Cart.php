<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property int $user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\CartItem> $items
 * @property-read int|null $items_count
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cart newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cart newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cart query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cart whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cart whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cart whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cart whereUserId($value)
 * @mixin \Eloquent
 */
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
}
