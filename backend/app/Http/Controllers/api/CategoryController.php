<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Http\Resources\BreadcrumbResource;
use App\Http\Resources\CategoryTreeResource;
use App\Http\Resources\ProductResource;
use App\Http\Resources\TopCategoryResource;
use Exception;
use App\Models\Category;
use App\Models\Product;
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
            $category = DB::transaction(function () use ($validatedData, $slug) {
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
    public function showProducts(Category $category, Request $request)
    {
        // 1. Tạo Breadcrumb
        $breadcrumb = BreadcrumbResource::collection($category->getBreadcrumb());

        // 2. Lấy sản phẩm trong danh mục (và các danh mục con) rồi phân trang
        $query = Product::inCategory($category);
        // lọc theo giá
        if ($request->filled('price_min')) {
            $query->where('price', '>=', $request->price_min);
        }
        if ($request->filled('price_max')) {
            $query->where('price', '<=', $request->price_max);
        }
        if ($request->filled('q')) {
            $keyword = $request->q;
            $query->search($keyword); 
        }
        $products = $query->paginate(8);
        // 3. Kiểm tra nếu không có sản phẩm nào
        if ($products->isEmpty() && $request->query('page', 1) == 1) {
            return response()->json([
                'breadcrumb' => $breadcrumb,
                'message' => 'Hiện chưa có sản phẩm nào trong danh mục này.',
                'data' => $products, // Trả về cấu trúc phân trang rỗng
            ]);
        }

        // 4. Trả về response hoàn chỉnh
        return ProductResource::collection($products)
            ->additional([
                'breadcrumb' => $breadcrumb,
            ]);
    }
    public function update(UpdateCategoryRequest $request, Category $category)
    {

        // 1. PHÂN QUYỀN: Tự động gọi CategoryPolicy@update
        // Nếu không có quyền, sẽ tự động trả về lỗi 403 Forbidden.
        $this->authorize('update', $category);

        // 2. LẤY DỮ LIỆU ĐÃ VALIDATE:
        $validatedData = $request->validated();

        try {
            // 3. XỬ LÝ TRANSACTION (để đảm bảo an toàn)
            DB::transaction(function () use ($category, $validatedData) {
                $category->update($validatedData);
            });

            // Model sẽ tự động tạo lại slug nếu 'name' thay đổi (nhờ code trong hàm booted())

            // 4. TRẢ VỀ KẾT QUẢ THÀNH CÔNG
            return response()->json([
                'success' => true,
                'message' => 'Cập nhật danh mục thành công.',
                'data' => $category->fresh(), // Lấy lại dữ liệu mới nhất từ DB
            ], 200);
        } catch (\Exception $e) {
            // 5. BẮT LỖI VÀ THÔNG BÁO
            Log::error('Lỗi khi cập nhật danh mục: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Cập nhật danh mục thất bại, vui lòng thử lại.'
            ], 500);
        }
    }
    public function destroy(Category $category)
    {
        try {
            $this->authorize('delete', $category);
        } catch (exception $e) {
            return response()->json([
                'success' => false,
                'message' =>  "Không có quyền xoá danh muc"
            ], 403);
        }
        if ($category->products()->exists()) {
            // Nếu có, trả về lỗi 409 Conflict.
            return response()->json([
                'success' => false,
                'message' => 'Không thể xóa, danh mục đang có sản phẩm.',
            ], 409); // 409 Conflict là mã lỗi phù hợp cho trường hợp này
        }
        if ($category->children()->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể xóa, vui lòng xóa các danh mục con trước.',
            ], 409);
        }
        try {
            // 2. DÙNG TRANSACTION: Đảm bảo an toàn nếu cần xóa nhiều thứ liên quan (ví dụ: ảnh).
            DB::beginTransaction();
            $category->delete();
            DB::commit();

            // 5. TRẢ VỀ THÔNG BÁO THÀNH CÔNG
            return response()->json([
                'success' => true,
                'message' => 'danh muc đã được xóa thành công.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Lỗi khi xóa danh muc: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Đã có lỗi xảy ra, không thể xóa danh muc.'
            ], 500);
        }
    }

    public function index()
    {
        // 1. PHÂN QUYỀN: Kiểm tra xem user có quyền xem danh sách không
        $this->authorize('viewAny', Category::class);

        // 2. GỌI PHƯƠNG THỨC TĨNH TỪ MODEL:
        // Toàn bộ logic phức tạp đã được xử lý trong getAdminTree()
        $categoriesTree = Category::getAdminTree();

        // 3. TRẢ VỀ DỮ LIỆU ĐÃ ĐƯỢC TRANSFORM
        return CategoryTreeResource::collection($categoriesTree);
    }
    public function topFive()
    {
        // 1. Gọi scope 'topFive' đã được định nghĩa trong Model
        $topCategories = Category::topFive()->get();

        // 2. Trả về dữ liệu đã được transform
        return TopCategoryResource::collection($topCategories);
    }
    public function getAttributes(int $categoryId)
    {
        try {
            // 1. Kiểm tra sự tồn tại của danh mục
            // Logic trong ProductAttributeController::getAttributes($categoryId)
            $attributes = Category::findOrFail($categoryId)->attributes()->with('attributesOptions')->get();
            // die($attributes);

            // 3. Chuẩn bị dữ liệu cho Frontend
            $data = $attributes->map(function ($attr) {
                // Định dạng lại tên thuộc tính để dễ dàng truy cập trong Vue form (dùng snake_case)
                $name = str_replace('-', '_', Str::slug($attr->name));

                return [
                    'id' => $attr->id,
                    'name' => $name, // Ví dụ: 'tinh_trang', 'thuong_hieu'
                    'label' => $attr->name, // Tên hiển thị: 'Tình trạng', 'Thương hiệu'
                    'required' => $attr->required, // Giả định có cột 'required'
                    'data_type' => $attr->data_type, // 'text', 'enum', 'number', etc.
                    'placeholder' => $attr->placeholder ?? '',

                    // Thêm attributesOptions chỉ khi là ENUM
                    'attributesOptions' => $attr->data_type === 'select'
                        ? $attr->attributesOptions->map(fn($opt) => ['value' => $opt->value, 'label' => $opt->value])
                        : null,
                ];
            });

            return response()->json([
                'status' => 'success',
                'message' => 'Thuộc tính danh mục đã được tải thành công.',
                'data' => $data,
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Danh mục không tồn tại.',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Lỗi server khi tải thuộc tính.',
                'debug' => $e->getMessage()
            ], 500);
        }
    }
}
