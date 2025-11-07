<script setup>
import { computed } from 'vue';
import { RouterLink } from 'vue-router'; 

// Props nhận từ RouterView trong AdminLayout
const props = defineProps({
    categories: Array,
    getFaIconArray: Function,
    showToast: Function,
    selectedCategoryId: Number,
    onSelectCategory: Function,
});

// --- COMPUTED PROPERTIES ---
const categoriesArray = props.categories || [];
alert(categoriesArray);
const categoryStats = computed(() => {
    const categoriesArray = props.categories || [];
    const level1Count = categoriesArray.filter(c => c.level === 1).length;
    const level2Count = categoriesArray.filter(c => c.level === 2).length;
    const totalProducts = categoriesArray.reduce((sum, c) => sum + c.product_count, 0);
    return {
        totalCategories: categoriesArray.length,
        level1Categories: level1Count,
        level2Categories: level2Count,
        activeProducts: totalProducts.toLocaleString('en-US')
    };
});

const categoryTreeData = computed(() => {
    const categoriesArray = props.categories || []; ``
    const level1Categories = categoriesArray.filter(c => c.level === 1).sort((a, b) => a.order - b.order);
    const tree = [];
    level1Categories.forEach(category => {
        tree.push(category);
        categoriesArray
            .filter(c => c.parent_id === category.id)
            .sort((a, b) => a.order - b.order)
            .forEach(subCategory => {
                tree.push(subCategory);
            });
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

// --- HANDLERS DUMMY (Sử dụng showToast) ---
function expandAll() { props.showToast('Đã mở rộng tất cả danh mục!', 'info'); }
function collapseAll() { props.showToast('Đã thu gọn tất cả danh mục!', 'info'); }
function importCategories() { props.showToast('Tính năng import đang được phát triển!', 'info'); }
function exportCategories() { props.showToast('Đã xuất danh sách danh mục!', 'success'); }
function viewProducts(id) { 
    const category = categoriesArray.find(c => c.id === id);
    props.showToast(`Chuyển đến sản phẩm của "${category.name}"...`, 'info'); 
}
function handleCategorySelect(id) { 
    // GỌI HÀM NẰM TRONG ADMINLAYOUT
    props.onSelectCategory(id); 
    alert(`Đã chọn ID: ${id}. Logic cập nhật nằm ở AdminLayout.`);
}
</script>

<template>
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

    <div class="row">
        <div class="col-md-8">
            <div class="category-tree">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="mb-0">Cây danh mục</h5>
                    <div class="d-flex gap-2">
                        <button class="btn btn-outline-primary btn-sm" @click="expandAll">
                            <fa :icon="['fas', 'expand-alt']" class="me-1" />Mở rộng tất cả
                        </button>
                        <button class="btn btn-outline-secondary btn-sm" @click="collapseAll">
                            <fa :icon="['fas', 'compress-aler']" class="me-1" />Thu gọn tất cả
                        </button>
                    </div>
                </div>
                
                <div id="categoryTree">
                    <div 
                        v-for="category in categoryTreeData" 
                        :key="category.id"
                        :class="['category-item', `category-level-${category.level}`, { 'border-primary bg-light': selectedCategoryId === category.id }]" 
                        @click="handleCategorySelect(category.id)"
                    >
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
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
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">Thông tin danh mục</h6>
                </div>
                <div class="card-body" id="categoryInfo">
                    <template v-if="selectedCategoryInfo">
                        <div class="text-center mb-3">
                            <fa :icon="props.getFaIconArray(selectedCategoryInfo.icon)" class="fa-3x mb-2" :style="{ color: selectedCategoryInfo.color }" />
                            <h5>{{ selectedCategoryInfo.name }}</h5>
                            <span class="badge" :class="selectedCategoryInfo.active ? 'bg-success' : 'bg-secondary'">
                                {{ selectedCategoryInfo.active ? 'Đang hoạt động' : 'Đã tắt' }}
                            </span>
                        </div>
                        
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
                        <template v-if="selectedCategoryInfo.parentCategory">
                            <div class="mb-3">
                                <strong>Danh mục cha:</strong>
                                <p class="mb-0">
                                    <fa :icon="getFaIconArray(selectedCategoryInfo.parentCategory.icon)" class="me-1" />
                                    {{ selectedCategoryInfo.parentCategory.name }}
                                </p>
                            </div>
                        </template>
                        <div class="mb-3">
                            <strong>Số sản phẩm:</strong>
                            <p class="mb-0 text-primary fw-bold">{{ selectedCategoryInfo.product_count }} sản phẩm</p>
                        </div>
                        
                        <div class="d-grid gap-2">
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
            
            <div class="card mt-3">
                <div class="card-header">
                    <h6 class="mb-0">Thao tác nhanh</h6>
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
/* (Giữ lại CSS cho các thành phần quản lý danh mục) */
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
/* ... (Các style khác: .category-level-1, .category-actions) ... */
</style>