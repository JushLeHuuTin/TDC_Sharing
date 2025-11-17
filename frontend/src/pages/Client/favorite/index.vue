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
import { useFavoriteStore } from "@/stores/favoriteStore";

const favoriteStore = useFavoriteStore();
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
    favoriteStore.fetchFavorites();
});

// Hàm để định dạng số (tương tự ProductCard, nhưng dùng cho Stats)
const formatNumber = (number) => {
    return new Intl.NumberFormat('vi-VN').format(number);
};
</script>

<template>
    <!-- Bọc toàn bộ nội dung trong Layout Component -->
    <AppLayout :user="user" title="StudentMarket - Chợ Sinh Viên">
        <section class="mb-8">
            <div class=" mb-6">
                <h3 class="text-xl font-bold text-gray-900">
                    <fa :icon="['fas', 'heart']" class="text-lg" style="color:red"/> Sản phẩm yêu thích
                </h3>
                <span>
                    Tìm thấy {{ favoriteStore.totalFavorites }} sản phẩm yêu thích
                </span>
            </div>

            <!-- Loading -->
            <div v-if="favoriteStore.isLoading" class="text-gray-500">
                Đang tải...
            </div>

            <!-- Nếu không có sản phẩm yêu thích -->
            <div v-else-if="favoriteStore.favorites.length === 0" class="text-gray-600">
                Bạn chưa thích sản phẩm nào.
            </div>

            <!-- Danh sách yêu thích -->
            <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <ProductCard v-for="product in favoriteStore.favorites" :key="product.id" :product="product" />
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
