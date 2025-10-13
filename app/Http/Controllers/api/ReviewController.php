<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreReviewRequest;
use App\Models\Review; // Import model Review
use App\Models\Product; // Import model Product
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\JsonResponse;

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
    
}