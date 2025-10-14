<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\StoreProductRequest;
use Exception;
use App\Models\Category;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;


class CategoryController extends Controller
{
    //
    public function Store(StoreCategoryRequest $request)
    {
        // die('lo store');
        // 1. PHÂN QUYỀN: Kiểm tra user có quyền 'create' không qua CategoryPolicy
        try {
            // die('lo try');
            $this->authorize('create', Category::class);
        } catch (Exception $e) {
            return response()->json([
                'success' => true,
                'message' => $e->getMessage()
            ], 201); // 201 Created là status code phù hợp cho việc tạo mới
        }

        // 2. LẤY DỮ LIỆU ĐÃ VALIDATE:
        $validatedData = $request->validated();

        try {
            // 3. XỬ LÝ TRANSACTION (để đảm bảo an toàn, dù chỉ có 1 câu lệnh)
            $slug = $this->generateUniqueSlug($request->name);
            $category = DB::transaction(function () use ($validatedData,$slug) {
                $dataToCreate = $validatedData;
                $dataToCreate['slug'] = $slug; // <-- Gán slug vào mảng data
                
                return Category::create($dataToCreate);
            });

            // 4. TRẢ VỀ KẾT QUẢ THÀNH CÔNG
            return response()->json([
                'success' => true,
                'message' => 'Thêm danh mục thành công.',
                'data' => $category,
            ], 201); // 201 Created là status code phù hợp cho việc tạo mới

        } catch (\Exception $e) {
            // 5. BẮT LỖI VÀ THÔNG BÁO
            Log::error('Lỗi khi thêm danh mục: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Thêm danh mục thất bại, vui lòng thử lại.' . $e->getMessage()
            ], 500);
        }
    }
    private function generateUniqueSlug($title)
    {
        $slug = Str::slug($title);
        $originalSlug = $slug;
        $count = 1;

        while (Category::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $count;
            $count++;
        }

        return $slug;
    }
}
