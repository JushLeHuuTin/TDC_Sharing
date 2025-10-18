<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePromotionRequest;
use App\Models\Promotion;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

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
            $promotion = Promotion::create($promotionData);

            // 2. Lưu các danh mục áp dụng (Promotion_Categories)
            // Giả định Promotion có mối quan hệ belongsToMany với Category
            $promotion->categories()->attach($categoryIds); 

            // 3. Lưu đối tượng sử dụng (Promotion_UserGroups)
            // Giả định Promotion có mối quan hệ belongsToMany với UserGroup
            $promotion->audiences()->attach($audienceIds); 
            
            DB::commit();

            // Ràng buộc 12: Thông báo thành công
            return response()->json([
                'message' => 'Tạo chương trình khuyến mãi thành công. 🎉',
                'data' => $promotion->load(['categories', 'audiences']),
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
}