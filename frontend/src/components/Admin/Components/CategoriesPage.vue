<script setup>
import { useCategoryStore } from '@/stores/categoryStore';
import { storeToRefs } from 'pinia';
import { onMounted, computed, ref } from 'vue';
import { RouterLink } from 'vue-router';
import { getCurrentInstance } from 'vue';

const instance = getCurrentInstance();
const $toast = instance.appContext.config.globalProperties.$toast;
const categoryStore = useCategoryStore();
const { categoriesArray, selectedCategoryInfo, expandedCategories, selectedCategoryId, submissionError, isCreating, isUpdating, isDeleting } = storeToRefs(categoryStore);
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

function openDeleteModal(id) {
    deletingCategoryId.value = id;
    showDeleteModal.value = true;
}

const showModal = ref(false);
const isEditing = ref(false);
const showDeleteModal = ref(false);
const deletingCategoryId = ref(null);

const form = ref({
    id: null,
    name: "",
    parent_id: "",
    description: "",
    icon: "fas fa-tag",
    color: "#0d6efd",
    display_order: 0,
    is_visible: true
});

// Chỉ lấy level = 1
const level1Categories = computed(() =>
    categoryTreeData.value.filter(c => c.level === 1)
);

function openAddModal(parentId = null) {
    isEditing.value = false;
    form.value = {
        id: null,
        name: "",
        parent_id: parentId ?? "",
        description: "",
        icon: "fas fa-tag",
        color: "#0d6efd",
        display_order: 0,
        is_visible: true
    };
    showModal.value = true;
}

function openEditModal(id) {
    const cat = categoryTreeData.value.find(c => c.id === id);
    if (!cat) return;

    isEditing.value = true;
    form.value = {
        id: cat.id,
        name: cat.name,
        parent_id: cat.parent_id ?? "",
        description: cat.description ?? "",
        icon: cat.icon ?? "fas fa-tag",
        color: cat.color ?? "#0d6efd",
        display_order: cat.display_order ?? 0,
        is_visible: cat.is_visible === true,
        updated_at: cat.updated_at
    };
    showModal.value = true;
}

async function saveCategory() {
    const formData = new FormData();
    Object.keys(form.value).forEach(key => {
        let value = form.value[key];
        if (typeof value === 'boolean') value = value ? 1 : 0;
        formData.append(key, value);
    });

    try {
        if (isEditing.value) {
            await categoryStore.updateCategory(form.value.id, formData);
            $toast.success('Cập nhật thành công!');
        } else {
            await categoryStore.createCategory(formData);
            $toast.success('Thêm danh mục thành công!');
        }
        showModal.value = false;
    } catch (error) {
        const errorMessage = categoryStore.submissionError?.general?.[0] || 'Vui lòng kiểm tra lại dữ liệu.';
        $toast.error(errorMessage);
        console.log('Backend error:', categoryStore.submissionError);
    }
}


