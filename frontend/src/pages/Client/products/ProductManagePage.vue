<script setup>
import { ref, computed, reactive, onMounted } from 'vue';
import { useRouter, RouterLink } from 'vue-router';
import AppLayout from '@/Layouts/AppLayout.vue';
import { useAuthStore } from '@/stores/auth';
import { storeToRefs } from 'pinia';
import { useProductStore } from '@/stores/productStore';
import BasePagination from '@/components/BasePagination.vue'; 
import { getCurrentInstance } from 'vue';
const instance = getCurrentInstance();
const $toast = instance.appContext.config.globalProperties.$toast;
// --- STORES VÀ UTILITY ---
const router = useRouter();
const authStore = useAuthStore();
const productStore = useProductStore();

const { user, isLoggedIn } = storeToRefs(authStore); // Giả định user được lấy từ Store
const { myProducts, submissionError,myProductsStatusCounts,pagination } = storeToRefs(productStore); // Giả định user được lấy từ Store
// 🎯 STATE MANAGEMENT
const currentStatus = ref('active'); // Trạng thái tab hiện tại
const searchQuery = ref('');
const sortBy = ref('newest');
const stats = ref({ totalRevenue: '45.000.000₫', totalViews: '1.250' });

const BASE_STORAGE_URL = import.meta.env.VITE_BASE_STORAGE_URL || '/storage/';

// Placeholder cho Bootstrap Modals (cần được khởi tạo sau mount)
let createProductModalInstance = null;

// --- DỮ LIỆU MẪU VÀ LOGIC KHỞI TẠO ---

const editForm = reactive({
    id: null,
    title: '',
    price: 0,
    status: '', // Lưu status tạm thời
    description: '',
    is_negotiable: false,
});

const cleanPriceForInput = (formattedPrice) => {
    if (!formattedPrice) return null;
    let str = String(formattedPrice);
    let cleanedString = str.replace(/[^\d]/g, '');
    if (cleanedString === '') {
        return null;
    }
    return parseInt(cleanedString, 10);
};
// Hàm format thời gian (Đã chuyển đổi logic từ JS thuần)
const formatTime = (date) => {
    const now = new Date();
    const diff = now - new Date(date);
    const days = Math.floor(diff / (1000 * 60 * 60 * 24));

    if (days === 0) return 'Hôm nay';
    if (days === 1) return 'Hôm qua';
    if (days < 7) return `${days} ngày trước`;
    return new Date(date).toLocaleDateString('vi-VN');
};

// --- COMPUTED PROPERTIES (Tương đương filterProducts và updateTabCounts) ---

// 1. Đếm số lượng sản phẩm theo trạng thái
const tabCounts = computed(() => {
    // 💡 Dữ liệu này đã được fetch từ API và là tổng số toàn hệ thống
    return myProductsStatusCounts.value; 
});
// 2. Lọc và sắp xếp sản phẩm (Logic chính)
const filteredProducts = computed(() => {
    // 1. Lấy dữ liệu đã được lọc sẵn từ API
    let list = myProducts.value ? myProducts.value.slice() : [];

    // 2. Lọc theo tìm kiếm (Giữ lại logic này vì nó là lọc cục bộ)
    if (searchQuery.value) {
        const query = searchQuery.value.toLowerCase();
        list = list.filter(p => p.title.toLowerCase().includes(query));
    }
    
    // 3. Sắp xếp (Logic sắp xếp giữ nguyên)
    const sorters = {
        'oldest': (a, b) => new Date(a.created_date) - new Date(b.created_date),
        'price_high': (a, b) => cleanPriceForInput(b.price) - cleanPriceForInput(a.price),
        'price_low': (a, b) => cleanPriceForInput(a.price) - cleanPriceForInput(b.price),
        'views': (a, b) => b.views - a.views,
        'newest': (a, b) => new Date(b.created_date) - new Date(a.created_date),
    };

    // Chỉ cần sắp xếp mảng đã lọc theo tìm kiếm (nếu có)
    return list.sort(sorters[sortBy.value] || sorters['newest']);
});

