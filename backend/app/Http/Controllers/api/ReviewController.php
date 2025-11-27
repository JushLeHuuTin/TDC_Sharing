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
    public function store(StoreReviewRequest $request): JsonResponse
    {
        $validatedData = $request->validated();
        $user = $request->user();
        $productId = $validatedData['product_id'];

        // --- DANH SÁCH TRẠNG THÁI HỢP LỆ ---
        $validStatuses = ['delivered', 'completed', 'success', 'đã giao', 'đã giao hàng'];

        // --- CHECK 1: Kiểm tra mua hàng ---
        $hasPurchased = Order::where('user_id', $user->id)
            ->whereIn('status', $validStatuses)
            ->whereHas('orderItems', function ($query) use ($productId) {
                $query->where('product_id', $productId);
            })
            ->exists();

        if (!$hasPurchased) {
            // === ĐOẠN CODE ĐIỀU TRA ===
            // Lấy tất cả ID sản phẩm mà User này đã mua thành công
            $boughtProductIds = DB::table('orders')
                ->join('order_items', 'orders.id', '=', 'order_items.order_id')
                ->where('orders.user_id', $user->id)
                ->whereIn('orders.status', $validStatuses)
                ->pluck('order_items.product_id')
                ->toArray();
            
            $listIds = implode(', ', $boughtProductIds);

            // Trả về lỗi chi tiết để bạn so sánh
            return response()->json([
                'success' => false,
                'message' => "LỖI LỆCH ID!\n" .
                             "- Bạn đang xem sản phẩm có ID: $productId\n" .
                             "- Nhưng bạn đã mua các sản phẩm ID: [$listIds]\n" .
                             "👉 Hãy kiểm tra xem 2 số này có khớp nhau không?"
            ], 403); 
        }

        // --- CHECK 2: Kiểm tra Spam ---
        $existingReview = Review::where('reviewer_id', $user->id)
            ->where('product_id', $productId)
            ->exists();

        if ($existingReview) {
            return response()->json(['success' => false, 'message' => 'Bạn đã đánh giá sản phẩm này rồi.'], 400); 
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
            return response()->json(['success' => true, 'message' => 'Gửi đánh giá thành công.', 'data' => new ReviewResource($review)], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()], 500);
        }
    }

    // Các hàm khác giữ nguyên...
    public function update(UpdateReviewRequest $request, Review $review): JsonResponse {
        $this->authorize('update', $review);
        $review->update($request->validated());
        return response()->json(['success' => true, 'message' => 'Cập nhật thành công.', 'data' => new ReviewResource($review)]);
    }
    public function destroy(Review $review): JsonResponse {
        $this->authorize('delete', $review);
        $review->delete();
        return response()->json(['success' => true, 'message' => 'Xóa thành công.']);
    }
    public function index(Request $request, Product $product): JsonResponse {
        $request->validate(['rating' => 'nullable|integer|in:1,2,3,4,5']);
        $reviewsQuery = $product->reviews()->with('user'); 
        if ($request->filled('rating')) { $reviewsQuery->where('rating', $request->query('rating')); }
        $reviews = $reviewsQuery->latest()->paginate(8);
        $totalReviews = $product->reviews()->count();
        $averageRating = $totalReviews > 0 ? round($product->reviews()->avg('rating'), 1) : 0;
        return response()->json(['success' => true, 'data' => [
            'summary' => ['total_reviews' => $totalReviews, 'average_rating' => $averageRating, 'rating_counts' => []],
            'reviews' => ReviewResource::collection($reviews),
        ]]);
    }
}