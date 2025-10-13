<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    /**
     * Thêm một sản phẩm vào giỏ hàng của người dùng đang đăng nhập.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function addItem(Request $request)
    {
        // --- Ràng buộc đầu vào ---
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|integer|exists:products,id',
            'quantity' => 'sometimes|integer|min:1', // `sometimes` nghĩa là chỉ validate nếu có, không bắt buộc
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422); // Unprocessable Entity
        }

        $productId = $request->input('product_id');
        $quantity = $request->input('quantity', 1); // Mặc định số lượng là 1 nếu không truyền
        $user = Auth::user();

        // --- Bắt đầu khối xử lý có Transaction để đảm bảo toàn vẹn dữ liệu ---
        DB::beginTransaction();
        try {
            $product = Product::find($productId);

            // --- XỬ LÝ CÁC RÀNG BUỘC LOGIC ---

            // 1. Ràng buộc: Người bán không thể tự mua sản phẩm của mình
            if ($product->user_id === $user->id) {
                DB::rollBack();
                return response()->json(['message' => 'Bạn không thể thêm sản phẩm của chính mình vào giỏ hàng.'], 403); // Forbidden
            }

            // 2. Ràng buộc: Kiểm tra sản phẩm có còn hàng không (stock > 0)
            if ($product->stocks < $quantity) {
                DB::rollBack();
                // Thông báo lỗi theo yêu cầu của bạn
                return response()->json(['message' => 'Sản phẩm đã hết hàng hoặc không đủ số lượng.'], 409); // Conflict
            }

            // --- XỬ LÝ LOGIC CHÍNH ---

            // Tìm hoặc tạo mới giỏ hàng cho người dùng
            $cart = Cart::firstOrCreate(['user_id' => $user->id]);

            // Kiểm tra xem sản phẩm này đã có trong giỏ hàng chưa
            $cartItem = CartItem::where('cart_id', $cart->id)
                ->where('product_id', $product->id)
                ->first();

            if ($cartItem) {
                // Nếu đã có, cập nhật số lượng
                $newQuantity = $cartItem->quantity + $quantity;
                // Kiểm tra lại số lượng tồn kho với tổng số lượng mới
                if ($product->stocks < $newQuantity) {
                    DB::rollBack();
                    return response()->json(['message' => 'Số lượng trong giỏ hàng vượt quá số lượng tồn kho.'], 409);
                }
                $cartItem->quantity = $newQuantity;
                $cartItem->save();
            } else {
                // Nếu chưa có, tạo mới cart_item
                CartItem::create([
                    'cart_id' => $cart->id,
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'price' => $product->price, // Lưu lại giá tại thời điểm thêm vào giỏ
                    'added_at' => now()
                ]);
            }

            DB::commit(); // Hoàn tất giao dịch nếu mọi thứ thành công

            return response()->json(['message' => 'Sản phẩm đã được thêm vào giỏ hàng thành công!'], 200);

        } catch (\Exception $e) {
            DB::rollBack(); // Hoàn tác lại tất cả thay đổi nếu có lỗi xảy ra
            Log::error('Lỗi khi thêm sản phẩm vào giỏ hàng: ' . $e->getMessage()); // Ghi log lỗi để debug

            // Thông báo lỗi chung theo yêu cầu của bạn
            return response()->json(['message' => 'Đã có lỗi xảy ra, vui lòng thử lại sau.'], 500); // Internal Server Error
        }
    }
}