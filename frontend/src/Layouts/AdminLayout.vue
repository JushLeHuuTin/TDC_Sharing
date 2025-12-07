<script setup>
import { ref, onMounted, computed, watch } from 'vue';
import { RouterView, useRoute } from 'vue-router';
import AdminSidebar from '@/components/Admin/Components/AdminSidebar.vue';
import { useCategoryStore } from '@/stores/categoryStore';
import { storeToRefs } from 'pinia';
const categoryStore = useCategoryStore();
const { categoriesTree } = storeToRefs(categoryStore);
const currentPage = ref('categories');
const sidebarOpen = ref(window.innerWidth > 768);
const selectedCategoryId = ref(null);

function addCategoryHandler() { emit('add-category'); }
// Hàm showToast 
function showToast(message, type = 'info') {
    const toastContainer = document.querySelector('.toast-container');
    if (!toastContainer) return;

    const toastId = 'toast-' + Date.now();
    const bgClass = {
        'success': 'bg-success',
        'error': 'bg-danger',
        'warning': 'bg-warning',
        'info': 'bg-primary' 
    }[type] || 'bg-primary';
    
    const toastHtml = `
    <div id="${toastId}" class="toast align-items-center text-white ${bgClass} border-0" role="aler">
        <div class="d-flex">
            <div class="toast-body">${message}</div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
            </div>
            `;
    toastContainer.insertAdjacentHTML('beforeend', toastHtml);

    // Yêu cầu Bootstrap 5 JS được load để hoạt động
    const toastElement = document.getElementById(toastId);
    if (window.bootstrap && toastElement) {
        const toast = new window.bootstrap.Toast(toastElement);
        toast.show();

        toastElement.addEventListener('hidden.bs.toast', () => {
            toastElement.remove();
        });
    }
}

// --- DATA LOGIC (Giữ nguyên data mẫu) ---
function generateSampleCategories() {
    // ... (Giữ nguyên array data categories) ...
    return [
        { id: 1, name: 'Điện tử - Công nghệ', parent_id: null, level: 1, description: 'Các sản phẩm điện tử và công nghệ', icon: 'fas fa-laptop', color: '#0d6efd', order: 1, active: true, product_count: 245, created_at: new Date('2024-01-15') },
        { id: 2, name: 'Laptop & Máy tính', parent_id: 1, level: 2, description: 'Laptop, PC, linh kiện máy tính', icon: 'fas fa-desktop', color: '#198754', order: 1, active: true, product_count: 89, created_at: new Date('2024-01-15') },
        { id: 3, name: 'Điện thoại & Tablet', parent_id: 1, level: 2, description: 'Smartphone, tablet và phụ kiện', icon: 'fas fa-mobile-alt', color: '#dc3545', order: 2, active: true, product_count: 156, created_at: new Date('2024-01-15') },
        { id: 4, name: 'Sách & Học tập', parent_id: null, level: 1, description: 'Sách giáo khoa, tài liệu học tập', icon: 'fas fa-book', color: '#fd7e14', order: 2, active: true, product_count: 423, created_at: new Date('2024-01-16') },
        { id: 5, name: 'Sách giáo khoa', parent_id: 4, level: 2, description: 'Sách giáo khoa các cấp', icon: 'fas fa-graduation-cap', color: '#20c997', order: 1, active: true, product_count: 234, created_at: new Date('2024-01-16') },
        { id: 6, name: 'Tài liệu tham khảo', parent_id: 4, level: 2, description: 'Sách tham khảo, đề thi', icon: 'fas fa-file-alt', color: '#6f42c1', order: 2, active: true, product_count: 189, created_at: new Date('2024-01-16') },
        { id: 7, name: 'Thời trang', parent_id: null, level: 1, description: 'Quần áo, giày dép, phụ kiện', icon: 'fas fa-tshirt', color: '#e83e8c', order: 3, active: true, product_count: 312, created_at: new Date('2024-01-17') },
        { id: 8, name: 'Quần áo nam', parent_id: 7, level: 2, description: 'Thời trang nam', icon: 'fas fa-male', color: '#0dcaf0', order: 1, active: true, product_count: 145, created_at: new Date('2024-01-17') },
        { id: 9, name: 'Quần áo nữ', parent_id: 7, level: 2, description: 'Thời trang nữ', icon: 'fas fa-female', color: '#f8d7da', order: 2, active: true, product_count: 167, created_at: new Date('2024-01-17') },
        { id: 10, name: 'Đồ gia dụng', parent_id: null, level: 1, description: 'Đồ dùng trong gia đình', icon: 'fas fa-home', color: '#6c757d', order: 4, active: true, product_count: 178, created_at: new Date('2024-01-18') }
    ];
}

