<script setup>
import { ref, onMounted, computed } from 'vue';
import { storeToRefs } from 'pinia';
import { useReviewStore } from '@/stores/reviewStore';
import { useAuthStore } from '@/stores/auth';
import { useRouter } from 'vue-router';
import Swal from 'sweetalert2';
import axios from 'axios';

// --- QUAN TRỌNG: Import CSS của Animate.css để có hiệu ứng đẹp ---
// Nếu dự án bạn chưa cài animate.css, hãy chạy: npm install animate.css
// Hoặc đơn giản là thêm link CDN vào index.html cũng được.
// Nhưng Swal2 cũng có animation mặc định khá ổn nếu không có animate.css.

const props = defineProps({
    productId: {
        type: [Number, String],
        required: true
    }
});

const router = useRouter();
const reviewStore = useReviewStore();
const { reviews, isLoading } = storeToRefs(reviewStore);
const authStore = useAuthStore();
const { isLoggedIn, user } = storeToRefs(authStore);

const newReview = ref({
    product_id: props.productId,
    rating: 5,
    comment: ''
});

const showEditModal = ref(false);
const editingReview = ref(null);
const pendingReviews = ref([]);

// --- STATE MỚI: Theo dõi lỗi tải ảnh ---
const imageErrors = ref({});

const handleImageError = (reviewId) => {
    // Khi ảnh lỗi, đánh dấu reviewId này vào danh sách lỗi để ẩn ảnh và hiện avatar chữ
    imageErrors.value[reviewId] = true;
};

const averageRating = computed(() => {
    if (reviews.value.length === 0) return 0;
    const sum = reviews.value.reduce((acc, review) => acc + review.rating, 0);
    return (sum / reviews.value.length).toFixed(1);
});

const formatDate = (dateStr) => {
    if (!dateStr) return '';
    const date = new Date(dateStr);
    return isNaN(date.getTime()) ? dateStr : date.toLocaleDateString('vi-VN');
};

onMounted(() => {
    if (props.productId) {
        reviewStore.fetchReviews(props.productId);
    }
});

// --- API Lấy sản phẩm chưa đánh giá ---
async function fetchPendingReviews() {
    try {
        const response = await axios.get('http://127.0.0.1:8000/api/reviews/pending', {
            headers: { Authorization: `Bearer ${authStore.token}` }
        });
        pendingReviews.value = response.data.data;
    } catch (error) {
        console.error("Lỗi lấy sản phẩm chờ đánh giá:", error);
    }
}

