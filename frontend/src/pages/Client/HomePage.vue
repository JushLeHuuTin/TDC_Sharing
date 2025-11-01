<script setup>
import { computed, ref } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue'; 
import ProductCard from '@/components/Products/ProductCard.vue'; 

// --- DỮ LIỆU TỪ BACKEND (PROPS) ---
const props = defineProps({
    // Danh sách sản phẩm nổi bật
    featuredProducts: {
        type: Array,
        default: () => []
    },
    // Danh sách sản phẩm mới nhất
    recentProducts: {
        type: Array,
        default: () => []
    },
    // Dữ liệu thống kê (Stats)
    stats: {
        type: Object,
        default: () => ({ products: 1234, students: 567, transactions: 89, universities: 12 })
    },
    // Trạng thái đăng nhập (được truyền từ server)
    isLoggedIn: {
        type: Boolean,
        default: false
    }
});

// --- DỮ LIỆU GIẢ/MOCK DỮ LIỆU CHO CATEGORIES (Giữ lại logic Blade) ---
// Trong ứng dụng thực tế, categories cũng sẽ được truyền qua props.
const categories = ref([
    { name: 'Điện tử', icon: 'fas fa-laptop', color: 'bg-blue-500', count: 234 },
    { name: 'Sách vở', icon: 'fas fa-book', color: 'bg-green-500', count: 189 },
    { name: 'Thời trang', icon: 'fas fa-tshirt', color: 'bg-pink-500', count: 156 },
    { name: 'Đồ dùng', icon: 'fas fa-home', color: 'bg-purple-500', count: 98 },
    { name: 'Xe cộ', icon: 'fas fa-bicycle', color: 'bg-orange-500', count: 67 },
    { name: 'Khác', icon: 'fas fa-ellipsis-h', color: 'bg-gray-500', count: 45 }
]);

// --- DỮ LIỆU GIẢ/MOCK DỮ LIỆU CHO TESTIMONIALS ---
const testimonials = ref([
    { name: 'Nguyễn Minh Anh', university: 'ĐH Khoa học Tự nhiên', avatar: 'https://via.placeholder.com/60', rating: 5, comment: 'Tuyệt vời! Tôi đã bán được laptop cũ và mua được máy tính mới với giá rất hợp lý.' },
    { name: 'Trần Văn Bình', university: 'ĐH Bách khoa', avatar: 'https://via.placeholder.com/60', rating: 5, comment: 'Giao diện thân thiện, dễ sử dụng. Đã tìm được nhiều sách giáo khoa với giá sinh viên.' },
    { name: 'Lê Thị Cẩm', university: 'ĐH Kinh tế', avatar: 'https://via.placeholder.com/60', rating: 5, comment: 'Cộng đồng sinh viên rất tích cực. Giao dịch nhanh chóng và an toàn.' }
]);


// --- XỬ LÝ ĐỊNH TUYẾN (NAVIGATIONS) ---
// Sử dụng các hàm để mô phỏng hành vi của route() trong Blade
const getRoute = (name) => {
    const routes = {
        'products.create': '/products/create',
        'products.index': '/products',
        'products.register': '/register', 
        'products.login': '/login',      
    };
    return routes[name] || '#';
};

// Hàm để định dạng số (tương tự ProductCard, nhưng dùng cho Stats)
const formatNumber = (number) => {
    return new Intl.NumberFormat('vi-VN').format(number);
};
</script>

