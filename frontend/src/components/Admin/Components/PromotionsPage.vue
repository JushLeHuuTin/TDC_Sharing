<script setup>
import { ref, computed, onMounted } from 'vue';
import { RouterLink } from 'vue-router'; 

const props = defineProps({
    showToast: Function,
    getFaIconArray: Function,
});

const activeTab = ref('promotions');
const voucherSearchQuery = ref('');
const voucherFilterStatus = ref('');
const voucherFilterType = ref('');

const mockPromotions = ref([
    { id: 1, name: 'Khuyến mãi Tựu trường', type: 'percentage', value: 15, time: '1/9 - 15/9', usage: 120, status: 'Active' },
    { id: 2, name: 'Thứ 6 siêu sale', type: 'fixed', value: 50000, time: '20/10 - 20/11', usage: 85, status: 'Scheduled' },
    { id: 3, name: 'Giảm giá cuối kỳ', type: 'percentage', value: 10, time: 'Hết hạn', usage: 200, status: 'Expired' },
]);

const mockVouchers = ref([
    { id: 1, code: 'STUDENT10', name: 'Giảm 10% cho sách', type: 'percentage', value: '10%', qty: 50, remaining: 15, expiry: '2025-06-30', status: 'active' },
    { id: 2, code: 'FREESHIP', name: 'Miễn phí vận chuyển', type: 'shipping', value: 'Miễn phí', qty: 200, remaining: 0, expiry: '2025-12-31', status: 'used' },
    { id: 3, code: 'SAVE50K', name: 'Giảm 50.000', type: 'fixed', value: '50,000đ', qty: 100, remaining: 100, expiry: '2024-01-01', status: 'expired' },
]);

const promotionStats = computed(() => {
    const totalPromotions = mockPromotions.value.length;
    const activePromotions = mockPromotions.value.filter(p => p.status === 'Active').length;
    const totalVouchers = mockVouchers.value.length;
    const usedVouchers = mockVouchers.value.filter(v => v.remaining === 0).length;

    return { totalPromotions, activePromotions, totalVouchers, usedVouchers };
});

const filteredVouchers = computed(() => {
    let vouchers = mockVouchers.value;
    
    if (voucherSearchQuery.value) {
        const query = voucherSearchQuery.value.toLowerCase();
        vouchers = vouchers.filter(v => 
            v.code.toLowerCase().includes(query) || 
            v.name.toLowerCase().includes(query)
        );
    }

    if (voucherFilterStatus.value) {
        vouchers = vouchers.filter(v => v.status === voucherFilterStatus.value);
    }
    
    if (voucherFilterType.value) {
        vouchers = vouchers.filter(v => v.type === voucherFilterType.value);
    }

    return vouchers;
});

function createPromotion() {
    props.showToast('Mở form tạo Chương trình Khuyến mãi mới...', 'info');
}

function createVoucher() {
    props.showToast('Mở form tạo Voucher giảm giá mới...', 'info');
}

function editPromotion(id) {
    props.showToast(`Chỉnh sửa Khuyến mãi #${id}`, 'info');
}

function deletePromotion(id) {
    if (confirm('Bạn có chắc chắn muốn xóa khuyến mãi này?')) {
        props.showToast(`Đã xóa Khuyến mãi #${id}`, 'success');
    }
}

function editVoucher(id) {
    props.showToast(`Chỉnh sửa Voucher #${id}`, 'info');
}

function deleteVoucher(id) {
    if (confirm('Bạn có chắc chắn muốn xóa voucher này?')) {
        props.showToast(`Đã xóa Voucher #${id}`, 'success');
    }
}

function resetVoucherFilters() {
    voucherSearchQuery.value = '';
    voucherFilterStatus.value = '';
    voucherFilterType.value = '';
    props.showToast('Đã đặt lại bộ lọc Voucher', 'info');
}

function getStatusBadge(status) {
    switch(status.toLowerCase()) {
        case 'active': return 'bg-success';
        case 'expired': return 'bg-danger';
        case 'pending': return 'bg-warning';
        case 'used': return 'bg-secondary';
        default: return 'bg-info';
    }
}
</script>

