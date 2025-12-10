<script setup>
import { useCategoryStore } from '@/stores/categoryStore';
import { ref, watch } from 'vue';
import { useRoute, useRouter } from 'vue-router';
const route = useRoute();
const router = useRouter();

const CategoryStore = useCategoryStore();
// --- STATE ---
const searchQuery = ref(''); // Lưu trữ nội dung input (thay thế value="{{ request('q') }}")
const suggestions = ref([]); // Danh sách gợi ý tìm kiếm
const showSuggestions = ref(false); // Trạng thái hiển thị/ẩn thanh gợi ý
let searchTimeout = null; // Biến để xử lý Debouncing
const localSearch = ref('');
import { getCurrentInstance } from 'vue';
const instance = getCurrentInstance();
const $toast = instance.appContext.config.globalProperties.$toast;
// --- METHODS ---

// Hàm xử lý Debounce cho input
const handleInput = () => {
    // Xóa timeout cũ nếu có
    if (searchTimeout) {
        clearTimeout(searchTimeout);
    }
    // Thiết lập timeout mới sau 300ms
    searchTimeout = setTimeout(() => {
        // fetchSuggestions(searchQuery.value.trim());
    }, 300);
};

// Hàm xử lý khi người dùng nhấn nút Tìm hoặc Enter
const handleSearch = () => {
    const keyword = localSearch.value ? localSearch.value.trim() : '';

// Gán vào Store
CategoryStore.filters.search = keyword;
    const slug = route.params.categorySlug || null;
    CategoryStore.fetchProductsBySlug(slug);
    // if(localSearch.value.length>150){
    //     $toast.error('vui long nhap it hon 150 ky tu');
    // }
    router.push({ name: 'products.index' });

};

// Hàm xử lý khi click ra ngoài (để ẩn suggestions)
const hideSuggestions = () => {
    // Chỉ ẩn suggestions, không xóa query
    showSuggestions.value = false;
};
const resetSearch = () => {
    localSearch.value = '';
};
</script>

<template>
    <!-- Gói trong div thay vì form nếu dùng Vue Router để quản lý định tuyến -->
    <div v-click-away="hideSuggestions" class="relative">
        <div class="relative">
            <!-- Sử dụng v-model để liên kết với searchQuery -->
            <input 
                type="text" 
                :maxlength="150"
                v-model="localSearch"
                @input="handleInput"
                @keyup.enter="handleSearch" 
                placeholder="Tìm kiếm sản phẩm..." 
                class="w-full pl-10 pr-16 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                autocomplete="off"
            >
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <fa :icon="['fas', 'search']" class="text-gray-400" />
            </div>
            <!-- Sử dụng @click.prevent="handleSubmit" để ngăn form submit mặc định -->
            <button @click.prevent="handleSearch" type="submit" class="absolute inset-y-0 right-0 pr-1 flex items-center">
                <span class="bg-blue-600 text-white px-3 py-1 rounded-lg text-sm hover:bg-blue-700">
                    Tìm
                </span>
            </button>
        </div>
        
        <!-- Search Suggestions -->
        <!-- Logic hiển thị/ẩn thay thế cho class hidden/remove('hidden') của JS -->
        <div v-show="showSuggestions" class="absolute top-full left-0 right-0 bg-white border border-gray-200 rounded-lg shadow-lg mt-1 z-50">
            <!-- Dynamic content will be loaded here using v-for -->
            <template v-if="suggestions.length > 0">
                <a 
                    v-for="item in suggestions"
                    :key="item.id"
                    href="#" 
                    class=" px-4 py-2 hover:bg-gray-50 flex items-center space-x-3"
                >
                    <img :src="item.image_url" :alt="item.title" class="w-10 h-10 object-cover rounded">
                    <div>
                        <p class="text-sm font-medium text-gray-900">{{ item.title }}</p>
                        <p class="text-xs text-gray-500">{{ item.price }}₫</p>
                    </div>
                </a>
            </template>
            <div v-else class="px-4 py-2 text-sm text-gray-500">
                Không tìm thấy gợi ý nào.
            </div>
        </div>
    </div>
</template>

<style scoped>
/* Không cần style bổ sung nếu chỉ dùng Tailwind CSS */
</style>