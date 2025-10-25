
<header class="bg-white shadow-sm sticky top-0 z-40">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
            <!-- Logo -->
            <div class="flex items-center">
                <a href="<?php echo e(route('home.index')); ?>" class="flex items-center">
                    <div class="w-10 h-10 bg-blue-600 rounded-lg flex items-center justify-center mr-3">
                        <i class="fas fa-graduation-cap text-white"></i>
                    </div>
                    <span class="text-xl font-bold text-blue-600 hidden sm:block">StudentMarket</span>
                </a>
            </div>

            <!-- Search Bar -->
            <div class="flex-1 max-w-2xl mx-8">
                <?php echo $__env->make('components.search-bar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            </div>

            <!-- Navigation -->
            <nav class="flex items-center space-x-4">
                <?php if(auth()->guard()->check()): ?>
                    <a href="<?php echo e(route('auth.login')); ?>" class="text-gray-700 hover:text-blue-600 px-3 py-2 rounded-md text-sm font-medium">
                        Đăng nhập
                    </a>
                    <a href="<?php echo e(route('auth.register')); ?>" class="bg-blue-600 text-white hover:bg-blue-700 px-4 py-2 rounded-md text-sm font-medium">
                        Đăng ký
                    </a>
                <?php else: ?>
                    <!-- Notifications -->
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" class="relative p-2 text-gray-600 hover:text-blue-600">
                            <i class="fas fa-bell text-lg"></i>
                            
                            <?php if(1!=1): ?>
                                <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">
                                    
                                    2
                                </span>
                            <?php endif; ?>
                        </button>
                        
                        <div x-show="open" hidden @click.away="open = false" class="absolute right-0 mt-2 w-80 bg-white rounded-md shadow-lg py-1 z-50">
                            <div class="px-4 py-2 border-b">
                                <h3 class="text-sm font-semibold text-gray-900">Thông báo</h3>
                            </div>
                            <div class="max-h-64 overflow-y-auto">
                                
                                    
                                    <div class="px-4 py-3 text-sm text-gray-500 text-center">
                                        Không có thông báo nào
                                    </div>
                                
                            </div>
                            <div class="border-t px-4 py-2">
                                <a href="#" class="text-sm text-blue-600 hover:text-blue-800">
                                    Xem tất cả
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Messages -->
                    <a href="#" class="relative p-2 text-gray-600 hover:text-blue-600">
                        <i class="fas fa-comments text-lg"></i>
                        
                        <?php if(1!=1): ?>
                            <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">
                                <?php echo e(auth()->user()->unreadMessagesCount()); ?>

                            </span>
                        <?php endif; ?>
                    </a>

                    <!-- Favorites -->
                    <a href="#" class="p-2 text-gray-600 hover:text-blue-600">
                        <i class="fas fa-heart text-lg"></i>
                    </a>

                    <!-- User Menu -->
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" class="flex items-center space-x-2 text-gray-700 hover:text-blue-600">
                            <img src="" alt="" class="w-8 h-8 rounded-full">
                            <span class="hidden md:block">tin</span>
                            <i class="fas fa-chevron-down text-xs"></i>
                        </button>
                        
                        <div x-show="open" hidden @click.away="open = false" class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50">
                            <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                <i class="fas fa-user mr-2"></i>Hồ sơ
                            </a>
                            <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                <i class="fas fa-box mr-2"></i>Sản phẩm của tôi
                            </a>
                            <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                <i class="fas fa-plus mr-2"></i>Đăng sản phẩm
                            </a>
                            <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                <i class="fas fa-cog mr-2"></i>Cài đặt
                            </a>
                            <div class="border-t border-gray-100"></div>
                            <form method="POST" action="#">
                                <?php echo csrf_field(); ?>
                                <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-sign-out-alt mr-2"></i>Đăng xuất
                                </button>
                            </form>
                        </div>
                    </div>
                <?php endif; ?>
            </nav>
        </div>
    </div>
</header><?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/ChuyenDeWeb1/Lab2/php-docker-dev/source/TDC_Sharing/resources/views/components/header.blade.php ENDPATH**/ ?>