function getFaIconArray(iconClass) {
    if (!iconClass || typeof iconClass !== 'string') return ['fas', 'tag'];
    const parts = iconClass.split(' ');
    // Logic đơn giản: lấy prefix và tên icon
    const prefix = parts.find(p => p.startsWith('fa-') && p.length <= 4) || 'fas';
    const name = parts.find(p => p.startsWith('fa-') && p.length > 4);

    if (name) {
        return [prefix.replace('fa-', ''), name.replace('fa-', '')];
    }
    return ['fas', 'tag'];
}

const currentPageComponent = computed(() => {
    return pageComponents[currentPage.value] || 'DevelopingPage';
});
// --- LIFECYCLE VÀ KHỞI TẠO ---
onMounted(() => {
    // categoryStore.fetchCategories(true);
    // console.log(categoriesTree);
});
const category = computed(() => {
  return categoriesTree.value
    .filter(c => c.level === 1)
    .map(parent => ({
      ...parent,
      children: categoriesTree.value.filter(c => c.parent_id === parent.id)
    }));
});
// --- CÁC HÀM THAO TÁC (CHUYỂN ĐỔI) ---

function toggleSidebar() {
    sidebarOpen.value = !sidebarOpen.value;
}
function selectCategory(id) {
    // 🎯 Đây là nơi DUY NHẤT cập nhật trạng thái
    selectedCategoryId.value = id; 
}
</script>

<template>
    <div class="d-flex">
        <div class="sidebar-overlay" :class="{ 'show': !sidebarOpen && window.innerWidth <= 768 }"
            @click="toggleSidebar"></div>

        <AdminSidebar/>
        <!-- Main content -->
        <main class="main-content flex-grow-1">
            <div class="container-fluid">
                <!-- nơi hiển thị các trang con -->
                <RouterView :categories="category" :get-fa-icon-array="getFaIconArray"
                    :show-toast="showToast"
                    :selectedCategoryId="selectedCategoryId"
                    :on-select-category="selectCategory" />
            </div>
        </main>
    </div>

    <!-- <div class="modal fade" id="addCategoryModal" tabindex="-1" aria-labelledby="modalTitle" aria-hidden="true">
    </div>
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    </div>

    <div class="toast-container"></div> -->
              
</template>

<style scoped>

.sidebar {
    min-height: 100vh;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
    width: 280px;
    /* Thêm độ rộng cố định */
}

.sidebar .nav-link {
    color: rgba(255, 255, 255, 0.8);
    padding: 12px 20px;
    border-radius: 8px;
    margin: 2px 10px;
    transition: all 0.3s ease;
}

.sidebar .nav-link:hover {
    background-color: rgba(255, 255, 255, 0.1);
    color: white;
    transform: translateX(5px);
}

.sidebar .nav-link.active {
    background-color: rgba(255, 255, 255, 0.2);
    color: white;
    font-weight: 600;
}

/* Sử dụng .nav-link > svg để định dạng icon Font Awesome SVG */
.sidebar .nav-link>svg {
    width: 20px;
    text-align: center;
    margin-right: 10px;
}

.main-content {
    margin-left: 280px;
    padding: 20px;
}

.stats-card {
    border: none;
    border-radius: 15px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s ease;
}

.stats-card:hover {
    transform: translateY(-5px);
}

.category-tree {
    background: white;
    border-radius: 10px;
    padding: 20px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

.category-item {
    padding: 10px 15px;
    border: 1px solid #e9ecef;
    border-radius: 8px;
    margin-bottom: 8px;
    background: white;
    transition: all 0.3s ease;
    cursor: pointer;
    /* Thêm cursor pointer cho hành động click */
}

.category-item:hover {
    background-color: #f8f9fa;
    border-color: #0d6efd;
}

.category-level-1 {
    border-left: 4px solid #0d6efd;
    font-weight: 600;
}

.category-level-2 {
    margin-left: 30px;
    border-left: 4px solid #6c757d;
    background-color: #f8f9fa;
}

.category-actions {
    opacity: 0;
    transition: opacity 0.3s ease;
}

.category-item:hover .category-actions {
    opacity: 1;
}

.breadcrumb {
    background: none;
    padding: 0;
}

.page-header {
    background: white;
    border-radius: 10px;
    padding: 20px;
    margin-bottom: 20px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

.modal-content {
    border: none;
    border-radius: 15px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
}

.form-control:focus,
.form-select:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
}

.btn-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
}

.btn-primary:hover {
    background: linear-gradient(135deg, #5a6fd8 0%, #6a4190 100%);
}

.toast-container {
    position: fixed;
    top: 20px;
    right: 20px;
    z-index: 1050;
}

@media (max-width: 768px) {
    .sidebar {
        position: fixed;
        left: -280px;
        width: 280px;
        z-index: 1000;
        transition: left 0.3s ease;
    }

    .sidebar.show {
        left: 0;
    }

    .main-content {
        margin-left: 0;
    }

    .sidebar-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        z-index: 999;
        display: none;
    }

    .sidebar-overlay.show {
        display: block;
    }
}
</style>