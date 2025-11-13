<script setup>
import { computed } from 'vue';

// Định nghĩa props, tương đương với @props trong Blade
const props = defineProps({
    product: {
        type: Object,
        required: true,
    },
    showFavoriteButton: {
        type: Boolean,
        default: true,
    },
});
const BASE_STORAGE_URL = import.meta.env.VITE_BASE_STORAGE_URL || '/storage/';

const getImageUrl = (imagePath) => {
    if (!imagePath) {
        return 'http://127.0.0.1:8000/storage/products/default-product.jpg';
    }
    const cleanedPath = imagePath.startsWith('/') ? imagePath.substring(1) : imagePath;
    return BASE_STORAGE_URL.endsWith('/')
        ? BASE_STORAGE_URL + cleanedPath
        : BASE_STORAGE_URL + '/' + cleanedPath;
};
// Chuyển đổi hàm định dạng số và logic định tuyến của Blade sang Vue

// const formattedOriginalPrice = computed(() => {
//     if (!props.product.original_price) return null;
//     return new Intl.NumberFormat('vi-VN', { 
//         style: 'currency', 
//         currency: 'VND' 
//     }).format(props.product.original_price);
// });

const formattedDate = computed(() => {
    if (props.product.created_at) {
        return new Date(props.product.created_at).toLocaleDateString('vi-VN');
    }
    return '';
});

// Giả định hàm chuyển trang (ví dụ: dùng Vue Router)
const navigateToProduct = () => {
    // Nếu dùng Vue Router:
    // router.push({ name: 'products.show', params: { id: props.product.id } });
    // Nếu dùng Inertia:
    // router.visit(route('products.show', props.product.id));
    console.log(`Chuyển đến trang chi tiết sản phẩm ID: ${props.product.id}`);
};

// Xử lý sự kiện thích sản phẩm
const toggleFavorite = (event) => {
    event.stopPropagation(); // Ngăn chặn sự kiện click lan truyền lên card
    console.log(`Thao tác YÊU THÍCH cho sản phẩm ID: ${props.product.id}`);
    // Thực hiện gọi API tại đây
};
</script>

<template>
    <div 
        class="bg-white rounded-xl shadow-sm overflow-hidden product-card cursor-pointer" 
        @click="navigateToProduct"
    >
        <div class="relative h-48 bg-gray-100">
            <img 
                :src="getImageUrl(product.product_image) || 'https://picsum.photos/300/200?random=17'" 
                :alt="product.title" 
                class="w-full h-full object-cover"
            />

            <div v-if="showFavoriteButton" class="absolute top-3 right-3 space-y-2">
                <form @submit.prevent="toggleFavorite" action="" method="POST">
                    <button 
                        type="submit" 
                        class="bg-white rounded-full w-8 h-8 flex items-center justify-center shadow-md hover:scale-110 transition-transform text-gray-400"
                        
                        >
                        <i class="fas fa-heart"></i>
                    </button>
                </form>
            </div>

            <!-- <div class="absolute bottom-3 left-3">
                <span 
                    class="condition-badge text-xs px-2 py-1 rounded-full font-medium"
                    :class="[
                        product.status === 'new' 
                            ? 'bg-blue-100 text-blue-700' 
                            : 'bg-gray-100 text-gray-600'
                    ]"
                >
                    {{ product.status === 'new' ? 'Như mới' : 'Đã qua sử dụng' }}
                </span>
            </div> -->
        </div>

        <div class="p-4">
            <h3 class="font-semibold text-gray-900 mb-2 line-clamp-2">
                {{ product.title }}
            </h3>
            
            <div class="flex items-center justify-between mb-3">
                <div>
                    <span class="text-xl font-bold text-blue-600">
                        {{ product.price }}
                    </span>
                    </div>
            </div>

            <div class="flex items-center justify-between text-sm text-gray-500">
                <div class="flex items-center">
                    <!-- <i class="fas fa-map-marker-alt mr-1"></i> -->
                    <!-- <span>{{ product.location }}</span> -->
                </div>
                <span>{{ product.created_date }}</span>
            </div>

            <div class="flex items-center mt-3 pt-3 border-t border-gray-100">
                <img 
                    :src="product.seller?.avatar || `https://ui-avatars.com/api/?name=${product.seller_name || 'Seller'}`" 
                    :alt="product.seller?.name" 
                    class="w-6 h-6 rounded-full mr-2"
                />
                <span class="text-sm text-gray-700 flex-1">{{ product.seller_name }}</span>
                <div class="flex items-center">
                    <fa :icon="['fas', 'star']" class="text-yellow-400 text-xs mr-1" />
                    <span class="text-xs text-gray-600">4</span>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
/* Có thể thêm các style đặc trưng cho component tại đây nếu cần */
.line-clamp-2 {
    overflow: hidden;
    display: -webkit-box;
    -webkit-box-orient: vertical;
    -webkit-line-clamp: 2;
}
</style>