<script setup>
import { ref, onMounted, computed } from 'vue';
import { storeToRefs } from 'pinia';
import { useReviewStore } from '@/stores/reviewStore';
import { useAuthStore } from '@/stores/auth'; // Cần để kiểm tra user

// --- 1. KHỞI TẠO & PROPS ---
const props = defineProps({
    productId: {
        type: [Number, String],
        required: true
    }
});

const reviewStore = useReviewStore();
const { reviews, isLoading, error } = storeToRefs(reviewStore);

const authStore = useAuthStore();
const { isLoggedIn, user } = storeToRefs(authStore);

// --- 2. STATE (Form Thêm mới) ---
const newReview = ref({
    product_id: props.productId,
    rating: 5,
    comment: ''
});

// --- 3. STATE (Modal Sửa) ---
const showEditModal = ref(false);
const editingReview = ref(null);

// --- 4. COMPUTED ---
const averageRating = computed(() => {
    if (reviews.value.length === 0) return 0;
    const sum = reviews.value.reduce((acc, review) => acc + review.rating, 0);
    return (sum / reviews.value.length).toFixed(1);
});

// --- 5. LIFECYCLE ---
onMounted(() => {
    reviewStore.fetchReviews(props.productId);
});

// --- 6. METHODS (CRUD) ---

// (Tính năng 1)
async function handleAddReview() {
    const success = await reviewStore.addReview(newReview.value);
    if (success) {
        // Reset form
        newReview.value.rating = 5;
        newReview.value.comment = '';
    }
    // Lỗi sẽ được hiển thị qua 'error'
}

// (Tính năng 3)
async function handleDelete(reviewId) {
    if (confirm('Bạn có chắc muốn xóa đánh giá này?')) {
        await reviewStore.deleteReview(reviewId);
    }
}

// (Tính năng 4)
function openEditModal(review) {
    editingReview.value = { ...review }; // Copy để chỉnh sửa
    showEditModal.value = true;
}

function closeEditModal() {
    showEditModal.value = false;
    editingReview.value = null;
}

async function handleUpdateReview() {
    if (!editingReview.value) return;
    
    const dataToUpdate = {
        rating: editingReview.value.rating,
        comment: editingReview.value.comment
    };
    
    const success = await reviewStore.updateReview(editingReview.value.id, dataToUpdate);
    if (success) {
        closeEditModal();
    }
}
</script>

