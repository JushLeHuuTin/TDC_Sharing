<script setup>
import { ref, onMounted, watch } from 'vue';
import { storeToRefs } from 'pinia';
import { useCategoryStore } from '@/stores/categoryStore';
// Cần thêm imports cho Font Awesome icons nếu chưa có
// import { faExclamationTriangle, faSpinner, faInfoCircle } from '@fortawesome/free-solid-svg-icons'; 

const props = defineProps({
    // Nhận hàm để đóng modal và truyền ID đã chọn
    onCategorySelected: Function,
    // Nhận trạng thái hiển thị modal từ component cha
    isVisible: Boolean,
});

const categoryStore = useCategoryStore();
// flattenedCategories chứa [id, name (có gạch ngang), level, isParent]
const { flattenedCategories, isLoading: isLoadingCategories } = storeToRefs(categoryStore);

const selectedCategory = ref('');

// --- LOGIC XỬ LÝ SỰ KIỆN (Giữ nguyên) ---

const selectAndClose = () => {
    if (selectedCategory.value) {
        props.onCategorySelected(selectedCategory.value);
    }
};

const closeModal = () => {
    // Truyền null để báo hiệu hủy bỏ nếu cần
    props.onCategorySelected(null);
};

// --- QUẢN LÝ BOOTSTRAP MODAL (Sử dụng JS) (Giữ nguyên) ---
let modalElement = null;
let bsModal = null;

onMounted(() => {
    modalElement = document.getElementById('categoryChooserModal');
    if (window.bootstrap && modalElement) {
        bsModal = new window.bootstrap.Modal(modalElement, {
            backdrop: 'static', // Bắt buộc người dùng phải chọn
            keyboard: false,    // Tắt thoát bằng phím ESC
        });

        // Watch prop `isVisible` để điều khiển Bootstrap Modal
        watch(() => props.isVisible, (newVal) => {
            if (newVal) {
                bsModal.show();
            } else {
                bsModal.hide();
            }
        });
        
        // Đảm bảo reset selectedCategory khi modal mở
        modalElement.addEventListener('show.bs.modal', () => {
             selectedCategory.value = '';
        });
    }
});
</script>

<template>
    <div class="modal fade" id="categoryChooserModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">
                        <fa :icon="['fas', 'exclamation-triangle']" class="me-2" />
                        Bước 1: Chọn Danh mục Bắt buộc
                    </h5>
                    </div>
                <div class="modal-body">
                    <p class="text-danger fw-bold mb-3">Vui lòng chọn danh mục chính xác trước khi nhập thông tin chi tiết của tài nguyên.</p>
                    
                    <div v-if="isLoadingCategories" class="text-center py-4">
                        <fa :icon="['fas', 'spinner']" class="fa-spin me-2" /> Đang tải danh mục...
                    </div>
                    
                    <div v-else class="mb-3">
                        <label for="initialCategory" class="form-label fw-bold">Danh mục chính</label>
                        
                        <select id="initialCategory" class="form-select form-control" v-model="selectedCategory">
                            <option value="" disabled>--- Chọn danh mục ---</option>
                            
                            <option 
                                v-for="cat in flattenedCategories" 
                                :key="cat.id" 
                                :value="cat.id"
                                
                                :disabled="cat.isParent" 
                                
                                :class="{ 
                                    'fw-bold bg-light text-primary': cat.isParent, 
                                    'text-muted': cat.isParent && cat.level === 0, 
                                    'text-dark': !cat.isParent 
                                }"
                            >
                                {{ cat.name }}
                            </option>
                        </select>
                    </div>

                    <p v-if="!selectedCategory" class="text-sm text-warning mt-2">
                         <fa :icon="['fas', 'info-circle']" class="me-1" />
                        Bạn phải chọn một danh mục để tiếp tục.
                    </p>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" @click="closeModal">
                        Hủy bỏ và quay lại trang chủ
                    </button>
                    <button 
                        type="button" 
                        class="btn btn-primary" 
                        :disabled="!selectedCategory"
                        @click="selectAndClose"
                    >
                        Tiếp tục nhập thông tin
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>