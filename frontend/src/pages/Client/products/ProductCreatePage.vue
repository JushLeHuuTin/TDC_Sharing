<script setup>
import { ref, reactive, computed, onMounted, watch } from 'vue'; // ⬅️ Thêm watch
import { useRouter } from 'vue-router';
import AppLayout from '@/Layouts/AppLayout.vue';
import { faCloudUploadAlt, faPlus, faEdit, faRocket, faSave, faTimes, faChevronDown } from '@fortawesome/free-solid-svg-icons';
import { storeToRefs } from 'pinia';
import { useCategoryStore } from '@/stores/categoryStore';
import CategoryChooserModal from '@/pages/Client/products/CategoryChooserModal.vue';

const router = useRouter();
const categoryStore = useCategoryStore();
// 💡 Lấy categories và trạng thái loading từ Store
const { categories: categoryOptions, isLoading: isLoadingCategories, error: categoryError } = storeToRefs(categoryStore);

// --- STATE CỦA FORM ---
const form = reactive({
    title: '',
    description: '',
    category_id: '',
    quantity: 1,
    price: null,
    author: '',          
    condition: '',       
    edition: '',         
});
const showCategoryModal = ref(false); 
const DYNAMIC_FIELDS_CONFIG = {
    // 1: Tài liệu học tập
    1: ['author', 'condition', 'edition'],
    // 2: Thiết bị & Công nghệ
    2: ['condition', 'brand', 'warranty'], 
    // 3: Đồ dùng cá nhân
    3: ['condition', 'size'],
    // Mặc định, nếu không khớp ID nào
    default: [],
};

// --- STATE CỤC BỘ ---
const maxTitleLength = 100;
const maxDescriptionLength = 2000;
const imageFiles = ref(new Array(7).fill(null)); 
const imagePreviews = ref(new Array(7).fill(null)); 
const isSubmitting = ref(false);
const errorMessages = ref({}); 

// --- COMPUTED PROPERTIES ---

const titleCount = computed(() => form.title.length);
const descCount = computed(() => form.description.length);

// 💡 COMPUTED QUYẾT ĐỊNH CÁC TRƯỜNG NÀO CẦN HIỂN THỊ
const fieldsToShow = computed(() => {
    // Lấy ID danh mục hiện tại
    const id = form.category_id;
    // Tìm cấu hình tương ứng, nếu không có, trả về mảng rỗng
    return DYNAMIC_FIELDS_CONFIG[id] || DYNAMIC_FIELDS_CONFIG.default;
});

// --- WATCHER (THEO DÕI): Đảm bảo các trường động bị reset khi đổi danh mục ---
watch(() => form.category_id, (newId) => {
    // Khi category_id thay đổi, reset các trường động cũ để tránh gửi dữ liệu không liên quan
    const fields = ['author', 'condition', 'edition', 'brand', 'warranty', 'size'];
    fields.forEach(field => {
        form[field] = '';
    });
});

// ... (Các hàm handleImageChange, removeImage, formatPriceInput giữ nguyên) ...
const handleInitialCategorySelected = (selectedId) => {
    showCategoryModal.value = false; // Đóng Modal
    if (selectedId) {
        // Gán giá trị vào form (cũng tự động kích hoạt form chính)
        form.category_id = selectedId; 
        
        // Cần tải lại options cho dropdown phụ (nếu có)
        // Ví dụ: loadSubCategories(selectedId);
    } else {
        // Người dùng nhấn Hủy bỏ, điều hướng về trang chủ
        router.push('/');
    }
};
const categoryOptionsFinal = computed(() => {
    // Nếu bạn muốn hiển thị toàn bộ danh mục từ store trong dropdown chính
    return categoryStore.categories; 
});

// --- LIFECYCLE HOOKS ---
onMounted(() => {
    // Lấy danh mục từ Store
    categoryStore.fetchCategories(); 
    if (!form.category_id) {
        // Sử dụng setTimeout để đảm bảo component đã render DOM và Bootstrap đã load
        setTimeout(() => {
             showCategoryModal.value = true;
        }, 100); 
    }
});
</script>

