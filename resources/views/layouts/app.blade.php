
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
    
    @stack('styles')
</head>
<body class="bg-gray-50">
    <div class="min-h-screen">
        <!-- Header -->
        @include('components.header')
        
        <!-- Main Content -->
        <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            @if (session('success'))
                @include('partials.alerts', ['type' => 'success', 'message' => session('success')])
            @endif
            
            @if (session('error'))
                @include('partials.alerts', ['type' => 'error', 'message' => session('error')])
            @endif
            
            @yield('content')
        </main>
        
        <!-- Footer -->
        @include('components.footer')
        
        <!-- Floating Action Button -->

    </div>
    
    @stack('scripts')
</body>
</html>