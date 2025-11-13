{{-- resources/views/products/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Tất cả sản phẩm - StudentMarket')

@section('content')
    <div class="flex flex-col lg:flex-row gap-6">
        <!-- Sidebar Filters -->
        <div class="lg:w-1/4">
            @include('pages.home.category-filter')
        </div>

        <!-- Main Content -->
        <div class="lg:w-3/4">
            <!-- Header -->
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 mb-2">Tất cả sản phẩm</h1>
                    <p class="text-gray-600">Tìm thấy 1,234 sản phẩm</p>
                </div>

                <!-- Sort Options -->
                <div class="flex items-center space-x-4 mt-4 sm:mt-0">
                    <span class="text-sm text-gray-600">Sắp xếp:</span>
                    <select class="px-3 py-2 border border-gray-300 rounded-md text-sm">
                        <option value="newest">Mới nhất</option>
                        <option value="price_low">Giá thấp đến cao</option>
                        <option value="price_high">Giá cao đến thấp</option>
                        <option value="popular">Phổ biến nhất</option>
                        <option value="nearest">Gần nhất</option>
                    </select>

                    <!-- View Toggle -->
                    <div class="flex border border-gray-300 rounded-md">
                        <button class="px-3 py-2 bg-blue-600 text-white rounded-l-md">
                            <i class="fas fa-th-large"></i>
                        </button>
                        <button class="px-3 py-2 text-gray-600 hover:bg-gray-50 rounded-r-md">
                            <i class="fas fa-list"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Products Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6 mb-8">
                @php
                    $products = [
                        [
                            'id' => 1,
                            'title' => 'iPhone 13 Pro Max 256GB',
                            'description' => 'Máy đẹp như mới, còn bảo hành 6 tháng',
                            'price' => 25000000,
                            'original_price' => 28000000,
                            'image_url' => 'https://via.placeholder.com/300x200/3B82F6/FFFFFF?text=iPhone+13',
                            'status' => 'hot',
                            'seller' => (object) [
                                'name' => 'Nguyễn Văn A',
                                'avatar_url' => 'https://via.placeholder.com/40',
                            ],
                            'created_at' => now()->subDays(2),
                            'views_count' => 156,
                            'favorites_count' => 23,
                            'condition' => 'new',
                            'location' => 'Thu Duc',
                        ],
                        [
                            'id' => 2,
                            'title' => 'MacBook Air M1 2020',
                            'description' => 'Laptop học tập lý tưởng cho sinh viên IT',
                            'price' => 22000000,
                            'original_price' => null,
                            'image_url' => 'https://via.placeholder.com/300x200/10B981/FFFFFF?text=MacBook+Air',
                            'status' => 'available',
                            'seller' => (object) [
                                'name' => 'Trần Thị B',
                                'avatar_url' => 'https://via.placeholder.com/40',
                            ],
                            'created_at' => now()->subDays(1),
                            'views_count' => 89,
                            'favorites_count' => 12,
                            'condition' => 'new',
                            'location' => 'Thu Duc',
                        ],
                        [
                            'id' => 3,
                            'title' => 'Xe đạp thể thao Giant',
                            'description' => 'Xe đạp 21 tốc độ, phù hợp đi học',
                            'price' => 3500000,
                            'original_price' => 4200000,
                            'image_url' => 'https://via.placeholder.com/300x200/F59E0B/FFFFFF?text=Xe+Dap',
                            'status' => 'available',
                            'seller' => (object) [
                                'name' => 'Lê Văn C',
                                'avatar_url' => 'https://via.placeholder.com/40',
                            ],
                            'created_at' => now()->subHours(12),
                            'views_count' => 45,
                            'favorites_count' => 8,
                            'condition' => 'new',
                            'location' => 'Thu Duc',
                        ],
                        [
                            'id' => 4,
                            'title' => 'Bộ sách Kinh tế học',
                            'description' => 'Trọn bộ sách giáo khoa năm 2-3-4',
                            'price' => 450000,
                            'original_price' => 800000,
                            'image_url' => 'https://via.placeholder.com/300x200/8B5CF6/FFFFFF?text=Sach+Giao+Khoa',
                            'status' => 'available',
                            'seller' => (object) [
                                'name' => 'Phạm Thị D',
                                'avatar_url' => 'https://via.placeholder.com/40',
                            ],
                            'created_at' => now()->subHours(6),
                            'views_count' => 67,
                            'favorites_count' => 15,
                            'condition' => 'new',
                            'location' => 'Thu Duc',
                        ],
                        [
                            'id' => 5,
                            'title' => 'Áo hoodie Uniqlo',
                            'description' => 'Size M, màu đen, mới 95%',
                            'price' => 350000,
                            'original_price' => 590000,
                            'image_url' => 'https://via.placeholder.com/300x200/EF4444/FFFFFF?text=Hoodie',
                            'status' => 'available',
                            'seller' => (object) [
                                'name' => 'Hoàng Văn E',
                                'avatar_url' => 'https://via.placeholder.com/40',
                            ],
                            'created_at' => now()->subMinutes(30),
                            'views_count' => 12,
                            'favorites_count' => 3,
                            'condition' => 'new',
                            'location' => 'Thu Duc',
                        ],
                        [
                            'id' => 6,
                            'title' => 'Máy tính bảng iPad Gen 9',
                            'description' => 'WiFi 64GB, màu xám, còn mới',
                            'price' => 8500000,
                            'original_price' => null,
                            'image_url' => 'https://via.placeholder.com/300x200/6366F1/FFFFFF?text=iPad',
                            'status' => 'available',
                            'seller' => (object) [
                                'name' => 'Vũ Thị F',
                                'avatar_url' => 'https://via.placeholder.com/40',
                            ],
                            'created_at' => now()->subHours(2),
                            'views_count' => 34,
                            'favorites_count' => 7,
                            'condition' => 'new',
                            'location' => 'Thu Duc',
                        ],
                        [
                            'id' => 6,
                            'title' => 'Máy tính bảng iPad Gen 9',
                            'description' => 'WiFi 64GB, màu xám, còn mới',
                            'price' => 8500000,
                            'original_price' => null,
                            'image_url' => 'https://via.placeholder.com/300x200/6366F1/FFFFFF?text=iPad',
                            'status' => 'available',
                            'seller' => (object) [
                                'name' => 'Vũ Thị F',
                                'avatar_url' => 'https://via.placeholder.com/40',
                            ],
                            'created_at' => now()->subHours(2),
                            'views_count' => 34,
                            'favorites_count' => 7,
                            'condition' => 'new',
                            'location' => 'Thu Duc',
                        ],
                        [
                            'id' => 6,
                            'title' => 'Máy tính bảng iPad Gen 9',
                            'description' => 'WiFi 64GB, màu xám, còn mới',
                            'price' => 8500000,
                            'original_price' => null,
                            'image_url' => 'https://via.placeholder.com/300x200/6366F1/FFFFFF?text=iPad',
                            'status' => 'available',
                            'seller' => (object) [
                                'name' => 'Vũ Thị F',
                                'avatar_url' => 'https://via.placeholder.com/40',
                            ],
                            'created_at' => now()->subHours(2),
                            'views_count' => 34,
                            'favorites_count' => 7,
                            'condition' => 'new',
                            'location' => 'Thu Duc',
                        ],
                        [
                            'id' => 6,
                            'title' => 'Máy tính bảng iPad Gen 9',
                            'description' => 'WiFi 64GB, màu xám, còn mới',
                            'price' => 8500000,
                            'original_price' => null,
                            'image_url' => 'https://via.placeholder.com/300x200/6366F1/FFFFFF?text=iPad',
                            'status' => 'available',
                            'seller' => (object) [
                                'name' => 'Vũ Thị F',
                                'avatar_url' => 'https://via.placeholder.com/40',
                            ],
                            'created_at' => now()->subHours(2),
                            'views_count' => 34,
                            'favorites_count' => 7,
                            'condition' => 'new',
                            'location' => 'Thu Duc',
                        ],
                        [
                            'id' => 6,
                            'title' => 'Máy tính bảng iPad Gen 9',
                            'description' => 'WiFi 64GB, màu xám, còn mới',
                            'price' => 8500000,
                            'original_price' => null,
                            'image_url' => 'https://via.placeholder.com/300x200/6366F1/FFFFFF?text=iPad',
                            'status' => 'available',
                            'seller' => (object) [
                                'name' => 'Vũ Thị F',
                                'avatar_url' => 'https://via.placeholder.com/40',
                            ],
                            'created_at' => now()->subHours(2),
                            'views_count' => 34,
                            'favorites_count' => 7,
                            'condition' => 'new',
                            'location' => 'Thu Duc',
                        ],
                        [
                            'id' => 6,
                            'title' => 'Máy tính bảng iPad Gen 9',
                            'description' => 'WiFi 64GB, màu xám, còn mới',
                            'price' => 8500000,
                            'original_price' => null,
                            'image_url' => 'https://via.placeholder.com/300x200/6366F1/FFFFFF?text=iPad',
                            'status' => 'available',
                            'seller' => (object) [
                                'name' => 'Vũ Thị F',
                                'avatar_url' => 'https://via.placeholder.com/40',
                            ],
                            'created_at' => now()->subHours(2),
                            'views_count' => 34,
                            'favorites_count' => 7,
                            'condition' => 'new',
                            'location' => 'Thu Duc',
                        ],
                    ];
                @endphp

                @foreach ($products as $product)
                    @include('components.product-card', ['product' => (object) $product])
                @endforeach
            </div>

            <!-- Pagination -->
            @include('partials.pagination')
        </div>
    </div>
@endsection