<template>
    <AppLayout title="Đăng bán sản phẩm - TDC_Sharing">
        <CategoryChooserModal 
            :is-visible="showCategoryModal" 
            :on-category-selected="handleInitialCategorySelected"
        />
        <div class="max-w-4xl mx-auto">
            <div class="bg-white rounded-lg shadow-xl p-6">
                <div class="border-b border-gray-200 pb-6 mb-6">
                    <h1 class="text-2xl font-bold text-gray-900 mb-2">Đăng bán sản phẩm</h1>
                    <p class="text-gray-600">Điền thông tin chi tiết để thu hút người mua</p>
                </div>

                <form @submit.prevent class="space-y-8">
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="category" class="block text-sm font-medium text-gray-700 mb-2">
                                Danh mục <span class="text-red-500">*</span>
                            </label>
                            <div v-if="isLoadingCategories" class="text-sm text-gray-500">Đang tải danh mục...</div>
                            <div v-else-if="categoryError" class="text-sm text-red-600">{{ categoryError }}</div>
                            <select v-else id="category" name="category_id" required v-model="form.category_id"
                                :class="['w-full px-3 py-2 border rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent', errorMessages['category_id'] ? 'border-red-500' : 'border-gray-300']">
                                <option value="">Chọn danh mục</option>
                                <option v-for="cat in categoryOptions" :key="cat.id" :value="cat.id">
                                    {{ cat.name }}
                                </option>
                            </select>
                            <p v-if="errorMessages['category_id']" class="mt-1 text-sm text-red-600">{{ errorMessages['category_id'][0] }}</p>
                        </div>
                        
                        <div>
                            <label for="quantity" class="block text-sm font-medium text-gray-700 mb-2">
                                Số lượng <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <input type="number" id="quantity" name="quantity" required min="1" step="1"
                                    v-model.number="form.quantity"
                                    :class="['w-full pl-3 pr-12 py-2 border rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent', errorMessages['quantity'] ? 'border-red-500' : 'border-gray-300']"
                                    placeholder="1" />
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500 sm:text-sm">sản phẩm</span>
                                </div>
                            </div>
                            <p v-if="errorMessages['quantity']" class="mt-1 text-sm text-red-600">{{ errorMessages['quantity'][0] }}</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="col-span-1 md:col-span-2">
                            <label for="price" class="block text-sm font-medium text-gray-700 mb-2">
                                Giá bán <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <input type="number" id="price" name="price" required min="0"
                                    v-model="form.price" @input="formatPriceInput"
                                    :class="['w-full pl-3 pr-12 py-2 border rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent', errorMessages['price'] ? 'border-red-500' : 'border-gray-300']"
                                    placeholder="0">
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500 sm:text-sm">₫</span>
                                </div>
                            </div>
                            <p v-if="errorMessages['price']" class="mt-1 text-sm text-red-600">{{ errorMessages['price'][0] }}</p>
                        </div>
                        
                        <div v-if="fieldsToShow.length" class="col-span-1 border-l pl-5">
                            <h6 class="text-sm font-semibold text-gray-700 mb-2">Thuộc tính chi tiết</h6>
                            
                            <div v-if="fieldsToShow.includes('author')" class="mb-3">
                                <label for="author" class="block text-sm font-medium text-gray-700 mb-2">
                                    Tác giả <span class="text-red-500">*</span>
                                </label>
                                <input type="text" id="author" name="author" v-model="form.author"
                                    :class="['w-full pl-3 pr-3 py-2 border rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent', errorMessages['author'] ? 'border-red-500' : 'border-gray-300']"
                                    placeholder="Tác giả hoặc người tạo">
                            </div>

                            <div v-if="fieldsToShow.includes('condition')" class="mb-3">
                                <label for="condition" class="block text-sm font-medium text-gray-700 mb-2">
                                    Tình trạng <span class="text-red-500">*</span>
                                </label>
                                <select id="condition" name="condition" v-model="form.condition" required
                                    :class="['w-full px-3 py-2 border rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent']">
                                    <option value="">Chọn tình trạng</option>
                                    <option value="new">Mới (100%)</option>
                                    <option value="99">Như mới (99%)</option>
                                    <option value="90">Đã qua sử dụng (90% trở lên)</option>
                                </select>
                            </div>

                            <div v-if="fieldsToShow.includes('edition')" class="mb-3">
                                <label for="edition" class="block text-sm font-medium text-gray-700 mb-2">Phiên bản/Năm xuất bản</label>
                                <input type="text" id="edition" name="edition" v-model="form.edition"
                                    class="w-full pl-3 pr-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>
                            
                            </div>
                    </div>

                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                            Mô tả chi tiết <span class="text-red-500">*</span>
                        </label>
                        <textarea id="description" name="description" rows="6" required v-model="form.description" :maxlength="maxDescriptionLength"
                            :class="['w-full px-3 py-2 border rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent', errorMessages['description'] ? 'border-red-500' : 'border-gray-300']"
                            placeholder="Mô tả chi tiết về sản phẩm: Tình trạng hiện tại, lý do bán, phụ kiện kèm theo, điều kiện giao dịch..."></textarea>
                        <div class="flex justify-between mt-1">
                            <p v-if="errorMessages['description']" class="text-sm text-red-600">{{ errorMessages['description'][0] }}</p>
                            <p v-else class="text-sm text-gray-500">Mô tả càng chi tiết, cơ hội bán càng cao</p>
                            <span class="text-sm text-gray-500" id="descCount">{{ descCount }}/{{ maxDescriptionLength }}</span>
                        </div>
                    </div>

                    <div class="border-t border-gray-200 pt-6">
                        <div class="flex flex-col sm:flex-row gap-4">
                            <button @click="submitForm('publish')" :disabled="isSubmitting"
                                :class="['flex-1 py-3 px-6 rounded-lg font-medium transition-colors', isSubmitting ? 'bg-blue-400 cursor-not-allowed' : 'bg-blue-600 text-white hover:bg-blue-700']">
                                <fa :icon="faRocket" class="mr-2" />
                                {{ isSubmitting ? 'Đang đăng...' : 'Đăng bán ngay' }}
                            </button>

                            <button @click="submitForm('draft')" :disabled="isSubmitting"
                                :class="['flex-1 py-3 px-6 rounded-lg font-medium transition-colors', isSubmitting ? 'bg-gray-400 cursor-not-allowed' : 'bg-gray-600 text-white hover:bg-gray-700']">
                                <fa :icon="faSave" class="mr-2" />
                                Lưu nháp
                            </button>

                            <RouterLink to="/"
                                class="flex-1 text-center border border-gray-300 text-gray-700 py-3 px-6 rounded-lg hover:bg-gray-50 transition-colors font-medium">
                                <fa :icon="faTimes" class="mr-2" />
                                Hủy bỏ
                            </RouterLink>
                        </div>

                        <p class="text-sm text-gray-500 mt-4 text-center">
                            Bằng cách đăng sản phẩm, bạn đồng ý với
                            <a href="#" class="text-blue-600 hover:text-blue-800">Điều khoản sử dụng</a>
                            của TDC_Sharing
                        </p>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
