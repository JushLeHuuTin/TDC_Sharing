{{-- resources/views/auth/reset-password.blade.php --}}
@extends('layouts.auth')

@section('title', 'Đặt lại mật khẩu - StudentMarket')

@section('content')
<div class="text-center mb-6">
    <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
        <i class="fas fa-shield-alt text-green-600 text-2xl"></i>
    </div>
    <h2 class="text-2xl font-bold text-gray-900">Đặt lại mật khẩu</h2>
    <p class="text-gray-600 mt-2">
        Nhập mật khẩu mới cho tài khoản của bạn
    </p>
</div>

<form method="POST" action="#" class="space-y-6">
    @csrf
    <input type="hidden" name="token" value="{{ $token ?? '' }}">
    
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
                   value="{{ old('email', $email ?? '') }}"
                   readonly
                   class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg bg-gray-50 text-gray-500"
                   placeholder="Email">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <i class="fas fa-envelope text-gray-400"></i>
            </div>
        </div>
    </div>

    <!-- New Password -->
    <div>
        <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
            Mật khẩu mới <span class="text-red-500">*</span>
        </label>
        <div class="relative">
            <input id="password" 
                   name="password" 
                   type="password" 
                   autocomplete="new-password" 
                   required
                   class="w-full pl-10 pr-12 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('password') border-red-500 @enderror"
                   placeholder="Nhập mật khẩu mới (tối thiểu 8 ký tự)">
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
        
        <!-- Password Strength Indicator -->
        <div class="mt-2">
            <div class="flex space-x-1">
                <div class="h-1 flex-1 bg-gray-200 rounded" id="strength-1"></div>
                <div class="h-1 flex-1 bg-gray-200 rounded" id="strength-2"></div>
                <div class="h-1 flex-1 bg-gray-200 rounded" id="strength-3"></div>
                <div class="h-1 flex-1 bg-gray-200 rounded" id="strength-4"></div>
            </div>
            <p class="text-xs text-gray-500 mt-1" id="strength-text">Độ mạnh mật khẩu</p>
        </div>
    </div>

    <!-- Confirm Password -->
    <div>
        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
            Xác nhận mật khẩu mới <span class="text-red-500">*</span>
        </label>
        <div class="relative">
            <input id="password_confirmation" 
                   name="password_confirmation" 
                   type="password" 
                   autocomplete="new-password" 
                   required
                   class="w-full pl-10 pr-12 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                   placeholder="Nhập lại mật khẩu mới">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <i class="fas fa-lock text-gray-400"></i>
            </div>
            <button type="button" 
                    onclick="togglePassword('password_confirmation')"
                    class="absolute inset-y-0 right-0 pr-3 flex items-center">
                <i class="fas fa-eye text-gray-400 hover:text-gray-600" id="password_confirmation-toggle"></i>
            </button>
        </div>
        <div class="mt-1" id="password-match">
            <!-- Password match indicator will be shown here -->
        </div>
    </div>

    <!-- Submit Button -->
    <button type="submit" 
            class="w-full bg-blue-600 text-white py-3 px-4 rounded-lg hover:bg-blue-700 focus:ring-4 focus:ring-blue-200 transition-colors font-medium">
        <i class="fas fa-check mr-2"></i>
        Đặt lại mật khẩu
    </button>

    <!-- Back to Login -->
    <div class="text-center">
        <a href="#" class="inline-flex items-center text-sm text-blue-600 hover:text-blue-800">
            <i class="fas fa-arrow-left mr-2"></i>
            Quay lại đăng nhập
        </a>
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

// Password strength checker
document.getElementById('password').addEventListener('input', function() {
    const password = this.value;
    const strength = checkPasswordStrength(password);
    updateStrengthIndicator(strength);
});

// Password match checker
document.getElementById('password_confirmation').addEventListener('input', function() {
    const password = document.getElementById('password').value;
    const confirmation = this.value;
    const matchDiv = document.getElementById('password-match');
    
    if (confirmation.length > 0) {
        if (password === confirmation) {
            matchDiv.innerHTML = '<p class="text-xs text-green-600"><i class="fas fa-check mr-1"></i>Mật khẩu khớp</p>';
        } else {
            matchDiv.innerHTML = '<p class="text-xs text-red-600"><i class="fas fa-times mr-1"></i>Mật khẩu không khớp</p>';
        }
    } else {
        matchDiv.innerHTML = '';
    }
});

function checkPasswordStrength(password) {
    let strength = 0;
    if (password.length >= 8) strength++;
    if (/[a-z]/.test(password)) strength++;
    if (/[A-Z]/.test(password)) strength++;
    if (/[0-9]/.test(password)) strength++;
    if (/[^A-Za-z0-9]/.test(password)) strength++;
    return Math.min(strength, 4);
}

function updateStrengthIndicator(strength) {
    const indicators = ['strength-1', 'strength-2', 'strength-3', 'strength-4'];
    const colors = ['bg-red-500', 'bg-orange-500', 'bg-yellow-500', 'bg-green-500'];
    const texts = ['Rất yếu', 'Yếu', 'Trung bình', 'Mạnh'];
    
    indicators.forEach((id, index) => {
        const element = document.getElementById(id);
        element.className = 'h-1 flex-1 rounded ' + (index < strength ? colors[strength - 1] : 'bg-gray-200');
    });
    
    document.getElementById('strength-text').textContent = strength > 0 ? texts[strength - 1] : 'Độ mạnh mật khẩu';
}
</script>
@endpush
@endsection