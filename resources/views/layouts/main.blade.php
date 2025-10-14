<main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- HOME PAGE -->
        <div id="home-page" class="page active fade-in">
            <!-- Hero Section -->
            <section class="bg-gradient-to-r from-blue-600 to-purple-600 rounded-2xl p-8 mb-8 text-white">
                <div class="max-w-2xl">
                    <h2 class="text-3xl font-bold mb-4">Chợ sinh viên trực tuyến</h2>
                    <p class="text-lg mb-6 opacity-90">Mua bán sách, laptop, đồ dùng học tập giữa sinh viên với nhau. Giá rẻ, chất lượng, uy tín!</p>
                    <div class="flex space-x-4">
                        <button class="bg-white text-blue-600 px-6 py-3 rounded-lg font-semibold hover:bg-gray-100 transition-colors">
                            <i class="fas fa-plus mr-2"></i>Đăng bán
                        </button>
                        <button class="border border-white text-white px-6 py-3 rounded-lg font-semibold hover:bg-white hover:text-blue-600 transition-colors">
                            <i class="fas fa-search mr-2"></i>Khám phá
                        </button>
                    </div>
                </div>
            </section>

            <!-- Categories -->
            <section class="mb-8">
                <h3 class="text-xl font-semibold mb-4">Danh mục sản phẩm</h3>
                <div class="flex flex-wrap gap-3">
                    <button class="category-btn category-active px-4 py-2 rounded-lg border">
                        <i class="fas fa-th-large mr-2"></i>Tất cả
                    </button>
                    <button class="category-btn bg-white text-gray-700 hover:bg-gray-50 px-4 py-2 rounded-lg border">
                        <i class="fas fa-book mr-2"></i>Sách giáo khoa
                    </button>
                    <button class="category-btn bg-white text-gray-700 hover:bg-gray-50 px-4 py-2 rounded-lg border">
                        <i class="fas fa-laptop mr-2"></i>Đồ điện tử
                    </button>
                    <button class="category-btn bg-white text-gray-700 hover:bg-gray-50 px-4 py-2 rounded-lg border">
                        <i class="fas fa-tshirt mr-2"></i>Quần áo
                    </button>
                    <button class="category-btn bg-white text-gray-700 hover:bg-gray-50 px-4 py-2 rounded-lg border">
                        <i class="fas fa-pencil-alt mr-2"></i>Đồ dùng học tập
                    </button>
                    <button class="category-btn bg-white text-gray-700 hover:bg-gray-50 px-4 py-2 rounded-lg border">
                        <i class="fas fa-ellipsis-h mr-2"></i>Khác
                    </button>
                </div>
            </section>

            <!-- Featured Products -->
            <section class="mb-8">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-xl font-semibold">Sản phẩm nổi bật</h3>
                    <a href="#" class="text-blue-600 hover:text-blue-700 font-medium">Xem tất cả <i class="fas fa-arrow-right ml-1"></i></a>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    <!-- Product Card 1 -->
                    <div class="product-card bg-white rounded-lg shadow-md overflow-hidden hover-shadow">
                        <div class="relative">
                            <img src="https://images.unsplash.com/photo-1544716278-ca5e3f4abd8c?w=400&h=300&fit=crop" alt="Sách Toán Cao Cấp" class="w-full h-48 object-cover">
                            <div class="absolute top-3 left-3 bg-green-500 text-white px-2 py-1 rounded-full text-xs font-semibold">
                                Còn hàng
                            </div>
                            <button class="heart-btn absolute top-3 right-3 w-8 h-8 bg-white rounded-full flex items-center justify-center text-gray-400 hover:text-red-500">
                                <i class="fas fa-heart"></i>
                            </button>
                        </div>
                        <div class="p-4">
                            <h4 class="font-semibold text-gray-900 mb-2">Sách Toán Cao Cấp A1</h4>
                            <p class="text-sm text-gray-600 mb-3">Sách giáo khoa Toán cao cấp, tình trạng 90%, không viết vẽ</p>
                            <div class="flex justify-between items-center mb-3">
                                <span class="text-lg font-bold text-blue-600">150.000₫</span>
                                <span class="text-sm text-gray-500 line-through">200.000₫</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <div class="flex items-center text-sm text-gray-500">
                                    <i class="fas fa-user mr-1"></i>
                                    <span>Nguyễn Văn A</span>
                                </div>
                                <div class="flex text-yellow-400 text-xs">
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                </div>
                            </div>
                            <button onclick="showProductDetail('1')" class="w-full mt-3 bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700 transition-colors">
                                <i class="fas fa-shopping-cart mr-2"></i>Xem chi tiết
                            </button>
                        </div>
                    </div>

                    <!-- Product Card 2 -->
                    <div class="product-card bg-white rounded-lg shadow-md overflow-hidden hover-shadow">
                        <div class="relative">
                            <img src="https://images.unsplash.com/photo-1496181133206-80ce9b88a853?w=400&h=300&fit=crop" alt="Laptop Dell" class="w-full h-48 object-cover">
                            <div class="absolute top-3 left-3 bg-red-500 text-white px-2 py-1 rounded-full text-xs font-semibold">
                                Hot
                            </div>
                            <button class="heart-btn heart-active absolute top-3 right-3 w-8 h-8 bg-white rounded-full flex items-center justify-center">
                                <i class="fas fa-heart"></i>
                            </button>
                        </div>
                        <div class="p-4">
                            <h4 class="font-semibold text-gray-900 mb-2">Laptop Dell Inspiron 15</h4>
                            <p class="text-sm text-gray-600 mb-3">i5-8250U, 8GB RAM, 256GB SSD, card đồ họa rời</p>
                            <div class="flex justify-between items-center mb-3">
                                <span class="text-lg font-bold text-blue-600">12.000.000₫</span>
                                <span class="text-sm text-gray-500 line-through">15.000.000₫</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <div class="flex items-center text-sm text-gray-500">
                                    <i class="fas fa-user mr-1"></i>
                                    <span>Trần Thị B</span>
                                </div>
                                <div class="flex text-yellow-400 text-xs">
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="far fa-star"></i>
                                </div>
                            </div>
                            <button onclick="showProductDetail('2')" class="w-full mt-3 bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700 transition-colors">
                                <i class="fas fa-shopping-cart mr-2"></i>Xem chi tiết
                            </button>
                        </div>
                    </div>

                    <!-- Product Card 3 -->
                    <div class="product-card bg-white rounded-lg shadow-md overflow-hidden hover-shadow">
                        <div class="relative">
                            <img src="https://images.unsplash.com/photo-1556821840-3a63f95609a7?w=400&h=300&fit=crop" alt="Áo hoodie" class="w-full h-48 object-cover">
                            <div class="absolute top-3 left-3 bg-blue-500 text-white px-2 py-1 rounded-full text-xs font-semibold">
                                Mới
                            </div>
                            <button class="heart-btn absolute top-3 right-3 w-8 h-8 bg-white rounded-full flex items-center justify-center text-gray-400">
                                <i class="fas fa-heart"></i>
                            </button>
                        </div>
                        <div class="p-4">
                            <h4 class="font-semibold text-gray-900 mb-2">Áo hoodie unisex</h4>
                            <p class="text-sm text-gray-600 mb-3">Màu đen, size M-L, chất liệu cotton 100%</p>
                            <div class="flex justify-between items-center mb-3">
                                <span class="text-lg font-bold text-blue-600">250.000₫</span>
                                <span class="text-sm text-gray-500 line-through">350.000₫</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <div class="flex items-center text-sm text-gray-500">
                                    <i class="fas fa-user mr-1"></i>
                                    <span>Lê Văn C</span>
                                </div>
                                <div class="flex text-yellow-400 text-xs">
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                </div>
                            </div>
                            <button onclick="showProductDetail('3')" class="w-full mt-3 bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700 transition-colors">
                                <i class="fas fa-shopping-cart mr-2"></i>Xem chi tiết
                            </button>
                        </div>
                    </div>

                    <!-- Product Card 4 -->
                    <div class="product-card bg-white rounded-lg shadow-md overflow-hidden hover-shadow">
                        <div class="relative">
                            <img src="https://images.unsplash.com/photo-1611224923853-80b023f02d71?w=400&h=300&fit=crop" alt="Máy tính Casio" class="w-full h-48 object-cover">
                            <div class="absolute top-3 left-3 bg-green-500 text-white px-2 py-1 rounded-full text-xs font-semibold">
                                Còn hàng
                            </div>
                            <button class="heart-btn heart-active absolute top-3 right-3 w-8 h-8 bg-white rounded-full flex items-center justify-center">
                                <i class="fas fa-heart"></i>
                            </button>
                        </div>
                        <div class="p-4">
                            <h4 class="font-semibold text-gray-900 mb-2">Máy tính Casio FX-580VN</h4>
                            <p class="text-sm text-gray-600 mb-3">Máy tính khoa học, còn mới 95%, đầy đủ chức năng</p>
                            <div class="flex justify-between items-center mb-3">
                                <span class="text-lg font-bold text-blue-600">450.000₫</span>
                                <span class="text-sm text-gray-500 line-through">550.000₫</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <div class="flex items-center text-sm text-gray-500">
                                    <i class="fas fa-user mr-1"></i>
                                    <span>Phạm Thị D</span>
                                </div>
                                <div class="flex text-yellow-400 text-xs">
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="far fa-star"></i>
                                </div>
                            </div>
                            <button onclick="showProductDetail('4')" class="w-full mt-3 bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700 transition-colors">
                                <i class="fas fa-shopping-cart mr-2"></i>Xem chi tiết
                            </button>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Stats Section -->
            <section class="mb-8">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="stats-card text-white p-6 rounded-xl">
                        <div class="flex items-center justify-between">
                            <div>
                                <h4 class="text-lg font-semibold mb-2">Sản phẩm</h4>
                                <p class="text-3xl font-bold">1,234</p>
                                <p class="text-sm opacity-80">Đang bán</p>
                            </div>
                            <i class="fas fa-box text-4xl opacity-80"></i>
                        </div>
                    </div>
                    <div class="stats-card-2 text-white p-6 rounded-xl">
                        <div class="flex items-center justify-between">
                            <div>
                                <h4 class="text-lg font-semibold mb-2">Sinh viên</h4>
                                <p class="text-3xl font-bold">567</p>
                                <p class="text-sm opacity-80">Đang hoạt động</p>
                            </div>
                            <i class="fas fa-users text-4xl opacity-80"></i>
                        </div>
                    </div>
                    <div class="stats-card-3 text-white p-6 rounded-xl">
                        <div class="flex items-center justify-between">
                            <div>
                                <h4 class="text-lg font-semibold mb-2">Giao dịch</h4>
                                <p class="text-3xl font-bold">890</p>
                                <p class="text-sm opacity-80">Thành công</p>
                            </div>
                            <i class="fas fa-handshake text-4xl opacity-80"></i>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Chat Preview -->
            <section class="mb-8">
                <h3 class="text-xl font-semibold mb-4">Tin nhắn gần đây</h3>
                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="space-y-4">
                        <div class="chat-bubble flex items-start space-x-3">
                            <div class="w-10 h-10 bg-blue-600 rounded-full flex items-center justify-center text-white font-semibold">
                                A
                            </div>
                            <div class="flex-1">
                                <div class="bg-gray-100 rounded-lg p-3">
                                    <p class="text-sm">Chào bạn, sách Toán cao cấp còn không?</p>
                                </div>
                                <p class="text-xs text-gray-500 mt-1">10:30 AM</p>
                            </div>
                        </div>
                        
                        <div class="chat-bubble flex items-start space-x-3 justify-end">
                            <div class="flex-1 text-right">
                                <div class="bg-blue-600 text-white rounded-lg p-3 inline-block">
                                    <p class="text-sm">Chào bạn! Sách vẫn còn nhé, bạn có muốn xem thêm hình không?</p>
                                </div>
                                <p class="text-xs text-gray-500 mt-1">10:32 AM</p>
                            </div>
                            <div class="w-10 h-10 bg-green-600 rounded-full flex items-center justify-center text-white font-semibold">
                                B
                            </div>
                        </div>
                        
                        <div class="text-center">
                            <button class="text-blue-600 hover:text-blue-700 font-medium">
                                Xem tất cả tin nhắn <i class="fas fa-arrow-right ml-1"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </section>
        </div>

        <!-- PRODUCT DETAIL PAGE -->
        <div id="product-detail-page" class="page fade-in">
            <div class="mb-6">
                <button onclick="showPage('home')" class="text-blue-600 hover:text-blue-700 font-medium">
                    <i class="fas fa-arrow-left mr-2"></i>Quay lại trang chủ
                </button>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
                <!-- Product Images -->
                <div class="space-y-4">
                    <div class="bg-white rounded-lg shadow-md overflow-hidden">
                        <img id="main-product-image" src="https://images.unsplash.com/photo-1544716278-ca5e3f4abd8c?w=600&h=400&fit=crop" alt="Sách Toán Cao Cấp" class="w-full h-96 object-cover zoom-image">
                    </div>
                    <div class="grid grid-cols-4 gap-2">
                        <img onclick="changeMainImage(this.src)" src="https://images.unsplash.com/photo-1544716278-ca5e3f4abd8c?w=150&h=150&fit=crop" alt="Ảnh 1" class="w-full h-20 object-cover rounded cursor-pointer border-2 border-blue-500">
                        <img onclick="changeMainImage(this.src)" src="https://images.unsplash.com/photo-1481627834876-b7833e8f5570?w=150&h=150&fit=crop" alt="Ảnh 2" class="w-full h-20 object-cover rounded cursor-pointer border-2 border-transparent hover:border-blue-300">
                        <img onclick="changeMainImage(this.src)" src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=150&h=150&fit=crop" alt="Ảnh 3" class="w-full h-20 object-cover rounded cursor-pointer border-2 border-transparent hover:border-blue-300">
                        <img onclick="changeMainImage(this.src)" src="https://images.unsplash.com/photo-1532012197267-da84d127e765?w=150&h=150&fit=crop" alt="Ảnh 4" class="w-full h-20 object-cover rounded cursor-pointer border-2 border-transparent hover:border-blue-300">
                    </div>
                </div>

                <!-- Product Info -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="mb-4">
                        <div class="flex items-center justify-between mb-2">
                            <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm font-medium">Còn hàng</span>
                            <button class="heart-btn text-gray-400 hover:text-red-500 text-2xl">
                                <i class="fas fa-heart"></i>
                            </button>
                        </div>
                        <h1 id="product-title" class="text-2xl font-bold text-gray-900 mb-2">Sách Toán Cao Cấp A1</h1>
                        <div class="flex items-center space-x-4 mb-4">
                            <div class="flex text-yellow-400">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                            </div>
                            <span class="text-gray-600">(4.8/5 - 12 đánh giá)</span>
                        </div>
                    </div>

                    <div class="mb-6">
                        <div class="flex items-center space-x-4 mb-4">
                            <span id="product-price" class="text-3xl font-bold text-blue-600">150.000₫</span>
                            <span class="text-lg text-gray-500 line-through">200.000₫</span>
                            <span class="bg-red-100 text-red-800 px-2 py-1 rounded text-sm font-medium">-25%</span>
                        </div>
                        <p class="text-gray-600 mb-4">Tiết kiệm: <span class="font-semibold text-green-600">50.000₫</span></p>
                    </div>

                    <!-- Product Details -->
                    <div class="mb-6">
                        <h3 class="font-semibold text-gray-900 mb-3">Thông tin sản phẩm</h3>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Tình trạng:</span>
                                <span class="font-medium">90% - Rất tốt</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Danh mục:</span>
                                <span class="font-medium">Sách giáo khoa</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Tác giả:</span>
                                <span class="font-medium">GS. Nguyễn Đình Trí</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Năm xuất bản:</span>
                                <span class="font-medium">2023</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Địa điểm:</span>
                                <span class="font-medium">Hà Nội</span>
                            </div>
                        </div>
                    </div>

                    <!-- Seller Info -->
                    <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                        <h3 class="font-semibold text-gray-900 mb-3">Thông tin người bán</h3>
                        <div class="flex items-center space-x-3 mb-3">
                            <div class="w-12 h-12 bg-blue-600 rounded-full flex items-center justify-center text-white font-semibold">
                                A
                            </div>
                            <div>
                                <h4 class="font-medium text-gray-900">Nguyễn Văn A</h4>
                                <p class="text-sm text-gray-600">Đại học Bách Khoa Hà Nội</p>
                                <div class="flex items-center text-sm">
                                    <div class="flex text-yellow-400 mr-2">
                                        <i class="fas fa-star text-xs"></i>
                                        <i class="fas fa-star text-xs"></i>
                                        <i class="fas fa-star text-xs"></i>
                                        <i class="fas fa-star text-xs"></i>
                                        <i class="fas fa-star text-xs"></i>
                                    </div>
                                    <span class="text-gray-600">(4.9/5 - 28 đánh giá)</span>
                                </div>
                            </div>
                        </div>
                        <div class="grid grid-cols-3 gap-4 text-center text-sm">
                            <div>
                                <p class="font-semibold text-blue-600">24</p>
                                <p class="text-gray-600">Đã bán</p>
                            </div>
                            <div>
                                <p class="font-semibold text-green-600">98%</p>
                                <p class="text-gray-600">Phản hồi</p>
                            </div>
                            <div>
                                <p class="font-semibold text-purple-600">2 năm</p>
                                <p class="text-gray-600">Kinh nghiệm</p>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="space-y-3">
                        <button class="w-full bg-blue-600 text-white py-3 rounded-lg font-semibold hover:bg-blue-700 transition-colors">
                            <i class="fas fa-comments mr-2"></i>Nhắn tin cho người bán
                        </button>
                        <button class="w-full border border-blue-600 text-blue-600 py-3 rounded-lg font-semibold hover:bg-blue-50 transition-colors">
                            <i class="fas fa-phone mr-2"></i>Gọi điện thoại
                        </button>
                        <div class="flex space-x-3">
                            <button class="flex-1 bg-green-600 text-white py-3 rounded-lg font-semibold hover:bg-green-700 transition-colors">
                                <i class="fas fa-shopping-cart mr-2"></i>Mua ngay
                            </button>
                            <button class="px-4 py-3 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                                <i class="fas fa-share-alt text-gray-600"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Product Description -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-8">
                <h3 class="text-xl font-semibold mb-4">Mô tả sản phẩm</h3>
                <div class="prose max-w-none">
                    <p class="text-gray-700 mb-4">
                        Sách Toán Cao Cấp A1 dành cho sinh viên năm nhất các ngành kỹ thuật. Cuốn sách được viết bởi GS. Nguyễn Đình Trí, 
                        một trong những chuyên gia hàng đầu về Toán học tại Việt Nam.
                    </p>
                    <p class="text-gray-700 mb-4">
                        <strong>Tình trạng sách:</strong> Còn rất mới (90%), không có viết vẽ, không rách hay hỏng. 
                        Bìa sách còn nguyên vẹn, các trang sách không bị ố vàng.
                    </p>
                    <p class="text-gray-700 mb-4">
                        <strong>Nội dung chính:</strong>
                    </p>
                    <ul class="list-disc list-inside text-gray-700 mb-4 space-y-1">
                        <li>Giới hạn và liên tục</li>
                        <li>Đạo hàm và vi phân</li>
                        <li>Tích phân không xác định</li>
                        <li>Tích phân xác định</li>
                        <li>Chuỗi số và chuỗi hàm</li>
                    </ul>
                    <p class="text-gray-700">
                        <strong>Lý do bán:</strong> Mình đã học xong môn này và muốn nhường lại cho các bạn sinh viên khác 
                        với giá ưu đãi. Sách rất hữu ích cho việc học tập và ôn thi.
                    </p>
                </div>
            </div>

            <!-- Reviews -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-xl font-semibold mb-4">Đánh giá từ người mua</h3>
                <div class="space-y-6">
                    <!-- Review 1 -->
                    <div class="border-b pb-6">
                        <div class="flex items-start space-x-4">
                            <div class="w-10 h-10 bg-green-600 rounded-full flex items-center justify-center text-white font-semibold">
                                B
                            </div>
                            <div class="flex-1">
                                <div class="flex items-center justify-between mb-2">
                                    <h4 class="font-medium text-gray-900">Trần Thị B</h4>
                                    <span class="text-sm text-gray-500">2 tuần trước</span>
                                </div>
                                <div class="flex text-yellow-400 mb-2">
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                </div>
                                <p class="text-gray-700">Sách rất tốt, đúng như mô tả. Người bán nhiệt tình, giao hàng nhanh. Rất hài lòng với giao dịch này!</p>
                            </div>
                        </div>
                    </div>

                    <!-- Review 2 -->
                    <div class="border-b pb-6">
                        <div class="flex items-start space-x-4">
                            <div class="w-10 h-10 bg-purple-600 rounded-full flex items-center justify-center text-white font-semibold">
                                C
                            </div>
                            <div class="flex-1">
                                <div class="flex items-center justify-between mb-2">
                                    <h4 class="font-medium text-gray-900">Lê Văn C</h4>
                                    <span class="text-sm text-gray-500">1 tháng trước</span>
                                </div>
                                <div class="flex text-yellow-400 mb-2">
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="far fa-star"></i>
                                </div>
                                <p class="text-gray-700">Chất lượng sách tốt, giá hợp lý. Giao dịch nhanh chóng và thuận tiện.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- LOGIN PAGE -->
        <div id="login-page" class="page fade-in">
            <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
                <div class="max-w-md w-full space-y-8">
                    <div class="text-center">
                        <div class="w-16 h-16 bg-blue-600 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-graduation-cap text-white text-2xl"></i>
                        </div>
                        <h2 class="text-3xl font-bold text-gray-900 mb-2">Đăng nhập</h2>
                        <p class="text-gray-600">Chào mừng trở lại StudentMarket!</p>
                    </div>

                    <div class="bg-white rounded-lg shadow-md p-8">
                        <form class="space-y-6" onsubmit="handleLogin(event)">
                            <div>
                                <label for="login-email" class="block text-sm font-medium text-gray-700 mb-2">
                                    Email sinh viên
                                </label>
                                <input 
                                    id="login-email" 
                                    name="email" 
                                    type="email" 
                                    required 
                                    class="form-input w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    placeholder="student@university.edu.vn"
                                >
                                <p class="text-xs text-gray-500 mt-1">Sử dụng email trường để xác thực tài khoản sinh viên</p>
                            </div>

                            <div>
                                <label for="login-password" class="block text-sm font-medium text-gray-700 mb-2">
                                    Mật khẩu
                                </label>
                                <div class="relative">
                                    <input 
                                        id="login-password" 
                                        name="password" 
                                        type="password" 
                                        required 
                                        class="form-input w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                        placeholder="Nhập mật khẩu"
                                    >
                                    <button type="button" onclick="togglePassword('login-password')" class="absolute right-3 top-2.5 text-gray-400 hover:text-gray-600">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <input 
                                        id="remember-me" 
                                        name="remember-me" 
                                        type="checkbox" 
                                        class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                                    >
                                    <label for="remember-me" class="ml-2 block text-sm text-gray-700">
                                        Ghi nhớ đăng nhập
                                    </label>
                                </div>
                                <a href="#" class="text-sm text-blue-600 hover:text-blue-700">
                                    Quên mật khẩu?
                                </a>
                            </div>

                            <button 
                                type="submit" 
                                class="w-full bg-blue-600 text-white py-2 px-4 rounded-lg font-semibold hover:bg-blue-700 transition-colors"
                            >
                                <i class="fas fa-sign-in-alt mr-2"></i>Đăng nhập
                            </button>
                        </form>

                        <div class="mt-6">
                            <div class="relative">
                                <div class="absolute inset-0 flex items-center">
                                    <div class="w-full border-t border-gray-300"></div>
                                </div>
                                <div class="relative flex justify-center text-sm">
                                    <span class="px-2 bg-white text-gray-500">Hoặc</span>
                                </div>
                            </div>

                            <div class="mt-6 space-y-3">
                                <button class="w-full flex items-center justify-center px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                                    <i class="fab fa-google text-red-500 mr-3"></i>
                                    Đăng nhập với Google
                                </button>
                                <button class="w-full flex items-center justify-center px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                                    <i class="fab fa-facebook text-blue-600 mr-3"></i>
                                    Đăng nhập với Facebook
                                </button>
                            </div>
                        </div>

                        <div class="mt-6 text-center">
                            <p class="text-sm text-gray-600">
                                Chưa có tài khoản? 
                                <button onclick="showPage('register')" class="text-blue-600 hover:text-blue-700 font-medium">
                                    Đăng ký ngay
                                </button>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- REGISTER PAGE -->
        <div id="register-page" class="page fade-in">
            <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
                <div class="max-w-2xl w-full space-y-8">
                    <div class="text-center">
                        <div class="w-16 h-16 bg-blue-600 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-graduation-cap text-white text-2xl"></i>
                        </div>
                        <h2 class="text-3xl font-bold text-gray-900 mb-2">Đăng ký tài khoản</h2>
                        <p class="text-gray-600">Tham gia cộng đồng mua bán sinh viên</p>
                    </div>

                    <!-- Registration Steps -->
                    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                        <div class="flex items-center justify-between mb-6">
                            <div class="verification-step active flex items-center space-x-2 px-4 py-2 rounded-lg border-2">
                                <div class="w-6 h-6 bg-blue-600 text-white rounded-full flex items-center justify-center text-xs font-bold">1</div>
                                <span class="text-sm font-medium">Thông tin cá nhân</span>
                            </div>
                            <div class="flex-1 h-px bg-gray-300 mx-4"></div>
                            <div class="verification-step flex items-center space-x-2 px-4 py-2 rounded-lg border-2 border-gray-200">
                                <div class="w-6 h-6 bg-gray-300 text-gray-600 rounded-full flex items-center justify-center text-xs font-bold">2</div>
                                <span class="text-sm text-gray-500">Xác thực email</span>
                            </div>
                            <div class="flex-1 h-px bg-gray-300 mx-4"></div>
                            <div class="verification-step flex items-center space-x-2 px-4 py-2 rounded-lg border-2 border-gray-200">
                                <div class="w-6 h-6 bg-gray-300 text-gray-600 rounded-full flex items-center justify-center text-xs font-bold">3</div>
                                <span class="text-sm text-gray-500">Hoàn thành</span>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg shadow-md p-8">
                        <!-- Step 1: Personal Information -->
                        <div id="register-step-1">
                            <form class="space-y-6" onsubmit="handleRegisterStep1(event)">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label for="register-firstname" class="block text-sm font-medium text-gray-700 mb-2">
                                            Họ và tên đệm *
                                        </label>
                                        <input 
                                            id="register-firstname" 
                                            name="firstname" 
                                            type="text" 
                                            required 
                                            class="form-input w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                            placeholder="Nguyễn Văn"
                                        >
                                    </div>
                                    <div>
                                        <label for="register-lastname" class="block text-sm font-medium text-gray-700 mb-2">
                                            Tên *
                                        </label>
                                        <input 
                                            id="register-lastname" 
                                            name="lastname" 
                                            type="text" 
                                            required 
                                            class="form-input w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                            placeholder="An"
                                        >
                                    </div>
                                </div>

                                <div>
                                    <label for="register-email" class="block text-sm font-medium text-gray-700 mb-2">
                                        Email sinh viên *
                                    </label>
                                    <input 
                                        id="register-email" 
                                        name="email" 
                                        type="email" 
                                        required 
                                        class="form-input w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                        placeholder="student@university.edu.vn"
                                    >
                                    <div class="mt-2 p-3 bg-blue-50 rounded-lg">
                                        <div class="flex items-start space-x-2">
                                            <i class="fas fa-info-circle text-blue-500 mt-0.5"></i>
                                            <div class="text-sm text-blue-700">
                                                <p class="font-medium mb-1">Yêu cầu email sinh viên:</p>
                                                <ul class="list-disc list-inside space-y-1 text-xs">
                                                    <li>Phải là email chính thức của trường đại học/cao đẳng</li>
                                                    <li>Thường có định dạng: @university.edu.vn hoặc @student.university.edu.vn</li>
                                                    <li>Sẽ được xác thực qua email để đảm bảo tính chính xác</li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label for="register-university" class="block text-sm font-medium text-gray-700 mb-2">
                                            Trường đại học *
                                        </label>
                                        <select 
                                            id="register-university" 
                                            name="university" 
                                            required 
                                            class="form-input w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                        >
                                            <option value="">Chọn trường</option>
                                            <option value="hust">Đại học Bách Khoa Hà Nội</option>
                                            <option value="vnu">Đại học Quốc gia Hà Nội</option>
                                            <option value="hcmus">Đại học Khoa học Tự nhiên TP.HCM</option>
                                            <option value="hcmut">Đại học Bách Khoa TP.HCM</option>
                                            <option value="neu">Đại học Kinh tế Quốc dân</option>
                                            <option value="ftu">Đại học Ngoại thương</option>
                                            <option value="other">Khác</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label for="register-major" class="block text-sm font-medium text-gray-700 mb-2">
                                            Ngành học *
                                        </label>
                                        <input 
                                            id="register-major" 
                                            name="major" 
                                            type="text" 
                                            required 
                                            class="form-input w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                            placeholder="Công nghệ thông tin"
                                        >
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label for="register-password" class="block text-sm font-medium text-gray-700 mb-2">
                                            Mật khẩu *
                                        </label>
                                        <div class="relative">
                                            <input 
                                                id="register-password" 
                                                name="password" 
                                                type="password" 
                                                required 
                                                class="form-input w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                                placeholder="Tối thiểu 8 ký tự"
                                            >
                                            <button type="button" onclick="togglePassword('register-password')" class="absolute right-3 top-2.5 text-gray-400 hover:text-gray-600">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div>
                                        <label for="register-confirm-password" class="block text-sm font-medium text-gray-700 mb-2">
                                            Xác nhận mật khẩu *
                                        </label>
                                        <div class="relative">
                                            <input 
                                                id="register-confirm-password" 
                                                name="confirm-password" 
                                                type="password" 
                                                required 
                                                class="form-input w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                                placeholder="Nhập lại mật khẩu"
                                            >
                                            <button type="button" onclick="togglePassword('register-confirm-password')" class="absolute right-3 top-2.5 text-gray-400 hover:text-gray-600">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <div>
                                    <label for="register-phone" class="block text-sm font-medium text-gray-700 mb-2">
                                        Số điện thoại
                                    </label>
                                    <input 
                                        id="register-phone" 
                                        name="phone" 
                                        type="tel" 
                                        class="form-input w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                        placeholder="0123456789"
                                    >
                                </div>

                                <div class="flex items-start space-x-3">
                                    <input 
                                        id="register-terms" 
                                        name="terms" 
                                        type="checkbox" 
                                        required 
                                        class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded mt-1"
                                    >
                                    <label for="register-terms" class="text-sm text-gray-700">
                                        Tôi đồng ý với 
                                        <a href="#" class="text-blue-600 hover:text-blue-700">Điều khoản sử dụng</a> 
                                        và 
                                        <a href="#" class="text-blue-600 hover:text-blue-700">Chính sách bảo mật</a> 
                                        của StudentMarket
                                    </label>
                                </div>

                                <button 
                                    type="submit" 
                                    class="w-full bg-blue-600 text-white py-3 px-4 rounded-lg font-semibold hover:bg-blue-700 transition-colors"
                                >
                                    <i class="fas fa-arrow-right mr-2"></i>Tiếp tục xác thực email
                                </button>
                            </form>
                        </div>

                        <!-- Step 2: Email Verification -->
                        <div id="register-step-2" class="hidden">
                            <div class="text-center mb-8">
                                <div class="w-16 h-16 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <i class="fas fa-envelope text-yellow-600 text-2xl"></i>
                                </div>
                                <h3 class="text-xl font-semibold text-gray-900 mb-2">Xác thực email sinh viên</h3>
                                <p class="text-gray-600">Chúng tôi đã gửi mã xác thực đến email của bạn</p>
                                <p class="text-sm text-blue-600 font-medium mt-2" id="verification-email"></p>
                            </div>

                            <form class="space-y-6" onsubmit="handleEmailVerification(event)">
                                <div>
                                    <label for="verification-code" class="block text-sm font-medium text-gray-700 mb-2">
                                        Mã xác thực (6 chữ số)
                                    </label>
                                    <input 
                                        id="verification-code" 
                                        name="code" 
                                        type="text" 
                                        required 
                                        maxlength="6"
                                        class="form-input w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-center text-2xl font-mono"
                                        placeholder="123456"
                                    >
                                </div>

                                <div class="text-center">
                                    <p class="text-sm text-gray-600 mb-4">
                                        Không nhận được mã? 
                                        <button type="button" onclick="resendVerificationCode()" class="text-blue-600 hover:text-blue-700 font-medium">
                                            Gửi lại mã
                                        </button>
                                    </p>
                                    <p class="text-xs text-gray-500">
                                        Mã xác thực có hiệu lực trong 10 phút
                                    </p>
                                </div>

                                <button 
                                    type="submit" 
                                    class="w-full bg-blue-600 text-white py-3 px-4 rounded-lg font-semibold hover:bg-blue-700 transition-colors"
                                >
                                    <i class="fas fa-check mr-2"></i>Xác thực email
                                </button>

                                <button 
                                    type="button" 
                                    onclick="showRegisterStep(1)"
                                    class="w-full border border-gray-300 text-gray-700 py-3 px-4 rounded-lg font-semibold hover:bg-gray-50 transition-colors"
                                >
                                    <i class="fas fa-arrow-left mr-2"></i>Quay lại
                                </button>
                            </form>
                        </div>

                        <!-- Step 3: Success -->
                        <div id="register-step-3" class="hidden">
                            <div class="text-center">
                                <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <i class="fas fa-check text-green-600 text-2xl"></i>
                                </div>
                                <h3 class="text-xl font-semibold text-gray-900 mb-2">Đăng ký thành công!</h3>
                                <p class="text-gray-600 mb-6">Chào mừng bạn đến với cộng đồng StudentMarket</p>

                                <div class="university-badge text-white p-4 rounded-lg mb-6">
                                    <div class="flex items-center justify-center space-x-2 mb-2">
                                        <i class="fas fa-shield-alt"></i>
                                        <span class="font-semibold">Tài khoản đã được xác thực</span>
                                    </div>
                                    <p class="text-sm opacity-90">Email sinh viên của bạn đã được xác nhận</p>
                                </div>

                                <div class="space-y-3">
                                    <button 
                                        onclick="showPage('home')" 
                                        class="w-full bg-blue-600 text-white py-3 px-4 rounded-lg font-semibold hover:bg-blue-700 transition-colors"
                                    >
                                        <i class="fas fa-home mr-2"></i>Khám phá StudentMarket
                                    </button>
                                    <button 
                                        onclick="showPage('profile')" 
                                        class="w-full border border-blue-600 text-blue-600 py-3 px-4 rounded-lg font-semibold hover:bg-blue-50 transition-colors"
                                    >
                                        <i class="fas fa-user mr-2"></i>Hoàn thiện hồ sơ
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="mt-6 text-center">
                            <p class="text-sm text-gray-600">
                                Đã có tài khoản? 
                                <button onclick="showPage('login')" class="text-blue-600 hover:text-blue-700 font-medium">
                                    Đăng nhập ngay
                                </button>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- FAVORITES PAGE -->
        <div id="favorites-page" class="page fade-in">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold">Sản phẩm yêu thích</h2>
                <div class="text-sm text-gray-500">3 sản phẩm</div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                <!-- Favorite Product 1 -->
                <div class="product-card bg-white rounded-lg shadow-md overflow-hidden hover-shadow">
                    <div class="relative">
                        <img src="https://images.unsplash.com/photo-1496181133206-80ce9b88a853?w=400&h=300&fit=crop" alt="Laptop Dell" class="w-full h-48 object-cover">
                        <div class="absolute top-3 left-3 bg-red-500 text-white px-2 py-1 rounded-full text-xs font-semibold">
                            Hot
                        </div>
                        <button class="heart-btn heart-active absolute top-3 right-3 w-8 h-8 bg-white rounded-full flex items-center justify-center">
                            <i class="fas fa-heart"></i>
                        </button>
                    </div>
                    <div class="p-4">
                        <h4 class="font-semibold text-gray-900 mb-2">Laptop Dell Inspiron 15</h4>
                        <p class="text-sm text-gray-600 mb-3">i5-8250U, 8GB RAM, 256GB SSD, card đồ họa rời</p>
                        <div class="flex justify-between items-center mb-3">
                            <span class="text-lg font-bold text-blue-600">12.000.000₫</span>
                            <span class="text-sm text-gray-500 line-through">15.000.000₫</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center text-sm text-gray-500">
                                <i class="fas fa-user mr-1"></i>
                                <span>Trần Thị B</span>
                            </div>
                            <div class="flex text-yellow-400 text-xs">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="far fa-star"></i>
                            </div>
                        </div>
                        <div class="flex space-x-2 mt-3">
                            <button onclick="showProductDetail('2')" class="flex-1 bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700 transition-colors">
                                <i class="fas fa-shopping-cart mr-2"></i>Xem
                            </button>
                            <button class="bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600 transition-colors">
                                <i class="fas fa-heart-broken"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Favorite Product 2 -->
                <div class="product-card bg-white rounded-lg shadow-md overflow-hidden hover-shadow">
                    <div class="relative">
                        <img src="https://images.unsplash.com/photo-1611224923853-80b023f02d71?w=400&h=300&fit=crop" alt="Máy tính Casio" class="w-full h-48 object-cover">
                        <div class="absolute top-3 left-3 bg-green-500 text-white px-2 py-1 rounded-full text-xs font-semibold">
                            Còn hàng
                        </div>
                        <button class="heart-btn heart-active absolute top-3 right-3 w-8 h-8 bg-white rounded-full flex items-center justify-center">
                            <i class="fas fa-heart"></i>
                        </button>
                    </div>
                    <div class="p-4">
                        <h4 class="font-semibold text-gray-900 mb-2">Máy tính Casio FX-580VN</h4>
                        <p class="text-sm text-gray-600 mb-3">Máy tính khoa học, còn mới 95%, đầy đủ chức năng</p>
                        <div class="flex justify-between items-center mb-3">
                            <span class="text-lg font-bold text-blue-600">450.000₫</span>
                            <span class="text-sm text-gray-500 line-through">550.000₫</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center text-sm text-gray-500">
                                <i class="fas fa-user mr-1"></i>
                                <span>Phạm Thị D</span>
                            </div>
                            <div class="flex text-yellow-400 text-xs">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="far fa-star"></i>
                            </div>
                        </div>
                        <div class="flex space-x-2 mt-3">
                            <button onclick="showProductDetail('4')" class="flex-1 bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700 transition-colors">
                                <i class="fas fa-shopping-cart mr-2"></i>Xem
                            </button>
                            <button class="bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600 transition-colors">
                                <i class="fas fa-heart-broken"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Favorite Product 3 -->
                <div class="product-card bg-white rounded-lg shadow-md overflow-hidden hover-shadow">
                    <div class="relative">
                        <img src="https://images.unsplash.com/photo-1549298916-b41d501d3772?w=400&h=300&fit=crop" alt="Giày Nike" class="w-full h-48 object-cover">
                        <div class="absolute top-3 left-3 bg-orange-500 text-white px-2 py-1 rounded-full text-xs font-semibold">
                            Giảm giá
                        </div>
                        <button class="heart-btn heart-active absolute top-3 right-3 w-8 h-8 bg-white rounded-full flex items-center justify-center">
                            <i class="fas fa-heart"></i>
                        </button>
                    </div>
                    <div class="p-4">
                        <h4 class="font-semibold text-gray-900 mb-2">Giày Nike Air Force 1</h4>
                        <p class="text-sm text-gray-600 mb-3">Size 42, màu trắng, tình trạng 85%</p>
                        <div class="flex justify-between items-center mb-3">
                            <span class="text-lg font-bold text-blue-600">1.800.000₫</span>
                            <span class="text-sm text-gray-500 line-through">2.500.000₫</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center text-sm text-gray-500">
                                <i class="fas fa-user mr-1"></i>
                                <span>Hoàng Văn E</span>
                            </div>
                            <div class="flex text-yellow-400 text-xs">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                            </div>
                        </div>
                        <div class="flex space-x-2 mt-3">
                            <button class="flex-1 bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700 transition-colors">
                                <i class="fas fa-shopping-cart mr-2"></i>Xem
                            </button>
                            <button class="bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600 transition-colors">
                                <i class="fas fa-heart-broken"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Empty State (hidden when there are favorites) -->
            <div class="text-center py-16 hidden">
                <i class="fas fa-heart text-gray-300 text-6xl mb-4"></i>
                <h3 class="text-xl font-semibold text-gray-600 mb-2">Chưa có sản phẩm yêu thích</h3>
                <p class="text-gray-500 mb-6">Hãy thêm sản phẩm vào danh sách yêu thích để xem lại sau</p>
                <button onclick="showPage('home')" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fas fa-search mr-2"></i>Khám phá sản phẩm
                </button>
            </div>
        </div>

        <!-- CHAT PAGE -->
        <div id="chat-page" class="page fade-in">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 h-96">
                <!-- Chat List -->
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    <div class="p-4 border-b bg-gray-50">
                        <h3 class="font-semibold">Tin nhắn</h3>
                        <div class="relative mt-3">
                            <input type="text" placeholder="Tìm kiếm cuộc trò chuyện..." class="w-full pl-8 pr-4 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <i class="fas fa-search absolute left-2.5 top-2.5 text-gray-400 text-sm"></i>
                        </div>
                    </div>
                    <div class="overflow-y-auto h-80">
                        <!-- Chat Item 1 -->
                        <div class="message-item p-4 border-b cursor-pointer bg-blue-50">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-blue-600 rounded-full flex items-center justify-center text-white font-semibold">
                                    A
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex justify-between items-start">
                                        <h4 class="font-medium text-gray-900 truncate">Nguyễn Văn A</h4>
                                        <span class="text-xs text-gray-500">10:32</span>
                                    </div>
                                    <p class="text-sm text-gray-600 truncate">Sách vẫn còn nhé, bạn có muốn xem...</p>
                                    <div class="flex items-center mt-1">
                                        <span class="w-2 h-2 bg-blue-600 rounded-full mr-2"></span>
                                        <span class="text-xs text-blue-600 font-medium">Tin nhắn mới</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Chat Item 2 -->
                        <div class="message-item p-4 border-b cursor-pointer hover:bg-gray-50">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-green-600 rounded-full flex items-center justify-center text-white font-semibold">
                                    B
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex justify-between items-start">
                                        <h4 class="font-medium text-gray-900 truncate">Trần Thị B</h4>
                                        <span class="text-xs text-gray-500">Hôm qua</span>
                                    </div>
                                    <p class="text-sm text-gray-600 truncate">Cảm ơn bạn, laptop rất tốt!</p>
                                </div>
                            </div>
                        </div>

                        <!-- Chat Item 3 -->
                        <div class="message-item p-4 border-b cursor-pointer hover:bg-gray-50">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-purple-600 rounded-full flex items-center justify-center text-white font-semibold">
                                    C
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex justify-between items-start">
                                        <h4 class="font-medium text-gray-900 truncate">Lê Văn C</h4>
                                        <span class="text-xs text-gray-500">2 ngày</span>
                                    </div>
                                    <p class="text-sm text-gray-600 truncate">Bạn có thể giao hàng không?</p>
                                </div>
                            </div>
                        </div>

                        <!-- Chat Item 4 -->
                        <div class="message-item p-4 border-b cursor-pointer hover:bg-gray-50">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-orange-600 rounded-full flex items-center justify-center text-white font-semibold">
                                    D
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex justify-between items-start">
                                        <h4 class="font-medium text-gray-900 truncate">Phạm Thị D</h4>
                                        <span class="text-xs text-gray-500">1 tuần</span>
                                    </div>
                                    <p class="text-sm text-gray-600 truncate">Máy tính còn bảo hành không?</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Chat Window -->
                <div class="lg:col-span-2 bg-white rounded-lg shadow-md flex flex-col">
                    <!-- Chat Header -->
                    <div class="p-4 border-b bg-gray-50 rounded-t-lg">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-blue-600 rounded-full flex items-center justify-center text-white font-semibold">
                                    A
                                </div>
                                <div>
                                    <h4 class="font-medium text-gray-900">Nguyễn Văn A</h4>
                                    <p class="text-sm text-green-600">
                                        <i class="fas fa-circle text-xs mr-1"></i>Đang hoạt động
                                    </p>
                                </div>
                            </div>
                            <div class="flex space-x-2">
                                <button class="p-2 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-100">
                                    <i class="fas fa-phone"></i>
                                </button>
                                <button class="p-2 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-100">
                                    <i class="fas fa-video"></i>
                                </button>
                                <button class="p-2 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-100">
                                    <i class="fas fa-ellipsis-v"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Chat Messages -->
                    <div class="flex-1 p-4 overflow-y-auto space-y-4">
                        <!-- Incoming Message -->
                        <div class="flex items-start space-x-2">
                            <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center text-white text-sm font-semibold">
                                A
                            </div>
                            <div class="flex-1">
                                <div class="bg-gray-100 rounded-lg p-3 max-w-xs">
                                    <p class="text-sm">Chào bạn, sách Toán cao cấp còn không?</p>
                                </div>
                                <p class="text-xs text-gray-500 mt-1">10:30 AM</p>
                            </div>
                        </div>

                        <!-- Outgoing Message -->
                        <div class="flex items-start space-x-2 justify-end">
                            <div class="flex-1 text-right">
                                <div class="bg-blue-600 text-white rounded-lg p-3 inline-block max-w-xs">
                                    <p class="text-sm">Chào bạn! Sách vẫn còn nhé, bạn có muốn xem thêm hình không?</p>
                                </div>
                                <p class="text-xs text-gray-500 mt-1">10:32 AM</p>
                            </div>
                        </div>

                        <!-- Incoming Message -->
                        <div class="flex items-start space-x-2">
                            <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center text-white text-sm font-semibold">
                                A
                            </div>
                            <div class="flex-1">
                                <div class="bg-gray-100 rounded-lg p-3 max-w-xs">
                                    <p class="text-sm">Có thể gửi thêm ảnh chi tiết được không? Và bạn có thể giảm giá không?</p>
                                </div>
                                <p class="text-xs text-gray-500 mt-1">10:35 AM</p>
                            </div>
                        </div>

                        <!-- Outgoing Message -->
                        <div class="flex items-start space-x-2 justify-end">
                            <div class="flex-1 text-right">
                                <div class="bg-blue-600 text-white rounded-lg p-3 inline-block max-w-xs">
                                    <p class="text-sm">Mình có thể giảm 10% cho bạn. Để mình gửi thêm ảnh nhé!</p>
                                </div>
                                <p class="text-xs text-gray-500 mt-1">10:37 AM</p>
                            </div>
                        </div>

                        <!-- Typing Indicator -->
                        <div class="flex items-start space-x-2">
                            <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center text-white text-sm font-semibold">
                                A
                            </div>
                            <div class="bg-gray-100 rounded-lg p-3">
                                <div class="flex space-x-1">
                                    <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce"></div>
                                    <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0.1s"></div>
                                    <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Chat Input -->
                    <div class="p-4 border-t">
                        <div class="flex items-center space-x-3">
                            <button class="p-2 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-100">
                                <i class="fas fa-paperclip"></i>
                            </button>
                            <button class="p-2 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-100">
                                <i class="fas fa-image"></i>
                            </button>
                            <div class="flex-1 relative">
                                <input 
                                    type="text" 
                                    placeholder="Nhập tin nhắn..."
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                >
                            </div>
                            <button class="bg-blue-600 text-white p-2 rounded-lg hover:bg-blue-700 transition-colors">
                                <i class="fas fa-paper-plane"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- PROFILE PAGE -->
        <div id="profile-page" class="page fade-in">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Profile Info -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <div class="text-center">
                            <div class="w-24 h-24 bg-blue-600 rounded-full flex items-center justify-center text-white text-3xl font-bold mx-auto mb-4">
                                SV
                            </div>
                            <h2 class="text-xl font-bold text-gray-900 mb-1">Sinh Viên ABC</h2>
                            <p class="text-gray-600 mb-2">Đại học XYZ - Khoa CNTT</p>
                            <p class="text-sm text-gray-500 mb-4">Thành viên từ tháng 3/2024</p>
                            
                            <!-- Rating -->
                            <div class="flex items-center justify-center mb-4">
                                <div class="flex text-yellow-400 mr-2">
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                </div>
                                <span class="text-gray-600">(4.8/5 - 24 đánh giá)</span>
                            </div>

                            <!-- Action Buttons -->
                            <div class="space-y-2">
                                <button class="w-full bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700 transition-colors">
                                    <i class="fas fa-edit mr-2"></i>Chỉnh sửa profile
                                </button>
                                <button class="w-full border border-gray-300 text-gray-700 py-2 rounded-lg hover:bg-gray-50 transition-colors">
                                    <i class="fas fa-cog mr-2"></i>Cài đặt
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Contact Info -->
                    <div class="bg-white rounded-lg shadow-md p-6 mt-6">
                        <h3 class="font-semibold mb-4">Thông tin liên hệ</h3>
                        <div class="space-y-3">
                            <div class="flex items-center text-sm">
                                <i class="fas fa-envelope text-gray-400 w-5"></i>
                                <span class="ml-3">student@university.edu.vn</span>
                            </div>
                            <div class="flex items-center text-sm">
                                <i class="fas fa-phone text-gray-400 w-5"></i>
                                <span class="ml-3">0123 456 789</span>
                            </div>
                            <div class="flex items-center text-sm">
                                <i class="fas fa-map-marker-alt text-gray-400 w-5"></i>
                                <span class="ml-3">Hà Nội, Việt Nam</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Profile Content -->
                <div class="lg:col-span-2">
                    <!-- Stats Cards -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                        <div class="profile-stat text-white p-6 rounded-xl">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h4 class="text-lg font-semibold mb-2">Đã bán</h4>
                                    <p class="text-3xl font-bold">15</p>
                                    <p class="text-sm opacity-80">Sản phẩm</p>
                                </div>
                                <i class="fas fa-check-circle text-4xl opacity-80"></i>
                            </div>
                        </div>
                        <div class="stats-card-2 text-white p-6 rounded-xl">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h4 class="text-lg font-semibold mb-2">Đang bán</h4>
                                    <p class="text-3xl font-bold">8</p>
                                    <p class="text-sm opacity-80">Sản phẩm</p>
                                </div>
                                <i class="fas fa-store text-4xl opacity-80"></i>
                            </div>
                        </div>
                        <div class="stats-card-3 text-white p-6 rounded-xl">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h4 class="text-lg font-semibold mb-2">Doanh thu</h4>
                                    <p class="text-3xl font-bold">25M</p>
                                    <p class="text-sm opacity-80">VNĐ</p>
                                </div>
                                <i class="fas fa-coins text-4xl opacity-80"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Tabs -->
                    <div class="bg-white rounded-lg shadow-md">
                        <div class="border-b">
                            <nav class="flex space-x-8 px-6">
                                <button onclick="showProfileTab('selling')" id="tab-selling" class="py-4 px-1 border-b-2 border-blue-600 font-medium text-sm text-blue-600">
                                    Đang bán (8)
                                </button>
                                <button onclick="showProfileTab('sold')" id="tab-sold" class="py-4 px-1 border-b-2 border-transparent font-medium text-sm text-gray-500 hover:text-gray-700">
                                    Đã bán (15)
                                </button>
                                <button onclick="showProfileTab('reviews')" id="tab-reviews" class="py-4 px-1 border-b-2 border-transparent font-medium text-sm text-gray-500 hover:text-gray-700">
                                    Đánh giá (24)
                                </button>
                            </nav>
                        </div>

                        <!-- Selling Tab -->
                        <div id="selling-tab" class="profile-tab p-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- Selling Product 1 -->
                                <div class="border rounded-lg p-4 hover:shadow-md transition-shadow">
                                    <div class="flex space-x-4">
                                        <img src="https://images.unsplash.com/photo-1544716278-ca5e3f4abd8c?w=100&h=100&fit=crop" alt="Sách" class="w-16 h-16 object-cover rounded">
                                        <div class="flex-1">
                                            <h4 class="font-medium text-gray-900 mb-1">Sách Toán Cao Cấp A1</h4>
                                            <p class="text-sm text-gray-600 mb-2">150.000₫</p>
                                            <div class="flex items-center justify-between">
                                                <span class="text-xs bg-green-100 text-green-800 px-2 py-1 rounded-full">Còn hàng</span>
                                                <div class="flex space-x-2">
                                                    <button class="text-blue-600 hover:text-blue-700 text-sm">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button class="text-red-600 hover:text-red-700 text-sm">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Selling Product 2 -->
                                <div class="border rounded-lg p-4 hover:shadow-md transition-shadow">
                                    <div class="flex space-x-4">
                                        <img src="https://images.unsplash.com/photo-1556821840-3a63f95609a7?w=100&h=100&fit=crop" alt="Áo hoodie" class="w-16 h-16 object-cover rounded">
                                        <div class="flex-1">
                                            <h4 class="font-medium text-gray-900 mb-1">Áo hoodie unisex</h4>
                                            <p class="text-sm text-gray-600 mb-2">250.000₫</p>
                                            <div class="flex items-center justify-between">
                                                <span class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded-full">Mới</span>
                                                <div class="flex space-x-2">
                                                    <button class="text-blue-600 hover:text-blue-700 text-sm">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button class="text-red-600 hover:text-red-700 text-sm">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Sold Tab -->
                        <div id="sold-tab" class="profile-tab p-6 hidden">
                            <div class="space-y-4">
                                <!-- Sold Product 1 -->
                                <div class="border rounded-lg p-4">
                                    <div class="flex space-x-4">
                                        <img src="https://images.unsplash.com/photo-1496181133206-80ce9b88a853?w=100&h=100&fit=crop" alt="Laptop" class="w-16 h-16 object-cover rounded">
                                        <div class="flex-1">
                                            <div class="flex justify-between items-start">
                                                <div>
                                                    <h4 class="font-medium text-gray-900 mb-1">Laptop Dell Inspiron 15</h4>
                                                    <p class="text-sm text-gray-600 mb-1">12.000.000₫</p>
                                                    <p class="text-xs text-gray-500">Bán cho: Trần Thị B</p>
                                                </div>
                                                <div class="text-right">
                                                    <span class="text-xs bg-green-100 text-green-800 px-2 py-1 rounded-full">Đã bán</span>
                                                    <p class="text-xs text-gray-500 mt-1">15/05/2024</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Sold Product 2 -->
                                <div class="border rounded-lg p-4">
                                    <div class="flex space-x-4">
                                        <img src="https://images.unsplash.com/photo-1481627834876-b7833e8f5570?w=100&h=100&fit=crop" alt="Sách" class="w-16 h-16 object-cover rounded">
                                        <div class="flex-1">
                                            <div class="flex justify-between items-start">
                                                <div>
                                                    <h4 class="font-medium text-gray-900 mb-1">Sách Tiếng Anh TOEIC</h4>
                                                    <p class="text-sm text-gray-600 mb-1">200.000₫</p>
                                                    <p class="text-xs text-gray-500">Bán cho: Vũ Thị F</p>
                                                </div>
                                                <div class="text-right">
                                                    <span class="text-xs bg-green-100 text-green-800 px-2 py-1 rounded-full">Đã bán</span>
                                                    <p class="text-xs text-gray-500 mt-1">10/05/2024</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Reviews Tab -->
                        <div id="reviews-tab" class="profile-tab p-6 hidden">
                            <div class="space-y-6">
                                <!-- Review 1 -->
                                <div class="border-b pb-6">
                                    <div class="flex items-start space-x-4">
                                        <div class="w-10 h-10 bg-green-600 rounded-full flex items-center justify-center text-white font-semibold">
                                            B
                                        </div>
                                        <div class="flex-1">
                                            <div class="flex items-center justify-between mb-2">
                                                <h4 class="font-medium text-gray-900">Trần Thị B</h4>
                                                <span class="text-sm text-gray-500">15/05/2024</span>
                                            </div>
                                            <div class="flex text-yellow-400 mb-2">
                                                <i class="fas fa-star"></i>
                                                <i class="fas fa-star"></i>
                                                <i class="fas fa-star"></i>
                                                <i class="fas fa-star"></i>
                                                <i class="fas fa-star"></i>
                                            </div>
                                            <p class="text-gray-700 mb-2">Laptop rất tốt, đúng như mô tả. Người bán nhiệt tình, giao hàng nhanh. Rất hài lòng!</p>
                                            <p class="text-sm text-gray-500">Sản phẩm: Laptop Dell Inspiron 15</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Review 2 -->
                                <div class="border-b pb-6">
                                    <div class="flex items-start space-x-4">
                                        <div class="w-10 h-10 bg-purple-600 rounded-full flex items-center justify-center text-white font-semibold">
                                            F
                                        </div>
                                        <div class="flex-1">
                                            <div class="flex items-center justify-between mb-2">
                                                <h4 class="font-medium text-gray-900">Vũ Thị F</h4>
                                                <span class="text-sm text-gray-500">10/05/2024</span>
                                            </div>
                                            <div class="flex text-yellow-400 mb-2">
                                                <i class="fas fa-star"></i>
                                                <i class="fas fa-star"></i>
                                                <i class="fas fa-star"></i>
                                                <i class="fas fa-star"></i>
                                                <i class="far fa-star"></i>
                                            </div>
                                            <p class="text-gray-700 mb-2">Sách còn mới, giá hợp lý. Giao dịch nhanh chóng và thuận tiện.</p>
                                            <p class="text-sm text-gray-500">Sản phẩm: Sách Tiếng Anh TOEIC</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Review 3 -->
                                <div class="border-b pb-6">
                                    <div class="flex items-start space-x-4">
                                        <div class="w-10 h-10 bg-orange-600 rounded-full flex items-center justify-center text-white font-semibold">
                                            G
                                        </div>
                                        <div class="flex-1">
                                            <div class="flex items-center justify-between mb-2">
                                                <h4 class="font-medium text-gray-900">Nguyễn Văn G</h4>
                                                <span class="text-sm text-gray-500">05/05/2024</span>
                                            </div>
                                            <div class="flex text-yellow-400 mb-2">
                                                <i class="fas fa-star"></i>
                                                <i class="fas fa-star"></i>
                                                <i class="fas fa-star"></i>
                                                <i class="fas fa-star"></i>
                                                <i class="fas fa-star"></i>
                                            </div>
                                            <p class="text-gray-700 mb-2">Người bán rất uy tín, sản phẩm chất lượng tốt. Sẽ tiếp tục ủng hộ!</p>
                                            <p class="text-sm text-gray-500">Sản phẩm: Máy tính Casio FX-580VN</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>