/* CSS cho khu vực Upload Ảnh (Giữ nguyên) */
.upload-area { /* ... */ }
.upload-area.uploaded-main, 
.upload-area.uploaded-sub { /* ... */ }

/* Cập nhật CSS cho input (Giữ nguyên) */
input[type="text"], input[type="number"], textarea, select { /* ... */ }
input:focus, textarea:focus, select:focus { /* ... */ }
input[type="number"]::-webkit-outer-spin-button,
input[type="number"]::-webkit-inner-spin-button { /* ... */ }

/* Thêm CSS cho khu vực Upload Ảnh */
.upload-area {
    border-color: #d1d5db; /* gray-300 */
    background-repeat: no-repeat;
    background-size: cover;
    background-position: center;
}

/* Các trường hợp đã có ảnh */
.upload-area.uploaded-main, 
.upload-area.uploaded-sub {
    border-style: solid;
    border-color: #3b82f6; /* blue-500 */
    background-color: transparent !important;
}

/* Cập nhật CSS cho input (từ Blade gốc) */
input[type="text"], input[type="number"], textarea, select {
    border-color: #D1D5DB;
    transition: all 0.2s ease-in-out;
}

input:focus, textarea:focus, select:focus {
    box-shadow: 0 0 0 1px #3b82f6;
    border-color: #3b82f6;
}

/* Ẩn các nút điều khiển input type="number" */
input[type="number"]::-webkit-outer-spin-button,
input[type="number"]::-webkit-inner-spin-button {
    -webkit-appearance: none;
    margin: 0;
}
</style>