<?php

// namespace App\Http\Controllers;
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\StoreProductRequest;
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
    private function generateUniqueSlug($title)
    {
        $slug = Str::slug($title);
        $originalSlug = $slug;
        $count = 1;

        while (Product::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $count;
            $count++;
        }

        return $slug;
    }

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
        }
        try {
            // 2. DÙNG TRANSACTION: Đảm bảo an toàn nếu cần xóa nhiều thứ liên quan (ví dụ: ảnh).
            DB::beginTransaction();

            // (Tùy chọn) Xóa các file ảnh liên quan trong storage
            // foreach ($product->images as $image) {
            //     Storage::disk('public')->delete($image->url);
            // }
            // $product->images()->delete(); // Xóa record ảnh trong DB

            // 3. XÓA SẢN PHẨM: Eloquent tự động chống SQL Injection.
            // Nếu dùng SoftDeletes, nó sẽ chỉ cập nhật `deleted_at`.
            $product->delete();

            // 4. COMMIT TRANSACTION: Xác nhận xóa thành công.
            DB::commit();

            // 5. TRẢ VỀ THÔNG BÁO THÀNH CÔNG
            return response()->json([
                'success' => true,
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
}
