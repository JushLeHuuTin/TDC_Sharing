{{-- resources/views/auth/register.blade.php --}}
@extends('layouts.auth')

@section('title', 'Đăng ký - StudentMarket')

@section('content')
<div class="text-center mb-6">
    <h2 class="text-2xl font-bold text-gray-900">Tạo tài khoản</h2>
    <p class="text-gray-600 mt-2">Tham gia cộng đồng sinh viên ngay hôm nay!</p>
</div>

<form method="POST" action="#" class="space-y-6">
    @csrf
    
    <!-- Name -->
    <div>
        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
            Họ và tên <span class="text-red-500">*</span>
        </label>
        <div class="relative">
            <input id="name" 
                   name="name" 
                   type="text" 
                   autocomplete="name" 
                   required 
                   value="{{ old('name') }}"
                   class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('name') border-red-500 @enderror"
                   placeholder="Nhập họ và tên">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <i class="fas fa-user text-gray-400"></i>
            </div>
        </div>
        @error('name')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <!-- Email -->
    <div>
        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
            Email <span class="text-red-500">*</span>
        </label>
        <div class="relative">
            <input id="email" 
                   name="email" 
                   type="email" 
                   autocomplete="email" 
                   required 
                   value="{{ old('email') }}"
                   class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('email') border-red-500 @enderror"
                   placeholder="Nhập email (@student.hcmus.edu.vn)">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <i class="fas fa-envelope text-gray-400"></i>
            </div>
        </div>
        @error('email')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
        <p class="mt-1 text-xs text-gray-500">Khuyến khích sử dụng email sinh viên</p>
    </div>

    <!-- Phone -->
    <div>
        <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">
            Số điện thoại
        </label>
        <div class="relative">
            <input id="phone" 
                   name="phone" 
                   type="tel" 
                   value="{{ old('phone') }}"
                   class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('phone') border-red-500 @enderror"
                   placeholder="Nhập số điện thoại">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <i class="fas fa-phone text-gray-400"></i>
            </div>
        </div>
        @error('phone')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <!-- University -->
    <div>
        <label for="university" class="block text-sm font-medium text-gray-700 mb-2">
            Trường đại học
        </label>
        <div class="relative">
            <select id="university" 
                    name="university" 
                    class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('university') border-red-500 @enderror">
                <option value="">Chọn trường đại học</option>
                <option value="hcmus" {{ old('university') == 'hcmus' ? 'selected' : '' }}>Đại học Khoa học Tự nhiên TP.HCM</option>
                <option value="hcmut" {{ old('university') == 'hcmut' ? 'selected' : '' }}>Đại học Bách khoa TP.HCM</option>
                <option value="hcmue" {{ old('university') == 'hcmue' ? 'selected' : '' }}>Đại học Sư phạm TP.HCM</option>
                <option value="ueh" {{ old('university') == 'ueh' ? 'selected' : '' }}>Đại học Kinh tế TP.HCM</option>
                <option value="other" {{ old('university') == 'other' ? 'selected' : '' }}>Khác</option>
            </select>
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <i class="fas fa-graduation-cap text-gray-400"></i>
            </div>
        </div>
        @error('university')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <!-- Password -->
    <div>
        <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
            Mật khẩu <span class="text-red-500">*</span>
        </label>
        <div class="relative">
            <input id="password" 
                   name="password" 
                   type="password" 
                   autocomplete="new-password" 
                   required
                   class="w-full pl-10 pr-12 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('password') border-red-500 @enderror"
                   placeholder="Nhập mật khẩu (tối thiểu 8 ký tự)">
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

    <!-- Confirm Password -->
    <div>
        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
            Xác nhận mật khẩu <span class="text-red-500">*</span>
        </label>
        <div class="relative">
            <input id="password_confirmation" 
                   name="password_confirmation" 
                   type="password" 
                   autocomplete="new-password" 
                   required
                   class="w-full pl-10 pr-12 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                   placeholder="Nhập lại mật khẩu">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <i class="fas fa-lock text-gray-400"></i>
            </div>
            <button type="button" 
                    onclick="togglePassword('password_confirmation')"
                    class="absolute inset-y-0 right-0 pr-3 flex items-center">
                <i class="fas fa-eye text-gray-400 hover:text-gray-600" id="password_confirmation-toggle"></i>
            </button>
        </div>
    </div>

    <!-- Terms Agreement -->
    <div class="flex items-start">
        <input id="terms" 
               name="terms" 
               type="checkbox" 
               required
               class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded mt-1">
        <label for="terms" class="ml-2 block text-sm text-gray-700">
            Tôi đồng ý với 
            <a href="#" class="text-blue-600 hover:text-blue-800">Điều khoản sử dụng</a> 
            và 
            <a href="#" class="text-blue-600 hover:text-blue-800">Chính sách bảo mật</a>
            <span class="text-red-500">*</span>
        </label>
    </div>

    <!-- Submit Button -->
    <button type="submit" 
            class="w-full bg-blue-600 text-white py-3 px-4 rounded-lg hover:bg-blue-700 focus:ring-4 focus:ring-blue-200 transition-colors font-medium">
        <i class="fas fa-user-plus mr-2"></i>
        Tạo tài khoản
    </button>

    <!-- Login Link -->
    <div class="text-center">
        <p class="text-sm text-gray-600">
            Đã có tài khoản? 
            <a href="{{ route('auth.login') }}" class="text-blue-600 hover:text-blue-800 font-medium">
                Đăng nhập ngay
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