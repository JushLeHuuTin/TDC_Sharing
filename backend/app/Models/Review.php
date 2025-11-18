<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Review extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'product_id',
        'reviewer_id', // Đảm bảo khớp cột DB
        'rating',
        'comment',
    ];

    /**
     * Mối quan hệ: Một đánh giá thuộc về một người dùng (người viết).
     * QUAN TRỌNG: Phải có hàm này tên là `user()` và chỉ rõ khóa ngoại.
     */
    public function user(): BelongsTo
    {
        // Liên kết cột 'reviewer_id' trong bảng 'reviews'
        // với cột 'id' (mặc định) trong bảng 'users'
        return $this->belongsTo(User::class, 'reviewer_id');
    }

    /**
     * Mối quan hệ: Một đánh giá thuộc về một sản phẩm.
     */
    public function product(): BelongsTo
    {
        // Laravel sẽ tự tìm khóa ngoại 'product_id'
        return $this->belongsTo(Product::class);
    }
}
