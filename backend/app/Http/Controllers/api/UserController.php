<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;

class UserController extends Controller
{
    // app/Http/Controllers/Api/UserController.php

    public function getProfile(): JsonResponse
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        // Eager load quan hệ addresses
        $user->load('addresses');

        $fullAddress = $user->full_address;
        // $defaultAddress = $user->addresses()->where('is_default', true)->first() 
        //           ?? $user->addresses()->first();
        $shippingPhone = $defaultAddress->phone ?? $user->phone;
        return response()->json([
            'message' => 'User profile fetched successfully.',
            'data' => [
                'id' => $user->id,
                'name' => $user->full_name ?? $user->name,
                'email' => $user->email,
                'phone' => $user->addresses()->first()->phone ,
                // Trả về địa chỉ đầy đủ đã được định dạng
                'full_shipping_address' => $fullAddress ?? 'Chưa cập nhật địa chỉ giao hàng',
            ],
        ]);
    }
}
