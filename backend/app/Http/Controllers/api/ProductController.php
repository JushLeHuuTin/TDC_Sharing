<?php

// namespace App\Http\Controllers;
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\SearchProductRequest;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Resources\FeaturedProductResource;
use App\Http\Resources\ProductDetailResource;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductAttribute;
use Illuminate\Http\UploadedFile;
use App\Models\Category;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Exception;
use Throwable;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

// use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function search(SearchProductRequest $request)
    {
        // 1. Tự động validate 'q' qua SearchProductRequest

        // 2. Lấy từ khóa đã được validate
        $keyword = $request->validated()['q'];
        // 3. Gọi scope 'search' và phân trang
        $products = Product::search($keyword)->paginate(8);

        // 4. Trả về dữ liệu đã được transform
        return ProductResource::collection($products);
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
    public function index(Request $request)
    {
        // Gọi scope đã định nghĩa và phân trang
        $products = Product::activeAndReady()->paginate(8);
        // Trả về dữ liệu qua API Resource như cũ
        return ProductResource::collection($products);
    }
    public function featured()
    {
        // Gọi scope 'featured' đã được định nghĩa trong Model
        $featuredProducts = Product::featured()->get();

        // Trả về dữ liệu đã được transform
        return FeaturedProductResource::collection($featuredProducts);
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
    // public function store(StoreProductRequest $request)
    // {
    //     DB::beginTransaction();
    //     $uploadedImages = [];
    //     try {
    //         // Tạo slug từ title
    //         $slug = $this->generateUniqueSlug($request->title);

    //         // Tạo sản phẩm
    //         $product = Product::create([
    //             // 'user_id' => auth()->id(),
    //             'user_id' => Auth::id(),
    //             'category_id' => $request->category_id,
    //             'title' => $request->title,
    //             'description' => $request->description,
    //             'price' => $request->price,
    //             'stocks' => $request->stocks,
    //             'status' => $request->status ?? 'active',
    //             'is_visible' => true,
    //             'is_featured' => $request->is_featured ?? false,
    //             'slug' => $slug
    //         ]);
    //         // Upload và lưu hình ảnh
    //         // $uploadedImages = $this->uploadProductImages(
    //         //     $request->file('images'), 
    //         //     $product->id,
    //         //     $request->featured_image_index
    //         // );  

    //         // if (empty($uploadedImages)) {
    //         //     throw new Exception('Lỗi khi upload hình ảnh sản phẩm');
    //         // }

    //         // Lưu thông tin ảnh vào database
    //         // foreach ($uploadedImages as $imageData) {
    //         //     ProductImage::create([
    //         //         'product_id' => $product->id,
    //         //         'image' => $imageData['path'],
    //         //         'is_featured' => $imageData['is_featured']
    //         //     ]);
    //         // }

    //         // Lưu các thuộc tính động
    //         // if ($request->filled('attributes')) {
    //         //     foreach ($request->attributes as $attribute) {
    //         //         $attrModel = \App\Models\Attribute::find($attribute['attribute_id']);

    //         //         $data = [
    //         //             'product_id' => $product->id,
    //         //             'attribute_id' => $attribute['attribute_id'],
    //         //             'value' => $attribute['value'],
    //         //         ];

    //         //         // Parse value theo data_type
    //         //         if ($attrModel) {
    //         //             switch ($attrModel->data_type) {
    //         //                 case 'number':
    //         //                     $data['value_int'] = is_numeric($attribute['value']) ? (int)$attribute['value'] : null;
    //         //                     break;
    //         //                 case 'boolean':
    //         //                     $data['value_boolean'] = filter_var($attribute['value'], FILTER_VALIDATE_BOOLEAN);
    //         //                     break;
    //         //                 case 'date':
    //         //                     try {
    //         //                         $data['value_date'] = \Carbon\Carbon::parse($attribute['value']);
    //         //                     } catch (\Exception $e) {
    //         //                         $data['value_date'] = null;
    //         //                     }
    //         //                     break;
    //         //             }
    //         //         }

    //         //         ProductAttribute::create($data);
    //         //     }
    //         // }
    //         if (!empty($request['attributes'])) {
    //             $attributesData = collect($request['attributes'])->map(fn($attr) => [
    //                 'attribute_id' => $attr['attribute_id'],
    //                 'value'        => $attr['value']
    //             ]);
    //             $product->productAttributes()->createMany($attributesData->all());
    //             // DIE($attributesData);
    //         }
    //         DB::commit();
    //         // die('DEBUG: Đã commit transaction thành công!');
    //         // Load relationships để trả về
    //         // $product->load(['images', 'attributes.attribute', 'category']);
    //         // return response()->json([
    //         //     'success' => true,
    //         //     'message' => 'DEBUG: Ghi dữ liệu vào DB thành công!'
    //         // ], 201);
    //         return response()->json([
    //             'success' => true,
    //             'message' => 'Tạo sản phẩm thành công',
    //             'data' => [
    //                 'product' => $product
    //             ]
    //         ], 201);
    //     } catch (Exception $e) {
    //         DB::rollBack();

    //         // Xóa các file đã upload nếu có lỗi
    //         if (isset($uploadedImages)) {
    //             foreach ($uploadedImages as $imageData) {
    //                 Storage::disk('public')->delete($imageData['path']);
    //             }
    //         }

    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Có lỗi xảy ra khi tạo sản phẩm',
    //             'error' => $e->getMessage()
    //         ], 500);
    //     }
    // }
    public function store(StoreProductRequest $request)
    {
        DB::beginTransaction();
        $uploadedImages = []; 

        try {
            // ... (Tạo Slug và Product Model) ...
            $product = Product::create([
                'user_id' => Auth::id(),
                'category_id' => $request->category_id,
                'title' => $request->title,
                'description' => $request->description,
                'price' => $request->price,
                'stocks' => $request->stocks, 
                'status' => $request->status ?? 'active',
                'is_visible' => true,
                'is_featured' => $request->is_featured ?? false,
                'slug' => $this->generateUniqueSlug($request->title)
            ]);
            $images = $request->file('images');
            if (!empty($images)) {
                // 💡 Gọi hàm helper đã định nghĩa
                $uploadedImages = $this->uploadProductImages($images, $product->id);
            }

            if (!empty($uploadedImages)) {
                $product->images()->createMany($uploadedImages);
            }

            $rawAttributes = $request->input('attributes'); 

            if (!empty($rawAttributes)) {
                $attributesData = collect($rawAttributes)
                    ->map(fn($attr) => [
                        'attribute_id' => $attr['attribute_id'],
                        'value'        => $attr['value']
                    ])
                    ->values(); 
                $product->productAttributes()->createMany($attributesData->all());
            }
            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Tạo sản phẩm thành công',
                'data' => $product->load(['images', 'category'])
            ], 201);
        } catch (Throwable $e) {
            DB::rollBack();

            // 5. Xóa các file đã upload nếu có lỗi
            if (!empty($uploadedImages)) {
                foreach ($uploadedImages as $imageData) {
                    Storage::disk('public')->delete($imageData['image']);
                }
            }

            // 🎯 Chuẩn hóa Mã lỗi Server
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra trong quá trình xử lý trên máy chủ.',
                'error' => $e->getMessage()
            ], 500); // Mã lỗi 500 cho Internal Server Error
        }
    }
    /**
     * Upload product images
     */
    private function uploadProductImages(?array $imageFiles, int $productId): array
    {
        $uploadedImages = [];
        if (empty($imageFiles)) {
            return [];
        }

        foreach ($imageFiles as $index => $imageFile) {
            if ($imageFile instanceof UploadedFile && $imageFile->isValid()) {
                // Lưu file vào storage/app/public/products/{productId}
                $path = $imageFile->store('products/' . $productId, 'public');

                $uploadedImages[] = [
                    'image' => $path,
                    'is_featured' => ($index === 0) // Ảnh đầu tiên (index 0) là featured
                ];
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
            $category = Category::with('attributes')->findOrFail($categoryId);

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
    public function show(Product $product)
    {
        // 1. Tăng lượt xem mỗi khi có người gọi API này
        $product->increment('views_count');

        // 2. Eager load các mối quan hệ cần thiết để tối ưu truy vấn
        $product->load(['images', 'seller']);

        // 3. Trả về dữ liệu đã được định dạng qua ProductDetailResource
        return new ProductDetailResource($product);
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
    public function update(UpdateProductRequest $request, Product $product)
    {
        // 1. PHÂN QUYỀN: Tự động gọi ProductPolicy@update
        try {
            $this->authorize('update', $product);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không có quyền .',
            ], 404);
        }

        // 2. LẤY DỮ LIỆU ĐÃ VALIDATE:
        // Dữ liệu ở đây đã được làm sạch, trim, và kiểm tra bởi UpdateProductRequest.
        $validatedData = $request->validated();

        try {

            // 3. XỬ LÝ TRANSACTION:
            // Đảm bảo tất cả các thao tác DB hoặc thành công, hoặc thất bại cùng nhau.
            DB::beginTransaction();

            $product->update($validatedData);

            // Nếu có các thao tác khác (VD: cập nhật kho, log...), hãy làm ở đây.

            DB::commit();

            // 4. TRẢ VỀ KẾT QUẢ THÀNH CÔNG
            return response()->json([
                'success' => true,
                'message' => 'Cập nhật sản phẩm thành công.',
                'data' => $product,
            ], 200);
        } catch (\Exception $e) {
            // 5. ROLLBACK NẾU CÓ LỖI:
            DB::rollBack();
            Log::error('Lỗi khi cập nhật sản phẩm: ' . $e->getMessage());

            // 6. TRẢ VỀ THÔNG BÁO LỖI
            return response()->json([
                'success' => false,
                'message' => 'Cập nhật sản phẩm thất bại, vui lòng thử lại.'
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        try {
            $this->authorize('delete', $product);
        } catch (exception $e) {
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
