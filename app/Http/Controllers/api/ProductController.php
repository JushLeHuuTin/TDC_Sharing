<?php

// namespace App\Http\Controllers;
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
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

class ProductController extends Controller
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
    public function store(StoreProductRequest $request)
    {
        DB::beginTransaction();

        try {
            // Tạo slug từ title
            $slug = $this->generateUniqueSlug($request->title);

            // Tạo sản phẩm
            $product = Product::create([
                // 'user_id' => auth()->id(),
                'user_id' => Auth::id(),
                'category_id' => $request->category_id,
                'title' => $request->title,
                'description' => $request->description,
                'price' => $request->price,
                'stocks' => $request->stocks,
                'status' => $request->status ?? 'active',
                'is_visible' => true,
                'is_featured' => $request->is_featured ?? false,
                'slug' => $slug
            ]);

            // Upload và lưu hình ảnh
            // $uploadedImages = $this->uploadProductImages(
            //     $request->file('images'), 
            //     $product->id,
            //     $request->featured_image_index
            // );  

            // if (empty($uploadedImages)) {
            //     throw new Exception('Lỗi khi upload hình ảnh sản phẩm');
            // }

            // Lưu thông tin ảnh vào database
            // foreach ($uploadedImages as $imageData) {
            //     ProductImage::create([
            //         'product_id' => $product->id,
            //         'image' => $imageData['path'],
            //         'is_featured' => $imageData['is_featured']
            //     ]);
            // }

            // Lưu các thuộc tính động
            // if ($request->filled('attributes')) {
            //     foreach ($request->attributes as $attribute) {
            //         $attrModel = \App\Models\Attribute::find($attribute['attribute_id']);

            //         $data = [
            //             'product_id' => $product->id,
            //             'attribute_id' => $attribute['attribute_id'],
            //             'value' => $attribute['value'],
            //         ];

            //         // Parse value theo data_type
            //         if ($attrModel) {
            //             switch ($attrModel->data_type) {
            //                 case 'number':
            //                     $data['value_int'] = is_numeric($attribute['value']) ? (int)$attribute['value'] : null;
            //                     break;
            //                 case 'boolean':
            //                     $data['value_boolean'] = filter_var($attribute['value'], FILTER_VALIDATE_BOOLEAN);
            //                     break;
            //                 case 'date':
            //                     try {
            //                         $data['value_date'] = \Carbon\Carbon::parse($attribute['value']);
            //                     } catch (\Exception $e) {
            //                         $data['value_date'] = null;
            //                     }
            //                     break;
            //             }
            //         }

            //         ProductAttribute::create($data);
            //     }
            // }
            if (!empty($request['attributes'])) {
                $attributesData = collect($request['attributes'])->map(fn($attr) => [
                    'attribute_id' => $attr['attribute_id'],
                    'value'        => $attr['value']
                ]);
                $product->productAttributes()->createMany($attributesData->all());
                // DIE($attributesData);
            }
            DB::commit();
            // die('DEBUG: Đã commit transaction thành công!');
            // Load relationships để trả về
            // $product->load(['images', 'attributes.attribute', 'category']);
            // return response()->json([
            //     'success' => true,
            //     'message' => 'DEBUG: Ghi dữ liệu vào DB thành công!'
            // ], 201);
            return response()->json([
                'success' => true,
                'message' => 'Tạo sản phẩm thành công',
                'data' => [
                    'product' => $product
                ]
            ], 201);
        } catch (Exception $e) {
            DB::rollBack();

            // Xóa các file đã upload nếu có lỗi
            if (isset($uploadedImages)) {
                foreach ($uploadedImages as $imageData) {
                    Storage::disk('public')->delete($imageData['path']);
                }
            }

            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi tạo sản phẩm',
                'error' => $e->getMessage()
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