// --- ACTION HANDLERS (CRUD & UI) ---

// UI Helpers (Đã chuyển từ JS thuần)
const getStatusBadgeClass = (status) => {
    const classes = { 'active': 'bg-success', 'draft': 'bg-warning', 'pending': 'bg-info', 'sold': 'bg-primary', 'hidden': 'bg-secondary' };
    return classes[status] || 'bg-secondary';
};

const getStatusText = (status) => {
    const texts = { 'active': 'Đang bán', 'draft': 'Bản nháp', 'pending': 'Đang duyệt', 'sold': 'Đã bán', 'hidden': 'Đã ẩn' };
    return texts[status] || status;
};

const getPerformanceClass = (performance) => {
    if (performance >= 80) return 'text-success';
    if (performance >= 60) return 'text-warning';
    return 'text-danger';
};

const changeTab = (status) => {
    currentStatus.value = status; // Cập nhật trạng thái tab UI

   productStore.fetchMyProducts(status, 1, sortBy.value);
};
const handlePageChange = (page) => {
    productStore.fetchMyProducts(currentStatus.value, page, sortBy.value);
};
// CRUD Handlers (Đã chuyển đổi logic từ JS thuần)
const startEdit = (id) => {
    const product = myProducts.value.find(p => p.id === id);
    if (product) {
        // Tắt edit mode của các sản phẩm khác (đảm bảo chỉ 1 form mở)
        myProducts.value.forEach(p => p.isEditing = false); ``

        editForm.id = product.id;
        editForm.title = product.title;
        editForm.price = cleanPriceForInput(product.price); 
        editForm.description = product.description;
        editForm.status = product.status;
        
        // Bật edit mode
        product.price = cleanPriceForInput(product.price);
        console.log(product.price);
        product.isEditing = true;
        // Lưu backup data
        product.originalData = { title: product.title, price: product.price, is_negotiable: product.is_negotiable, description: product.description };
        showToast('Chế độ chỉnh sửa đã bật!', 'info');
    }
};

const cancelEdit = (id) => {
    const product = myProducts.value.find(p => p.id === id);
    if (product && product.originalData) {
        // Khôi phục dữ liệu gốc
        Object.assign(product, product.originalData);
        product.isEditing = false;
        delete product.originalData;
        showToast('Đã hủy chỉnh sửa!', 'info');
    }
};

const saveProduct = async (id) => {
    const product = myProducts.value.find(p => p.id === id); 
    
    if (!product || editForm.id !== id) return showToast('Lỗi: Phiên chỉnh sửa không hợp lệ!', 'error');

    if (!editForm.title || editForm.price < 1000) {
        showToast('Tiêu đề hoặc Giá bán không hợp lệ!', 'error');
        return;
    }
    if (!product) return showToast('Sản phẩm không tồn tại!', 'error');

    // 1. Client-side Validation Tối thiểu
    if (!product.title || product.price < 1000) {
        showToast('Tiêu đề và Giá bán không hợp lệ!', 'error');
        return;
    }

    // 2. 🎯 TẠO FORMDATA CHO VIỆC CẬP NHẬT
    const formData = new FormData();
    formData.append('title', editForm.title);
    formData.append('price', editForm.price);
    formData.append('description', editForm.description || '');
    formData.append('status', editForm.status); 
    try {
        const updatedData = await productStore.updateProduct(product.id, formData);
        product.isEditing = false;
        delete product.originalData;
        productStore.updateProductInList(updatedData);
        $toast.success('Đã lưu thay đổi thành công!');

    } catch (error) {
        if (error.message !== 'Unauthorized') {
            $toast.error('Lưu thất bại !');
            console.log(submissionError.value);
        }
        cancelEdit(product.id);
    }
};

// Các hàm thao tác khác
const changeProductStatus = (id, newStatus) => {
    const product = myProducts.value.find(p => p.id === id);
    if (product) {
        product.status = newStatus;
        console.log(`Đã chuyển sản phẩm sang trạng thái ${newStatus}!`, 'success');
    }
};

