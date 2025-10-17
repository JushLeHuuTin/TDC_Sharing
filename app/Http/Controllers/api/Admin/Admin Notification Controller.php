<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreNotificationRequest;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\Admin\UpdateNotificationRequest;
use App\Http\Resources\Admin\NotificationResource;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Store a newly created notification in storage for multiple users.
     * API để Admin tạo và gửi thông báo cho một hoặc nhiều người dùng.
     */
    public function store(StoreNotificationRequest $request): JsonResponse
    {
        // 1. Kiểm tra quyền hạn: Đã được xử lý trong StoreNotificationRequest

        // 2. Lấy dữ liệu đã được validate
        $validatedData = $request->validated();

        $userIds = $validatedData['user_ids'];
        $notificationData = [
            'object' => $validatedData['object'],
            'content' => $validatedData['content'],
            'is_read' => false, // Mặc định là chưa đọc
            'created_at' => now(),
            'updated_at' => now(),
        ];

        // 3. Chuẩn bị dữ liệu để insert hàng loạt
        $notificationsToInsert = [];
        foreach ($userIds as $userId) {
            $notificationsToInsert[] = array_merge($notificationData, ['user_id' => $userId]);
        }

        // 4. Sử dụng DB Transaction để đảm bảo toàn vẹn dữ liệu
        DB::beginTransaction();
        try {
            // Insert hàng loạt để tối ưu hiệu năng
            Notification::insert($notificationsToInsert);

            DB::commit();

            // Logic gửi thông báo realtime (Pusher, Socket.IO) hoặc email có thể được trigger ở đây
            // event(new NotificationCreated($notificationsToInsert));

            return response()->json([
                'success' => true,
                'message' => 'Đã tạo thành công ' . count($notificationsToInsert) . ' thông báo.'
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Lỗi khi tạo thông báo hàng loạt: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Lưu thông báo thất bại, vui lòng thử lại.'
            ], 500);
        }
    }
    public function update(UpdateNotificationRequest $request, Notification $notification): JsonResponse
    {
        // 1. Quyền hạn đã được kiểm tra trong UpdateNotificationRequest

        // 2. Lấy dữ liệu đã được validate
        $validatedData = $request->validated();

        try {
            // 3. Cập nhật thông báo
            $notification->update($validatedData);

            // 4. Trả về response thành công
            return response()->json([
                'success' => true,
                'message' => 'Cập nhật thông báo thành công.',
                'data'    => $notification // Trả về dữ liệu mới của thông báo
            ]);
        } catch (\Exception $e) {
            Log::error('Lỗi khi cập nhật thông báo: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Cập nhật thông báo thất bại, vui lòng thử lại.'
            ], 500);
        }
    }
    public function index(Request $request): JsonResponse
    {
        // 1. Xác thực các tham số filter (nếu có)
        $request->validate([
            'is_read' => 'nullable|boolean',
            'object' => 'nullable|string|in:Thông tin,Khuyến mãi,Cảnh báo',
        ]);

        // 2. Xây dựng câu truy vấn, eager load 'user' để lấy tên người nhận
        $notificationsQuery = Notification::with('user')->latest();

        // 3. Áp dụng các bộ lọc
        if ($request->filled('is_read')) {
            $notificationsQuery->where('is_read', $request->query('is_read'));
        }
        if ($request->filled('object')) {
            $notificationsQuery->where('object', $request->query('object'));
        }

        // 4. Phân trang kết quả
        $notifications = $notificationsQuery->paginate(15);

        // 5. Trả về collection đã được định dạng qua API Resource
        return response()->json([
            'success' => true,
            'data' => NotificationResource::collection($notifications)
        ]);
    }
}
