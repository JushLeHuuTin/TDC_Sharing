<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    /**
     * Hiển thị danh sách sản phẩm yêu thích của người dùng đang đăng nhập.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        // Lấy danh sách sản phẩm yêu thích, eager load các quan hệ và phân trang
        $favoriteProducts = $user->favorites()
                                  ->with(['seller', 'featuredImage']) // Tối ưu truy vấn
                                  ->latest('wishlists.created_at') // Sắp xếp theo ngày yêu thích mới nhất
                                  ->paginate(8);

        // Xử lý trường hợp không có sản phẩm yêu thích nào
        if ($favoriteProducts->isEmpty() && $request->query('page', 1) == 1) {
            return response()->json([
                'message' => 'Bạn chưa có sản phẩm yêu thích nào.',
                'data' => [], // Trả về mảng rỗng
            ], 200);
        }

        // Trả về collection đã được format qua ProductResource
        return ProductResource::collection($favoriteProducts);
    }

    /**
     * Thêm một sản phẩm vào danh sách yêu thích.
     */
    public function store(Request $request)
    {
        $request->validate(['product_id' => 'required|exists:products,id']);
        
        $user = $request->user();
        
        // syncWithoutDetaching sẽ thêm mới mà không xóa các bản ghi đã có.
        // Nó sẽ tự động bỏ qua nếu đã tồn tại, tránh lỗi trùng lặp khi người dùng spam click.
        $user->favorites()->syncWithoutDetaching($request->product_id);

        return response()->json([
            'success' => true,
            'message' => 'Đã thêm sản phẩm vào danh sách yêu thích.',
        ], 201); // 201 Created
    }

    /**
     * Xóa một sản phẩm khỏi danh sách yêu thích.
     */
    public function destroy(Request $request, Product $product)
    {
        $user = $request->user();
        
        // Detach sẽ xóa mối quan hệ trong bảng trung gian (wishlist)
        $user->favorites()->detach($product->id);

        return response()->json([
            'success' => true,
            'message' => 'Đã bỏ sản phẩm khỏi danh sách yêu thích.',
        ], 200); // 200 OK
    }
}