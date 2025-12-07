<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);
    
        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'message' => 'Sai email hoặc mật khẩu'
            ], 401);
        }
        
        $user = Auth::user();
        // 🔥 Nếu cần debug, dùng log thay vì dd()
        logger()->info($user);
    
        // 🔥 Tạo token (Sanctum)
        $token = $user->createToken('api_token')->plainTextToken;

    
        return response()->json([
            'message' => 'Đăng nhập thành công',
            'token' => $token,
            'user' => $user
        ], 200);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Đã đăng xuất']);
    }
}
