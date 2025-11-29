<script setup>
import { ref, computed, onMounted, watch } from 'vue';
import { storeToRefs } from 'pinia';
import { useWishlistStore } from '@/stores/wishlistStore'; // Import Wishlist Store
import AppLayout from '@/Layouts/AppLayout.vue';
import ProductCard from '@/components/Products/ProductCard.vue';
import BasePagination from '@/components/BasePagination.vue';
import { useRouter } from 'vue-router';

const wishlistStore = useWishlistStore();
const { products, isLoading, pagination, error } = storeToRefs(wishlistStore);
const router = useRouter();

// --- STATE UI ---
const currentView = ref('grid'); 
const sortBy = ref('newest'); // Vẫn giữ sorting local nếu muốn

onMounted(() => {
    // Tải danh sách yêu thích khi component được mount
    wishlistStore.fetchFavorites(1);
});

// Hàm xử lý khi chuyển trang
const handlePageChange = (page) => {
    wishlistStore.fetchFavorites(page);
};

// Hàm xử lý khi xóa một sản phẩm khỏi wishlist (để cập nhật giao diện)
// Có thể truyền ID sản phẩm vào và chạy lại fetchFavorites hoặc xử lý local
const handleItemRemoved = () => {
    // Sau khi xóa, tải lại trang hiện tại để đồng bộ
    wishlistStore.fetchFavorites(pagination.value.current_page);
};

// 💡 Sắp xếp Local (Tương tự như logic cũ của bạn)
const filteredProducts = computed(() => {
    let list = products.value ? products.value.slice() : [];
    // Chỉ sắp xếp local theo created_date/views/price (sau khi đã lấy từ API)
    const sorters = {
        // Cần đảm bảo API trả về các trường này (ví dụ: created_at)
        'newest': (a, b) => new Date(b.created_date) - new Date(a.created_date), 
        // ... thêm các logic sắp xếp khác nếu cần ...
    };

    return list.slice().sort(sorters[sortBy.value] || sorters['newest']);
});

</script>

<template>
    <AppLayout title="Sản phẩm Yêu thích">
        <div class="max-w-7xl mx-auto p-4 md:p-6">
            <h1 class="text-3xl font-bold text-gray-900 mb-6 flex items-center space-x-3">
                <fa :icon="['fas', 'heart']" class="text-red-500" />
                <span>Danh sách Yêu thích</span>
                <span class="text-xl text-gray-500">({{ wishlistStore.totalItems }})</span>
            </h1>

            <div class="flex flex-col lg:flex-row gap-6">

                <!-- MAIN CONTENT -->
                <div class="lg:w-full">
                    
                    <div class="flex justify-end items-center mb-6">
                        <div class="flex items-center space-x-4">
                             <span class="text-sm text-gray-600" style="white-space: nowrap;">Sắp xếp:</span>
                             <select class="form-select" v-model="sortBy">
                                <option value="newest">Mới nhất (Yêu thích)</option>
                                <option value="oldest">Cũ nhất</option>
                                <!-- Thêm các tùy chọn sắp xếp khác nếu cần -->
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
                            <!-- Truyền 'isFavorited' vào ProductCard nếu cần hiển thị trạng thái tim -->
                            <ProductCard 
                                v-for="product in filteredProducts" 
                                :key="product.id" 
                                :product="product"
                                :view-mode="currentView"
                            />
                        </template>
                        
                        <div v-else-if="error" class="col-span-full text-center py-12 bg-red-50 rounded-lg shadow-md text-red-700">
                             <fa :icon="['fas', 'exclamation-circle']" class="text-2xl mb-2" />
                             <h3 class="text-lg font-semibold mb-2">Lỗi truy cập</h3>
                             <p>{{ error }}</p>
                        </div>

                        <div v-else class="col-span-full text-center py-12 bg-gray-50 rounded-lg shadow-md">
                            <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <fa :icon="['fas', 'heart-broken']" class="text-gray-400 text-3xl" />
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">Danh sách yêu thích trống</h3>
                            <p class="text-gray-600 mb-4">Hãy lướt xem và thêm những sản phẩm bạn thích!</p>
                            <button @click="router.push({ name: 'products.index' })"
                                class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                                <fa :icon="['fas', 'box']" class="mr-1" />Khám phá Sản phẩm
                            </button>
                        </div>
                    </div>
                     <!-- Pagination -->
                     <BasePagination :pagination="pagination" :on-page-change="handlePageChange" />
                </div>
            </div>
        </div>
    </AppLayout>
</template>