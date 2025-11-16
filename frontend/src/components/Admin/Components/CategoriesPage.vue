<script setup>
import { useCategoryStore } from '@/stores/categoryStore';
import { storeToRefs } from 'pinia';
import { onMounted, computed } from 'vue';
import { RouterLink } from 'vue-router';

const categoryStore = useCategoryStore();
const { categoriesArray, selectedCategoryInfo, expandedCategories, selectedCategoryId } = storeToRefs(categoryStore);
const categoryTreeData = computed(() => categoryStore.categoryTreeData);
onMounted(async () => {
    await categoryStore.fetchCategories(true); // fetch full tree
    console.log('categoriesTree:', categoryStore.categoriesTree);
    console.log('categoriesTree:', categoryTreeData);
});
// --- LOCAL HANDLERS ---
function viewProducts(id) {
    const category = categoryTreeData.value.find(c => c.id === id);
    alert(`Chuyển đến sản phẩm của "${category.name}"...`);
}

function toggleExpand(id) {
    categoryStore.toggleExpand(id);
}

function expandAll() {
    categoryStore.expandAll();
}

function collapseAll() {
    categoryStore.collapseAll();
}

function hasSubCategories(id) {
    return categoryStore.hasSubCategories(id);
}

function selectCategory(id) {
    categoryStore.selectCategory(id);
}
</script>

<template>
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <RouterLink to="/admin/dashboard">Dashboard</RouterLink>
                        </li>
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

    <!-- Category Tree Panel -->
    <div class="row mt-4">
        <div class="col-md-8">
            <div class="category-tree">
                <div class="d-flex justify-content-between align-items-center mb-3 border-bottom pb-2">
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

                <div>

                    <div v-if="categoryTreeData.length > 0">
                        <div v-for="category in categoryTreeData" :key="category.id" :class="[
                            'category-item',
                            `category-level-${category.level}`,
                            { 'border-primary bg-light': selectedCategoryId === category.id }
                        ]" @click="selectCategory(category.id)">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center flex-grow-1">
                                    <!-- Icon Toggle -->
                                    <button v-if="category.level === 1 && categoryStore.hasSubCategories(category.id)"
                                        @click.stop="categoryStore.toggleExpand(category.id)"
                                        class="btn btn-sm p-0 me-2 category-toggle-btn" :class="{
                                            'text-primary': categoryStore.expandedCategories.includes(category.id),
                                            'text-muted': !categoryStore.expandedCategories.includes(category.id)
                                        }">
                                        <fa :icon="['fas', categoryStore.expandedCategories.includes(category.id) ? 'chevron-down' : 'chevron-right']"
                                            class="fa-xs" />
                                    </button>

                                    <fa :icon="category.icon" class="me-3" :style="{ color: category.color }" />
                                    <div>
                                        <h6 class="mb-1">{{ category.name }}</h6>
                                        <small class="text-muted">
                                            {{ category.total_products }} sản phẩm
                                            <template v-if="!category.is_visible"> • <span class="text-danger">Đã
                                                    tắt</span></template>
                                        </small>
                                    </div>
                                </div>
                                <div class="category-actions">
                                    <div class="btn-group btn-group-sm">
                                        <button class="btn btn-outline-primary"
                                            @click.stop="props.onEditCategory(category.id)" title="Chỉnh sửa">
                                            <fa :icon="['fas', 'edit']" />
                                        </button>
                                        <template v-if="category.level === 1">
                                            <button class="btn btn-outline-success"
                                                @click.stop="props.onAddSubCategory(category.id)"
                                                title="Thêm danh mục con">
                                                <fa :icon="['fas', 'plus']" />
                                            </button>
                                        </template>
                                        <button class="btn btn-outline-danger"
                                            @click.stop="props.onDeleteCategory(category.id)" title="Xóa">
                                            <fa :icon="['fas', 'trash']" />
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div v-else class="text-center text-muted py-4">
                        Đang tải danh mục...
                    </div>
                </div>
            </div>
        </div>

        <!-- Info Panel -->
        <div class="col-md-4">
            <div class="card shadow-sm category-info-card">
                <div class="card-header bg-light">
                    <h6 class="mb-0 fw-bold">Thông tin danh mục</h6>
                </div>
                <div class="card-body">
                    <template v-if="selectedCategoryInfo">
                        <div class="text-center mb-3 border-bottom pb-3">
                            <fa :icon="selectedCategoryInfo.icon" class="fa-3x mb-2"
                                :style="{ color:selectedCategoryInfo.color }" />

                            <h5>{{ selectedCategoryInfo.name }}</h5>

                            <span class="badge"
                                :class="selectedCategoryInfo.is_visible ? 'bg-success' : 'bg-secondary'">
                                {{ selectedCategoryInfo.is_visible ? 'Đang hoạt động' : 'Đã tắt' }}
                            </span>
                        </div>
                        <div class="mb-3">
                            <strong>Mô tả:</strong>
                            <p class="text-muted mb-0">
                                {{ selectedCategoryInfo.description || 'Chưa có mô tả' }}
                            </p>
                        </div>

                        <div class="row mb-3">
                            <div class="col-6">
                                <strong>Cấp độ:</strong>
                                <p class="mb-0">Cấp {{ selectedCategoryInfo.level }}</p>
                            </div>
                            <div class="col-6">
                                <strong>Thứ tự:</strong>
                                <p class="mb-0">{{ selectedCategoryInfo.display_order }}</p>
                            </div>
                        </div>

                        <div class="mb-3">
                            <strong>Số sản phẩm:</strong>
                            <p class="mb-0 text-primary fw-bold">
                                {{ selectedCategoryInfo.total_products }} sản phẩm
                            </p>
                        </div>

                        <div class="d-grid gap-2 border-top pt-3">
                            <button class="btn btn-primary btn-sm"
                                @click="props.onEditCategory(selectedCategoryInfo.id)">
                                <fa :icon="['fas', 'edit']" class="me-2" />
                                Chỉnh sửa
                            </button>

                            <template v-if="selectedCategoryInfo.level === 1">
                                <button class="btn btn-success btn-sm"
                                    @click="props.onAddSubCategory(selectedCategoryInfo.id)">
                                    <fa :icon="['fas', 'plus']" class="me-2" />
                                    Thêm danh mục con
                                </button>
                            </template>

                            <button class="btn btn-outline-info btn-sm" @click="viewProducts(selectedCategoryInfo.id)">
                                <fa :icon="['fas', 'box']" class="me-2" />
                                Xem sản phẩm
                            </button>
                        </div>
                    </template>

                    <div v-else class="text-center text-muted py-4">
                        <fa :icon="['fas', 'info-circle']" class="fa-3x mb-3" />
                        <p>Chọn một danh mục để xem thông tin chi tiết</p>
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
    color: #667eea;
    /* Màu primary */
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