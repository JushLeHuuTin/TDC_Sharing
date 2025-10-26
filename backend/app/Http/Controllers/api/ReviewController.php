<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreReviewRequest;
use App\Models\Review; // Import model Review
use App\Models\Product; // Import model Product
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\ReviewResource; // Import API Resource
use App\Http\Requests\UpdateReviewRequest;

use Illuminate\Http\Request;

class ReviewController extends Controller
{
    /**
     * Store a newly created review in storage.
     * API để người dùng thêm đánh giá cho một sản phẩm.
     */
    public function store(StoreReviewRequest $request): JsonResponse
    {
        // Dữ liệu đã được validate bởi StoreReviewRequest
        $validatedData = $request->validated();
        $user = $request->user();

        // loi 1
        // Kiểm tra xem người dùng đã đánh giá sản phẩm này chưa
        // $existingReview = Review::where('user_id', $user->id)
        //                         ->where('product_id', $validatedData['product_id'])
        //                         ->exists();
        // loi 2
        // if ($existingReview) {
        //     return response()->json([
        //         'success' => false,
        //         'message' => 'Bạn đã đánh giá sản phẩm này rồi.'
        //     ], 409); // 409 Conflict
        // }

        // Kiểm tra quyền (ví dụ: người dùng có mua sản phẩm này không?)
        // Dòng này sẽ gọi đến 'create' method trong ReviewPolicy
        $product = Product::findOrFail($validatedData['product_id']);
        $this->authorize('create', [Review::class, $product]);

        DB::beginTransaction();
        try {
            // die('tin');
            $review = Review::create([
                'product_id' => $validatedData['product_id'],
                'reviewer_id'    => $user->id,
                'rating'     => $validatedData['rating'],
                'comment'    => strip_tags($validatedData['comment'] ?? ''), // Strip HTML tags để tăng bảo mật
            ]);
           DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Gửi đánh giá thành công.',
                'data'    => $review
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Lỗi khi gửi đánh giá: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gửi đánh giá thất bại, vui lòng thử lại.'
            ], 500);
        }
    }
    //xoa
    public function destroy(Review $review): JsonResponse
    {
        // 1. Kiểm tra quyền hạn: Dòng này sẽ gọi đến 'delete' method trong ReviewPolicy
        // Nó sẽ kiểm tra xem người dùng hiện tại có phải chủ đánh giá hoặc admin không.
        $this->authorize('delete', $review);

        try {
            // 2. Thực hiện xóa
            $review->delete();

            // 3. Trả về thông báo thành công
            return response()->json([
                'success' => true,
                'message' => 'Xóa đánh giá thành công.'
            ]); // Mặc định trả về status 200 OK

        } catch (\Exception $e) {
            Log::error('Lỗi khi xóa đánh giá: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Xóa đánh giá thất bại, vui lòng thử lại.'
            ], 500); // 500 Internal Server Error
        }
    }
    //sua
     public function update(UpdateReviewRequest $request, Review $review): JsonResponse
    {
        // 1. Kiểm tra quyền hạn: Gọi đến 'update' method trong ReviewPolicy
        $this->authorize('update', $review);

        try {
            // 2. Lấy dữ liệu đã được validate từ UpdateReviewRequest
            $validatedData = $request->validated();
            // 3. Cập nhật đánh giá
            $review->update($validatedData);

            // 4. Trả về response thành công với dữ liệu đã được cập nhật
            return response()->json([
                'success' => true,
                'message' => 'Cập nhật đánh giá thành công.',
                'data' => new  ReviewResource($review) // Dùng Resource để định dạng data
            ]);

        } catch (\Exception $e) {
            Log::error('Lỗi khi cập nhật đánh giá: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Cập nhật đánh giá thất bại, vui lòng thử lại.'.$e->getMessage()
            ], 500);
        }
    }

    //hien thi
     public function index(Request $request, Product $product): JsonResponse
    {
        // Validate dữ liệu đầu vào từ query string (để lọc)
        $request->validate([
            'rating' => 'nullable|integer|in:1,2,3,4,5',
        ]);

        // --- 1. Tính toán các số liệu thống kê ---
        $statsQuery = $product->reviews();
        $totalReviews = $statsQuery->count();
        // Tính sao trung bình, làm tròn 1 chữ số, trả về 0 nếu chưa có đánh giá
        $averageRating = $totalReviews > 0 ? round($statsQuery->avg('rating'), 1) : 0;

        // Đếm số lượng đánh giá cho mỗi loại sao (5 sao, 4 sao, ...)
        $ratingCounts = $product->reviews()
            ->select('rating', DB::raw('count(*) as count'))
            ->groupBy('rating')
            ->pluck('count', 'rating')->all();

        // Gán 0 cho các loại sao không có lượt đánh giá
        for ($i = 5; $i >= 1; $i--) {
            $ratingCounts[$i] = $ratingCounts[$i] ?? 0;
        }

        // --- 2. Lấy danh sách đánh giá đã được lọc và phân trang ---
        $reviewsQuery = Review::with('user') // Eager loading để lấy thông tin người dùng
                               ->where('product_id', $product->id);

        // Áp dụng bộ lọc theo số sao nếu có
        if ($request->filled('rating')) {
            $reviewsQuery->where('rating', $request->query('rating'));
        }

        // Sắp xếp theo mới nhất và phân trang (8 đánh giá mỗi trang)
        $reviews = $reviewsQuery->latest()->paginate(8);

        // --- 3. Trả về Response hoàn chỉnh ---
        return response()->json([
            'success' => true,
            'data' => [
                'summary' => [
                    'total_reviews' => $totalReviews,
                    'average_rating' => $averageRating,
                    'rating_counts' => $ratingCounts,
                ],
                'reviews' => ReviewResource::collection($reviews),
            ]
        ]);
    }
}