async function confirmDeleteCategory() {
    try {
        await categoryStore.deleteCategory(deletingCategoryId.value);
        showDeleteModal.value = false;
        deletingCategoryId.value = null;
        $toast.success('Xóa danh mục thành công!');
    } catch (error) {
        const errorMessage = categoryStore.submissionError?.general?.[0] || 'Lỗi: Danh mục đã bị xóa hoặc có ràng buộc.';
        $toast.error(errorMessage);

        await categoryStore.fetchCategories(true); 
    } 
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
            <button class="btn btn-primary" @click="openAddModal()">
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
                                        <button class="btn btn-outline-primary" @click.stop="openEditModal(category.id)"
                                            title="Chỉnh sửa">
                                            <fa :icon="['fas', 'edit']" />
                                        </button>
                                        <template v-if="category.level === 1">
                                            <button class="btn btn-outline-success"
                                                @click.stop="openAddModal(category.id)" title="Thêm danh mục con">
                                                <fa :icon="['fas', 'plus']" />
                                            </button>
                                        </template>
                                        <button class="btn btn-outline-danger"
                                            @click.stop="openDeleteModal(category.id)" title="Xóa">
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
                                :style="{ color: selectedCategoryInfo.color }" />

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
                            <button class="btn btn-primary btn-sm" @click="openEditModal(selectedCategoryInfo.id)">
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
    <!-- CATEGORY MODAL -->
    <div v-if="showModal" class="modal fade show" style="display: block;" aria-modal="true" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">
                        {{ isEditing ? 'Chỉnh sửa danh mục' : 'Thêm danh mục' }}
                    </h5>
                    <button type="button" class="btn-close" @click="showModal = false"></button>
                </div>

                <form @submit.prevent="saveCategory">
                    <div class="modal-body">

                        <!-- NAME -->
                        <div class="mb-3">
                            <label class="form-label">
                                Tên danh mục <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control" v-model="form.name" 
                                :class="{'is-invalid': submissionError?.name}" />
                            <div v-if="submissionError?.name" class="text-danger mt-1">
                                {{ submissionError.name[0] }}
                            </div>
                        </div>
                        <!-- PARENT -->
                        <div class="mb-3">
                            <label class="form-label">Danh mục cha</label>
                            <select class="form-select" v-model="form.parent_id"
                                :class="{'is-invalid': submissionError?.parent_id}">
                                <option value="">-- Danh mục gốc (Cấp 1) --</option>
                                <option v-for="c in level1Categories" :key="c.id" :value="c.id">
                                    {{ c.name }}
                                </option>
                            </select>
                            <div v-if="submissionError?.parent_id" class="text-danger mt-1">
                                {{ submissionError.parent_id[0] }}
                            </div>
                        </div>

                        <!-- DESCRIPTION -->
                        <div class="mb-3">
                            <label class="form-label">Mô tả</label>
                            <textarea class="form-control" rows="3" v-model="form.description"
                                :class="{'is-invalid': submissionError?.description}"></textarea>
                            <div v-if="submissionError?.description" class="text-danger mt-1">
                                {{ submissionError.description[0] }}
                            </div>
                        </div>

                        <!-- ICON -->
                        <div class="mb-3">
                            <label class="form-label">Icon</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i :class="form.icon"></i>
                                </span>
                                <input type="text" class="form-control" placeholder="fas fa-tag" v-model="form.icon"
                                    :class="{'is-invalid': submissionError?.icon}">
                            </div>
                            <small class="text-muted">FontAwesome (vd: fas fa-book, fas fa-laptop)</small>
                            <div v-if="submissionError?.icon" class="text-danger mt-1">
                                {{ submissionError.icon[0] }}
                            </div>
                        </div>

                        <!-- COLOR -->
                        <div class="mb-3">
                            <label class="form-label">Màu sắc</label>
                            <input type="color" class="form-control form-control-color" v-model="form.color"
                                :class="{'is-invalid': submissionError?.color}">
                            <div v-if="submissionError?.color" class="text-danger mt-1">
                                {{ submissionError.color[0] }}
                            </div>
                        </div>

                        <!-- ORDER -->
                        <div class="mb-3">
                            <label class="form-label">Thứ tự hiển thị</label>
                            <input type="number" class="form-control" v-model="form.display_order" min="0"
                                :class="{'is-invalid': submissionError?.display_order}">
                            <div v-if="submissionError?.display_order" class="text-danger mt-1">
                                {{ submissionError.display_order[0] }}
                            </div>
                        </div>

                        <!-- ACTIVE -->
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" v-model="form.is_visible"
                                :class="{'is-invalid': submissionError?.is_visible}">
                            <label class="form-check-label">Kích hoạt danh mục</label>
                            <div v-if="submissionError?.is_visible" class="text-danger mt-1">
                                {{ submissionError.is_visible[0] }}
                            </div>
                        </div>
                        
                        <!-- LỖI CHUNG (409 Conflict hoặc lỗi không thuộc field cụ thể) -->
                        <div v-if="submissionError?.general" class="alert alert-danger mt-3" role="alert">
                            <p class="mb-0" v-for="(msg, index) in submissionError.general" :key="index">{{ msg }}</p>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" @click="showModal = false">Hủy</button>
                        <button type="submit" class="btn btn-primary"
                            :class="{ 'btn-loading': isCreating || isUpdating }" :disabled="isCreating || isUpdating">
                            <i v-if="!(isCreating || isUpdating)" class="fas fa-save me-2"></i>
                            <span v-if="!(isCreating || isUpdating)">Lưu danh mục</span>
                            <span v-else class="spinner"></span>
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
    <div v-if="showDeleteModal" class="modal fade show" style="display: block;" aria-modal="true" role="dialog">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Xác nhận xóa</h5>
                    <button type="button" class="btn-close" @click="showDeleteModal = false"></button>
                </div>
                <div class="modal-body">
                    <p>Bạn có chắc chắn muốn xóa danh mục này không?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" @click="showDeleteModal = false">Hủy</button>
                    <button type="button" class="btn btn-danger" @click="confirmDeleteCategory"
                        :class="{ 'btn-loading': isDeleting }" :disabled="isDeleting">
                        <i v-if="!(isDeleting)" class="fas fa-save me-2"></i>
                        <span v-if="!(isDeleting)">Xóa</span>
                        <span v-else class="spinner"></span>
                    </button>

                </div>
            </div>
        </div>
    </div>
    <div v-if="showDeleteModal" class="modal-backdrop fade show"></div>

    <!-- BACKDROP -->
    <div v-if="showModal" class="modal-backdrop fade show"></div>

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
/* Hiệu ứng loading */
.btn-loading {
    position: relative;
    pointer-events: none;
    opacity: 0.9;
}

/* Spinner nằm trong nút */
.btn-loading .spinner {
    display: inline-block;
    width: 1rem;
    height: 1rem;
    margin-right: 8px;
    border: 2px solid rgba(255, 255, 255, 0.35);
    border-top-color: #fff; /* màu xoay */
    border-radius: 50%;
    animation: spin 0.6s linear infinite;
    vertical-align: middle;
}

/* Text mờ nhẹ khi loading */
.btn-loading .btn-text {
    opacity: 0.6;
}

/* Animation */
@keyframes spin {
    100% {
        transform: rotate(360deg);
    }
}
</style>