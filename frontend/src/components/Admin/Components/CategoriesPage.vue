<script setup>
import { computed, ref } from 'vue';
import { RouterLink } from 'vue-router'; 

// --- STATE CỤC BỘ ---
const expandedCategories = ref([]); // Lưu trữ ID của các danh mục cha đang mở

// --- PROPS NHẬN TỪ LAYOUT ---
const props = defineProps({
    // Data Props
    categories: Array,
    selectedCategoryId: [Number, String, null],
    
    // CRUD Action Handlers (Gọi logic Modal/CRUD trong Layout)
    onAddCategory: Function,
    onSelectCategory: Function,
    onEditCategory: Function,
    onDeleteCategory: Function,
    onAddSubCategory: Function,
    
    // Utilities
    getFaIconArray: Function,
    showToast: Function,
});

// --- COMPUTED PROPERTIES ---

// Đảm bảo không bị lỗi filter() khi categories chưa load xong
const categoriesArray = computed(() => props.categories || []);

const categoryStats = computed(() => {
    const level1Count = categoriesArray.value.filter(c => c.level === 1).length;
    const level2Count = categoriesArray.value.filter(c => c.level === 2).length;
    const totalProducts = categoriesArray.value.reduce((sum, c) => sum + c.product_count, 0);
    return {
        totalCategories: categoriesArray.value.length,
        level1Categories: level1Count,
        level2Categories: level2Count,
        activeProducts: totalProducts.toLocaleString('en-US')
    };
});

// Logic tạo cây danh mục
const categoryTreeData = computed(() => {
    const level1Categories = categoriesArray.value.filter(c => c.level === 1).sort((a, b) => a.order - b.order);
    const tree = [];
    level1Categories.forEach(category => {
        // 1. Thêm danh mục cấp 1
        tree.push(category);
        
        // 2. Nếu danh mục cấp 1 đang mở, thêm danh mục con
        if (expandedCategories.value.includes(category.id)) {
            categoriesArray.value
                .filter(c => c.parent_id === category.id)
                .sort((a, b) => a.order - b.order)
                .forEach(subCategory => {
                    tree.push(subCategory);
                });
        }
    });
    return tree;
});

const selectedCategoryInfo = computed(() => {
    if (!props.selectedCategoryId) return null;
    const category = categoriesArray.value.find(c => c.id === props.selectedCategoryId);
    if (!category) return null;

    const parentCategory = category.parent_id ? categoriesArray.value.find(c => c.id === category.parent_id) : null;
    const subCategories = categoriesArray.value.filter(c => c.parent_id === category.id);
    
    return { ...category, parentCategory, subCategories };
});

// --- LOGIC THU/MỞ RỘNG DANH MỤC ---

// Kiểm tra xem danh mục có danh mục con không
const hasSubCategories = (id) => {
    return categoriesArray.value.some(c => c.parent_id === id);
};

// Chuyển đổi trạng thái mở/đóng của một danh mục cha
const toggleExpand = (id) => {
    if (expandedCategories.value.includes(id)) {
        expandedCategories.value = expandedCategories.value.filter(catId => catId !== id);
    } else {
        expandedCategories.value = [...expandedCategories.value, id];
    }
};

// Mở rộng tất cả
function expandAll() {
    expandedCategories.value = categoriesArray.value
        .filter(c => c.level === 1 && hasSubCategories(c.id))
        .map(c => c.id);
    props.showToast('Đã mở rộng tất cả danh mục!', 'info');
}

// Thu gọn tất cả
function collapseAll() {
    expandedCategories.value = [];
    props.showToast('Đã thu gọn tất cả danh mục!', 'info');
}

// --- LOCAL HANDLERS ---
function viewProducts(id) { 
    const category = categoriesArray.value.find(c => c.id === id);
    props.showToast(`Chuyển đến sản phẩm của "${category.name}"...`, 'info'); 
}
function importCategories() { props.showToast('Tính năng import đang được phát triển!', 'info'); }
function exportCategories() { props.showToast('Đã xuất danh sách danh mục!', 'success'); }

</script>

