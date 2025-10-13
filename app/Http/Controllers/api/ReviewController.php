<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreReviewRequest;
use App\Models\Review;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Exception;

class ReviewController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreReviewRequest  $request)
    {
        die('alo');
        // try {
        //     // Kiểm tra đăng nhập
        //     if (!Auth::check()) {
        //         return response()->json([
        //             'success' => false,
        //             'message' => 'Không lưu, session hết hạn',
        //         ], 401);
        //     }

        //     $validated = $request->validated();

        //     // Kiểm tra trùng đánh giá (người dùng đã review rồi → ghi đè)
        //     $existingReview = Review::where('product_id', $validated['product_id'])
        //         ->where('reviewer_id', Auth::id())
        //         ->first();

        //     if ($existingReview) {
        //         // Ghi đè (user nhấn nhiều lần)
        //         $existingReview->update([
        //             'rating' => $validated['rating'],
        //             'comment' => strip_tags($validated['comment'] ?? ''),
        //         ]);

        //         return response()->json([
        //             'success' => true,
        //             'message' => 'Nhấn nhiều lần → ghi đè',
        //             'data' => $existingReview,
        //         ]);
        //     }

        //     // Tạo mới review (dùng transaction để tránh lỗi SQL)
        //     DB::beginTransaction();

        //     $review = Review::create([
        //         'product_id'  => $validated['product_id'],
        //         'reviewer_id' => Auth::id(),
        //         'rating'      => $validated['rating'],
        //         'comment'     => strip_tags($validated['comment'] ?? ''),
        //     ]);

        //     DB::commit();

        //     return response()->json([
        //         'success' => true,
        //         'message' => 'Đánh giá sản phẩm thành công!',
        //         'data' => $review,
        //     ], 201);
        // } catch (Exception $e) {
        //     DB::rollBack();
        //     Log::error('Lỗi khi lưu đánh giá: ' . $e->getMessage());

        //     // Kiểm tra lỗi SQL hoặc Injection
        //     if (str_contains(strtolower($e->getMessage()), 'sql') || str_contains(strtolower($e->getMessage()), 'syntax')) {
        //         return response()->json([
        //             'success' => false,
        //             'message' => 'SQL / Injection, XSS',
        //         ], 500);
        //     }

        //     // Lỗi server chung
        //     return response()->json([
        //         'success' => false,
        //         'message' => 'Lỗi server, không lưu được',
        //     ], 500);
        // }
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreReviewRequest  $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
