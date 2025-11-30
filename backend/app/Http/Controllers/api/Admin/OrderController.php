<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\OrderResource;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;


class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        // 1. Kiểm tra quyền hạn thông qua Policy
        $this->authorize('viewAny', Order::class);

        // 2. Eager load các mối quan hệ để tối ưu query
        // Lấy đơn hàng kèm thông tin: người mua, các mục trong đơn, sản phẩm và người bán sản phẩm đó
        $ordersQuery = Order::with(['user', 'items.product.seller'])->latest();

        // 3. Phân trang kết quả
        $orders = $ordersQuery->paginate(15);

        // 4. Trả về collection đã được định dạng qua API Resource
        return response()->json([
            'success' => true,
            'data'    => OrderResource::collection($orders),
        ]);
    }
   
}