// --- XỬ LÝ THÊM ĐÁNH GIÁ ---
async function handleAddReview() {
    if (!newReview.value.comment.trim()) {
        Swal.fire({
            icon: 'warning',
            title: 'Oops...',
            text: 'Bạn chưa nhập nội dung đánh giá!',
            confirmButtonColor: '#3085d6',
            confirmButtonText: 'Để tôi nhập lại',
            // Hiệu ứng Rung lắc nhẹ khi cảnh báo
            showClass: { popup: 'animate__animated animate__shakeX' },
            hideClass: { popup: 'animate__animated animate__fadeOut' }
        });
        return;
    }

    const result = await reviewStore.addReview(newReview.value);
    
    if (result.success) {
        newReview.value.rating = 5;
        newReview.value.comment = '';
        
        Swal.fire({
            icon: 'success',
            title: 'Tuyệt vời!',
            text: 'Cảm ơn bạn đã đánh giá sản phẩm.',
            showConfirmButton: false,
            timer: 2000,
            // Hiệu ứng trượt xuống mượt mà
            showClass: { popup: 'animate__animated animate__fadeInDown' },
            hideClass: { popup: 'animate__animated animate__fadeOutUp' }
        });
    } else {
        // --- NẾU LỖI (CHƯA MUA HÀNG) ---
        await fetchPendingReviews();

        let htmlContent = `<div class="text-gray-600 mb-4 text-sm">${result.message}</div>`;
        
        if (pendingReviews.value.length > 0) {
            htmlContent += `
                <div class="border-t pt-3">
                    <p class="font-bold text-gray-800 text-left mb-2 text-sm">
                        <i class="fas fa-gift text-yellow-500 mr-1"></i> Các sản phẩm bạn có thể đánh giá:
                    </p>
                    <div class="text-left space-y-2 max-h-48 overflow-y-auto custom-scrollbar pr-1">
            `;
            pendingReviews.value.forEach(p => {
                const imgUrl = p.images?.[0]?.path || 'https://placehold.co/50';
                htmlContent += `
                    <div class="flex items-center gap-3 p-2 hover:bg-blue-50 rounded-lg cursor-pointer transition-colors border border-transparent hover:border-blue-100 product-link" data-slug="${p.slug}">
                        <img src="${imgUrl}" class="w-12 h-12 object-cover rounded-md border border-gray-200">
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-800 truncate">${p.title}</p>
                            <p class="text-xs text-blue-600 mt-0.5">Viết đánh giá ngay &rarr;</p>
                        </div>
                    </div>
                `;
            });
            htmlContent += `</div></div>`;
        } else {
             htmlContent += `<p class="text-xs text-gray-400 mt-4 italic">(Bạn hiện không có sản phẩm nào chờ đánh giá)</p>`;
        }

        Swal.fire({
            icon: 'info',
            title: 'Thông báo',
            html: htmlContent,
            confirmButtonText: 'Đã hiểu',
            confirmButtonColor: '#3085d6',
            width: '32rem',
            // Hiệu ứng Zoom In mạnh mẽ để gây chú ý
            showClass: { popup: 'animate__animated animate__zoomIn' },
            hideClass: { popup: 'animate__animated animate__zoomOut' },
            didOpen: () => {
                const links = Swal.getHtmlContainer().querySelectorAll('.product-link');
                links.forEach(link => {
                    link.addEventListener('click', () => {
                        const slug = link.getAttribute('data-slug');
                        Swal.close();
                        router.push(`/products/${slug}`);
                    });
                });
            }
        }).then(() => {
            newReview.value.comment = ''; 
            newReview.value.rating = 5; 
        });
    }
}

// --- XỬ LÝ XÓA ---
async function handleDelete(reviewId) {
    const confirmResult = await Swal.fire({
        title: 'Bạn chắc chắn chứ?',
        text: "Hành động này sẽ xóa vĩnh viễn đánh giá này!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Xóa ngay!',
        cancelButtonText: 'Hủy',
        // Hiệu ứng nảy nảy (Bounce)
        showClass: { popup: 'animate__animated animate__bounceIn' },
        hideClass: { popup: 'animate__animated animate__bounceOut' }
    });

    if (confirmResult.isConfirmed) {
        const result = await reviewStore.deleteReview(reviewId, props.productId);
        if (result.success) {
            Swal.fire('Đã xóa!', 'Đánh giá đã được xóa.', 'success');
        } else {
            // Dịch thông báo lỗi tiếng Anh sang tiếng Việt
            let errorMessage = result.message;
            if (errorMessage === 'This action is unauthorized.') {
                errorMessage = 'Bạn không có quyền xóa đánh giá này.';
            }
            Swal.fire('Lỗi!', errorMessage, 'error');
        }
    }
}

function openEditModal(review) {
    editingReview.value = { ...review };
    showEditModal.value = true;
}

function closeEditModal() {
    showEditModal.value = false;
    editingReview.value = null;
}

