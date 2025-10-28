<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\StoreVoucherRequest;
use App\Models\Voucher;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\UpdateVoucherRequest;


class VoucherController extends Controller
{
    /**
     * API để tạo mới một mã giảm giá (voucher).
     *
     * @param StoreVoucherRequest $request
     * @return JsonResponse
     */
    public function store(StoreVoucherRequest $request): JsonResponse
    {
        // 16. Bảo mật: Việc kiểm tra Auth và Role đã được thực hiện trong StoreVoucherRequest::authorize().
        // Nếu request này được gọi, nghĩa là user đã được xác thực và có vai trò hợp lệ.

        // Lấy dữ liệu đã được validate.
        $validatedData = $request->validated();

        // 14. Nút Lưu voucher: Insert DB và xử lý lỗi
        try {
            // Thêm created_by (ID người dùng hiện tại)
            $validatedData['created_by'] = Auth::id();
            // Đặt mặc định số lần đã dùng là 0 (Đã có trong migration, nhưng đặt lại để đảm bảo)
            $validatedData['used_count'] = 0;

            // Bắt đầu transaction để đảm bảo tính toàn vẹn
            DB::beginTransaction();

            $voucher = Voucher::create($validatedData);

            DB::commit();

            return response()->json([
                'message' => 'Tạo voucher thành công.',
                'voucher' => $voucher,
            ], 201); // 201 Created

        } catch (\Exception $e) {
            DB::rollBack();

            // Ghi log lỗi để dễ dàng gỡ lỗi server
            Log::error('Lỗi khi tạo voucher:', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id(),
                'data' => $validatedData,
            ]);

            // Lỗi/Bug có thể gặp: Lỗi lưu DB, Lỗi server
            // Giải pháp/Thông báo:
            return response()->json([
                'message' => 'Lưu voucher thất bại, vui lòng thử lại.',
                // Trong môi trường development, có thể trả về lỗi chi tiết.
                // 'error_detail' => $e->getMessage(),
            ], 500); // 500 Internal Server Error
        }
    }
    public function show(int $id): JsonResponse
    {
        // Giả sử đã check role 'admin' trong middleware/policy
        
        $voucher = Voucher::find($id);

        if (!$voucher) {
            return response()->json([
                'message' => 'Mã voucher không tồn tại.',
            ], 404);
        }

        return response()->json([
            'data' => $voucher
        ], 200);
    }

    /**
     * API cập nhật thông tin mã voucher
     * Ràng buộc 1-15
     */
    public function update(UpdateVoucherRequest $request, int $id): JsonResponse
    {
        // Dữ liệu đã được xác thực và làm sạch bởi UpdateVoucherRequest
        $data = $request->validated();
        
        // Ràng buộc 1: Tìm voucher
        $voucher = Voucher::find($id);

        if (!$voucher) {
            return response()->json([
                'message' => 'Không tìm thấy mã voucher cần cập nhật.',
            ], 404);
        }

        // Ràng buộc 15: Bắt đầu Transaction và xử lý lỗi DB
        DB::beginTransaction();
        try {
            // Ràng buộc 1-13: Cập nhật dữ liệu
            $voucher->update($data);

            DB::commit();

            // Ràng buộc 15: Thông báo thành công
            return response()->json([
                'message' => 'Cập nhật voucher thành công. 🎉',
                'data' => $voucher,
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();

            // Ràng buộc 15: Xử lý lỗi server
            // Log::error("Update Voucher Error: " . $e->getMessage()); // Nên log lỗi
            return response()->json([
                'message' => 'Lưu voucher thất bại, vui lòng thử lại. Lỗi hệ thống.',
                'error' => $e->getMessage()
            ], 500);
        }
}
}
