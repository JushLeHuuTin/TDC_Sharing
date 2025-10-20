<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ReviewResource;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class ReviewController extends Controller
{
    public function index(Request $request, Product $product): JsonResponse
    {
        $request->validate([
            'rating' => 'nullable|integer|in:1,2,3,4,5',
        ]);

        // --- 1. Tính toán các số liệu thống kê ---
        $statsQuery = $product->reviews();
        $totalReviews = $statsQuery->count();
        $averageRating = $totalReviews > 0 ? round($statsQuery->avg('rating'), 1) : 0;
        $ratingCounts = $product->reviews()
            ->select('rating', DB::raw('count(*) as count'))
            ->groupBy('rating')
            ->pluck('count', 'rating')->all();
        for ($i = 5; $i >= 1; $i--) {
            $ratingCounts[$i] = $ratingCounts[$i] ?? 0;
        }

        // --- 2. Lấy danh sách đánh giá (PHIÊN BẢN ĐÃ SỬA LỖI) ---
        $reviewsQuery = $product->reviews()->with('user');

        // Chỉ áp dụng bộ lọc NẾU có tham số 'rating' trong URL
        if ($request->filled('rating')) {
            $reviewsQuery->where('rating', $request->query('rating'));
        }

        // Sắp xếp và phân trang
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
