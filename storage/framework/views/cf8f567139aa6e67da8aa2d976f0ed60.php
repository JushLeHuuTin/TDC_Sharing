


<?php $__env->startSection('title', 'StudentMarket - Chợ Sinh Viên'); ?>

<?php $__env->startSection('content'); ?>
<!-- Hero Section -->
<section class="bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-2xl p-8 mb-8">
    <div class="max-w-4xl mx-auto text-center">
        <h1 class="text-4xl md:text-5xl font-bold mb-4">
            Chào mừng đến với <span class="text-yellow-300">StudentMarket</span>
        </h1>
        <p class="text-xl mb-8 opacity-90">
            Nền tảng mua bán dành riêng cho sinh viên - Kết nối, chia sẻ, tiết kiệm!
        </p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <?php if(auth()->guard()->check()): ?>
                <a href="#" class="bg-white text-blue-600 px-8 py-3 rounded-lg font-semibold hover:bg-gray-100 transition-colors">
                    <i class="fas fa-user-plus mr-2"></i>Đăng ký ngay
                </a>
                <a href="#" class="border-2 border-white text-white px-8 py-3 rounded-lg font-semibold hover:bg-white hover:text-blue-600 transition-colors">
                    <i class="fas fa-sign-in-alt mr-2"></i>Đăng nhập
                </a>
            <?php else: ?>
                <a href="<?php echo e(route('products.create')); ?>" class="bg-white text-blue-600 px-8 py-3 rounded-lg font-semibold hover:bg-gray-100 transition-colors">
                    <i class="fas fa-plus mr-2"></i>Đăng bán ngay
                </a>
                <a href="<?php echo e(route('products.index')); ?>" class="border-2 border-white text-white px-8 py-3 rounded-lg font-semibold hover:bg-white hover:text-blue-600 transition-colors">
                    <i class="fas fa-search mr-2"></i>Khám phá sản phẩm
                </a>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Stats Section -->
<section class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
    <div class="bg-white rounded-lg p-6 text-center shadow-sm">
        <div class="text-3xl font-bold text-blue-600 mb-2">1,234</div>
        <div class="text-gray-600">Sản phẩm</div>
    </div>
    <div class="bg-white rounded-lg p-6 text-center shadow-sm">
        <div class="text-3xl font-bold text-green-600 mb-2">567</div>
        <div class="text-gray-600">Sinh viên</div>
    </div>
    <div class="bg-white rounded-lg p-6 text-center shadow-sm">
        <div class="text-3xl font-bold text-purple-600 mb-2">89</div>
        <div class="text-gray-600">Giao dịch</div>
    </div>
    <div class="bg-white rounded-lg p-6 text-center shadow-sm">
        <div class="text-3xl font-bold text-orange-600 mb-2">12</div>
        <div class="text-gray-600">Trường ĐH</div>
    </div>
</section>

<!-- Categories Section -->
<section class="mb-8">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-bold text-gray-900">Danh mục phổ biến</h2>
        <a href="#" class="text-blue-600 hover:text-blue-800 font-medium">
            Xem tất cả <i class="fas fa-arrow-right ml-1"></i>
        </a>
    </div>
    
    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
        <?php
            $categories = [
                ['name' => 'Điện tử', 'icon' => 'fas fa-laptop', 'color' => 'bg-blue-500', 'count' => 234],
                ['name' => 'Sách vở', 'icon' => 'fas fa-book', 'color' => 'bg-green-500', 'count' => 189],
                ['name' => 'Thời trang', 'icon' => 'fas fa-tshirt', 'color' => 'bg-pink-500', 'count' => 156],
                ['name' => 'Đồ dùng', 'icon' => 'fas fa-home', 'color' => 'bg-purple-500', 'count' => 98],
                ['name' => 'Xe cộ', 'icon' => 'fas fa-bicycle', 'color' => 'bg-orange-500', 'count' => 67],
                ['name' => 'Khác', 'icon' => 'fas fa-ellipsis-h', 'color' => 'bg-gray-500', 'count' => 45]
            ];
        ?>
        
        <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <a href="#" class="group bg-white rounded-lg p-4 shadow-sm hover:shadow-md transition-all duration-300 text-center">
                <div class="w-12 h-12 <?php echo e($category['color']); ?> rounded-lg flex items-center justify-center mx-auto mb-3 group-hover:scale-110 transition-transform">
                    <i class="<?php echo e($category['icon']); ?> text-white text-lg"></i>
                </div>
                <h3 class="font-semibold text-gray-900 mb-1"><?php echo e($category['name']); ?></h3>
                <p class="text-sm text-gray-500"><?php echo e($category['count']); ?> sản phẩm</p>
            </a>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
</section>

