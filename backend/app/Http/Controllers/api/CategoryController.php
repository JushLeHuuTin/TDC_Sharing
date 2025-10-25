<?php

<<<<<<< HEAD:backend/app/Http/Controllers/api/CategoryController.php
=======
// namespace App\Http\Controllers;
>>>>>>> hanh/f16/show-total-products:app/Http/Controllers/api/CategoryController.php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\StoreProductRequest;
<<<<<<< HEAD:backend/app/Http/Controllers/api/CategoryController.php
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
=======
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductAttribute;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

// use Illuminate\Support\Facades\Auth;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function search(StoreProductRequest $request)
    {
        $search = $request->query('s');
        // $products = Product::searchByKeyword($search);

        return view('pages.products.search', [
            // 'products' => $products,
            'search'   => $search
        ]);
    }
    public function getProduct(StoreProductRequest $request)
    {
        // $search = $request->query('s');
        // $products = Product::searchByKeyword($search);

        return view('pages.products.productManage', [
            // 'products' => $products,
            // 'search'   => $search
        ]);
    }
    public function index()
    {
        //
        return view('pages.products.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        return view('pages.products.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCategoryRequest $request)
    {
        try{

            $this->authorize('create', Category::class);
        }catch(exception $e){
                 return response()->json([
                'message' => 'Ban khong co quyen admin'
            ], 500);
        }
        // Lấy dữ liệu đã được validate từ StoreCategoryRequest
        $validatedData = $request->validated();

        // Bắt đầu một transaction để đảm bảo toàn vẹn dữ liệu
        DB::beginTransaction();

        try {
            // Tạo mới danh mục với dữ liệu đã được validate
            $category = Category::create([
                'name' => $validatedData['name'],
                'slug' => Str::slug($validatedData['name']), // Tự động tạo slug từ tên
                'parent_id' => $validatedData['parent_id'] ?? null, // Nếu không có thì là null
                'description' => $validatedData['description'] ?? null,
                'icon' => $validatedData['icon'] ?? 'fas fa-folder', // Icon mặc định
                'color' => $validatedData['color'] ?? '#000000', // Màu mặc định
                'display_order' => $validatedData['display_order'] ?? 0, // Thứ tự mặc định
                'is_visible' => $validatedData['is_visible'] ?? 1, // Mặc định là kích hoạt
            ]);

            // Nếu mọi thứ thành công, commit transaction
            DB::commit();

            // Trả về response thành công với mã 201 (Created)
            return response()->json([
                'message' => 'Tạo danh mục thành công.',
                'data' => $category
            ], 201);

        } catch (\Exception $e) {
            // Nếu có lỗi, rollback lại tất cả các thay đổi trong transaction
            DB::rollBack();

            // Ghi log lỗi để debug
            Log::error('Lỗi khi tạo danh mục: ' . $e->getMessage());

            // Trả về response lỗi với mã 500 (Internal Server Error)
            return response()->json([
                'message' => 'Tạo danh mục thất bại, vui lòng thử lại.'
            ], 500);
        }
    }
    /**
     * Upload product images
     */
    private function uploadProductImages($images, $productId, $featuredIndex)
    {
        $uploadedImages = [];

        foreach ($images as $index => $image) {
            try {
                // Tạo tên file unique
                $extension = $image->getClientOriginalExtension();
                $filename = 'product_' . $productId . '_' . time() . '_' . Str::random(10) . '.' . $extension;

                // Lưu vào storage/app/public/products
                $path = $image->storeAs('products', $filename, 'public');

                $uploadedImages[] = [
                    'path' => $path,
                    'is_featured' => ($index == $featuredIndex)
                ];
            } catch (Exception $e) {
                // Log error nhưng tiếp tục với các ảnh khác
                // \Log::error('Upload image error: ' . $e->getMessage());
            }
        }

        return $uploadedImages;
    }

    /**
     * Generate unique slug
     */
>>>>>>> hanh/f16/show-total-products:app/Http/Controllers/api/CategoryController.php
    private function generateUniqueSlug($title)
    {
        $slug = Str::slug($title);
        $originalSlug = $slug;
        $count = 1;

<<<<<<< HEAD:backend/app/Http/Controllers/api/CategoryController.php
        while (Category::where('slug', $slug)->exists()) {
=======
        while (Product::where('slug', $slug)->exists()) {
>>>>>>> hanh/f16/show-total-products:app/Http/Controllers/api/CategoryController.php
            $slug = $originalSlug . '-' . $count;
            $count++;
        }

        return $slug;
    }
<<<<<<< HEAD:backend/app/Http/Controllers/api/CategoryController.php
    public function showProducts(Category $category, Request $request)
    {
        // 1. Tạo Breadcrumb
        $breadcrumb = BreadcrumbResource::collection($category->getBreadcrumb());

        // 2. Lấy sản phẩm trong danh mục (và các danh mục con) rồi phân trang
        $products = Product::inCategory($category)->paginate(8);

        // 3. Kiểm tra nếu không có sản phẩm nào
        if ($products->isEmpty() && $request->query('page', 1) == 1) {
            return response()->json([
                'breadcrumb' => $breadcrumb,
                'message' => 'Hiện chưa có sản phẩm nào trong danh mục này.',
                'products' => $products, // Trả về cấu trúc phân trang rỗng
            ]);
        }

        // 4. Trả về response hoàn chỉnh
        return response()->json([
            'breadcrumb' => $breadcrumb,
            'products' => ProductResource::collection($products),
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
        try{$this->authorize('delete', $category);}
        catch (exception $e){
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
=======

    /**
     * Get category attributes for form
     */
    public function getCategoryAttributes($categoryId)
    {
        try {
            $category = \App\Models\Category::with('attributes')->findOrFail($categoryId);

            return response()->json([
                'success' => true,
                'data' => [
                    'category' => $category->only(['id', 'name', 'slug']),
                    'attributes' => $category->attributes
                ]
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy danh mục'
            ], 404);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
        $product = ['name' => 'iphone 8'];
        return view('pages.products.show');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreProductRequest $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        try{$this->authorize('delete', $product);}
        catch (\Exception $e){
            return response()->json([
                'success' => false,
                'message' =>  "Không có quyền xoá sản phẩm"
            ], 500);
>>>>>>> hanh/f16/show-total-products:app/Http/Controllers/api/CategoryController.php
        }
        try {
            // 2. DÙNG TRANSACTION: Đảm bảo an toàn nếu cần xóa nhiều thứ liên quan (ví dụ: ảnh).
            DB::beginTransaction();
<<<<<<< HEAD:backend/app/Http/Controllers/api/CategoryController.php
            $category->delete();
=======

            // (Tùy chọn) Xóa các file ảnh liên quan trong storage
            // foreach ($product->images as $image) {
            //     Storage::disk('public')->delete($image->url);
            // }
            // $product->images()->delete(); // Xóa record ảnh trong DB

            // 3. XÓA SẢN PHẨM: Eloquent tự động chống SQL Injection.
            // Nếu dùng SoftDeletes, nó sẽ chỉ cập nhật `deleted_at`.
            $product->delete();
>>>>>>> hanh/f16/show-total-products:app/Http/Controllers/api/CategoryController.php

            // 4. COMMIT TRANSACTION: Xác nhận xóa thành công.
            DB::commit();

            // 5. TRẢ VỀ THÔNG BÁO THÀNH CÔNG
            return response()->json([
                'success' => true,
<<<<<<< HEAD:backend/app/Http/Controllers/api/CategoryController.php
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
=======
                'message' => 'Sản phẩm đã được xóa thành công.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Lỗi khi xóa sản phẩm: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Đã có lỗi xảy ra, không thể xóa sản phẩm.'
            ], 500);
        }
    }
>>>>>>> hanh/f16/show-total-products:app/Http/Controllers/api/CategoryController.php
}
