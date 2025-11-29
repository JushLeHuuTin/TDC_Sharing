<?php

namespace App\Policies;

use App\Models\CartItem;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class CartItemPolicy
{
   public function delete(User $user, CartItem $cartItem): bool
    {
        return $cartItem->cart->user_id === $user->id;
    }
   public function update(User $user, CartItem $cartItem): bool
    {
        return $cartItem->cart->user_id === $user->id;
    }
}
