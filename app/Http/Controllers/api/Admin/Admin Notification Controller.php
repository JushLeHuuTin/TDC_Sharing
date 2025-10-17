<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreNotificationRequest;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class NotificationController extends Controller
{
    /**
     * Store a newly created notification in storage for multiple users.
     * API để Admin tạo và gửi thông báo cho một hoặc nhiều người dùng.
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
}