<template>
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><RouterLink to="/admin/dashboard">Dashboard</RouterLink></li>
                        <li class="breadcrumb-item active" aria-current="page">Khuyến mãi & Voucher</li>
                    </ol>
                </nav>
                <h2 class="mb-0">Quản lý khuyến mãi & Voucher</h2>
                <p class="text-muted mb-0">Tạo và quản lý các chương trình khuyến mãi</p>
            </div>
            <div class="dropdown">
                <button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    <fa :icon="['fas', 'plus']" class="me-2" />Tạo mới
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="#" @click.prevent="createPromotion">
                        <fa :icon="['fas', 'percentage']" class="me-2" />Chương trình khuyến mãi
                    </a></li>
                    <li><a class="dropdown-item" href="#" @click.prevent="createVoucher">
                        <fa :icon="['fas', 'ticket-alt']" class="me-2" />Voucher giảm giá
                    </a></li>
                </ul>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card stats-card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="mb-1">{{ promotionStats.totalPromotions }}</h3>
                            <p class="mb-0 opacity-75">Tổng khuyến mãi</p>
                        </div>
                        <fa :icon="['fas', 'percentage']" class="fa-2x opacity-75" />
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card stats-card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="mb-1">{{ promotionStats.activePromotions }}</h3>
                            <p class="mb-0 opacity-75">Đang hoạt động</p>
                        </div>
                        <fa :icon="['fas', 'play-circle']" class="fa-2x opacity-75" />
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card stats-card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="mb-1">{{ promotionStats.totalVouchers }}</h3>
                            <p class="mb-0 opacity-75">Voucher đã tạo</p>
                        </div>
                        <fa :icon="['fas', 'ticket-alt']" class="fa-2x opacity-75" />
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card stats-card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="mb-1">{{ promotionStats.usedVouchers }}</h3>
                            <p class="mb-0 opacity-75">Đã sử dụng hết</p>
                        </div>
                        <fa :icon="['fas', 'check-circle']" class="fa-2x opacity-75" />
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm promotion-card">
        <div class="card-header bg-white border-bottom-0">
            <ul class="nav nav-tabs card-header-tabs" id="promotionTabs">
                <li class="nav-item">
                    <button 
                        class="nav-link" 
                        :class="{ 'active': activeTab === 'promotions' }"
                        @click="activeTab = 'promotions'"
                    >
                        <fa :icon="['fas', 'percentage']" class="me-2" />Chương trình khuyến mãi
                    </button>
                </li>
                <li class="nav-item">
                     <button 
                        class="nav-link" 
                        :class="{ 'active': activeTab === 'vouchers' }"
                        @click="activeTab = 'vouchers'"
                    >
                        <fa :icon="['fas', 'ticket-alt']" class="me-2" />Voucher giảm giá
                    </button>
                </li>
            </ul>
        </div>
        <div class="card-body bg-light-gray">
            <div id="promotionTabContent">
                
                <div v-if="activeTab === 'promotions'">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="mb-0 text-dark fw-bold">Danh sách chương trình khuyến mãi ({{ mockPromotions.length }})</h6>
                        <button class="btn btn-primary btn-sm" @click="createPromotion">
                            <fa :icon="['fas', 'plus']" class="me-2" />Tạo khuyến mãi
                        </button>
                    </div>
                    
                    <div class="table-responsive bg-white rounded shadow-sm">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Tên chương trình</th>
                                    <th>Loại giảm giá</th>
                                    <th>Giá trị</th>
                                    <th>Thời gian</th>
                                    <th>Sử dụng</th>
                                    <th>Trạng thái</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="promo in mockPromotions" :key="promo.id">
                                    <td>{{ promo.name }}</td>
                                    <td>
                                        <span class="badge" :class="promo.type === 'percentage' ? 'bg-info' : 'bg-success'">
                                            {{ promo.type === 'percentage' ? 'Phần trăm' : 'Cố định' }}
                                        </span>
                                    </td>
                                    <td>{{ promo.value }}{{ promo.type === 'percentage' ? '%' : ' VNĐ' }}</td>
                                    <td>{{ promo.time }}</td>
                                    <td>{{ promo.usage }} lần</td>
                                    <td>
                                        <span class="badge" :class="getStatusBadge(promo.status)">
                                            {{ promo.status }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <button class="btn btn-outline-primary" @click="editPromotion(promo.id)" title="Chỉnh sửa">
                                                <fa :icon="['fas', 'edit']" />
                                            </button>
                                            <button class="btn btn-outline-danger" @click="deletePromotion(promo.id)" title="Xóa">
                                                <fa :icon="['fas', 'trash']" />
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div v-else-if="activeTab === 'vouchers'">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="mb-0 text-dark fw-bold">Danh sách voucher giảm giá ({{ filteredVouchers.length }} / {{ mockVouchers.length }})</h6>
                        <button class="btn btn-primary btn-sm" @click="createVoucher">
                            <fa :icon="['fas', 'plus']" class="me-2" />Tạo voucher
                        </button>
                    </div>
                    
                    <div class="row mb-3 p-3 bg-white rounded shadow-sm align-items-center">
                        <div class="col-md-4">
                            <input type="text" class="form-control" placeholder="Tìm kiếm mã voucher..." v-model="voucherSearchQuery">
                        </div>
                        <div class="col-md-3">
                            <select class="form-select" v-model="voucherFilterStatus">
                                <option value="">Tất cả trạng thái</option>
                                <option value="active">Đang hoạt động</option>
                                <option value="expired">Đã hết hạn</option>
                                <option value="used">Đã sử dụng hết</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select class="form-select" v-model="voucherFilterType">
                                <option value="">Tất cả loại</option>
                                <option value="percentage">Phần trăm</option>
                                <option value="fixed">Số tiền cố định</option>
                                <option value="shipping">Miễn phí vận chuyển</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button class="btn btn-outline-secondary w-100" @click="resetVoucherFilters">
                                <fa :icon="['fas', 'undo']" /> Reset
                            </button>
                        </div>
                    </div>
                    
                    <div class="table-responsive bg-white rounded shadow-sm mt-3">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Mã voucher</th>
                                    <th>Tên voucher</th>
                                    <th>Loại & Giá trị</th>
                                    <th>Số lượng</th>
                                    <th>Thời hạn</th>
                                    <th>Trạng thái</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="voucher in filteredVouchers" :key="voucher.id">
                                    <td><span class="fw-bold text-primary">{{ voucher.code }}</span></td>
                                    <td>{{ voucher.name }}</td>
                                    <td>
                                        <span class="badge" :class="voucher.type === 'shipping' ? 'bg-warning' : 'bg-primary'">
                                            {{ voucher.type === 'percentage' ? 'Phần trăm' : voucher.type === 'fixed' ? 'Cố định' : 'Vận chuyển' }}
                                        </span>
                                        <span class="ms-2">{{ voucher.value }}</span>
                                    </td>
                                    <td>
                                        <span :class="voucher.remaining === 0 ? 'text-danger fw-bold' : ''">
                                            {{ voucher.remaining }} / {{ voucher.qty }}
                                        </span>
                                    </td>
                                    <td>{{ voucher.expiry }}</td>
                                    <td>
                                        <span class="badge" :class="getStatusBadge(voucher.status)">
                                            {{ voucher.status === 'active' ? 'Hoạt động' : voucher.status === 'expired' ? 'Hết hạn' : 'Đã dùng hết' }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <button class="btn btn-outline-primary" @click="editVoucher(voucher.id)" title="Chỉnh sửa">
                                                <fa :icon="['fas', 'edit']" />
                                            </button>
                                            <button class="btn btn-outline-danger" @click="deleteVoucher(voucher.id)" title="Xóa">
                                                <fa :icon="['fas', 'trash']" />
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>
</template>

<style scoped>

.page-header {
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    background: white;
    margin-bottom: 20px;
    padding: 20px;
}

.breadcrumb-item a {
    color: #667eea; 
    text-decoration: none;
}
.breadcrumb-item.active {
    color: #495057; 
}

.stats-card {
    border: none;
    border-radius: 15px;
    box-shadow: 0 6px 18px rgba(0, 0, 0, 0.1); 
    transition: transform 0.3s ease;
}

.stats-card:hover {
    transform: translateY(-5px);
}

/* 3. Main Card (Chứa Tabs và Content) */
.promotion-card {
    border-radius: 10px;
    border: 1px solid #dee2e6;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
}

/* 4. Tabs Navigation: Phân biệt rõ ràng */
.card-header-tabs {
    border-bottom: none;
}

.card-header-tabs .nav-link {
    color: #6c757d; /* Màu chữ xám mặc định */
    font-weight: 500;
    padding: 10px 15px;
    border: none;
    border-bottom: 3px solid transparent;
    transition: all 0.2s;
}

.card-header-tabs .nav-link:hover {
    color: #0d6efd;
}

.card-header-tabs .nav-link.active {
    background-color: transparent !important;
    color: #667eea; 
    border-bottom: 3px solid #667eea;
    font-weight: 600;
}

.card-body {
    padding: 20px;

}

.table-responsive {
    border: 1px solid #e9ecef; 
}

.table thead th {
    font-weight: 600;
    color: #495057;
    background-color: #f1f2f6; 
    border-bottom: 2px solid #dee2e6;
}
</style>