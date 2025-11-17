<?php 

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreReviewRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Chỉ cần người dùng đã đăng nhập là có thể gửi request.
        // Việc kiểm tra quyền hạn chi tiết sẽ do Policy xử lý.
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // ID sản phẩm phải được gửi kèm, là bắt buộc và phải tồn tại trong bảng products
            'product_id' => 'required|integer|exists:products,id',
            // 1. Số sao: Bắt buộc, là số nguyên, giá trị từ 1 đến 5.
            'rating'     => 'required|integer|min:1|max:5',
            // 2. Nội dung: Không bắt buộc, là chuỗi, tối đa 300 ký tự.
            'comment'    => 'nullable|string|max:300',
        ];
    }
 
    /**
     * Get the custom error messages for validator failures.
     */
    public function messages(): array
    {
        return [
            'product_id.required' => 'Thiếu thông tin sản phẩm cần đánh giá.',
            'product_id.exists'   => 'Sản phẩm bạn đang đánh giá không tồn tại.',
            'rating.required'     => 'Vui lòng chọn số sao đánh giá.',
            'rating.integer'      => 'Số sao phải là một số nguyên.',
            'rating.min'          => 'Vui lòng chọn số sao hợp lệ (1-5).',
            'rating.max'          => 'Vui lòng chọn số sao hợp lệ (1-5).',
            'comment.max'         => 'Nội dung đánh giá không vượt quá 300 ký tự.',
        ];
    }

    /**
     * Handle a failed validation attempt.
     */
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success'   => false,
            'message'   => 'Dữ liệu đầu vào không hợp lệ.',
            'errors'    => $validator->errors()
        ], 422));
    }
}
    