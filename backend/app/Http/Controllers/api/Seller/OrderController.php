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
    // ... (Các hàm index, show giữ nguyên) ...
    public function index(Request $request)
    {
        $sellerId = Auth::id();
        $query = Order::query()
            ->where('seller_id', $sellerId)
            ->with(['user', 'orderItems.product']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('id', 'like', "%$search%")
                  ->orWhereHas('user', function($u) use ($search) {
                      $u->where('full_name', 'like', "%$search%");
                  });
            });
        }

        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

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

    public function show($id)
    {
        $user = Auth::user();
        $order = Order::with(['user', 'address', 'orderItems.product'])->find($id);

        if (!$order) {
            return response()->json(['success' => false, 'message' => 'Đơn hàng không tồn tại.'], 404);
        }

        $isSeller = $order->seller_id === $user->id;
        $isAdmin = ($user->role === 'admin');

        if (!$isSeller && !$isAdmin) {
            return response()->json(['success' => false, 'message' => 'Bạn không có quyền xem đơn hàng này.'], 403);
        }

        return response()->json([
            'success' => true,
            'data' => new OrderDetailResource($order)
        ]);
    }

    // 3. DUYỆT ĐƠN (Chuyển sang 'processing' - Đang xử lý/Đóng gói)
    public function approve($id)
    {
        $user = Auth::user();
        $order = Order::find($id);

        if (!$order || $order->seller_id !== $user->id) {
             return response()->json(['success' => false, 'message' => 'Không có quyền.'], 403);
        }

        if ($order->status !== 'pending') { 
            return response()->json(['success' => false, 'message' => 'Chỉ được duyệt đơn chờ xử lý.'], 400);
        }

        // SỬA: Chuyển sang processing thay vì shipped
        $order->update(['status' => 'processing']);

        return response()->json(['success' => true, 'message' => 'Đơn hàng đang được xử lý.']);
    }

    // 4. GIAO HÀNG (MỚI: Chuyển từ 'processing' sang 'shipped')
    public function ship($id)
    {
        $user = Auth::user();
        $order = Order::find($id);

        if (!$order || $order->seller_id !== $user->id) {
             return response()->json(['success' => false, 'message' => 'Không có quyền.'], 403);
        }

        if ($order->status !== 'processing') { 
            return response()->json(['success' => false, 'message' => 'Đơn hàng chưa sẵn sàng để giao.'], 400);
        }

        $order->update(['status' => 'shipped']);

        return response()->json(['success' => true, 'message' => 'Đơn hàng đã được giao cho vận chuyển.']);
    }

    // 5. Từ chối đơn hàng
    public function reject($id)
    {
        $user = Auth::user();
        $order = Order::find($id);

        if (!$order || $order->seller_id !== $user->id) {
             return response()->json(['success' => false, 'message' => 'Không có quyền.'], 403);
        }

        if ($order->status !== 'pending') {
            return response()->json(['success' => false, 'message' => 'Không thể hủy đơn hàng này.'], 400);
        }

        $order->update(['status' => 'cancelled']);

        return response()->json(['success' => true, 'message' => 'Đã từ chối đơn hàng.']);
    }
}