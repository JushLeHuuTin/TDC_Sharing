{{-- resources/views/layouts/auth.blade.php --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>@yield('title', 'StudentMarket - Chợ Sinh Viên')</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <!-- Scripts -->
    <script src="https://cdn.tailwindcss.com"></script>

</head>
<body class="bg-gradient-to-br from-blue-50 to-indigo-100 min-h-screen">
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0">
        <!-- Logo -->
        <div class="mb-6">
            <a href="{{ route('home.index') }}" class="flex items-center">
                <div class="w-12 h-12 bg-blue-600 rounded-xl flex items-center justify-center mr-3">
                    <i class="fas fa-graduation-cap text-white text-xl"></i>
                </div>
                <span class="text-2xl font-bold text-blue-600">StudentMarket</span>
            </a>
        </div>

        <!-- Auth Card -->
        <div class="w-full sm:max-w-md mt-6 px-6 py-8 bg-white shadow-lg overflow-hidden sm:rounded-lg">
            @if (session('status'))
                <div class="mb-4 font-medium text-sm text-green-600">
                    {{ session('status') }}
                </div>
            @endif

            @if (session('error'))
                <div class="mb-4 font-medium text-sm text-red-600">
                    {{ session('error') }}
                </div>
            @endif

            @yield('content')
        </div>

        <!-- Footer -->
        <div class="mt-8 text-center text-sm text-gray-600">
            <p>&copy; {{ date('Y') }} StudentMarket. Tất cả quyền được bảo lưu.</p>
            <div class="mt-2 space-x-4">
                <a href="#" class="hover:text-blue-600">Điều khoản</a>
                <a href="#" class="hover:text-blue-600">Bảo mật</a>
                <a href="#" class="hover:text-blue-600">Liên hệ</a>
            </div>
        </div>
    </div>
</body>
</html>