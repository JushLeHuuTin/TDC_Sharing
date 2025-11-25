<script setup>
import { ref, computed, watch, onMounted, reactive } from 'vue';
import { useRouter, useRoute, RouterLink } from 'vue-router';
import ProductCard from '@/components/Products/ProductCard.vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import { useCategoryStore } from '@/stores/categoryStore';
import { storeToRefs } from 'pinia';
import BasePagination from '@/components/BasePagination.vue';

import CategoryFilter from '@/components/CategoryFilter.vue';
const categoryStore = useCategoryStore();

const { flattenedCategories, isLoading, products, pagination } = storeToRefs(categoryStore);
const router = useRouter();
const route = useRoute();
// --- STATE UI/FILTERING ---
const sortBy = ref('newest');
const currentView = ref('grid');
// const currentStatus = ref('active'); 


const slug = computed(() => route.params.categorySlug || null);
onMounted(async () => {
    await categoryStore.fetchCategories();
    // await categoryStore.fetchProductsBySlug(slug.value);
    console.log(slug.value);
    setTimeout(() => {
        isLoading.value = false;
    }, 1500);
});
// // 1. Tên Danh mục cho Breadcrumb
const currentCategoryName = computed(() => {
    const category = flattenedCategories.value.find(c => c.slug === slug.value);
    return category ? category.name.replace(/—\s*/g, '') : 'Tất cả sản phẩm';
});
const truncateText = (text, maxLength) => {
    if (text.length <= maxLength) return text;
    let truncated = text.slice(0, maxLength);
    const lastSpace = truncated.lastIndexOf(' ');
    return lastSpace > 0 ? truncated.substring(0, lastSpace) + '...' : truncated + '...';
};

const currentSearch = computed(() => {
    return truncateText(categoryStore.filters.search || '', 20);
});
const cleanPriceForInput = (formattedPrice) => {
    if (!formattedPrice) return null;
    let str = String(formattedPrice);
    let cleanedString = str.replace(/[^\d]/g, '');
    if (cleanedString === '') {
        return null;
    }
    return parseInt(cleanedString, 10);
};
// // 2. Logic Lọc và Sắp xếp chính
const filteredProducts = computed(() => {
    const list = Array.isArray(products.value) ? [...products.value] : [];
    const sorters = {
        'oldest': (a, b) => new Date(a.created_date) - new Date(b.created_date),
        'price_high': (a, b) => cleanPriceForInput(b.price) - cleanPriceForInput(a.price),
        'price_low': (a, b) => cleanPriceForInput(a.price) - cleanPriceForInput(b.price),
        'views': (a, b) => b.views - a.views,
        'newest': (a, b) => new Date(b.created_date) - new Date(a.created_date),
    };
    return list.slice().sort(sorters[sortBy.value] || sorters['newest']);
});
const handleApplyFilters = async () => {
    try {
        isLoading.value = true;
        await categoryStore.fetchProductsBySlug(slug.value, 1);
    } catch (error) {
        console.error('Lỗi khi áp dụng bộ lọc:', error);
    } finally {
        isLoading.value = false;
    }
};
const resetFilters = async () => {
    // 1. Reset các bộ lọc trong Store
    categoryStore.filters.search = '';
    categoryStore.filters.priceRange = null; // Hoặc giá trị mặc định của bạn
    categoryStore.filters.categories = [];
    categoryStore.filters.conditions = [];
    categoryStore.filters.location = '';
    categoryStore.filters.negotiable = false;
    categoryStore.filters.hasImages = false;
    categoryStore.filters.verified = false;
    
    // 2. Reset các trạng thái UI/Component
    sortBy.value = 'newest';
    // Đảm bảo reset input tìm kiếm

    // 3. Kích hoạt tải lại dữ liệu
    await categoryStore.fetchProductsBySlug(slug.value, 1);
};
const backSlug = async () => {
    categoryStore.filters.search='';
    categoryStore.fetchProductsBySlug(slug.value)
};
const handlePageChange = (page) => {
    categoryStore.fetchProductsBySlug(slug.value, page);
};
// Theo dõi categorySlug VÀ Route Name để bắt được khi chuyển từ trang khác sang /products
watch(() => [route.params.categorySlug, route.name], ([newSlug, newName]) => {
    if (newName === 'products.index' || newName === 'category.products') {
        categoryStore.fetchProductsBySlug(newSlug || null,1);
    }
}, { immediate: true });
</script>