// --- XỬ LÝ CẬP NHẬT ---
async function handleUpdateReview() {
    if (!editingReview.value) return;
    
    const result = await reviewStore.updateReview(editingReview.value.id, {
        product_id: props.productId,
        rating: editingReview.value.rating,
        comment: editingReview.value.comment
    });
    
    if (result.success) {
        closeEditModal();
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
            }
        });
        Toast.fire({
            icon: 'success',
            title: 'Cập nhật đánh giá thành công!'
        });
    } else {
        // Dịch thông báo lỗi tiếng Anh sang tiếng Việt
        let errorMessage = result.message;
        if (errorMessage === 'This action is unauthorized.') {
            errorMessage = 'Bạn không có quyền chỉnh sửa đánh giá này.';
        }
        Swal.fire('Lỗi!', errorMessage, 'error');
    }
}
</script>

<template>
    <!-- Template giữ nguyên như cũ -->
    <div class="bg-white rounded-lg shadow-md p-6 mt-6 border border-gray-100">
        <h3 class="text-xl font-bold text-gray-800 mb-6 flex items-center">
            <fa :icon="['fas', 'comments']" class="mr-2 text-blue-600" />
            Đánh giá sản phẩm <span class="text-gray-500 ml-2 text-base font-normal">({{ reviews.length }} đánh giá)</span>
        </h3>

        <!-- Tổng quan -->
        <div class="bg-blue-50 rounded-xl p-6 mb-8 flex items-center border border-blue-100">
            <div class="text-center mr-8">
                <div class="text-5xl font-extrabold text-gray-800">{{ averageRating }}</div>
                <div class="text-xl mt-1">
                    <span v-for="n in 5" :key="n" :class="n <= Math.round(averageRating) ? 'text-yellow-400' : 'text-gray-300'">
                        <fa :icon="['fas', 'star']" />
                    </span>
                </div>
            </div>
            <div class="flex-grow border-l border-blue-200 pl-8">
                <p class="text-gray-600 mb-1 font-medium">Bạn nghĩ gì về sản phẩm này?</p>
            </div>
        </div>

        <!-- Form Viết đánh giá -->
        <div v-if="isLoggedIn" class="mb-8 border-b border-gray-200 pb-8">
            <h4 class="text-lg font-semibold text-gray-700 mb-4">Viết đánh giá của bạn</h4>
            <div class="flex items-center mb-4">
                <span class="text-sm font-medium text-gray-700 mr-3">Chất lượng:</span>
                <div class="flex space-x-1">
                    <button v-for="n in 5" :key="n" @click="newReview.rating = n" 
                            class="focus:outline-none transition-transform hover:scale-110"
                            :class="n <= newReview.rating ? 'text-yellow-400' : 'text-gray-300'">
                        <fa :icon="['fas', 'star']" class="text-2xl" />
                    </button>
                </div>
                <span class="ml-3 text-sm font-medium text-blue-600">
                    {{ newReview.rating === 5 ? 'Tuyệt vời' : newReview.rating === 4 ? 'Tốt' : newReview.rating === 3 ? 'Bình thường' : 'Tệ' }}
                </span>
            </div>
            
            <textarea v-model="newReview.comment" rows="3" 
                      placeholder="Chia sẻ cảm nhận..."
                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"></textarea>
            
            <div class="mt-3 flex justify-end">
                <button @click="handleAddReview" :disabled="isLoading"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium flex items-center shadow-sm transition-colors">
                    <span v-if="isLoading"><fa :icon="['fas', 'spinner']" spin class="mr-2" />Đang gửi...</span>
                    <span v-else><fa :icon="['fas', 'paper-plane']" class="mr-2" />Gửi đánh giá</span>
                </button>
            </div>
        </div>

        <div v-else class="mb-8 p-6 bg-gray-50 rounded-lg text-center border border-dashed border-gray-300">
            <p class="text-gray-600 mb-3">Bạn cần đăng nhập để viết đánh giá.</p>
            <router-link :to="{name: 'login'}" class="text-blue-600 font-medium hover:underline">Đăng nhập ngay</router-link>
        </div>

        <!-- Danh sách đánh giá -->
        <div class="space-y-6">
            <div v-if="isLoading && reviews.length === 0" class="text-center py-4">Đang tải...</div>
            <div v-else-if="reviews.length === 0" class="text-center py-4 text-gray-500">Chưa có đánh giá nào.</div>
            
            <div v-for="review in reviews" :key="review.id" class="border-b border-gray-100 pb-6 last:border-0">
                <div class="flex items-start space-x-4">
                    
                    <!-- AVATAR: ĐÃ SỬA: Thêm @error để bắt lỗi ảnh hỏng -->
                    <div class="flex-shrink-0">
                        <img 
                            v-if="review.user_avatar && !imageErrors[review.id]" 
                            :src="review.user_avatar" 
                            @error="handleImageError(review.id)"
                            class="h-12 w-12 rounded-full object-cover border border-gray-200" 
                            alt="Avatar"
                        >
                        <!-- Fallback: Icon chữ cái -->
                        <div 
                            v-else 
                            class="h-12 w-12 rounded-full bg-gray-200 flex items-center justify-center text-gray-600 font-bold text-xl uppercase border border-gray-200"
                        >
                            {{ review.user_name ? review.user_name.charAt(0) : '?' }}
                        </div>
                    </div>

                    <div class="flex-grow">
                        <div class="flex justify-between">
                            <div>
                                <h5 class="font-bold text-gray-800 text-sm">{{ review.user_name }}</h5>
                                <div class="flex text-xs mt-1">
                                    <span v-for="n in 5" :key="n" :class="n <= review.rating ? 'text-yellow-400' : 'text-gray-300'">
                                        <fa :icon="['fas', 'star']" />
                                    </span>
                                    <span class="text-gray-400 ml-2">{{ formatDate(review.date) }}</span>
                                </div>
                            </div>
                            
                            <div v-if="isLoggedIn && user && (user.id == review.user_id || (user.role && user.role.toLowerCase() === 'admin'))" class="flex space-x-2">
                                <button @click="openEditModal(review)" class="text-gray-400 hover:text-blue-600"><fa :icon="['fas', 'pencil-alt']" /></button>
                                <button @click="handleDelete(review.id)" class="text-gray-400 hover:text-red-600"><fa :icon="['fas', 'trash-alt']" /></button>
                            </div>
                        </div>
                        <p class="text-gray-700 text-sm mt-2">{{ review.comment }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Sửa -->
        <div v-if="showEditModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4 backdrop-blur-sm transition-opacity">
            <div class="bg-white rounded-xl max-w-lg w-full p-6 shadow-2xl transform transition-all scale-100">
                <h3 class="text-lg font-bold mb-4 text-gray-800">Sửa đánh giá</h3>
                <div class="flex justify-center mb-4 space-x-2">
                    <button v-for="n in 5" :key="n" @click="editingReview.rating = n" 
                            class="text-2xl transition-transform hover:scale-110" 
                            :class="n <= editingReview.rating ? 'text-yellow-400' : 'text-gray-300'">
                        <fa :icon="['fas', 'star']" />
                    </button>
                </div>
                <textarea v-model="editingReview.comment" rows="4" class="w-full border border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-blue-500 focus:border-transparent"></textarea>
                <div class="mt-6 flex justify-end space-x-3">
                    <button @click="closeEditModal" class="px-5 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 font-medium">Hủy</button>
                    <button @click="handleUpdateReview" class="px-5 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium shadow-md">Lưu thay đổi</button>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
/* Tùy chỉnh thanh cuộn cho đẹp */
.custom-scrollbar::-webkit-scrollbar {
    width: 6px;
}
.custom-scrollbar::-webkit-scrollbar-track {
    background: #f1f1f1;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 10px;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}

/* * Mẹo: Nếu muốn dùng Animate.css để hiệu ứng mượt hơn nữa, 
* bạn có thể thêm dòng này vào file index.html của project:
* <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
*/
</style>