const deleteProduct = (id) => {
    if (confirm('Bạn có chắc muốn xóa sản phẩm này?')) {
        myProducts.value = myProducts.value.filter(p => p.id !== id);
        showToast('Đã xóa sản phẩm!', 'success');
    }
};

const getImageUrl = (imagePath) => {
    if (!imagePath) {
        return 'http://127.0.0.1:8000/storage/products/default-product.jpg';
    }
    const cleanedPath = imagePath.startsWith('/') ? imagePath.substring(1) : imagePath;
    return BASE_STORAGE_URL.endsWith('/')
        ? BASE_STORAGE_URL + cleanedPath
        : BASE_STORAGE_URL + '/' + cleanedPath;
};
// Modal actions
const goToCreatePage = (type) => {
    createProductModalInstance?.hide();
    if (type === 'scratch') {
        router.push({ name: 'products.create' }); // Giả định route này tồn tại
    } else {
        router.push({ name: 'products.copy' }); // Giả định route này tồn tại
    }
};

// Utility (Bạn cần đảm bảo showToast được định nghĩa trong Layout hoặc ở đây)
function showToast(message, type = 'info') {
    // Logic Toast tạm thời (Chắc chắn đã được định nghĩa ở đây hoặc truyền từ Layout)
    console.log(`[TOAST - ${type.toUpperCase()}]: ${message}`);
}

// --- LIFECYCLE ---
onMounted(() => {
    console.log('ddd');
    productStore.fetchMyProductsStatusCounts();
    productStore.fetchMyProducts();
    // Khởi tạo Bootstrap Modal instance
    if (window.bootstrap) {
        const modalElement = document.getElementById('createProductModal');
        if (modalElement) {
            createProductModalInstance = new window.bootstrap.Modal(modalElement);
        }
    }
});
</script>

