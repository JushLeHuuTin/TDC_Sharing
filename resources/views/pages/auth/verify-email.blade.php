{{-- resources/views/auth/verify-email.blade.php --}}
@extends('layouts.auth')

@section('title', 'Xác thực email - StudentMarket')

@section('content')
<div class="text-center mb-6">
    <div class="w-16 h-16 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-4">
        <i class="fas fa-envelope-open text-yellow-600 text-2xl"></i>
    </div>
    <h2 class="text-2xl font-bold text-gray-900">Xác thực email</h2>
    <p class="text-gray-600 mt-2 max-w-md mx-auto">
        Chúng tôi đã gửi link xác thực đến email <strong>{{ auth()->user()->email ?? 'của bạn' }}</strong>. 
        Vui lòng kiểm tra email và nhấp vào link để hoàn tất đăng ký.
    </p>
</div>

<!-- Status Message -->
@if (session('status') == 'verification-link-sent')
    <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg">
        <div class="flex items-center">
            <i class="fas fa-check-circle text-green-500 mr-2"></i>
            <p class="text-sm text-green-700">
                Link xác thực mới đã được gửi đến email của bạn.
            </p>
        </div>
    </div>
@endif

<!-- Action Buttons -->
<div class="space-y-4">
    <!-- Resend Verification Email -->
    <form method="POST" action="#">
        @csrf
        <button type="submit" 
                class="w-full bg-blue-600 text-white py-3 px-4 rounded-lg hover:bg-blue-700 focus:ring-4 focus:ring-blue-200 transition-colors font-medium">
            <i class="fas fa-paper-plane mr-2"></i>
            Gửi lại email xác thực
        </button>
    </form>

    <!-- Check Email Button -->
    <button onclick="checkEmail()" 
            class="w-full bg-gray-100 text-gray-700 py-3 px-4 rounded-lg hover:bg-gray-200 transition-colors font-medium">
        <i class="fas fa-sync-alt mr-2"></i>
        Tôi đã xác thực, kiểm tra lại
    </button>

    <!-- Logout -->
    <form method="POST" action="#">
        @csrf
        <button type="submit" 
                class="w-full text-gray-600 hover:text-gray-800 py-2 text-sm">
            Đăng xuất và đăng nhập bằng tài khoản khác
        </button>
    </form>
</div>

<!-- Help Section -->
<div class="mt-8 p-4 bg-gray-50 rounded-lg">
    <h3 class="text-sm font-medium text-gray-900 mb-3">Không nhận được email?</h3>
    <div class="space-y-2 text-xs text-gray-600">
        <div class="flex items-start">
            <i class="fas fa-check text-green-500 mr-2 mt-0.5"></i>
            <span>Kiểm tra thư mục spam/junk mail</span>
        </div>
        <div class="flex items-start">
            <i class="fas fa-check text-green-500 mr-2 mt-0.5"></i>
            <span>Đảm bảo email chính xác: {{ auth()->user()->email ?? '' }}</span>
        </div>
        <div class="flex items-start">
            <i class="fas fa-check text-green-500 mr-2 mt-0.5"></i>
            <span>Chờ vài phút trước khi gửi lại</span>
        </div>
    </div>
    
    <div class="mt-4 pt-3 border-t border-gray-200">
        <p class="text-xs text-gray-600 mb-2">Vẫn gặp vấn đề? Liên hệ hỗ trợ:</p>
        <div class="flex items-center space-x-4 text-xs">
            <a href="mailto:support@studentmarket.vn" class="text-blue-600 hover:text-blue-800">
                <i class="fas fa-envelope mr-1"></i>
                support@studentmarket.vn
            </a>
            <a href="tel:0123456789" class="text-blue-600 hover:text-blue-800">
                <i class="fas fa-phone mr-1"></i>
                0123 456 789
            </a>
        </div>
    </div>
</div>

@push('scripts')
<script>
function checkEmail() {
    // Reload the page to check verification status
    window.location.reload();
}

// Auto-refresh every 30 seconds to check verification status
let autoRefreshInterval = setInterval(function() {
    fetch('/email/verification-status')
        .then(response => response.json())
        .then(data => {
            if (data.verified) {
                clearInterval(autoRefreshInterval);
                window.location.href = '/dashboard';
            }
        })
        .catch(error => {
            console.log('Auto-check failed:', error);
        });
}, 30000);

// Clear interval when page is hidden
document.addEventListener('visibilitychange', function() {
    if (document.hidden) {
        clearInterval(autoRefreshInterval);
    }
});
</script>
@endpush
@endsection