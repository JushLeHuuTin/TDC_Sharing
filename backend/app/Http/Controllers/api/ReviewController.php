<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreReviewRequest;
use App\Http\Requests\UpdateReviewRequest;
use App\Http\Resources\ReviewResource;
use App\Models\Review;
use App\Models\Product;
use App\Models\Order;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    /**
     * Store a newly created review in storage.
     */
    public function store(StoreReviewRequest $request): JsonResponse
    {
        $validatedData = $request->validated();
        $user = $request->user();
        $productId = $validatedData['product_id'];

        // --- CẤU HÌNH CÁC TRẠNG THÁI ĐƯỢC PHÉP ĐÁNH GIÁ ---
        $validStatuses = ['delivered', 'completed', 'success', 'đã giao', 'đã giao hàng', 'thành công'];

        // --- CHECK 1: Kiểm tra mua hàng ---
        $hasPurchased = Order::where('user_id', $user->id)
            ->whereIn('status', $validStatuses)
            ->whereHas('orderItems', function ($query) use ($productId) {
                $query->where('product_id', $productId);
            })
            ->exists();

        if (!$hasPurchased) {
            // Lấy tên sản phẩm để hiển thị thông báo
            $productName = Product::where('id', $productId)->value('title') ?? 'Sản phẩm này';

            return response()->json([
                'success' => false,
                'message' => "Rất tiếc, bạn cần mua và nhận hàng thành công sản phẩm \"$productName\" để có thể viết đánh giá."
            ], 403); 
        }

        // --- CHECK 2: Kiểm tra Spam ---
        $existingReview = Review::where('reviewer_id', $user->id)
            ->where('product_id', $productId)
            ->exists();

        if ($existingReview) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn đã đánh giá sản phẩm này rồi.'
            ], 400); 
        }

        // --- LƯU ĐÁNH GIÁ ---
        DB::beginTransaction();
        try {
            $review = Review::create([
                'product_id'  => $productId,
                'reviewer_id' => $user->id,
                'rating'      => $validatedData['rating'],
                'comment'     => strip_tags($validatedData['comment'] ?? ''),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Gửi đánh giá thành công.',
                'data'    => new ReviewResource($review)
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Lỗi khi gửi đánh giá: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Gửi thất bại.'], 500);
        }
    }

    public function update(UpdateReviewRequest $request, Review $review): JsonResponse
    {
        $this->authorize('update', $review); 
    
        // Lấy giá trị updated_at (TIMESTAMP) cũ từ Frontend
        $oldUpdatedAt = $request->input('updated_at');
    
        // 1. Kiểm tra tính hợp lệ của updated_at từ request
        if (!$oldUpdatedAt) {
            return response()->json(['success' => false, 'message' => 'Thiếu trường updated_at.'], 400);
        }
        
        // 2. Bắt đầu giao dịch (Tùy chọn, nhưng tốt cho consistency)
        DB::beginTransaction();
        try {
            // Lấy giá trị updated_at hiện tại trong database (sẽ tự động là chuỗi ISO)
            $currentUpdatedAt = $review->updated_at->toISOString(); 
    
            // 3. SO SÁNH GIÁ TRỊ CŨ (từ Request) VỚI GIÁ TRỊ HIỆN TẠI (trong DB)
            if ($oldUpdatedAt !== $currentUpdatedAt) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Dữ liệu đã bị thay đổi bởi người dùng khác, vui lòng tải lại.'
                ], 409); // 409 Conflict
            }
            
            // 4. Nếu không có xung đột, thực hiện cập nhật
            $review->update($request->validated());
            
            DB::commit();
    
            return response()->json(['success' => true, 'message' => 'Cập nhật thành công.', 'data' => new ReviewResource($review)]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Lỗi cập nhật đánh giá: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Lỗi cập nhật.'], 500);
        }
    }

    public function destroy(Request $request, Review $review): JsonResponse
    {
        // Gọi Policy để kiểm tra quyền (Admin hoặc Chính chủ)
        $this->authorize('delete', $review);

        try {
            $review->delete();
            return response()->json(['success' => true, 'message' => 'Xóa thành công.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Lỗi xóa.'], 500);
        }
    }

    /**
     * Display reviews.
     */
    public function index(Request $request, Product $product): JsonResponse
    {
        $request->validate(['rating' => 'nullable|integer|in:1,2,3,4,5']);
        
        $baseQuery = $product->reviews(); 
        $totalReviews = $baseQuery->count();
        $averageRating = $totalReviews > 0 ? round($baseQuery->avg('rating'), 1) : 0;
        
        $ratingCountsRaw = $product->reviews()
            ->select('rating', DB::raw('count(*) as count'))
            ->groupBy('rating')->pluck('count', 'rating')->toArray();
            
        $ratingCounts = [];
        for ($i = 5; $i >= 1; $i--) { $ratingCounts[$i] = $ratingCountsRaw[$i] ?? 0; }

        $reviewsQuery = $product->reviews()->with('user'); 
        if ($request->filled('rating')) { $reviewsQuery->where('rating', $request->query('rating')); }
        $reviews = $reviewsQuery->latest()->paginate(8);

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