<!-- Featured Products -->
<section class="mb-8">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-bold text-gray-900">Sản phẩm nổi bật</h2>
        <a href="#" class="text-blue-600 hover:text-blue-800 font-medium">
            Xem tất cả <i class="fas fa-arrow-right ml-1"></i>
        </a>
    </div>
    
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <?php
            $featuredProducts = [
                [
                    'id' => 1,
                    'title' => 'iPhone 13 Pro Max 256GB',
                    'description' => 'Máy đẹp như mới, còn bảo hành 6 tháng',
                    'price' => 25000000,
                    'original_price' => 28000000,
                    'image_url' => 'https://via.placeholder.com/300x200/3B82F6/FFFFFF?text=iPhone+13',
                    'status' => 'hot',
                    'seller' => (object)['name' => 'Nguyễn Văn A', 'avatar_url' => 'https://via.placeholder.com/40'],
                    'created_at' => now()->subDays(2),
                    'views_count' => 156,
                    'favorites_count' => 23,
                     'condition' => 'new',
                        'location' =>'Thu Duc'
                ],
                [
                    'id' => 2,
                    'title' => 'MacBook Air M1 2020',
                    'description' => 'Laptop học tập lý tưởng cho sinh viên IT',
                    'price' => 22000000,
                    'original_price' => null,
                    'image_url' => 'https://via.placeholder.com/300x200/10B981/FFFFFF?text=MacBook+Air',
                    'status' => 'available',
                    'seller' => (object)['name' => 'Trần Thị B', 'avatar_url' => 'https://via.placeholder.com/40'],
                    'created_at' => now()->subDays(1),
                    'views_count' => 89,
                    'favorites_count' => 12,
                     'condition' => 'new',
                        'location' =>'Thu Duc'
                ],
                [
                    'id' => 3,
                    'title' => 'Xe đạp thể thao Giant',
                    'description' => 'Xe đạp 21 tốc độ, phù hợp đi học',
                    'price' => 3500000,
                    'original_price' => 4200000,
                    'image_url' => 'https://via.placeholder.com/300x200/F59E0B/FFFFFF?text=Xe+Dap',
                    'status' => 'available',
                    'seller' => (object)['name' => 'Lê Văn C', 'avatar_url' => 'https://via.placeholder.com/40'],
                    'created_at' => now()->subHours(12),
                    'views_count' => 45,
                    'favorites_count' => 8,
                     'condition' => 'new',
                        'location' =>'Thu Duc'
                ],
                [
                    'id' => 4,
                    'title' => 'Bộ sách Kinh tế học',
                    'description' => 'Trọn bộ sách giáo khoa năm 2-3-4',
                    'price' => 450000,
                    'original_price' => 800000,
                    'image_url' => 'https://via.placeholder.com/300x200/8B5CF6/FFFFFF?text=Sach+Giao+Khoa',
                    'status' => 'available',
                    'seller' => (object)['name' => 'Phạm Thị D', 'avatar_url' => 'https://via.placeholder.com/40'],
                    'created_at' => now()->subHours(6),
                    'views_count' => 67,
                    'favorites_count' => 15,
                     'condition' => 'new',
                        'location' =>'Thu Duc'
                ]
            ];
        ?>
        
        <?php $__currentLoopData = $featuredProducts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php echo $__env->make('components.product-card', ['product' => (object)$product], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
</section>

<!-- Recent Products -->
<section class="mb-8">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-bold text-gray-900">Sản phẩm mới nhất</h2>
        <a href="#" class="text-blue-600 hover:text-blue-800 font-medium">
            Xem tất cả <i class="fas fa-arrow-right ml-1"></i>
        </a>
    </div>
    
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <?php
            $recentProducts = [
                [
                    'id' => 5,
                    'title' => 'Áo hoodie Uniqlo',
                    'description' => 'Size M, màu đen, mới 95%',
                    'price' => 350000,
                    'original_price' => 590000,
                    'image_url' => 'https://via.placeholder.com/300x200/EF4444/FFFFFF?text=Hoodie',
                    'status' => 'available',
                    'seller' => (object)['name' => 'Hoàng Văn E', 'avatar_url' => 'https://via.placeholder.com/40'],
                    'created_at' => now()->subMinutes(30),
                    'views_count' => 12,
                    'favorites_count' => 3,
                     'condition' => 'new',
                        'location' =>'Thu Duc'
                ],
                [
                    'id' => 6,
                    'title' => 'Máy tính bảng iPad Gen 9',
                    'description' => 'WiFi 64GB, màu xám, còn mới',
                    'price' => 8500000,
                    'original_price' => null,
                    'image_url' => 'https://via.placeholder.com/300x200/6366F1/FFFFFF?text=iPad',
                    'status' => 'available',
                    'seller' => (object)['name' => 'Vũ Thị F', 'avatar_url' => 'https://via.placeholder.com/40'],
                    'created_at' => now()->subHours(2),
                    'views_count' => 34,
                    'favorites_count' => 7,
                     'condition' => 'new',
                        'location' =>'Thu Duc'
                ],
                [
                    'id' => 7,
                    'title' => 'Bàn học gỗ có ngăn kéo',
                    'description' => 'Bàn học sinh viên, kích thước 120x60cm',
                    'price' => 1200000,
                    'original_price' => 1800000,
                    'image_url' => 'https://via.placeholder.com/300x200/059669/FFFFFF?text=Ban+Hoc',
                    'status' => 'available',
                    'seller' => (object)['name' => 'Đỗ Văn G', 'avatar_url' => 'https://via.placeholder.com/40'],
                    'created_at' => now()->subHours(4),
                    'views_count' => 28,
                    'favorites_count' => 5,
                     'condition' => 'new',
                        'location' =>'Thu Duc'
                ],
                [
                    'id' => 8,
                    'title' => 'Tai nghe Sony WH-1000XM4',
                    'description' => 'Chống ồn chủ động, pin 30h',
                    'price' => 6500000,
                    'original_price' => 8500000,
                    'image_url' => 'https://via.placeholder.com/300x200/DC2626/FFFFFF?text=Tai+Nghe',
                    'status' => 'available',
                    'seller' => (object)['name' => 'Bùi Thị H', 'avatar_url' => 'https://via.placeholder.com/40'],
                    'created_at' => now()->subHours(8),
                    'views_count' => 56,
                    'favorites_count' => 11,
                     'condition' => 'new',
                        'location' =>'Thu Duc'
                ]
            ];
        ?>
        
        <?php $__currentLoopData = $recentProducts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php echo $__env->make('components.product-card', ['product' => (object)$product], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
</section>

<!-- How It Works -->
<section class="bg-white rounded-2xl p-8 mb-8">
    <div class="text-center mb-8">
        <h2 class="text-2xl font-bold text-gray-900 mb-4">Cách thức hoạt động</h2>
        <p class="text-gray-600 max-w-2xl mx-auto">
            Chỉ với 3 bước đơn giản, bạn có thể mua bán dễ dàng trên StudentMarket
        </p>
    </div>
    
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <div class="text-center">
            <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-user-plus text-blue-600 text-2xl"></i>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">1. Đăng ký tài khoản</h3>
            <p class="text-gray-600">Tạo tài khoản miễn phí với email sinh viên để bắt đầu</p>
        </div>
        
        <div class="text-center">
            <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-camera text-green-600 text-2xl"></i>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">2. Đăng sản phẩm</h3>
            <p class="text-gray-600">Chụp ảnh, mô tả sản phẩm và đăng bán trong vài phút</p>
        </div>
        
        <div class="text-center">
            <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-handshake text-purple-600 text-2xl"></i>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">3. Kết nối & giao dịch</h3>
            <p class="text-gray-600">Chat trực tiếp với người mua/bán và hoàn tất giao dịch</p>
        </div>
    </div>
</section>

<!-- Testimonials -->
<section class="mb-8">
    <div class="text-center mb-8">
        <h2 class="text-2xl font-bold text-gray-900 mb-4">Sinh viên nói gì về chúng tôi</h2>
        <p class="text-gray-600">Những phản hồi tích cực từ cộng đồng sinh viên</p>
    </div>
    
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <?php
            $testimonials = [
                [
                    'name' => 'Nguyễn Minh Anh',
                    'university' => 'ĐH Khoa học Tự nhiên',
                    'avatar' => 'https://via.placeholder.com/60',
                    'rating' => 5,
                    'comment' => 'Tuyệt vời! Tôi đã bán được laptop cũ và mua được máy tính mới với giá rất hợp lý.'
                ],
                [
                    'name' => 'Trần Văn Bình',
                    'university' => 'ĐH Bách khoa',
                    'avatar' => 'https://via.placeholder.com/60',
                    'rating' => 5,
                    'comment' => 'Giao diện thân thiện, dễ sử dụng. Đã tìm được nhiều sách giáo khoa với giá sinh viên.'
                ],
                [
                    'name' => 'Lê Thị Cẩm',
                    'university' => 'ĐH Kinh tế',
                    'avatar' => 'https://via.placeholder.com/60',
                    'rating' => 5,
                    'comment' => 'Cộng đồng sinh viên rất tích cực. Giao dịch nhanh chóng và an toàn.'
                ]
            ];
        ?>
        
        <?php $__currentLoopData = $testimonials; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $testimonial): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="bg-white rounded-lg p-6 shadow-sm">
                <div class="flex items-center mb-4">
                    <img src="<?php echo e($testimonial['avatar']); ?>" alt="<?php echo e($testimonial['name']); ?>" class="w-12 h-12 rounded-full mr-4">
                    <div>
                        <h4 class="font-semibold text-gray-900"><?php echo e($testimonial['name']); ?></h4>
                        <p class="text-sm text-gray-600"><?php echo e($testimonial['university']); ?></p>
                    </div>
                </div>
                
                <div class="flex mb-3">
                    <?php for($i = 1; $i <= 5; $i++): ?>
                        <i class="fas fa-star <?php echo e($i <= $testimonial['rating'] ? 'text-yellow-400' : 'text-gray-300'); ?>"></i>
                    <?php endfor; ?>
                </div>
                
                <p class="text-gray-700 italic">"<?php echo e($testimonial['comment']); ?>"</p>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
</section>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\TDC_Sharing\TDC_Sharing\resources\views/pages/home/index.blade.php ENDPATH**/ ?>