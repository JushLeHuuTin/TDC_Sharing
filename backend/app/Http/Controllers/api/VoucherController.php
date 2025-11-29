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
use App\Http\Requests\ValidateVoucherRequest;
use App\Http\Requests\VoucherIndexRequest;
use Carbon\Carbon;
use App\Models\Cart;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;


class VoucherController extends Controller
{
    /**
     * API để tạo mới một mã giảm giá (voucher).
     *
     * @param StoreVoucherRequest $request
     * @return JsonResponse
     */
    public function validateVoucher(ValidateVoucherRequest $request): JsonResponse
    {
        $buyerId = Auth::id();
        $voucherCode = $request->voucher_code;

        // 1. Lấy Cart Items hiện tại của người dùng
        $selectedCarts = Cart::getSelectedItemsByBuyer($buyerId)->get();
        $selectedCartItems = $selectedCarts->pluck('cartItems')->flatten(1);

        if ($selectedCartItems->isEmpty()) {
            return response()->json(['message' => 'Giỏ hàng trống.'], 400);
        }

        $groupedCartItems = $selectedCartItems->groupBy('product.user_id');

        $overallSubtotal = 0.00;
        foreach ($groupedCartItems as $cartItemsForSeller) {
            foreach ($cartItemsForSeller as $item) {
                $overallSubtotal += $item->product->price * $item->quantity;
            }
        }

        // 3. Tìm ID Voucher
        $voucherId = \App\Models\Voucher::IdFromCode($voucherCode)->value('id');

        // 4. KIỂM TRA TÍNH HỢP LỆ (Dùng lại logic kiểm tra)
        $voucher = \App\Models\Voucher::isActive()->find($voucherId);

        if (!$voucher) {
            return response()->json([
                'valid' => false,
                'message' => 'Mã giảm giá không tồn tại hoặc đã hết hạn.'
            ]);
        }

        // 5. Kiểm tra điều kiện Min Purchase (trên tổng giỏ hàng)
        if ($overallSubtotal < $voucher->min_purchase) {
            $missingAmount = $voucher->min_purchase - $overallSubtotal;
            return response()->json([
                'valid' => false,
                'message' => 'Đơn hàng chưa đạt mức tối thiểu. Thiếu ' . number_format($missingAmount) . ' VNĐ.'
            ]);
        }

        // 6. Tính toán mức chiết khấu ước tính (chỉ cần mức giảm)
        $estimatedDiscount = 0;
        if ($voucher->discount_type === 'percentage') {
            $discount = $overallSubtotal * ($voucher->discount_value / 100.00);
            // Nếu có max_discount, áp dụng ở đây
            $estimatedDiscount = $discount;
        } elseif ($voucher->discount_type === 'fixed') {
            $estimatedDiscount = $voucher->discount_value;
        }

        // 7. Trả về kết quả thành công
        return response()->json([
            'valid' => true,
            'message' => 'Áp dụng mã giảm giá thành công!',
            'discount_amount' => round($estimatedDiscount, 0),
            'voucher_code' => $voucherCode,
        ]);
    }
    public function store(StoreVoucherRequest $request): JsonResponse
    {
        // 16. Bảo mật: Việc kiểm tra Auth và Role đã được thực hiện trong StoreVoucherRequest::authorize().
        // Nếu request này được gọi, nghĩa là user đã được xác thực và có vai trò hợp lệ.

        // Lấy dữ liệu đã được validate.
        $validatedData = $request->validated();
        if (isset($validatedData['is_active'])) {
            // die('123');
            $validatedData['is_active'] = $validatedData['is_active'] ? 1 : 0;
        }
        // die("$validatedData[is_active]");
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
                'error_detail' => $e->getMessage(),
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
        try {
            $voucher = Voucher::findOrFail($id);
            $this->authorize('update', $voucher);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy mã voucher cần cập nhật.'
            ], 404);
        } catch (AuthorizationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không có quyền thực hiện thao tác này.',
            ], 403);
        }
        // LẤY DỮ LIỆU ĐÃ VALIDATE:
        $data = $request->validated();
        
        // --- Xử lý Optimistic Locking ---
        $requestUpdatedAt = $request->input('updated_at');
        $currentUpdatedAt = $voucher->updated_at ? strtotime($voucher->updated_at) : null;
        $requestUpdatedAtTimestamp = $requestUpdatedAt ? strtotime($requestUpdatedAt) : null;

        if ($requestUpdatedAtTimestamp && $currentUpdatedAt && $requestUpdatedAtTimestamp < $currentUpdatedAt) {
            return response()->json([
                'success' => false,
                'message' => 'Voucher đã được người dùng khác cập nhật. Vui lòng tải lại trang để xem dữ liệu mới nhất trước khi chỉnh sửa.',
                'errors' => [ 
                    'general' => ['Voucher đã được người dùng khác cập nhật. Vui lòng tải lại trang.']
                ]
            ], 409); 
        }
        if (isset($data['is_active'])) {
            $data['is_active'] = $data['is_active'] ? 1 : 0;
        }
        DB::beginTransaction();
        try {
            $voucher->update($data);
            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Cập nhật voucher thành công.',
                'data' => $voucher->fresh(),
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Update Voucher Error: " . $e->getMessage()); // Nên log lỗi
            return response()->json([
                'success' => false,
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

        $perPage = $validatedData['per_page'] ?? 4;
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
                    'name' => $voucher->name,
                    'description' => $voucher->description,
                    'type' => $voucher->discount_type,
                    'value' => $voucher->discount_value,
                    'min_purchase' => $voucher->min_purchase,
                    'quantity' => $voucher->usage_limit,
                    'used_count' => $voucher->used_count,
                    'is_active' => $voucher->is_active,
                    'start_date' => $voucher->start_date,
                    'end_date' => $voucher->end_date,
                    'updated_at' => $voucher->updated_at,
                    // Ràng buộc 6: Trạng thái hiển thị (Logic Front-end nên xử lý màu sắc)
                    'status_text' => $this->getVoucherStatusText($voucher, $now),
                    // 'is_active' => $voucher->is_active,
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
            return 'expired';
        }
        if ($voucher->start_date > $now) {
            return 'Chưa bắt đầu';
        }
        if ($voucher->usage_limit < $voucher->used_count) {
            return 'used';
        }
        return 'active';
    }
    public function destroy(int $voucherId): JsonResponse
    {
        // die($voucherId);
        try {
            // Ràng buộc 6: Kiểm tra Policy (Auth:: user() có quyền xóa voucher này không)
            $voucher = Voucher::findOrFail($voucherId);
            $this->authorize('delete', $voucher);
            // Ràng buộc 3: Bắt đầu Transaction để đảm bảo tính toàn vẹn
            DB::beginTransaction();

            // Kiểm tra ràng buộc ngoại lệ: Voucher đã được sử dụng trong Order chưa
            // Giả định bạn có mối quan hệ voucherUsages (hoặc orderItems)
            if ($voucher->usage_count > 0) {
                // Nếu voucher đã được sử dụng (Ràng buộc 3: Lỗi do ràng buộc FK)
                DB::rollBack();
                return response()->json([
                    'message' => 'Không thể xóa voucher đang áp dụng cho các đơn hàng đã tạo.',
                    'code' => 'VOUCHER_IN_USE'
                ], 400);
            }

            // Thực hiện xóa mềm (soft delete) hoặc xóa cứng (force delete)
            // Nếu Model Voucher có SoftDeletes, hãy dùng $voucher->delete() để xóa mềm.
            $voucher->delete();

            DB::commit();

            // Ràng buộc 4: Thông báo thành công
            return response()->json([
                'message' => "Xóa voucher {$voucher->code} thành công.",
            ], 200);
        }  
        catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi: Voucher bạn muốn xóa không tồn tại.'
            ], 404);
        }
        catch (AuthorizationException $e) {
            // Lỗi Policy (Ràng buộc 6: Không có quyền)
            DB::rollBack();
            return response()->json([
                'message' => 'Bạn không có quyền thực hiện thao tác này.',
            ], 403);
        } catch (\Exception $e) {
            // Lỗi chung (Ràng buộc 3: Lỗi DB)
            DB::rollBack();
            // Ghi log lỗi tại đây
            return response()->json([
                'message' => 'Xóa voucher thất bại, vui lòng thử lại.',
            ], 500);
        }
    }
}
