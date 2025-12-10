<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreNotificationRequest;
use App\Http\Requests\Admin\UpdateNotificationRequest;
use App\Models\Notification;
use App\Models\User; // Import User
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\Admin\NotificationResource;

class NotificationController extends Controller
{
    // 1. Lấy danh sách (Kèm Lọc & Phân trang)
    public function index(Request $request): JsonResponse
    {
        // Eager load 'user' để lấy tên người nhận
        $query = Notification::with('user')->latest();

        // Lọc theo nội dung
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('content', 'like', "%{$search}%")
                  ->orWhereHas('user', function($q) use ($search) {
                      $q->where('full_name', 'like', "%{$search}%"); // Tìm theo tên người nhận
                  });
        }

        // Lọc theo loại
        if ($request->filled('type') && $request->type !== '') {
            $query->where('type', $request->type);
        }

        $notifications = $query->paginate(10);

        return response()->json([
            'success' => true,
            'data' => NotificationResource::collection($notifications),
            'meta' => [
                'current_page' => $notifications->currentPage(),
                'last_page' => $notifications->lastPage(),
                'total' => $notifications->total(),
                'per_page' => $notifications->perPage(),
                'from' => $notifications->firstItem(),
                'to' => $notifications->lastItem(),
            ]
        ]);
    }

    // 2. Thêm mới
    public function store(request $request): JsonResponse
    {
        $validated = $request->validate([
            'user_id' => 'nullable|integer|exists:users,id', // Cho phép null (Gửi tất cả) hoặc phải là ID tồn tại
            'type'    => 'required|string|in:system,promotion,order,warning,message',
            'content' => 'required|string|max:255',
        ]);

        $validated['is_read'] = false; // Mặc định chưa xem

        $notification = Notification::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Tạo thông báo thành công.',
            'data' => new NotificationResource($notification)
        ]);
    }

    // 3. Cập nhật
    public function update(UpdateNotificationRequest $request, $id): JsonResponse
    {
        $notification = Notification::find($id);

        if (!$notification) {
            return response()->json(['success' => false, 'message' => 'Thông báo không tồn tại.'], 404);
        }
        if ($request->has('updated_at') && $request->input('updated_at') != $notification->updated_at->toISOString()) {
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu đã bị thay đổi, vui lòng tải lại.'
            ], 409); // 409 Conflict
        }
        $validated = $request->validate([
            'user_id' => 'nullable|integer|exists:users,id',
            'type'    => 'sometimes|string|in:system,promotion,order,warning,message',
            'content' => 'sometimes|string|max:255',
            'is_read' => 'boolean'
        ]);

        $notification->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật thành công.',
            'data' => new NotificationResource($notification)
        ]);
    }

    // 4. Xóa
    public function destroy($id): JsonResponse
    {
        $notification = Notification::find($id);
        if (!$notification) {
            return response()->json(['success' => false, 'message' => 'Thông báo không tồn tại.'], 404);
        }

        $notification->delete();
        return response()->json(['success' => true, 'message' => 'Đã xóa thông báo.']);
    }

    // 5. API phụ: Lấy danh sách User để hiển thị trong dropdown chọn người nhận
    public function getUsersForSelect(): JsonResponse
    {
        $users = User::select('id', 'full_name', 'email')->orderBy('id', 'desc')->get();
        return response()->json([
            'success' => true,
            'data' => $users
        ]);
    }
}