<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreNotificationRequest;
use App\Models\Notification;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\Admin\UpdateNotificationRequest;
use App\Http\Resources\Admin\NotificationResource;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
     /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Notification::class);

        $notificationsQuery = Notification::with('user')->latest();

        $notifications = $notificationsQuery->paginate(15);

        return response()->json([
            'success' => true,
            'data'    => NotificationResource::collection($notifications)
        ]);
    }
    /**
     * Store a newly created notification in storage.
     * API để Admin tạo thông báo mới cho nhiều người dùng.
     */
    public function store(StoreNotificationRequest $request): JsonResponse
    {
        // 1. Kiểm tra quyền hạn thông qua Policy
        // This will automatically check the 'create' method in NotificationPolicy
        $this->authorize('create', Notification::class);

        // 2. Lấy dữ liệu đã được validate từ file Request
        $validatedData = $request->validated();

        // 3. Sử dụng transaction để đảm bảo an toàn dữ liệu
        DB::beginTransaction();
        try {
            $notifications = [];
            $userCount = count($validatedData['user_ids']);

            foreach ($validatedData['user_ids'] as $userId) {
                $notifications[] = [
                    'user_id'    => $userId,
                    'type'       => $validatedData['type'],
                    'content'    => $validatedData['content'],
                    'is_read'    => false, // Mặc định là chưa đọc
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            // Dùng insert() để tăng hiệu năng khi thêm nhiều bản ghi
            Notification::insert($notifications);
            
            
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Đã tạo thông báo thành công cho {$userCount} người dùng."
            ], 201); // 201 Created

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Lỗi khi tạo thông báo: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Tạo thông báo thất bại, vui lòng thử lại.'
            ], 500); // Internal Server Error
        }
    }
        /**
     * Update the specified notification in storage.
     * API để Admin cập nhật một thông báo.
     */
    public function update(UpdateNotificationRequest $request, Notification $notification): JsonResponse
    {
        // 1. Kiểm tra quyền hạn thông qua Policy
        $this->authorize('update', $notification);

        // 2. Lấy dữ liệu đã được validate
        $validatedData = $request->validated();

        // 3. Thực hiện cập nhật
        try {
            $notification->update($validatedData);

            return response()->json([
                'success' => true,
                'message' => 'Cập nhật thông báo thành công.',
                'data'    => $notification // Trả về thông tin thông báo đã được cập nhật
            ]);

        } catch (\Exception $e) {
            Log::error('Lỗi khi cập nhật thông báo: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Cập nhật thông báo thất bại, vui lòng thử lại.'
            ], 500);
        }
    }

}