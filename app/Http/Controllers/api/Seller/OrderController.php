<?php

namespace App\Http\Controllers\Api\Seller;

use App\Http\Controllers\Controller;
use App\Http\Resources\Seller\OrderResource;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource for the authenticated seller.
     */
    public function index(Request $request): JsonResponse
    {
        // 1. Kiểm tra quyền hạn: Người dùng có được xem danh sách đơn hàng của seller không?
        $this->authorize('viewAnySeller', Order::class);

        $sellerId = Auth::id();

        // 2. Xây dựng câu truy vấn
        $ordersQuery = Order::with(['buyer', 'items.product'])
            // Chỉ lấy những đơn hàng có chứa ít nhất một sản phẩm của người bán này
            ->whereHas('items.product', function ($query) use ($sellerId) {
                $query->where('user_id', $sellerId);
            })
            ->latest(); // Sắp xếp theo mới nhất

        // 3. Phân trang kết quả
        $orders = $ordersQuery->paginate(15);

        // 4. Trả về response đã được định dạng
        return response()->json([
            'success' => true,
            'data'    => OrderResource::collection($orders)
        ]);
    }
}