<template>
    <AppLayout :title="`Sản phẩm: ${currentCategoryName}`">
        <div class="flex flex-col lg:flex-row gap-6 p-4 md:p-6">

            <div class="lg:w-1/4">
                <CategoryFilter 
                    @handleFilterChange="handleApplyFilters" />
            </div>

            <div class="lg:w-3/4">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6">

                    <nav class="flex mb-6 sm:mb-0" aria-label="Breadcrumb">
                        <ol class="inline-flex items-center space-x-1 md:space-x-3">
                            <li class="inline-flex items-center">
                                <RouterLink to="/" class="text-gray-700 hover:text-blue-600">
                                    <fa :icon="['fas', 'home']" class="mr-2" />Trang chủ
                                </RouterLink>
                            </li>
                            <li>
                                <div class="flex items-center" @click="backSlug" style="cursor: pointer;">
                                    <fa :icon="['fas', 'chevron-right']" class="text-gray-400 mx-2 fa-xs" />
                                    <span class="text-gray-900 font-medium">{{ currentCategoryName }}</span>
                                </div>
                            </li>
                            <li v-if="currentSearch">
                                <div class="flex items-center">
                                    <fa :icon="['fas', 'chevron-right']" class="text-gray-400 mx-2 fa-xs" />
                                    <span class="text-gray-900 font-medium">{{ currentSearch }}</span>
                                </div>
                            </li>
                        </ol>
                    </nav>

                    <div class="flex items-center space-x-4 mt-4 sm:mt-0">
                        <span class="text-sm text-gray-600" style="white-space: nowrap;">Sắp xếp:</span>
                        <select class="form-select" v-model="sortBy">
                            <option value="newest">Mới nhất</option>
                            <option value="oldest">Cũ nhất</option>
                            <option value="price_high">Giá cao nhất</option>
                            <option value="price_low">Giá thấp nhất</option>
                            <option value="views">Nhiều lượt xem</option>
                        </select>

                        <div class="flex border border-gray-300 rounded-md">
                            <button
                                :class="['px-3 py-2 transition-colors rounded-l-md', currentView === 'grid' ? 'bg-blue-600 text-white' : 'text-gray-600 hover:bg-gray-50']"
                                @click="currentView = 'grid'">
                                <fa :icon="['fas', 'th-large']" />
                            </button>
                            <button
                                :class="['px-3 py-2 transition-colors rounded-r-md', currentView === 'list' ? 'bg-blue-600 text-white' : 'text-gray-600 hover:bg-gray-50']"
                                @click="currentView = 'list'">
                                <fa :icon="['fas', 'list']" />
                            </button>
                        </div>
                    </div>
                </div>

                <div
                    :class="['grid gap-6 mb-8', currentView === 'grid' ? 'grid-cols-1 md:grid-cols-2 xl:grid-cols-4' : 'grid-cols-1']">

                    <template v-if="isLoading">
                        <div v-for="i in 4" :key="i" class="col-span-1">
                            <div class="bg-gray-200 h-64 rounded-lg animate-pulse shadow-md"></div>
                        </div>
                    </template>

                    <template v-else-if="filteredProducts.length > 0">
                        <ProductCard v-for="product in filteredProducts" :key="product.id" :product="product"
                            :view-mode="currentView" />

                    </template>
                    <div v-else class="col-span-full text-center py-12 bg-gray-50 rounded-lg shadow-md">
                        <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <fa :icon="['fas', 'box-open']" class="text-gray-400 text-2xl" />
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Không tìm thấy sản phẩm</h3>
                        <p class="text-gray-600 mb-4">Thử thay đổi từ khóa tìm kiếm hoặc bộ lọc</p>
                        <button @click="resetFilters"
                            class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                            <fa :icon="['fas', 'undo']" class="mr-1" />Đặt lại bộ lọc
                        </button>
                    </div>
                </div>
                <div v-if="filteredProducts.length > 0" class="flex justify-center mt-10">
                    <BasePagination :pagination="pagination" :on-page-change="handlePageChange" />
                </div>
            </div>
        </div>
    </AppLayout>
</template>