<template>
    <div class="bg-white rounded-lg shadow-md p-6 mt-6">
        <h3 class="text-xl font-semibold text-gray-800 mb-4">
            Đánh giá sản phẩm ({{ reviews.length }})
        </h3>

        <!-- 1. TỔNG QUAN ĐÁNH GIÁ -->
        <div class="bg-gray-50 rounded-lg p-4 mb-6 flex items-center">
            <div class="text-5xl font-bold text-yellow-500">{{ averageRating }}</div>
            <div class="ml-4">
                <div class="text-gray-700 font-medium">Trên 5 sao</div>
                <div class="flex items-center">
                    <!-- Hiển thị sao trung bình -->
                    <span v-for="n in 5" :key="n" class="text-yellow-400">
                        <fa :icon="n <= averageRating ? ['fas', 'star'] : ['far', 'star']" />
                    </span>
                </div>
            </div>
        </div>

        <!-- 2. FORM THÊM ĐÁNH GIÁ (Tính năng 1) -->
        <div v-if="isLoggedIn" class="mb-6 border-b pb-6">
            <h4 class="text-md font-semibold text-gray-700 mb-2">Để lại đánh giá của bạn</h4>
            
            <!-- Ô chọn sao -->
            <div class="flex items-center space-x-1 mb-3">
                <label class="text-sm font-medium text-gray-700 mr-2">Chọn số sao:</label>
                <button v-for="n in 5" :key="n" @click="newReview.rating = n" 
                        :class="[n <= newReview.rating ? 'text-yellow-400' : 'text-gray-300', 'text-2xl transition-colors']">
                    <fa :icon="['fas', 'star']" />
                </button>
            </div>
            
            <!-- Ô bình luận -->
            <textarea v-model="newReview.comment" rows="3" 
                      placeholder="Chia sẻ cảm nhận của bạn về sản phẩm..."
                      class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
            
            <button @click="handleAddReview" :disabled="isLoading"
                    class="mt-3 action-btn bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md font-medium disabled:opacity-50">
                <span v-if="isLoading">Đang gửi...</span>
                <span v-else>Gửi đánh giá</span>
            </button>
            <div v-if="error" class="text-red-600 text-sm mt-2">{{ error }}</div>
        </div>
        <div v-else class="mb-6 border-b pb-6 text-center">
            <p class="text-gray-600">Bạn cần <router-link :to="{name: 'login'}" class="text-blue-600 font-medium hover:underline">đăng nhập</router-link> để để lại đánh giá.</p>
        </div>


        <!-- 3. DANH SÁCH ĐÁNH GIÁ (Tính năng 2) -->
        <div class="space-y-6">
            <div v-if="isLoading && reviews.length === 0" class="text-center text-gray-500">Đang tải đánh giá...</div>
            <div v-else-if="reviews.length === 0" class="text-center text-gray-500">Chưa có đánh giá nào.</div>
            
            <div v-for="review in reviews" :key="review.id" class="flex space-x-4 border-b pb-4 last:border-b-0">
                <!-- Avatar (Giả định) -->
                <img :src="`https://ui-avatars.io/api/?name=${review.user_name}&background=0D8ABC&color=fff`" 
                     class="h-10 w-10 rounded-full" :alt="review.user_name">
                
                <div class_ ="flex-grow">
                    <div class="flex justify-between items-center">
                        <div>
                            <span class="font-semibold text-gray-800">{{ review.user_name }}</span>
                            <span class="text-xs text-gray-500 ml-2">{{ new Date(review.created_at).toLocaleDateString('vi-VN') }}</span>
                        </div>
                        
                        <!-- Nút Sửa/Xóa (Tính năng 3, 4) -->
                        <div v-if="isLoggedIn && user && user.id === review.user_id" class="flex space-x-2">
                            <button @click="openEditModal(review)" class="action-btn p-1 rounded-md text-gray-500 hover:bg-yellow-100 hover:text-yellow-600" title="Sửa">
                                <fa :icon="['fas', 'pencil-alt']" class="h-4 w-4" />
                            </button>
                            <button @click="handleDelete(review.id)" class="action-btn p-1 rounded-md text-gray-500 hover:bg-red-100 hover:text-red-600" title="Xóa">
                                <fa :icon="['fas', 'trash']" class="h-4 w-4" />
                            </button>
                        </div>
                    </div>

                    <!-- Sao đánh giá -->
                    <div class="flex items-center my-1">
                        <span v-for="n in 5" :key="n" class="text-yellow-400">
                            <fa :icon="n <= review.rating ? ['fas', 'star'] : ['far', 'star']" />
                        </span>
                    </div>

                    <!-- Nội dung bình luận -->
                    <p class="text-gray-700 text-sm">{{ review.comment }}</p>
                </div>
            </div>
        </div>


        <!-- 4. MODAL SỬA ĐÁNH GIÁ (Tính năng 4) -->
        <div v-if="showEditModal && editingReview" class="fixed inset-0 bg-gray-900 bg-opacity-75 flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-lg shadow-xl max-w-lg w-full">
                <!-- Header -->
                <div class="flex justify-between items-center p-5 border-b">
                    <h3 class="text-lg font-semibold text-gray-900">Chỉnh sửa đánh giá</h3>
                    <button @click="closeEditModal" class="text-gray-400 hover:text-gray-600">
                        <fa :icon="['fas', 'times']" class="h-5 w-5" />
                    </button>
                </div>

                <!-- Form Body -->
                <div class="p-6 space-y-4">
                    <div class="flex items-center space-x-1">
                        <label class="text-sm font-medium text-gray-700 mr-2">Chỉnh sửa số sao:</label>
                        <button v-for="n in 5" :key="n" @click="editingReview.rating = n" 
                                :class="[n <= editingReview.rating ? 'text-yellow-400' : 'text-gray-300', 'text-2xl transition-colors']">
                            <fa :icon="['fas', 'star']" />
                        </button>
                    </div>
                    <div>
                        <label class_ ="block text-sm font-medium text-gray-700 mb-1">Chỉnh sửa bình luận</label>
                        <textarea v-model="editingReview.comment" rows="4"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                    </div>
                    <div v-if="error" class="text-red-600 text-sm p-3 bg-red-50 rounded-md">
                        Lỗi: {{ error }}
                    </div>
                </div>

                <!-- Footer -->
                <div class="p-4 bg-gray-50 border-t flex justify-end space-x-3">
                    <button @click="closeEditModal" class="px-5 py-2 text-sm font-medium text-gray-700 bg-white border rounded-lg hover:bg-gray-100">
                        Hủy
                    </button>
                    <button @click="handleUpdateReview" :disabled="isLoading" class="px-5 py-2 text-sm font-medium text-white bg-green-600 rounded-lg hover:bg-green-700 disabled:opacity-50 flex items-center">
                        <fa :icon="['fas', 'save']" class="h-4 w-4 mr-2" />
                        <span v-if="isLoading">Đang lưu...</span>
                        <span v-else>Lưu thay đổi</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
/* (Style cho nút bấm giống các trang admin) */
.action-btn {
    transition: all 0.2s ease-in-out; 
    font-weight: 500;
}
.action-btn:hover {
    transform: translateY(-1px);
}
</style>