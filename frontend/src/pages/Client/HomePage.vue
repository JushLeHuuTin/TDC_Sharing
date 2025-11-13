<script setup>
import { computed, ref } from 'vue';

import AppLayout from '@/Layouts/AppLayout.vue';
import ProductCard from '@/components/Products/ProductCard.vue';

// 🎯 Import store
import { useCategoryStore } from '@/stores/categoryStore';
import { useProductStore } from '@/stores/productStore';
import { onMounted } from 'vue';
import { defineProps } from 'vue';
import { useAuthStore } from '@/stores/auth';
import { storeToRefs } from 'pinia';

import tdc_campus from '@/assets/tdc_campus.png';

// --- DỮ LIỆU TỪ BACKEND (PROPS) ---
const props = defineProps({
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

});
// --- QUẢN LÝ STATE TỪ PINIA ---
const authStore = useAuthStore();
const categoryStore = useCategoryStore();
const productStore = useProductStore();

const { user, isLoggedIn, isAdmin } = storeToRefs(authStore);
const { topFiveCategories, isLoading, error } = storeToRefs(categoryStore);
const { featuredProducts, isLoadingFeatured, featuredError } = storeToRefs(productStore);
console.log(featuredProducts);
// --- DỮ LIỆU GIẢ/MOCK DỮ LIỆU CHO TESTIMONIALS ---
const testimonials = ref([
    { name: 'Nguyễn Minh Anh', university: 'ĐH Khoa học Tự nhiên', avatar: 'https://via.placeholder.com/60', rating: 5, comment: 'Tuyệt vời! Tôi đã bán được laptop cũ và mua được máy tính mới với giá rất hợp lý.' },
    { name: 'Trần Văn Bình', university: 'ĐH Bách khoa', avatar: 'https://via.placeholder.com/60', rating: 5, comment: 'Giao diện thân thiện, dễ sử dụng. Đã tìm được nhiều sách giáo khoa với giá sinh viên.' },
    { name: 'Lê Thị Cẩm', university: 'ĐH Kinh tế', avatar: 'https://via.placeholder.com/60', rating: 5, comment: 'Cộng đồng sinh viên rất tích cực. Giao dịch nhanh chóng và an toàn.' }
]);

onMounted(() => {
    categoryStore.fetchCategories(false);
    productStore.fetchFeaturedProducts();
});

// Hàm để định dạng số (tương tự ProductCard, nhưng dùng cho Stats)
const formatNumber = (number) => {
    return new Intl.NumberFormat('vi-VN').format(number);
};
</script>

