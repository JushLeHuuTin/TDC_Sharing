<?php

namespace App\Http\Controllers\Api\Seller;

use App\Http\Controllers\Controller;
use App\Http\Resources\Seller\OrderResource;
use App\Http\Resources\Seller\OrderDetailResource;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    // 1. DANH SÁCH ĐƠN HÀNG (Kèm phân trang, lọc)
    public function index(Request $request)
    {
        $sellerId = Auth::id();
        
        $query = Order::query()
            ->where('seller_id', $sellerId)
            ->with(['user', 'orderItems.product']);

        // --- BỘ LỌC CHUẨN ---
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        // --- FIX LOGIC TÌM KIẾM: Chỉ tìm Mã đơn và Khách hàng ---
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                // 1. Tìm theo ID/Mã đơn
                $q->where('id', 'like', "%$search%")
                  // 2. Hoặc tìm theo tên khách hàng
                  ->orWhereHas('user', function($u) use ($search) {
                      $u->where('full_name', 'like', "%$search%");
                  });
                // Đã loại bỏ tìm kiếm theo seller tại đây
            });
        }

        // --- LỌC THEO NGÀY (Logic này là chuẩn, không cần sửa) ---
        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        $orders = $query->latest()->paginate(4);

        return response()->json([
            'success' => true,
            'data' => OrderResource::collection($orders),
            'meta' => [
                'current_page' => $orders->currentPage(),
                'last_page' => $orders->lastPage(),
                'per_page' => $orders->perPage(),
                'total' => $orders->total(),
            ]
        ]);
    }

    // ... (Các hàm show, approve, ship, reject giữ nguyên) ...
    public function show($id)
    {
        $user = Auth::user();
        $order = Order::with(['user', 'address', 'orderItems.product'])->find($id);
        if (!$order) return response()->json(['success' => false, 'message' => 'Đơn hàng không tồn tại.'], 404);
        
        $isSeller = $order->seller_id === $user->id;
        $isAdmin = ($user->role === 'admin');

        if (!$isSeller && !$isAdmin) {
            return response()->json(['success' => false, 'message' => 'Bạn không có quyền xem đơn hàng này.'], 403);
        }

        return response()->json(['success' => true, 'data' => new OrderDetailResource($order)]);
    }

    public function approve($id)
    {
        $sellerId = Auth::id();
$order = Order::where('seller_id', $sellerId)->find($id);

        if (!$order) return response()->json(['success' => false, 'message' => 'Không tìm thấy đơn hàng.'], 404);
        if ($order->status !== 'pending') return response()->json(['success' => false, 'message' => 'Đơn hàng không ở trạng thái chờ duyệt.'], 400);

        $order->update(['status' => 'processing']);

        return response()->json(['success' => true, 'message' => 'Đơn hàng đang được xử lý.']);
    }

    public function ship($id)
    {
        $sellerId = Auth::id();
        $order = Order::where('seller_id', $sellerId)->find($id);

        if (!$order) return response()->json(['success' => false, 'message' => 'Không tìm thấy đơn hàng.'], 404);
        if ($order->status !== 'processing') return response()->json(['success' => false, 'message' => 'Đơn hàng chưa sẵn sàng để giao.'], 400);

        $order->update(['status' => 'shipped']);

        return response()->json(['success' => true, 'message' => 'Đã chuyển trạng thái sang giao hàng.']);
    }

    public function reject($id)
    {
        $sellerId = Auth::id();
        $order = Order::where('seller_id', $sellerId)->find($id);

        if (!$order) return response()->json(['success' => false, 'message' => 'Không tìm thấy đơn hàng.'], 404);
        if ($order->status !== 'pending') return response()->json(['success' => false, 'message' => 'Chỉ có thể từ chối đơn hàng mới.'], 400);

        $order->update(['status' => 'cancelled']);

        return response()->json(['success' => true, 'message' => 'Đã từ chối đơn hàng.']);
    }
}