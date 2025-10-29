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
use App\Http\Requests\VoucherIndexRequest;
use Carbon\Carbon;


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
    public function index(VoucherIndexRequest $request): JsonResponse
    {
        try {
            $this->authorize('viewAny', Voucher::class);
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            // Người dùng đã đăng nhập nhưng không có quyền
            return response()->json([
                'success' => false,
                'message' => "Bạn không có quyền truy cập danh sách voucher."
            ], 403);
        } catch (\Exception $e) {
            // Bắt các lỗi khác (ví dụ: lỗi DB, nhưng sau khi fix bước 1 sẽ ít gặp)
            return response()->json([
                'success' => false,
                'message' => 'Lỗi server nội bộ: ' . $e->getMessage()
            ], 500);
        }
        $validatedData = $request->validated();

        $perPage = $validatedData['per_page'] ?? 15;
        // Bắt đầu Query Builder
        $query = Voucher::query();
        $now = Carbon::now();

        // Ràng buộc 1: Tìm kiếm theo mã hoặc tên
        if (!empty($validatedData['search'])) {
            $keyword = '%' . $validatedData['search'] . '%';
            $query->where(function ($q) use ($keyword) {
                $q->where(DB::raw('LOWER(code)'), 'like', $keyword)
                    ->orWhere(DB::raw('LOWER(name)'), 'like', $keyword);
            });
        }

        // Ràng buộc 2: Bộ lọc trạng thái
        if (!empty($validatedData['status'])) {
            switch ($validatedData['status']) {
                case 'active':
                    // Đang hoạt động: is_active = true VÀ chưa hết hạn
                    $query->where('is_active', true)
                        ->where('start_date', '<=', $now)
                        ->where('end_date', '>=', $now);
                    break;
                case 'expired':
                    // Hết hạn: end_date đã qua HOẶC số lượng đã hết
                    $query->where('end_date', '<', $now)
                        ->orWhere('quantity', '<=', DB::raw('used_count')); // Ràng buộc 8
                    break;
                case 'inactive':
                    // Chưa hoạt động hoặc bị tắt thủ công
                    $query->where('is_active', false)
                        ->orWhere('start_date', '>', $now);
                    break;
            }
        }

        // Ràng buộc 3: Bộ lọc loại giảm giá
        if (!empty($validatedData['type'])) {
            $query->where('discount_type', $validatedData['type']);
        }

        // Ràng buộc 10: Sắp xếp
        $sortBy = $validatedData['sort_by'] ?? 'created_at';
        $sortDir = $validatedData['sort_dir'] ?? 'desc';
        $query->orderBy($sortBy, $sortDir);

        // // Ràng buộc 9 & 13: Phân trang và Hiệu năng
        $vouchers = $query->paginate($perPage);

        // // Ràng buộc 5: Thống kê nhanh
        $stats = $this->getQuickStats();

        // Ràng buộc 4 & 6: Hiển thị và Trạng thái
        return response()->json([
            'message' => 'Lấy danh sách voucher thành công.',
            'stats' => $stats,
            'data' => $vouchers->through(function ($voucher) use ($now) {
                return [
                    'id' => $voucher->id,
                    'code' => $voucher->code,
                    'type' => $voucher->discount_type,
                    'value' => $voucher->discount_value,
                    'max_value' => $voucher->discount_max,
                    'quantity' => $voucher->quantity,
                    'used_count' => $voucher->used_count,
                    'start_date' => $voucher->start_date,
                    'end_date' => $voucher->end_date,
                    // Ràng buộc 6: Trạng thái hiển thị (Logic Front-end nên xử lý màu sắc)
                    'status_text' => $this->getVoucherStatusText($voucher, $now),
                    'is_active' => $voucher->is_active,
                    'actions' => ['edit', 'delete', 'duplicate'], // Ràng buộc 7: Cột thao tác
                ];
            }),
        ]);
    }

    /**
     * Ràng buộc 5: Tính toán thống kê nhanh
     */
    private function getQuickStats(): array
    {
        return [
            'total_vouchers' => Voucher::count(),
            'active_vouchers' => Voucher::where('is_active', true)
                ->where('end_date', '>=', Carbon::now())
                ->count(),
            'used_vouchers' => Voucher::sum('used_count'),
        ];
    }

    /**
     * Ràng buộc 6: Logic xác định trạng thái hiển thị
     */
    private function getVoucherStatusText(Voucher $voucher, Carbon $now): string
    {
        if (!$voucher->is_active) {
            return 'Không hoạt động';
        }
        if ($voucher->end_date < $now) {
            return 'Hết hạn';
        }
        if ($voucher->start_date > $now) {
            return 'Chưa bắt đầu';
        }
        if ($voucher->quantity <= $voucher->used_count) {
            return 'Hết lượt sử dụng';
        }
        return 'Đang hoạt động';
    }
}
