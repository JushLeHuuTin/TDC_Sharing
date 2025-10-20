<?php

namespace App\Http\Controllers\Api\Seller;

use App\Http\Controllers\Controller;
use App\Http\Resources\Seller\OrderResource;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class OrderController extends Controller
{
    /**
     * Display a listing of orders for the authenticated seller.
     * API để lấy danh sách đơn hàng cho người bán đã đăng nhập.
     */
    public function index(Request $request): JsonResponse
    {
        // 1. Validate dữ liệu filter từ query string
        $request->validate([
            'status' => 'nullable|string|in:pending,confirmed,delivering,completed,rejected,cancelled',
        ]);

        $seller = $request->user();

        // 2. Xây dựng câu truy vấn chính
        // Lấy các đơn hàng mà CÓ CHỨA (whereHas) ít nhất một sản phẩm (items.product)
        // thuộc về người bán này (where user_id = $seller->id)
        $ordersQuery = Order::whereHas('items.product', function ($query) use ($seller) {
            $query->where('user_id', $seller->id);
        })->with('buyer'); // Eager load thông tin người mua để tối ưu

        // 3. Áp dụng bộ lọc trạng thái nếu có
        if ($request->filled('status')) {
            $ordersQuery->where('status', $request->query('status'));
        }

        // 4. Sắp xếp theo mới nhất và phân trang
        $orders = $ordersQuery->latest()->paginate(10);

        // 5. Trả về dữ liệu đã được định dạng qua API Resource
        return response()->json([
            'success' => true,
            'data' => OrderResource::collection($orders),
        ]);
    }
}