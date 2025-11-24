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
        // $this->authorize('viewAnySeller', Order::class);

        $sellerId = Auth::id();
        // 2. Bắt đầu câu truy vấn cơ bản
        $ordersQuery = Order::with(['user'])
            ->whereHas('items.product', function ($query) use ($sellerId) {
                $query->where('seller_id', $sellerId);
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
     * Approve an order.
     */
    public function approve(Order $order): JsonResponse
    {
        $this->authorize('approve', $order);

        try {
            $order->status = 'processing';
            $order->save();

            // SỬA LỖI: Tải lại các mối quan hệ cần thiết cho OrderDetailResource
            $freshOrder = $order->fresh()->load(['user', 'address', 'items.product']);

            return response()->json([
                'success' => true,
                'message' => 'Đã xác nhận đơn hàng thành công.',
                'data'    => new OrderDetailResource($freshOrder)
            ]);

        } catch (\Exception $e) {
            Log::error('Lỗi khi duyệt đơn hàng: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Duyệt đơn hàng thất bại, vui lòng thử lại.'
            ], 500);
        }
    }

    /**
     * Reject an order.
     */
   /**
     * Reject an order.
     * API để Seller từ chối đơn hàng.
     */
    public function reject(Order $order): JsonResponse
    {
        // 1. Kiểm tra quyền hạn (Gọi hàm reject trong Policy)
        $this->authorize('reject', $order);

        try {
            // 2. Cập nhật trạng thái thành 'cancelled' (hoặc 'rejected' tùy ENUM của bạn)
            $order->status = 'cancelled';
            $order->save();

            // 3. Tải lại thông tin để trả về
            $freshOrder = $order->fresh()->load(['buyer', 'address', 'items.product']);

            return response()->json([
                'success' => true,
                'message' => 'Đã từ chối đơn hàng thành công.',
                'data'    => new OrderDetailResource($freshOrder)
            ]);

        } catch (\Exception $e) {
            Log::error('Lỗi khi từ chối đơn hàng: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Từ chối đơn hàng thất bại, vui lòng thử lại.'
            ], 500);
        }
    }
    public function show(Order $order): JsonResponse
    {
        // 1. Kiểm tra quyền hạn: Seller có quyền xem chi tiết đơn hàng này không?
        $this->authorize('view', $order);

        // 2. Tải các mối quan hệ cần thiết
        $order->load(['buyer', 'address', 'items.product']);

        // 3. Trả về response đã được định dạng
        return response()->json([
            'success' => true,
            'data'    => new OrderDetailResource($order)
        ]);
    }
}