<template>
    <AppLayout :user="user" title="Sản phẩm của tôi">

        <div class="row mb-4">
            <div class="col-md-3 mb-3">
                <div class="card stats-card h-100">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <p class="card-text opacity-75 mb-1">Tổng doanh thu</p>
                            <h4 class="card-title mb-0" id="totalRevenue">{{ stats.totalRevenue }}</h4>
                        </div>
                        <fa :icon="['fas', 'coins']" class="fa-2x opacity-75" />
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card h-100">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <p class="card-text text-muted mb-1">Đang bán</p>
                            <h4 class="card-title mb-0 text-success" id="activeCount">{{ tabCounts.active }}</h4>
                        </div>
                        <fa :icon="['fas', 'store']" class="fa-2x text-success" />
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card h-100">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <p class="card-text text-muted mb-1">Đã bán</p>
                            <h4 class="card-title mb-0 text-primary" id="soldCount">{{ tabCounts.sold }}</h4>
                        </div>
                        <fa :icon="['fas', 'check-circle']" class="fa-2x text-primary" />
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card h-100">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <p class="card-text text-muted mb-1">Lượt xem</p>
                            <h4 class="card-title mb-0 text-info" id="totalViews">{{ stats.totalViews }}</h4>
                        </div>
                        <fa :icon="['fas', 'eye']" class="fa-2x text-info" />
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow">
            <div class="card-header bg-white">
                <ul class="nav nav-tabs card-header-tabs" id="statusTabs">
                    <li v-for="(count, status) in tabCounts" :key="status" class="nav-item">
                        <a class="nav-link" :class="{ 'active': currentStatus === status }"
                            @click.prevent="changeTab(status)">
                            <fa :icon="['fas', status === 'active' ? 'store' : status === 'draft' ? 'edit' : status === 'pending' ? 'clock' : status === 'sold' ? 'check-circle' : 'eye-slash']"
                                class="me-2" />
                            {{ getStatusText(status) }}
                            <span class="badge bg-secondary ms-2">{{ count }}</span>
                        </a>
                    </li>
                </ul>
            </div>

            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="input-group">
                            <span class="input-group-text">
                                <fa :icon="['fas', 'search']" />
                            </span>
                            <input type="text" class="form-control" placeholder="Tìm kiếm sản phẩm..."
                                v-model="searchQuery">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <select class="form-select" v-model="sortBy">
                            <option value="newest">Mới nhất</option>
                            <option value="oldest">Cũ nhất</option>
                            <option value="price_high">Giá cao nhất</option>
                            <option value="price_low">Giá thấp nhất</option>
                            <option value="views">Nhiều lượt xem</option>
                        </select>
                    </div>
                    <div class="col-md-3 d-flex align-items-center justify-content-end">
                        <div class="text-muted">
                            Hiển thị <span id="resultCount">{{ filteredProducts.length }}</span> sản phẩm
                        </div>
                    </div>
                </div>

                <div class="tab-content">
                    <div class="tab-pane fade show active">
                        <div id="productsContainer">
                            <template v-if="filteredProducts.length > 0">
                                <div v-for="product in filteredProducts" :key="product.id" class="col-12 mb-3">
                                    <div class="card product-card shadow-sm">
                                        <div class="card-body">
                                            <div class="row align-items-center">
                                                <div class="col-md-2 col-3">
                                                    <img :src="getImageUrl(product.product_image)"
                                                        class="img-fluid rounded"
                                                        style="height: 80px; width: 100%; object-fit: cover;"
                                                        :alt="product.title">
                                                </div>

                                                <div class="col-md-6 col-9">
                                                    <template v-if="product.isEditing">
                                                        <div class="mb-2">
                                                            <input type="text" class="form-control form-control-sm"
                                                                v-model="editForm.title">
                                                                <p v-if="submissionError?.title" class="text-sm text-red-600">{{ submissionError['title'][0]
                            }}</p>
                                                        </div>
                                                        <div class="row mb-2">
                                                            <div class="col-md-6">
                                                                <div class="input-group input-group-sm">
                                                                    <input type="number" class="form-control"
                                                                        v-model.number="editForm.price">
                                                                    <span class="input-group-text">₫</span>
                                                                    <p v-if="submissionError?.price" class="text-sm text-red-600">{{ submissionError['price'][0]
                            }}</p>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <select class="form-select form-select-sm"
                                                                    v-model="editForm.status">
                                                                    <option value="active">Đang bán</option>
                                                                    <option value="draft">Bản nháp</option>
                                                                    <option value="pending">Đang duyệt</option>
                                                                    <option value="hidden">Đã ẩn</option>
                                                                    <option value="sold">Đã bán</option>
                                                                </select>
                                                                <p v-if="submissionError?.status" class="text-sm text-red-600">{{ submissionError['status'][0]
                            }}</p>
                                                            </div>
                                                        </div>
                                                        <div class="mb-2">
                                                            <textarea class="form-control form-control-sm" rows="2"
                                                                placeholder="Mô tả sản phẩm..."
                                                                v-model="editForm.description"></textarea>
                                                                <p v-if="submissionError?.description" class="text-sm text-red-600">{{ submissionError['description'][0]
                            }}</p>
                                                        </div>
                                                    </template>
                                                    <template v-else>
                                                        <h6 class="mb-1 fw-bold">{{ product.title }}</h6>
                                                        <div class="d-flex align-items-center mb-2">
                                                            <span class="h5 text-primary mb-0 me-3">{{
                                                                product.price }}</span>
                                                            <span v-if="product.is_negotiable"
                                                                class="badge bg-success me-2">Có TL</span>
                                                            <span class="badge"
                                                                :class="getStatusBadgeClass(product.status)">
                                                                {{ getStatusText(product.status) }}
                                                            </span>
                                                        </div>
                                                        <p v-if="product.description" class="text-muted small mb-2">{{
                                                            product.description }}</p>
                                                    </template>

                                                    <div
                                                        class="d-flex justify-content-between align-items-center text-muted small">
                                                        <span>
                                                            <fa :icon="['fas', 'calendar']" class="me-1" />{{
                                                                formatTime(product.created_date) }}
                                                        </span>
                                                        <!-- <span>
                                                            <fa :icon="['fas', 'map-marker-alt']" class="me-1" />{{
                                                            product.location }}
                                                        </span> -->
                                                    </div>
                                                </div>

                                                <div class="col-md-2 col-6 text-center">
                                                    <div class="d-flex justify-content-center gap-2 mb-1">
                                                        <span class="badge bg-light text-dark">
                                                            <fa :icon="['fas', 'eye']" class="me-1" />{{ product.views
                                                            }}
                                                        </span>
                                                        <span class="badge bg-light text-dark">
                                                            <fa :icon="['fas', 'heart']" class="me-1" />{{
                                                                product.favorites }}
                                                        </span>
                                                        <span v-if="product.messages > 0" class="badge bg-primary">
                                                            <fa :icon="['fas', 'comment']" class="me-1" />{{
                                                                product.messages }}
                                                        </span>
                                                    </div>
                                                    <small class="text-muted">
                                                        Hiệu suất: <span
                                                            :class="getPerformanceClass(product.performance)">{{
                                                                product.performance }}%</span>
                                                    </small>
                                                </div>

                                                <div class="col-md-2 col-6 text-end">
                                                    <template v-if="product.isEditing">
                                                        <div class="btn-group-vertical btn-group-sm" role="group">
                                                            <button class="btn btn-success btn-sm"
                                                                @click="saveProduct(product.id)">
                                                                <fa :icon="['fas', 'check']" class="me-1" />Lưu
                                                            </button>
                                                            <button class="btn btn-secondary btn-sm"
                                                                @click="cancelEdit(product.id)">
                                                                <fa :icon="['fas', 'times']" class="me-1" />Hủy
                                                            </button>
                                                        </div>
                                                    </template>
                                                    <template v-else>
                                                        <div class="btn-group btn-group-sm" role="group">
                                                            <button class="btn btn-outline-primary"
                                                                @click="startEdit(product.id)" title="Chỉnh sửa">
                                                                <fa :icon="['fas', 'edit']" />
                                                            </button>
                                                            <div class="btn-group btn-group-sm" role="group">
                                                                <button
                                                                    class="btn btn-outline-secondary dropdown-toggle"
                                                                    data-bs-toggle="dropdown" title="Thêm">
                                                                    <fa :icon="['fas', 'ellipsis-v']" />
                                                                </button>
                                                                <ul class="dropdown-menu dropdown-menu-end">
                                                                    <li><a class="dropdown-item" href="#"
                                                                            @click.prevent="changeProductStatus(product.id, 'active')">
                                                                            <fa :icon="['fas', 'refresh']"
                                                                                class="me-2 text-success" />Đẩy tin
                                                                        </a></li>
                                                                    <li><a class="dropdown-item" href="#"
                                                                            @click.prevent="duplicateProduct(product.id)">
                                                                            <fa :icon="['fas', 'copy']"
                                                                                class="me-2 text-info" />Sao chép
                                                                        </a></li>
                                                                    <li><a class="dropdown-item" href="#"
                                                                            @click.prevent="viewAnalytics(product.id)">
                                                                            <fa :icon="['fas', 'chart-line']"
                                                                                class="me-2 text-primary" />Thống kê
                                                                        </a></li>
                                                                    <li>
                                                                        <hr class="dropdown-divider">
                                                                    </li>
                                                                    <template v-if="product.status === 'pending'">
                                                                        <li><a class="dropdown-item" href="#"
                                                                                @click.prevent="viewReviewStatus(product.id)">
                                                                                <fa :icon="['fas', 'info-circle']"
                                                                                    class="me-2 text-info" />Xem trạng
                                                                                thái duyệt
                                                                            </a></li>
                                                                        <li><a class="dropdown-item" href="#"
                                                                                @click.prevent="changeProductStatus(product.id, 'draft')">
                                                                                <fa :icon="['fas', 'arrow-left']"
                                                                                    class="me-2 text-warning" />Rút về
                                                                                nháp
                                                                            </a></li>
                                                                    </template>
                                                                    <template v-else-if="product.status !== 'active'">
                                                                        <li><a class="dropdown-item" href="#"
                                                                                @click.prevent="changeProductStatus(product.id, 'active')">
                                                                                <fa :icon="['fas', 'eye']"
                                                                                    class="me-2 text-success" />Hiển thị
                                                                            </a></li>
                                                                        <template v-if="product.status === 'draft'">
                                                                            <li><a class="dropdown-item" href="#"
                                                                                    @click.prevent="changeProductStatus(product.id, 'pending')">
                                                                                    <fa :icon="['fas', 'paper-plane']"
                                                                                        class="me-2 text-info" />Gửi
                                                                                    duyệt
                                                                                </a></li>
                                                                        </template>
                                                                    </template>
                                                                    <template v-if="product.status === 'active'">
                                                                        <li><a class="dropdown-item" href="#"
                                                                                @click.prevent="changeProductStatus(product.id, 'hidden')">
                                                                                <fa :icon="['fas', 'eye-slash']"
                                                                                    class="me-2 text-warning" />Ẩn tin
                                                                            </a></li>
                                                                        <li><a class="dropdown-item" href="#"
                                                                                @click.prevent="changeProductStatus(product.id, 'sold')">
                                                                                <fa :icon="['fas', 'check']"
                                                                                    class="me-2 text-primary" />Đánh dấu
                                                                                đã bán
                                                                            </a></li>
                                                                    </template>
                                                                    <li>
                                                                        <hr class="dropdown-divider">
                                                                    </li>
                                                                    <li><a class="dropdown-item text-danger" href="#"
                                                                            @click.prevent="deleteProduct(product.id)">
                                                                            <fa :icon="['fas', 'trash']" class="me-2" />
                                                                            Xóa
                                                                        </a></li>
                                                                </ul>
                                                            </div>
                                                        </div>
                                                    </template>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </template>
                            <div v-else class="col-12">
                                <div class="text-center py-5" id="emptyState">
                                    <div class="mb-4">
                                        <fa :icon="['fas', 'box-open']" class="fa-4x text-muted" />
                                    </div>
                                    <h5 class="text-muted mb-3">TIN</h5>
                                    <p class="text-muted mb-4">TIN</p>
                                    <button class="btn btn-primary" data-bs-toggle="modal"
                                        data-bs-target="#createProductModal">
                                        <fa :icon="['fas', 'plus']" class="me-2" />Đăng tin ngay
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <BasePagination 
                    :pagination="pagination"
                    :on-page-change="handlePageChange"
                />
            </div>

            <div class="modal fade" id="createProductModal" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Đăng tin mới</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <p class="text-muted mb-4">Chọn cách tạo sản phẩm mới:</p>

                            <div class="d-grid gap-3">
                                <button class="btn btn-outline-primary p-4 text-start"
                                    @click="goToCreatePage('scratch')">
                                    <div class="d-flex align-items-center">
                                        <fa :icon="['fas', 'plus']" class="fa-2x me-3 text-muted" />
                                        <div>
                                            <h6 class="mb-1">Tạo từ đầu</h6>
                                            <small class="text-muted">Nhập thông tin sản phẩm mới</small>
                                        </div>
                                    </div>
                                </button>

                                <button class="btn btn-outline-success p-4 text-start" @click="goToCreatePage('copy')">
                                    <div class="d-flex align-items-center">
                                        <fa :icon="['fas', 'copy']" class="fa-2x me-3 text-muted" />
                                        <div>
                                            <h6 class="mb-1">Sao chép từ tin cũ</h6>
                                            <small class="text-muted">Dựa trên sản phẩm đã đăng</small>
                                        </div>
                                    </div>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
