<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreNotificationRequest;
use App\Http\Requests\Admin\UpdateNotificationRequest;
use App\Http\Resources\Admin\NotificationResource;
use App\Models\Notification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Thêm Auth Facade
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class NotificationController extends Controller
{
    /**
     * Display a listing of the resource.
     * API để Admin xem danh sách tất cả thông báo.
     */
    public function index(Request $request): JsonResponse
    {
        // 1. Xác thực các tham số filter (nếu có)
        $request->validate([
            'is_read' => 'nullable|boolean',
            'type' => 'nullable|string|in:Thông tin,Khuyến mãi,Cảnh báo',
        ]);

        // 2. Xây dựng câu truy vấn, eager load 'user' để lấy tên người nhận
        $notificationsQuery = Notification::with('user')->latest();

        // 3. Áp dụng các bộ lọc
        if ($request->filled('is_read')) {
            $notificationsQuery->where('is_read', $request->query('is_read'));
        }
        if ($request->filled('type')) {
            $notificationsQuery->where('type', $request->query('type'));
        }

        // 4. Phân trang kết quả
        $notifications = $notificationsQuery->paginate(15);

        // 5. Trả về collection đã được định dạng qua API Resource
        return response()->json([
            'success' => true,
            'data' => NotificationResource::collection($notifications)
        ]);
    }

    /**
     * Store a newly created resource in storage.
     * API để Admin tạo thông báo mới cho nhiều người dùng.
     */
    public function store(StoreNotificationRequest $request): JsonResponse
    {
        $validatedData = $request->validated();
        
        DB::beginTransaction();
        try {
            $notifications = [];
            foreach ($validatedData['user_ids'] as $userId) {
                $notifications[] = [
                    'user_id' => $userId,
                    'type'    => $validatedData['type'],
                    'content' => $validatedData['content'],
                    'is_read' => false, // Mặc định là chưa đọc
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            // Dùng insert() để tăng hiệu năng khi thêm nhiều bản ghi
            Notification::insert($notifications);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Đã tạo thông báo thành công.'
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Lỗi khi tạo thông báo: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Tạo thông báo thất bại, vui lòng thử lại.'
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     * API để Admin cập nhật một thông báo.
     */
    public function update(UpdateNotificationRequest $request, Notification $notification): JsonResponse
    {
        $validatedData = $request->validated();

        try {
            $notification->update($validatedData);

            return response()->json([
                'success' => true,
                'message' => 'Cập nhật thông báo thành công.',
                'data'    => new NotificationResource($notification)
            ]);

        } catch (\Exception $e) {
            Log::error('Lỗi khi cập nhật thông báo: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Cập nhật thông báo thất bại, vui lòng thử lại.'
            ], 500);
        }
    }


    /**
     * Remove the specified notification from storage.
     * API để Admin xóa một thông báo.
     */
    public function destroy(Notification $notification): JsonResponse
    {
        // Sử dụng Auth Facade để IDE có thể nhận diện
        $user = Auth::user();

        // 1. Kiểm tra người dùng có tồn tại và có phải là Admin không
        if (!$user || $user->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không có quyền thực hiện hành động này.'
            ], 403);
        }
        
        try {
            // 2. Thực hiện xóa
            $notification->delete();

            // 3. Trả về response thành công
            return response()->json([
                'success' => true,
                'message' => 'Xóa thông báo thành công.'
            ]);

        } catch (\Exception $e) {
            Log::error('Lỗi khi xóa thông báo: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Xóa thông báo thất bại, vui lòng thử lại.'
            ], 500);
        }
    }
}

