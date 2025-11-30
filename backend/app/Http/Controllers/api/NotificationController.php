<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Notification;
use App\Http\Resources\NotificationResource; // Sử dụng chung hoặc tạo mới

class NotificationController extends Controller
{
    // Lấy danh sách thông báo của user đang đăng nhập
    public function index()
    {
        $user = Auth::user();
        
        // Lấy thông báo riêng của user HOẶC thông báo hệ thống (user_id = null)
        // Lưu ý: Với thông báo hệ thống (null), việc check "đã đọc" cần bảng phụ, 
        // nhưng ở đây ta tạm thời chỉ xử lý thông báo cá nhân để đơn giản hóa logic "đã đọc".
        $notifications = Notification::where('user_id', $user->id)
                                     ->orderBy('created_at', 'desc')
                                     ->paginate(10);

        return response()->json([
            'success' => true,
            'data' => $notifications->items(),
            'unread_count' => Notification::where('user_id', $user->id)->where('is_read', false)->count()
        ]);
    }

    // Đánh dấu đã đọc
    public function markAsRead($id)
    {
        $notification = Notification::where('user_id', Auth::id())->where('id', $id)->first();

        if ($notification) {
            $notification->update(['is_read' => true]);
            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false, 'message' => 'Không tìm thấy thông báo'], 404);
    }

    // Đánh dấu tất cả là đã đọc
    public function markAllRead()
    {
        Notification::where('user_id', Auth::id())
                    ->where('is_read', false)
                    ->update(['is_read' => true]);
                    
        return response()->json(['success' => true]);
    }
}