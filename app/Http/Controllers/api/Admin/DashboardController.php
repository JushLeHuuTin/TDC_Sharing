<?php
// Vị trí: app/Http/Controllers/Api/Admin/DashboardController.php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    /**
     * Get dashboard statistics for the admin.
     */
    public function stats(): JsonResponse
    {
        // 1. Kiểm tra quyền hạn thông qua Policy/Gate
        Gate::authorize('viewAdminDashboard');

        try {
            // 2. Lấy tổng số đơn hàng
            $totalOrders = Order::count();

            // (Bạn có thể thêm các thống kê khác ở đây trong tương lai)
            // $totalUsers = User::count();
            // $totalRevenue = Order::where('status', 'delivered')->sum('final_amount');

            // 3. Trả về response thành công
            return response()->json([
                'success' => true,
                'data' => [
                    'total_orders' => $totalOrders,
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Lỗi khi lấy thống kê dashboard: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Không thể tải dữ liệu thống kê.'
            ], 500);
        }
    }
}