<template>
    <!-- Bọc toàn bộ nội dung trong Layout Component -->
    <AppLayout :user="user" title="StudentMarket - Chợ Sinh Viên">

        <!-- Hero Section -->
        <section :style="{
            backgroundImage: `linear-gradient(rgba(0,0,80,0.4), rgba(0,0,120,0.5)), url(${tdc_campus})`,
            backgroundSize: 'cover',
            backgroundPosition: 'bottom',
        }" class="text-white rounded-2xl p-8 mb-8 min-h-[370px] d-flex align-items-center">
            <div class="max-w-4xl mx-auto text-center">
                <h1 class="text-4xl font-bold mb-4 drop-shadow-[0_2px_4px_rgba(0,0,0,0.3)]">
                    Chào mừng đến với <span class="text-yellow-400">TDC_Sharing</span>
                </h1>
                <p class="text-xl mb-3 opacity-90">
                    Cộng đồng sinh viên TDC học tập, chia sẻ và phát triển cùng nhau!
                </p>
                <div class="flex flex-col sm:flex-row gap-3 justify-center">
                    <!-- Logic @auth / @else được thay bằng v-if / v-else và sử dụng props.isLoggedIn -->
                    <template v-if="!isLoggedIn">
                        <router-link to="/register"
                            class="bg-white text-blue-300 px-8 py-3 rounded-lg font-semibold hover:bg-gray-100 transition-colors">
                            <fa :icon="['fas', 'user-plus']" class="mr-2" />Đăng ký ngay
                        </router-link>
                        <router-link to="/login"
                            class="border-2 border-white text-white px-8 py-3 rounded-lg font-semibold hover:bg-gray-100 hover:text-blue-600 transition-colors">
                            <fa :icon="['fas', 'sign-in-alt']" class="mr-2" />Đăng nhập
                        </router-link>
                    </template>
                    <template v-else>
                        <router-link to="/products/create" style="background-color: rgb(102 126 234 / 87%)"
                            class=" text-white font-semibold px-8 py-3  rounded-lg hover:bg-blue-700 transition">
                            <fa :icon="['fas', 'plus']" class="mr-2" />Đăng chia sẻ tài nguyên
                        </router-link>
                        <!-- <a :href="getRoute('products.create')"
                        class=" text-white font-semibold px-8 py-3  rounded-lg hover:bg-blue-700 transition">
                            <fa :icon="['fas', 'plus']" class="mr-2" />Đăng chia sẻ tài nguyên
                        </a> -->
                        <router-link to="/sanpham" 
                        class="border-2 border-white text-white px-8 py-3 rounded-lg font-semibold  hover:text-blue-600 transition-colors">
                            <fa :icon="['fas', 'plus']" class="mr-2" />Khám phá hoạt động sinh viên
                        </router-link>
                        <!-- <a :href="getRoute('products.index')"
                            class="border-2 border-white text-white px-8 py-3 rounded-lg font-semibold  hover:text-blue-600 transition-colors">
                            <fa :icon="['fas', 'search']" class="mr-2" />Khám phá hoạt động sinh viên
                        </a> -->
                    </template>
                </div>
            </div>
        </section>

        <!-- Stats Section -->
        <section class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-8">
            <div class="bg-white rounded-lg p-6 text-center shadow-sm">
                <div class="text-3xl font-bold text-blue-600 mb-2">{{ formatNumber(props.stats.products) }}</div>
                <div class="text-gray-600">Tài nguyên học tập</div>
            </div>
            <div class="bg-white rounded-lg p-6 text-center shadow-sm">
                <div class="text-3xl font-bold text-green-600 mb-2">{{ formatNumber(props.stats.students) }}</div>
                <div class="text-gray-600">Thành viên</div>
            </div>
            <div class="bg-white rounded-lg p-6 text-center shadow-sm">
                <div class="text-3xl font-bold text-purple-600 mb-2">{{ formatNumber(props.stats.transactions) }}</div>
                <div class="text-gray-600">Hoạt động</div>
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
                    Xem tất cả
                    <fa :icon="['fas', 'arrow-right']" class="ml-1" />
                </a>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-4">
                <router-link v-for="category in topFiveCategories" :to="`/danhmuc/${category.slug}`" :key="category.name" href="#"
                    class="group bg-white rounded-lg p-4 shadow-sm hover:shadow-md transition-all duration-300 text-center">
                    <div :style="{ 'background-color': category.color }"
                        class="w-12 h-12 rounded-lg flex items-center justify-center mx-auto mb-3 group-hover:scale-110 transition-transform">
                        <fa :icon="category.icon" class="text-white text-lg" />
                    </div>
                    <h3 class="font-semibold text-gray-900 mb-1">{{ category.name }}</h3>
                    <p class="text-sm text-gray-500">{{ category.count }} sản phẩm</p>
                </router-link>
            </div>
        </section>

        <!-- Featured Products -->
        <section class="mb-8">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-2xl font-bold text-gray-900">Sản phẩm nổi bật</h2>
                <a href="#" class="text-blue-600 hover:text-blue-800 font-medium">
                    Xem tất cả
                    <fa :icon="['fas', 'arrow-right']" class="ml-1" />
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Thay thế @foreach và @include bằng v-for và Component ProductCard -->
                <ProductCard v-for="product in featuredProducts" :key="product.id" :product="product" />
            </div>
        </section>

        <!-- Recent Products -->
        <section class="mb-8">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-2xl font-bold text-gray-900">Sản phẩm mới nhất</h2>
                <a href="#" class="text-blue-600 hover:text-blue-800 font-medium">
                    Xem tất cả
                    <fa :icon="['fas', 'arrow-right']" class="ml-1" />
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Thay thế @foreach và @include bằng v-for và Component ProductCard -->
                <ProductCard v-for="product in recentProducts" :key="product.id" :product="product" />
            </div>
        </section>

        <!-- How It Works -->
        <section class="bg-white rounded-2xl p-8 mb-8">
            <div class="text-center mb-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">Cách thức hoạt động</h2>
                <p class="text-gray-600 max-w-2xl mx-auto text-xl">
                    Chỉ với 3 bước đơn giản, bạn có thể mua bán dễ dàng trên StudentMarket
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="text-center">
                    <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <fa :icon="['fas', 'user-plus']" class="text-blue-600 text-2xl" />
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">1. Đăng ký tài khoản</h3>
                    <p class="text-gray-600">Tạo tài khoản miễn phí với email sinh viên để bắt đầu</p>
                </div>

                <div class="text-center">
                    <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <fa :icon="['fas', 'camera']" class="text-green-600 text-2xl" />
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">2. Đăng sản phẩm</h3>
                    <p class="text-gray-600">Chụp ảnh, mô tả sản phẩm và đăng bán trong vài phút</p>
                </div>

                <div class="text-center">
                    <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <fa :icon="['fas', 'handshake']" class="text-purple-600 text-2xl" />
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
                <div v-for="testimonial in testimonials" :key="testimonial.name"
                    class="bg-white rounded-lg p-6 shadow-sm">
                    <div class="flex items-center mb-4">
                        <img :src="testimonial.avatar" :alt="testimonial.name" class="w-12 h-12 rounded-full mr-4">
                        <div>
                            <h4 class="font-semibold text-gray-900">{{ testimonial.name }}</h4>
                            <p class="text-sm text-gray-600">{{ testimonial.university }}</p>
                        </div>
                    </div>

                    <div class="flex mb-3">
                        <i v-for="i in 5" :key="i" class="fas fa-star"
                            :class="i <= testimonial.rating ? 'text-yellow-400' : 'text-gray-300'"></i>
                    </div>

                    <p class="text-gray-700 italic">"{{ testimonial.comment }}"</p>
                </div>
            </div>
        </section>

    </AppLayout>
</template>

<style scoped>
.hover-card:hover {
    transform: translateY(-5px);
    transition: all 0.2s;
    box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
}
</style>
