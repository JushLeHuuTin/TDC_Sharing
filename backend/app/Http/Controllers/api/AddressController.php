<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;

use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StoreAddressRequest;

class AddressController extends Controller
{
    /**
     * Tải tất cả địa chỉ của người dùng hiện tại.
     */
    public function index(): JsonResponse
    {
        $addresses = Auth::user()->addresses()->orderBy('id', 'desc')->get();
        
        return response()->json($addresses);
    }

    /**
     * Lưu địa chỉ mới vào cơ sở dữ liệu.
     */
    // public function store(StoreAddressRequest $request): JsonResponse
    // {
    //     if ($request->boolean('is_default')) {
    //         Auth::user()->addresses()->update(['is_default' => false]);
    //     }
        
    //     $address = Auth::user()->addresses()->create($request->validated());
        
    //     return response()->json([
    //         'message' => 'Địa chỉ đã được thêm.', 
    //         'address' => $address
    //     ], 201);
    // }

    // /**
    //  * Cập nhật địa chỉ đã tồn tại.
    //  */
    // public function update(StoreAddressRequest $request, Address $address): JsonResponse
    // {
    //     // Đảm bảo người dùng sở hữu địa chỉ
    //     if ($address->user_id !== Auth::id()) {
    //         return response()->json(['message' => 'Không có quyền truy cập.'], 403);
    //     }

    //     // Tương tự, nếu đặt làm mặc định, hủy đặt mặc định các địa chỉ khác
    //     if ($request->boolean('is_default')) {
    //         Auth::user()->addresses()->update(['is_default' => false]);
    //     }
        
    //     $address->update($request->validated());
        
    //     return response()->json([
    //         'message' => 'Địa chỉ đã được cập nhật.', 
    //         'address' => $address
    //     ]);
    // }

    // /**
    //  * Xóa địa chỉ.
    //  */
    // public function destroy(Address $address): JsonResponse
    // {
    //     if ($address->user_id !== Auth::id()) {
    //         return response()->json(['message' => 'Không có quyền truy cập.'], 403);
    //     }

    //     $address->delete();
        
    //     return response()->json(['message' => 'Địa chỉ đã được xóa thành công.']);
    // }
}