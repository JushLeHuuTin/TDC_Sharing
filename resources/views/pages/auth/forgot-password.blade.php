{{-- resources/views/auth/forgot-password.blade.php --}}
@extends('layouts.auth')

@section('title', 'Quên mật khẩu - StudentMarket')

@section('content')
<div class="text-center mb-6">
    <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
        <i class="fas fa-key text-blue-600 text-2xl"></i>
    </div>
    <h2 class="text-2xl font-bold text-gray-900">Quên mật khẩu?</h2>
    <p class="text-gray-600 mt-2 max-w-md mx-auto">
        Không sao! Nhập email của bạn và chúng tôi sẽ gửi link đặt lại mật khẩu.
    </p>
</div>

<form method="POST" action="#" class="space-y-6">
    @csrf
    
    <!-- Email -->
    <div>
        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
            Email
        </label>
        <div class="relative">
            <input id="email" 
                   name="email" 
                   type="email" 
                   autocomplete="email" 
                   required 
                   value="{{ old('email') }}"
                   class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('email') border-red-500 @enderror"
                   placeholder="Nhập email đã đăng ký">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <i class="fas fa-envelope text-gray-400"></i>
            </div>
        </div>
        @error('email')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <!-- Submit Button -->
    <button type="submit" 
            class="w-full bg-blue-600 text-white py-3 px-4 rounded-lg hover:bg-blue-700 focus:ring-4 focus:ring-blue-200 transition-colors font-medium">
        <i class="fas fa-paper-plane mr-2"></i>
        Gửi link đặt lại mật khẩu
    </button>

    <!-- Back to Login -->
    <div class="text-center">
        <a href="#" class="inline-flex items-center text-sm text-blue-600 hover:text-blue-800">
            <i class="fas fa-arrow-left mr-2"></i>
            Quay lại đăng nhập
        </a>
    </div>
</form>

<!-- Help Section -->
<div class="mt-8 p-4 bg-gray-50 rounded-lg">
    <h3 class="text-sm font-medium text-gray-900 mb-2">Cần hỗ trợ?</h3>
    <p class="text-xs text-gray-600 mb-3">
        Nếu bạn không nhận được email, hãy kiểm tra thư mục spam hoặc liên hệ với chúng tôi.
    </p>
    <div class="flex items-center space-x-4 text-xs">
        <a href="#" class="text-blue-600 hover:text-blue-800">
            <i class="fas fa-envelope mr-1"></i>
            support@studentmarket.vn
        </a>
        <a href="#" class="text-blue-600 hover:text-blue-800">
            <i class="fas fa-phone mr-1"></i>
            0123 456 789
        </a>
    </div>
</div>
@endsection