<?php

namespace App\Http\Controllers\Api\Seller;

use App\Http\Controllers\Controller;
use App\Http\Requests\Seller\FilterSellerOrdersRequest; // <-- Import file Request mới
use App\Http\Resources\Seller\OrderDetailResource;
use App\Http\Resources\Seller\OrderResource;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    /**
     * CẬP NHẬT: Display a listing of the resource for the authenticated seller with filters.
     */
    public function index(FilterSellerOrdersRequest $request): JsonResponse // <-- Thay đổi ở đây
    {
        // 1. Kiểm tra quyền
        $this->authorize('viewAnySeller', Order::class);

        $sellerId = Auth::id();

        // 2. Bắt đầu câu truy vấn cơ bản
        $ordersQuery = Order::with(['buyer'])
            ->whereHas('items.product', function ($query) use ($sellerId) {
                $query->where('user_id', $sellerId);
            });

        // 3. ÁP DỤNG CÁC BỘ LỌC (PHẦN MỚI)
        // Chỉ thêm điều kiện lọc KHI người dùng có gửi tham số tương ứng
        $ordersQuery->when($request->filled('status'), function ($query) use ($request) {
            $query->where('status', $request->query('status'));
        });

        $ordersQuery->when($request->filled('from_date'), function ($query) use ($request) {
            $query->whereDate('created_at', '>=', $request->query('from_date'));
        });

        $ordersQuery->when($request->filled('to_date'), function ($query) use ($request) {
            $query->whereDate('created_at', '<=', $request->query('to_date'));
        });

        // 4. Sắp xếp và phân trang
        $orders = $ordersQuery->latest()->paginate(15);

        return response()->json([
            'success' => true,
            'data'    => OrderResource::collection($orders)
        ]);
    }

    /**
     * PHẦN MỚI: Approve an order.
     * API để người bán xác nhận một đơn hàng.
     */
    public function approve(Order $order): JsonResponse
    {
        // 1. Kiểm tra quyền hạn thông qua Policy
        $this->authorize('approve', $order);

        try {
            // 2. Cập nhật trạng thái đơn hàng
            $order->status = 'delivering'; // Hoặc 'confirmed' tùy theo quy trình của bạn
            $order->save();

            // 3. Trả về response thành công
            return response()->json([
                'success' => true,
                'message' => 'Đã xác nhận đơn hàng thành công.',
                'data'    => new OrderDetailResource($order->fresh()) // Trả về chi tiết đơn hàng đã cập nhật
            ]);

        } catch (\Exception $e) {
            Log::error('Lỗi khi duyệt đơn hàng: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Duyệt đơn hàng thất bại, vui lòng thử lại.'
            ], 500);
        }
    }
}