<template>
    <!-- Page Header (Đã có style trong AdminLayout) -->
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><RouterLink to="/admin/dashboard">Dashboard</RouterLink></li>
                        <li class="breadcrumb-item active" aria-current="page">Quản lý danh mục</li>
                    </ol>
                </nav>
                <h2 class="mb-0">Quản lý danh mục sản phẩm</h2>
                <p class="text-muted mb-0">Tổ chức và quản lý danh mục theo cấp độ</p>
            </div>
            <button class="btn btn-primary" @click="props.onAddCategory">
                <fa :icon="['fas', 'plus']" class="me-2" />Thêm danh mục
            </button>
        </div>
    </div>

    <!-- Stats Cards (Giữ nguyên) -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card stats-card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div><h3 class="mb-1">{{ categoryStats.totalCategories }}</h3><p class="mb-0 opacity-75">Tổng danh mục</p></div>
                        <fa :icon="['fas', 'tags']" class="fa-2x opacity-75" />
                    </div>
                </div>
            </div>
        </div>
         <div class="col-md-3 mb-3">
            <div class="card stats-card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div><h3 class="mb-1">{{ categoryStats.level1Categories }}</h3><p class="mb-0 opacity-75">Danh mục cấp 1</p></div>
                        <fa :icon="['fas', 'layer-group']" class="fa-2x opacity-75" />
                    </div>
                </div>
            </div>
        </div>
         <div class="col-md-3 mb-3">
            <div class="card stats-card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div><h3 class="mb-1">{{ categoryStats.level2Categories }}</h3><p class="mb-0 opacity-75">Danh mục cấp 2</p></div>
                        <fa :icon="['fas', 'sitemap']" class="fa-2x opacity-75" />
                    </div>
                </div>
            </div>
        </div>
         <div class="col-md-3 mb-3">
            <div class="card stats-card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div><h3 class="mb-1">{{ categoryStats.activeProducts }}</h3><p class="mb-0 opacity-75">Sản phẩm đang bán</p></div>
                        <fa :icon="['fas', 'box']" class="fa-2x opacity-75" />
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Category Management -->
    <div class="row">
        <div class="col-md-8">
            <!-- Đã có sẵn .category-tree với nền trắng và shadow -->
            <div class="category-tree"> 
                <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
                    <h5 class="mb-0">Cây danh mục</h5>
                    <div class="d-flex gap-2">
                        <button class="btn btn-outline-primary btn-sm" @click="expandAll">
                            <fa :icon="['fas', 'expand-alt']" class="me-1" />Mở rộng tất cả
                        </button>
                        <button class="btn btn-outline-secondary btn-sm" @click="collapseAll">
                            <fa :icon="['fas', 'compress-alt']" class="me-1" />Thu gọn tất cả
                        </button>
                    </div>
                </div>
                
                <div id="categoryTree" class="pt-3">
                    <div 
                        v-for="category in categoryTreeData" 
                        :key="category.id"
                        :class="[
                            'category-item', 
                            `category-level-${category.level}`, 
                            { 'border-primary bg-light': selectedCategoryId === category.id }
                        ]" 
                        @click="props.onSelectCategory(category.id)"
                    >
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center flex-grow-1">
                                
                                <!-- Icon Toggle -->
                                <button v-if="category.level === 1 && hasSubCategories(category.id)" 
                                        @click.stop="toggleExpand(category.id)"
                                        class="btn btn-sm p-0 me-2 category-toggle-btn"
                                        :class="{'text-primary': expandedCategories.includes(category.id), 'text-muted': !expandedCategories.includes(category.id)}">
                                    <fa :icon="['fas', expandedCategories.includes(category.id) ? 'chevron-down' : 'chevron-right']" class="fa-xs" />
                                </button>
                                
                                <fa :icon="props.getFaIconArray(category.icon)" class="me-3" :style="{ color: category.color }" />
                                <div>
                                    <h6 class="mb-1">{{ category.name }}</h6>
                                    <small class="text-muted">
                                        {{ category.product_count }} sản phẩm
                                        <template v-if="!category.active"> • <span class="text-danger">Đã tắt</span></template>
                                    </small>
                                </div>
                            </div>
                            
                            <div class="category-actions">
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-outline-primary" @click.stop="props.onEditCategory(category.id)" title="Chỉnh sửa">
                                        <fa :icon="['fas', 'edit']" />
                                    </button>
                                    <template v-if="category.level === 1">
                                        <button class="btn btn-outline-success" @click.stop="props.onAddSubCategory(category.id)" title="Thêm danh mục con">
                                            <fa :icon="['fas', 'plus']" />
                                        </button>
                                    </template>
                                    <button class="btn btn-outline-danger" @click.stop="props.onDeleteCategory(category.id)" title="Xóa">
                                        <fa :icon="['fas', 'trash']" />
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card shadow-sm category-info-card">
                <div class="card-header bg-light">
                    <h6 class="mb-0 fw-bold">Thông tin danh mục</h6>
                </div>
                <div class="card-body" id="categoryInfo">
                    <template v-if="selectedCategoryInfo">
                        <div class="text-center mb-3 border-bottom pb-3">
                            <fa :icon="props.getFaIconArray(selectedCategoryInfo.icon)" class="fa-3x mb-2" :style="{ color: selectedCategoryInfo.color }" />
                            <h5>{{ selectedCategoryInfo.name }}</h5>
                            <span class="badge" :class="selectedCategoryInfo.active ? 'bg-success' : 'bg-secondary'">
                                {{ selectedCategoryInfo.active ? 'Đang hoạt động' : 'Đã tắt' }}
                            </span>
                        </div>
                        
                        <!-- Thông tin chi tiết -->
                        <div class="mb-3">
                            <strong>Mô tả:</strong>
                            <p class="text-muted mb-0">{{ selectedCategoryInfo.description || 'Chưa có mô tả' }}</p>
                        </div>
                        <div class="row mb-3">
                            <div class="col-6">
                                <strong>Cấp độ:</strong>
                                <p class="mb-0">Cấp {{ selectedCategoryInfo.level }}</p>
                            </div>
                            <div class="col-6">
                                <strong>Thứ tự:</strong>
                                <p class="mb-0">{{ selectedCategoryInfo.order }}</p>
                            </div>
                        </div>
                        <!-- ... (Parent category, product count) ... -->
                        <div class="mb-3">
                            <strong>Số sản phẩm:</strong>
                            <p class="mb-0 text-primary fw-bold">{{ selectedCategoryInfo.product_count }} sản phẩm</p>
                        </div>
                        
                        <div class="d-grid gap-2 border-top pt-3">
                            <button class="btn btn-primary btn-sm" @click="props.onEditCategory(selectedCategoryInfo.id)">
                                <fa :icon="['fas', 'edit']" class="me-2" />Chỉnh sửa
                            </button>
                            <template v-if="selectedCategoryInfo.level === 1">
                                <button class="btn btn-success btn-sm" @click="props.onAddSubCategory(selectedCategoryInfo.id)">
                                    <fa :icon="['fas', 'plus']" class="me-2" />Thêm danh mục con
                                </button>
                            </template>
                            <button class="btn btn-outline-info btn-sm" @click="viewProducts(selectedCategoryInfo.id)">
                                <fa :icon="['fas', 'box']" class="me-2" />Xem sản phẩm
                            </button>
                        </div>
                    </template>
                    <div v-else class="text-center text-muted py-4">
                        <fa :icon="['fas', 'info-circle']" class="fa-3x mb-3" />
                        <p>Chọn một danh mục để xem thông tin chi tiết</p>
                    </div>
                </div>
            </div>
            
            <div class="card mt-3 shadow-sm">
                <div class="card-header bg-light">
                    <h6 class="mb-0 fw-bold">Thao tác nhanh</h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <button class="btn btn-primary" @click="props.onAddCategory">
                            <fa :icon="['fas', 'plus']" class="me-2" />Thêm danh mục mới
                        </button>
                        <button class="btn btn-outline-info" @click="importCategories">
                            <fa :icon="['fas', 'file-import']" class="me-2" />Import danh mục
                        </button>
                        <button class="btn btn-outline-success" @click="exportCategories">
                            <fa :icon="['fas', 'file-export']" class="me-2" />Export danh mục
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
/* Các style được chuyển từ AdminLayout để đảm bảo tính cục bộ */

.page-header {
    background: white;
    border-radius: 10px;
    padding: 20px;
    margin-bottom: 20px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
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

/* -------------------------------------- */
/* CSS CỦA CATEGORIES PANEL */
/* -------------------------------------- */

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
    /* Điều chỉnh margin-left và border để tạo cấp độ */
    margin-left: 40px; 
    border-left: 4px solid #6c757d;
    background-color: #f8f9fa;
    /* Loại bỏ padding-left: 15px !important; vì nó đã được xử lý bởi margin-left */
}

.category-actions {
    opacity: 0;
    transition: opacity 0.3s ease;
}

.category-item:hover .category-actions {
    opacity: 1;
}

.breadcrumb-item a {
    color: #667eea; /* Màu primary */
    text-decoration: none;
}

/* Icon toggle button */
.category-toggle-btn {
    border: none;
    background: none;
    line-height: 1;
    margin-top: 2px;
}
</style>