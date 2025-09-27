{{-- resources/views/products/show.blade.php --}}
@extends('layouts.app')

@section('title', 'iPhone 13 Pro Max 256GB - StudentMarket')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Breadcrumb -->
    <nav class="flex mb-6" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li class="inline-flex items-center">
                <a href="#" class="text-gray-700 hover:text-blue-600">
                    <i class="fas fa-home mr-2"></i>Trang chủ
                </a>
            </li>
            <li>
                <div class="flex items-center">
                    <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                    <a href="#" class="text-gray-700 hover:text-blue-600">Điện tử</a>
                </div>
            </li>
            <li>
                <div class="flex items-center">
                    <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                    <span class="text-gray-500">iPhone 13 Pro Max 256GB</span>
                </div>
            </li>
        </ol>
    </nav>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Product Images -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                <!-- Main Image -->
                <div class="relative">
                    <img id="mainImage" 
                         src="https://via.placeholder.com/600x400/3B82F6/FFFFFF?text=iPhone+13+Pro+Max" 
                         alt="iPhone 13 Pro Max" 
                         class="w-full h-96 object-cover">
                    
                    <!-- Status Badge -->
                    <div class="absolute top-4 left-4">
                        <span class="bg-red-500 text-white px-3 py-1 rounded-full text-sm font-medium">
                            <i class="fas fa-fire mr-1"></i>Hot
                        </span>
                    </div>
                    
                    <!-- Favorite Button -->
                    <button class="absolute top-4 right-4 w-10 h-10 bg-white rounded-full shadow-md flex items-center justify-center hover:bg-gray-50 transition-colors">
                        <i class="far fa-heart text-gray-600 hover:text-red-500"></i>
                    </button>
                    
                    <!-- Share Button -->
                    <button class="absolute top-16 right-4 w-10 h-10 bg-white rounded-full shadow-md flex items-center justify-center hover:bg-gray-50 transition-colors">
                        <i class="fas fa-share-alt text-gray-600"></i>
                    </button>
                </div>
                
                <!-- Thumbnail Images -->
                <div class="p-4">
                    <div class="flex space-x-2 overflow-x-auto">
                        @for($i = 1; $i <= 5; $i++)
                            <img src="https://via.placeholder.com/100x80/3B82F6/FFFFFF?text={{ $i }}" 
                                 alt="Ảnh {{ $i }}" 
                                 class="w-20 h-16 object-cover rounded-md cursor-pointer border-2 {{ $i === 1 ? 'border-blue-500' : 'border-gray-200' }} hover:border-blue-500 transition-colors"
                                 onclick="changeMainImage(this.src)">
                        @endfor
                    </div>
                </div>
            </div>
            
            <!-- Product Description -->
            <div class="bg-white rounded-lg shadow-sm p-6 mt-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Mô tả sản phẩm</h3>
                <div class="prose max-w-none text-gray-700">
                    <p class="mb-4">
                        <strong>iPhone 13 Pro Max 256GB</strong> - Chiếc điện thoại flagship đỉnh cao từ Apple với nhiều tính năng vượt trội:
                    </p>
                    
                    <ul class="list-disc list-inside space-y-2 mb-4">
                        <li>Chip A15 Bionic mạnh mẽ, xử lý mọi tác vụ mượt mà</li>
                        <li>Camera Pro 12MP với chế độ Cinematic và ProRAW</li>
                        <li>Màn hình Super Retina XDR 6.7 inch với ProMotion 120Hz</li>
                        <li>Pin sử dụng cả ngày, hỗ trợ sạc nhanh và sạc không dây</li>
                        <li>Khả năng chống nước IP68</li>
                    </ul>
                    
                    <p class="mb-4">
                        <strong>Tình trạng máy:</strong> Như mới 95%, không trầy xước, đầy đủ phụ kiện gốc bao gồm:
                    </p>
                    
                    <ul class="list-disc list-inside space-y-1 mb-4">
                        <li>Hộp máy nguyên seal</li>
                        <li>Cáp Lightning to USB-C</li>
                        <li>Sách hướng dẫn</li>
                        <li>Ốp lưng silicon Apple chính hãng (tặng kèm)</li>
                    </ul>
                    
                    <p class="text-sm text-gray-600">
                        <strong>Lý do bán:</strong> Nâng cấp lên iPhone 14 Pro Max nên cần thanh lý máy cũ. 
                        Máy còn bảo hành Apple Care+ đến tháng 8/2024.
                    </p>
                </div>
            </div>
            
            <!-- Specifications -->
            <div class="bg-white rounded-lg shadow-sm p-6 mt-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Thông số kỹ thuật</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="space-y-3">
                        <div class="flex justify-between py-2 border-b border-gray-100">
                            <span class="text-gray-600">Thương hiệu:</span>
                            <span class="font-medium">Apple</span>
                        </div>
                        <div class="flex justify-between py-2 border-b border-gray-100">
                            <span class="text-gray-600">Model:</span>
                            <span class="font-medium">iPhone 13 Pro Max</span>
                        </div>
                        <div class="flex justify-between py-2 border-b border-gray-100">
                            <span class="text-gray-600">Dung lượng:</span>
                            <span class="font-medium">256GB</span>
                        </div>
                        <div class="flex justify-between py-2 border-b border-gray-100">
                            <span class="text-gray-600">Màu sắc:</span>
                            <span class="font-medium">Xanh Alpine</span>
                        </div>
                    </div>
                    <div class="space-y-3">
                        <div class="flex justify-between py-2 border-b border-gray-100">
                            <span class="text-gray-600">Tình trạng:</span>
                            <span class="font-medium text-green-600">Như mới (95%)</span>
                        </div>
                        <div class="flex justify-between py-2 border-b border-gray-100">
                            <span class="text-gray-600">Bảo hành:</span>
                            <span class="font-medium">Còn 6 tháng</span>
                        </div>
                        <div class="flex justify-between py-2 border-b border-gray-100">
                            <span class="text-gray-600">Xuất xứ:</span>
                            <span class="font-medium">Chính hãng VN/A</span>
                        </div>
                        <div class="flex justify-between py-2 border-b border-gray-100">
                            <span class="text-gray-600">Phụ kiện:</span>
                            <span class="font-medium">Đầy đủ</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Product Info & Actions -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow-sm p-6 sticky top-24">
                <!-- Price -->
                <div class="mb-6">
                    <div class="flex items-center space-x-3 mb-2">
                        <span class="text-3xl font-bold text-red-600">25.000.000₫</span>
                        <span class="text-lg text-gray-500 line-through">28.000.000₫</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <span class="bg-red-100 text-red-800 px-2 py-1 rounded text-sm font-medium">
                            Tiết kiệm 3.000.000₫
                        </span>
                        <span class="text-sm text-gray-600">(-11%)</span>
                    </div>
                </div>
                
                <!-- Seller Info -->
                <div class="border-t border-b border-gray-200 py-4 mb-6">
                    <div class="flex items-center space-x-3 mb-3">
                        <img src="https://via.placeholder.com/50" alt="Seller" class="w-12 h-12 rounded-full">
                        <div>
                            <h4 class="font-semibold text-gray-900">Nguyễn Văn A</h4>
                            <div class="flex items-center space-x-2">
                                <div class="flex items-center">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="fas fa-star text-yellow-400 text-sm"></i>
                                    @endfor
                                    <span class="text-sm text-gray-600 ml-1">(4.8)</span>
                                </div>
                                <span class="text-sm text-green-600">
                                    <i class="fas fa-check-circle mr-1"></i>Đã xác thực
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-3 gap-4 text-center text-sm">
                        <div>
                            <div class="font-semibold text-gray-900">23</div>
                            <div class="text-gray-600">Đã bán</div>
                        </div>
                        <div>
                            <div class="font-semibold text-gray-900">4.8</div>
                            <div class="text-gray-600">Đánh giá</div>
                        </div>
                        <div>
                            <div class="font-semibold text-gray-900">2h</div>
                            <div class="text-gray-600">Phản hồi</div>
                        </div>
                    </div>
                </div>
                
                <!-- Location & Time -->
                <div class="space-y-3 mb-6">
                    <div class="flex items-center text-gray-600">
                        <i class="fas fa-map-marker-alt mr-3 text-red-500"></i>
                        <span>Quận 1, TP. Hồ Chí Minh</span>
                    </div>
                    <div class="flex items-center text-gray-600">
                        <i class="fas fa-clock mr-3 text-blue-500"></i>
                        <span>Đăng 2 ngày trước</span>
                    </div>
                    <div class="flex items-center text-gray-600">
                        <i class="fas fa-eye mr-3 text-green-500"></i>
                        <span>156 lượt xem</span>
                    </div>
                </div>
                
                <!-- Action Buttons -->
                <div class="space-y-3">
                    <button class="w-full bg-blue-600 text-white py-3 px-4 rounded-lg hover:bg-blue-700 transition-colors font-medium">
                        <i class="fas fa-comments mr-2"></i>
                        Nhắn tin cho người bán
                    </button>
                    
                    <button class="w-full bg-green-600 text-white py-3 px-4 rounded-lg hover:bg-green-700 transition-colors font-medium">
                        <i class="fas fa-phone mr-2"></i>
                        Gọi điện: 0123 456 789
                    </button>
                    
                    <div class="grid grid-cols-2 gap-3">
                        <button class="flex-1 border border-gray-300 text-gray-700 py-2 px-4 rounded-lg hover:bg-gray-50 transition-colors">
                            <i class="far fa-heart mr-2"></i>Yêu thích
                        </button>
                        <button class="flex-1 border border-gray-300 text-gray-700 py-2 px-4 rounded-lg hover:bg-gray-50 transition-colors">
                            <i class="fas fa-share-alt mr-2"></i>Chia sẻ
                        </button>
                    </div>
                </div>
                
                <!-- Safety Tips -->
                <div class="mt-6 p-4 bg-yellow-50 rounded-lg">
                    <h5 class="font-medium text-yellow-800 mb-2">
                        <i class="fas fa-shield-alt mr-2"></i>Mẹo an toàn
                    </h5>
                    <ul class="text-sm text-yellow-700 space-y-1">
                        <li>• Gặp mặt tại nơi công cộng</li>
                        <li>• Kiểm tra kỹ sản phẩm trước khi mua</li>
                        <li>• Không chuyển tiền trước</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Related Products -->
    <div class="mt-12">
        <h3 class="text-2xl font-bold text-gray-900 mb-6">Sản phẩm tương tự</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            @php
                $relatedProducts = [
                    [
                        'id' => 10,
                        'title' => 'iPhone 12 Pro Max 128GB',
                        'description' => 'Máy đẹp, pin tốt, full box',
                        'price' => 20000000,
                        'original_price' => 23000000,
                        'image_url' => 'https://via.placeholder.com/300x200/6366F1/FFFFFF?text=iPhone+12',
                        'status' => 'available',
                        'seller' => (object)['name' => 'Trần Văn B', 'avatar_url' => 'https://via.placeholder.com/40'],
                        'created_at' => now()->subDays(1),
                        'views_count' => 89,
                        'favorites_count' => 12,
                        'location' => 'Thu Duc',
                        'condition' => 'new'
                    ],
                    [
                        'id' => 11,
                        'title' => 'iPhone 14 128GB',
                        'description' => 'Mới 100%, chưa kích hoạt',
                        'price' => 22000000,
                        'original_price' => null,
                        'image_url' => 'https://via.placeholder.com/300x200/8B5CF6/FFFFFF?text=iPhone+14',
                        'status' => 'available',
                        'seller' => (object)['name' => 'Lê Thị C', 'avatar_url' => 'https://via.placeholder.com/40'],
                        'created_at' => now()->subHours(12),
                        'views_count' => 45,
                        'favorites_count' => 8,
                        'location' => 'Thu Duc',
                        'condition' => 'new'
                    ],
                    [
                        'id' => 12,
                        'title' => 'Samsung Galaxy S22 Ultra',
                        'description' => 'Bản 256GB, màu đen, như mới',
                        'price' => 18000000,
                        'original_price' => 25000000,
                        'image_url' => 'https://via.placeholder.com/300x200/10B981/FFFFFF?text=Galaxy+S22',
                        'status' => 'available',
                        'seller' => (object)['name' => 'Phạm Văn D', 'avatar_url' => 'https://via.placeholder.com/40'],
                        'created_at' => now()->subHours(6),
                        'views_count' => 67,
                        'favorites_count' => 15,
                        'location' => 'Thu Duc',
                        'condition' => 'new'
                    ],
                    [
                        'id' => 13,
                        'title' => 'iPhone 13 128GB',
                        'description' => 'Máy đẹp, pin 89%, full phụ kiện',
                        'price' => 17500000,
                        'original_price' => 20000000,
                        'image_url' => 'https://via.placeholder.com/300x200/F59E0B/FFFFFF?text=iPhone+13',
                        'status' => 'available',
                        'seller' => (object)['name' => 'Hoàng Thị E', 'avatar_url' => 'https://via.placeholder.com/40'],
                        'created_at' => now()->subMinutes(30),
                        'views_count' => 12,
                        'favorites_count' => 3,
                        'location' => 'Thu Duc',
                        'condition' => 'new'
                    ]
                ];
            @endphp
            
            @foreach($relatedProducts as $product)
                @include('components.product-card', ['product' => (object)$product])
            @endforeach
        </div>
    </div>
</div>

@push('scripts')
<script>
function changeMainImage(src) {
    document.getElementById('mainImage').src = src;
    
    // Update thumbnail borders
    document.querySelectorAll('.w-20').forEach(img => {
        img.classList.remove('border-blue-500');
        img.classList.add('border-gray-200');
    });
    
    event.target.classList.remove('border-gray-200');
    event.target.classList.add('border-blue-500');
}
</script>
@endpush
@endsection