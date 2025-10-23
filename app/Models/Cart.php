<?php

namespace App\Models;

use App\Http\Requests\ProcessOrderRequest;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;

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

    protected $fillable = ['user_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }

    public function getTotalPrice()
    {
        return $this->items->sum(function ($item) {
            return $item->price * $item->quantity;
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
}