<template>
    <!-- Bọc toàn bộ nội dung trong Layout Component -->
    <AppLayout title="StudentMarket - Chợ Sinh Viên">
        
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
                    <!-- Logic @auth / @else được thay bằng v-if / v-else và sử dụng props.isLoggedIn -->
                    <template v-if="!isLoggedIn">
                        <a :href="getRoute('products.register')" class="bg-white text-blue-600 px-8 py-3 rounded-lg font-semibold hover:bg-gray-100 transition-colors">
                           <i class="fas fa-user-plus mr-2"></i>Đăng ký ngay
                       </a>
                       <a :href="getRoute('products.login')" class="border-2 border-white text-white px-8 py-3 rounded-lg font-semibold hover:bg-white hover:text-blue-600 transition-colors">
                           <i class="fas fa-sign-in-alt mr-2"></i>Đăng nhập
                       </a>
                    </template>
                    <template v-else>
                        <a :href="getRoute('products.create')" class="bg-white text-blue-600 px-8 py-3 rounded-lg font-semibold hover:bg-gray-100 transition-colors">
                            <i class="fas fa-plus mr-2"></i>Đăng bán ngay
                        </a>
                        <a :href="getRoute('products.index')" class="border-2 border-white text-white px-8 py-3 rounded-lg font-semibold hover:bg-white hover:text-blue-600 transition-colors">
                            <i class="fas fa-search mr-2"></i>Khám phá sản phẩm
                        </a>
                    </template>
                </div>
            </div>
        </section>

        <!-- Stats Section -->
        <section class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
            <div class="bg-white rounded-lg p-6 text-center shadow-sm">
                <div class="text-3xl font-bold text-blue-600 mb-2">{{ formatNumber(props.stats.products) }}</div>
                <div class="text-gray-600">Sản phẩm</div>
            </div>
            <div class="bg-white rounded-lg p-6 text-center shadow-sm">
                <div class="text-3xl font-bold text-green-600 mb-2">{{ formatNumber(props.stats.students) }}</div>
                <div class="text-gray-600">Sinh viên</div>
            </div>
            <div class="bg-white rounded-lg p-6 text-center shadow-sm">
                <div class="text-3xl font-bold text-purple-600 mb-2">{{ formatNumber(props.stats.transactions) }}</div>
                <div class="text-gray-600">Giao dịch</div>
            </div>
            <div class="bg-white rounded-lg p-6 text-center shadow-sm">
                <div class="text-3xl font-bold text-orange-600 mb-2">{{ formatNumber(props.stats.universities) }}</div>
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
                <!-- Thay thế @foreach bằng v-for -->
                <a 
                    v-for="category in categories" 
                    :key="category.name" 
                    href="#" 
                    class="group bg-white rounded-lg p-4 shadow-sm hover:shadow-md transition-all duration-300 text-center"
                >
                    <div 
                        :class="category.color" 
                        class="w-12 h-12 rounded-lg flex items-center justify-center mx-auto mb-3 group-hover:scale-110 transition-transform"
                    >
                        <i :class="category.icon" class="text-white text-lg"></i>
                    </div>
                    <h3 class="font-semibold text-gray-900 mb-1">{{ category.name }}</h3>
                    <p class="text-sm text-gray-500">{{ category.count }} sản phẩm</p>
                </a>
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
                <!-- Thay thế @foreach và @include bằng v-for và Component ProductCard -->
                <ProductCard
                    v-for="product in props.featuredProducts"
                    :key="product.id"
                    :product="product"
                />
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
                <!-- Thay thế @foreach và @include bằng v-for và Component ProductCard -->
                <ProductCard
                    v-for="product in props.recentProducts"
                    :key="product.id"
                    :product="product"
                />
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
                <!-- Thay thế @foreach bằng v-for -->
                <div v-for="testimonial in testimonials" :key="testimonial.name" class="bg-white rounded-lg p-6 shadow-sm">
                    <div class="flex items-center mb-4">
                        <img :src="testimonial.avatar" :alt="testimonial.name" class="w-12 h-12 rounded-full mr-4">
                        <div>
                            <h4 class="font-semibold text-gray-900">{{ testimonial.name }}</h4>
                            <p class="text-sm text-gray-600">{{ testimonial.university }}</p>
                        </div>
                    </div>
                    
                    <div class="flex mb-3">
                        <i 
                            v-for="i in 5" 
                            :key="i"
                            class="fas fa-star" 
                            :class="i <= testimonial.rating ? 'text-yellow-400' : 'text-gray-300'"
                        ></i>
                    </div>
                    
                    <p class="text-gray-700 italic">"{{ testimonial.comment }}"</p>
                </div>
            </div>
        </section>

    </AppLayout>
</template>

<style scoped>
</style>
