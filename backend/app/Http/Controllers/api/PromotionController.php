<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\StorePromotionRequest;
use App\Models\Promotion;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Http\Requests\PromotionIndexRequest;
use App\Http\Controllers\Controller;

class PromotionController extends Controller
{
    /**
     * Tạo một chương trình khuyến mãi mới.
     * Ràng buộc 1-12
     */
    public function store(StorePromotionRequest $request): JsonResponse
    {
        // Ràng buộc 12: Dữ liệu đã được xác thực
        $data = $request->validated();
        
        // Cần tách dữ liệu cho bảng chính và các bảng pivot
        $promotionData = $request->except(['category_ids', 'target_audiences']);
        $categoryIds = $request->input('category_ids');
        $audienceIds = $request->input('target_audiences');
        
        // Ràng buộc 12: Bắt đầu Transaction để đảm bảo tính toàn vẹn (Promotion + Pivot)
        DB::beginTransaction();
        try {
            // 1. Tạo bản ghi Promotion chính
            if (isset($validatedData['is_active'])) {
                $validatedData['is_active'] = $validatedData['is_active'] ? 1 : 0;
            }
            $promotion = Promotion::create($data);

            
            DB::commit();

            // Ràng buộc 12: Thông báo thành công
            return response()->json([
                'message' => 'Tạo chương trình khuyến mãi thành công. 🎉',
                'data' => $promotion,
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();

            // Ràng buộc 12: Xử lý lỗi server/DB
            // Log::error("Create Promotion Error: " . $e->getMessage());
            return response()->json([
                'message' => 'Tạo chương trình khuyến mãi thất bại, vui lòng thử lại.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function index(PromotionIndexRequest $request): JsonResponse
    {
        // Dữ liệu đã được làm sạch và xác thực
        $validatedData = $request->validated();
        $perPage = $validatedData['per_page'] ?? 4;
        $now = Carbon::now();

        // Bắt đầu Query Builder
        $query = Promotion::query();
        
        // Ràng buộc 1: Thanh tìm kiếm (theo tên)
        if (!empty($validatedData['search'])) {
            $keyword = '%' . $validatedData['search'] . '%';
            $query->where(DB::raw('LOWER(name)'), 'like', $keyword);
        }

        // Ràng buộc 3: Bộ lọc loại giảm giá
        if (!empty($validatedData['type'])) {
            $query->where('discount_type', $validatedData['type']);
        }
        
        // Ràng buộc 2: Bộ lọc trạng thái (Logic phức tạp)
        if (!empty($validatedData['status'])) {
            switch ($validatedData['status']) {
                case 'active':
                    $query->where('start_date', '<=', $now)
                          ->where('end_date', '>=', $now);
                    break;
                case 'expired':
                    $query->where('end_date', '<', $now);
                    break;
                case 'upcoming':
                    $query->where('start_date', '>', $now);
                    break;
            }
        }
        
        // Phân trang
        $promotions = $query->latest()->paginate($perPage);
        // Ràng buộc 4, 6, 7, 8, 9: Định dạng dữ liệu đầu ra
        return response()->json([
            'message' => 'Lấy danh sách chương trình khuyến mãi thành công.',
            'data' => $promotions->through(function ($promotion) use ($now) {
                return [
                    'id' => $promotion->id,
                    
                    // Ràng buộc 5: Tên chương trình
                    'name' => $promotion->name,
                    'description_short' => substr($promotion->description ?? '', 0, 50) . '...', 
                    
                    // Ràng buộc 6: Loại & Giá trị
                    'type' => $promotion->discount_type,
                    'value_display' => $this->formatDiscountValue($promotion),
                    
                    // Ràng buộc 7: Thời gian (dd/MM/yyyy)
                    'time_start' => Carbon::parse($promotion->start_date)->format('Y/m/d'),
                    'time_end' => Carbon::parse($promotion->end_date)->format('Y/m/d'),
                    
                    // Ràng buộc 8: Sử dụng (Giả định có cột total_uses)
                    // 'usage_count' => $promotion->total_uses ?? 0,
                    'usage_limit' => $promotion->usage_limit,
                    'usage_display' => ($promotion->total_uses ?? 0) . '/' . $promotion->max_uses_per_user,

                    // Ràng buộc 9: Trạng thái
                    'status' => $this->getPromotionStatus($promotion, $now),
                    'is_active' => $promotion->is_active,

                    // Ràng buộc 10: Thao tác (Sửa, Xóa)
                    'actions' => ['edit', 'delete'], 
                ];
            }),
        ], 200, [], JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_IGNORE);
    }
    
    /**
     * Logic định dạng giá trị giảm (Ràng buộc 6)
     */
    private function formatDiscountValue(Promotion $promotion): string
    {
        if ($promotion->discount_type === 'percentage') {
            return $promotion->discount_value . '%';
        }
        if ($promotion->discount_type === 'fixed') {
            return number_format($promotion->discount_value) . ' VNĐ';
        }
        if ($promotion->discount_type === 'freeship') {
            return 'Miễn phí vận chuyển';
        }
        return 'Không xác định';
    }

    /**
     * Logic xác định trạng thái (Ràng buộc 9)
     */
    private function getPromotionStatus(Promotion $promotion, Carbon $now): string
    {
        if (!$promotion->is_active) {
            return 'Không hoạt động';
        }
        if ($promotion->end_date < $now) {
            return 'expired';
        }
        if ($promotion->start_date > $now) {
            return 'Chưa bắt đầu';
        }
        if ($promotion->usage_limit < $promotion->used_count) {
            return 'used';
        }
        return 'active';
    }
}