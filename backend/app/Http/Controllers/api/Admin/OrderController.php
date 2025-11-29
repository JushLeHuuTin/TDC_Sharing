<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\OrderResource;
use App\Http\Resources\Admin\OrderDetailResource; // Resource riêng cho chi tiết
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log; // Để log lỗi nếu có

class OrderController extends Controller
{
    // 1. DANH SÁCH ĐƠN HÀNG
    public function index(Request $request)
    {
        // Eager load để tránh N+1 query: lấy luôn user (khách), seller (người bán), và items (để đếm số lượng)
        $query = Order::query()->with(['user', 'seller', 'orderItems']);

        // --- BỘ LỌC ---
        if ($request->filled('status')) {
            if ($request->status !== '') { // Chỉ lọc nếu status không rỗng
                 $query->where('status', $request->status);
            }
        }
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('id', 'like', "%$search%")
                  ->orWhereHas('user', function($u) use ($search) {
                      $u->where('full_name', 'like', "%$search%"); // Tìm theo tên khách
                  })
                  ->orWhereHas('seller', function($s) use ($search) {
                      $s->where('full_name', 'like', "%$search%"); // Tìm theo tên shop
                  });
            });
        }

        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        // Phân trang 10 dòng/trang, sắp xếp mới nhất
        $orders = $query->latest()->paginate(10);

        return response()->json([
            'success' => true,
            'data' => OrderResource::collection($orders),
            'meta' => [
                'current_page' => $orders->currentPage(),
                'last_page' => $orders->lastPage(),
                'total' => $orders->total(),
            ]
        ]);
    }

    // 2. XEM CHI TIẾT ĐƠN HÀNG
    public function show($id)
    {
        // Tìm đơn hàng, kèm theo thông tin User, Seller, Địa chỉ, và Sản phẩm
        $order = Order::with(['user', 'seller', 'address', 'orderItems.product'])->find($id);

        if (!$order) {
            return response()->json(['success' => false, 'message' => 'Đơn hàng không tồn tại.'], 404);
        }

        // Trả về resource chi tiết (OrderDetailResource)
        return response()->json([
            'success' => true,
            'data' => new OrderDetailResource($order)
        ]);
    }

    // 3. XÓA ĐƠN HÀNG
    public function destroy($id)
    {
        $order = Order::find($id);

        if (!$order) {
            return response()->json(['success' => false, 'message' => 'Đơn hàng không tồn tại.'], 404);
        }

        try {
            // Xóa các chi tiết đơn hàng trước (nếu chưa cài đặt cascade delete trong DB)
            $order->orderItems()->delete();
            
            // Xóa đơn hàng chính
            $order->delete();

            return response()->json([
                'success' => true,
                'message' => 'Đã xóa đơn hàng thành công.'
            ]);
        } catch (\Exception $e) {
            Log::error("Lỗi xóa đơn hàng Admin: " . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Lỗi server khi xóa đơn hàng.'], 500);
        }
    }
}