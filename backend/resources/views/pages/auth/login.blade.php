{{-- resources/views/auth/login.blade.php --}}
@extends('layouts.auth')

@section('title', 'Đăng nhập - StudentMarket')

@section('content')
<div class="text-center mb-6">
    <h2 class="text-2xl font-bold text-gray-900">Đăng nhập</h2>
    <p class="text-gray-600 mt-2">Chào mừng bạn quay trở lại!</p>
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
                   placeholder="Nhập email của bạn">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <i class="fas fa-envelope text-gray-400"></i>
            </div>
        </div>
        @error('email')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <!-- Password -->
    <div>
        <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
            Mật khẩu
        </label>
        <div class="relative">
            <input id="password" 
                   name="password" 
                   type="password" 
                   autocomplete="current-password" 
                   required
                   class="w-full pl-10 pr-12 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('password') border-red-500 @enderror"
                   placeholder="Nhập mật khẩu">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <i class="fas fa-lock text-gray-400"></i>
            </div>
            <button type="button" 
                    onclick="togglePassword('password')"
                    class="absolute inset-y-0 right-0 pr-3 flex items-center">
                <i class="fas fa-eye text-gray-400 hover:text-gray-600" id="password-toggle"></i>
            </button>
        </div>
        @error('password')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <!-- Remember Me & Forgot Password -->
    <div class="flex items-center justify-between">
        <div class="flex items-center">
            <input id="remember_me" 
                   name="remember" 
                   type="checkbox" 
                   class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
            <label for="remember_me" class="ml-2 block text-sm text-gray-700">
                Ghi nhớ đăng nhập
            </label>
        </div>
        <a href="#" class="text-sm text-blue-600 hover:text-blue-800">
            Quên mật khẩu?
        </a>
    </div>

    <!-- Submit Button -->
    <button type="submit" 
            class="w-full bg-blue-600 text-white py-3 px-4 rounded-lg hover:bg-blue-700 focus:ring-4 focus:ring-blue-200 transition-colors font-medium">
        <i class="fas fa-sign-in-alt mr-2"></i>
        Đăng nhập
    </button>

    <!-- Social Login -->
    <div class="mt-6">
        <div class="relative">
            <div class="absolute inset-0 flex items-center">
                <div class="w-full border-t border-gray-300"></div>
            </div>
            <div class="relative flex justify-center text-sm">
                <span class="px-2 bg-white text-gray-500">Hoặc đăng nhập với</span>
            </div>
        </div>

        <div class="mt-6 grid grid-cols-2 gap-3">
            <button type="button" 
                    class="w-full inline-flex justify-center py-2 px-4 border border-gray-300 rounded-lg shadow-sm bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                <i class="fab fa-google text-red-500 mr-2"></i>
                Google
            </button>
            <button type="button" 
                    class="w-full inline-flex justify-center py-2 px-4 border border-gray-300 rounded-lg shadow-sm bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                <i class="fab fa-facebook text-blue-600 mr-2"></i>
                Facebook
            </button>
        </div>
    </div>

    <!-- Register Link -->
    <div class="text-center">
        <p class="text-sm text-gray-600">
            Chưa có tài khoản? 
            <a href="{{ route('auth.register') }}" class="text-blue-600 hover:text-blue-800 font-medium">
                Đăng ký ngay
            </a>
        </p>
    </div>
</form>

@push('scripts')
<script>
function togglePassword(fieldId) {
    const field = document.getElementById(fieldId);
    const toggle = document.getElementById(fieldId + '-toggle');
    
    if (field.type === 'password') {
        field.type = 'text';
        toggle.classList.remove('fa-eye');
        toggle.classList.add('fa-eye-slash');
    } else {
        field.type = 'password';
        toggle.classList.remove('fa-eye-slash');
        toggle.classList.add('fa-eye');
    }
}
</script>
@endpush
@endsection