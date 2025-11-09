<script setup>
import { ref, onMounted, computed } from 'vue';
import { RouterLink } from 'vue-router'; 

// --- PROPS VÀ UTILITY ---
const props = defineProps({
    showToast: Function,
});

// --- STATE CỤC BỘ ---
const stats = ref({
    totalUsers: '1,234',
    totalProducts: '5,678',
    totalOrders: '892',
    revenue: '45.2M'
});
const recentOrders = ref([]);
const newUsers = ref([]);
const chartData = ref(null);

// --- DỮ LIỆU MẪU VÀ LOGIC LOAD ---

function loadRecentOrders() {
    // Logic giả định tải dữ liệu đơn hàng gần đây
    recentOrders.value = [
        { id: 1001, customer: 'Minh Anh', total: '150,000', status: 'Delivered' },
        { id: 1002, customer: 'Văn Bình', total: '45,000', status: 'Processing' },
        { id: 1003, customer: 'Lê Cẩm', total: '2,300,000', status: 'Canceled' },
    ];
}

function loadNewUsers() {
    // Logic giả định tải dữ liệu người dùng mới
    newUsers.value = [
        { id: 10, name: 'Nguyễn B', joinDate: '2025-10-30', university: 'ĐH Quốc gia', avatar: 'https://ui-avatars.io/api/?name=Nguyen+B&background=0d6efd&color=fff' },
        { id: 11, name: 'Trần C', joinDate: '2025-10-30', university: 'ĐH Bách Khoa', avatar: 'https://ui-avatars.io/api/?name=Tran+C&background=198754&color=fff' },
        { id: 12, name: 'Lý D', joinDate: '2025-10-29', university: 'ĐH Kinh tế', avatar: 'https://ui-avatars.io/api/?name=Ly+D&background=fd7e14&color=fff' },
    ];
}

function createRevenueChart() {
    // Khởi tạo biểu đồ (Cần thư viện Chart.js được import global)
    const ctx = document.getElementById('revenueChart');
    if (!ctx) return;
    
    // Giả định Chart.js đã được tải global
    if (window.Chart) {
        chartData.value = new window.Chart(ctx, {
            type: 'line',
            data: {
                labels: ['Thứ 4', 'Thứ 5', 'Thứ 6', 'Thứ 7', 'CN', 'T2', 'T3'],
                datasets: [{
                    label: 'Doanh thu (VNĐ)',
                    data: [12, 19, 3, 5, 2, 3, 15],
                    backgroundColor: 'rgba(102, 126, 234, 0.4)',
                    borderColor: '#667eea',
                    borderWidth: 2,
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                scales: { y: { beginAtZero: true, display: false }, x: { display: true } },
                plugins: { legend: { display: false } }
            }
        });
    }
}

function loadDashboard() {
    loadRecentOrders();
    loadNewUsers();
    
    // Phải chờ DOM load xong mới tạo chart
    setTimeout(createRevenueChart, 100); 
    props.showToast('Dashboard đã được tải.', 'info');
}

// --- ACTION HANDLERS ---
function refreshDashboard() {
    // Hủy biểu đồ cũ nếu tồn tại
    if (chartData.value) {
        chartData.value.destroy();
    }
    loadDashboard();
    props.showToast('Đang làm mới dữ liệu...', 'info');
}

function exportReport() {
    props.showToast('Xuất báo cáo đang được tiến hành...', 'success');
}

// Hàm helper để định dạng trạng thái
function getStatusBadge(status) {
    switch (status) {
        case 'Delivered': return 'bg-success';
        case 'Processing': return 'bg-warning';
        case 'Canceled': return 'bg-danger';
        default: return 'bg-info';
    }
}

// --- LIFECYCLE ---
onMounted(() => {
    loadDashboard();
});
</script>

<template>
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="mb-0">Dashboard</h2>
                <p class="text-muted mb-0">Tổng quan hệ thống StudentMarket</p>
            </div>
            <div class="d-flex gap-2">
                <button class="btn btn-outline-primary" @click="refreshDashboard">
                    <fa :icon="['fas', 'sync-alt']" class="me-2" />Làm mới
                </button>
                <button class="btn btn-primary" @click="exportReport">
                    <fa :icon="['fas', 'download']" class="me-2" />Xuất báo cáo
                </button>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card stats-card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="mb-1">{{ stats.totalUsers }}</h3>
                            <p class="mb-0 opacity-75">Tổng người dùng</p>
                            <small class="opacity-75">+12% so với tháng trước</small>
                        </div>
                        <fa :icon="['fas', 'users']" class="fa-2x opacity-75" />
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card stats-card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="mb-1">{{ stats.totalProducts }}</h3>
                            <p class="mb-0 opacity-75">Tổng sản phẩm</p>
                            <small class="opacity-75">+8% so với tháng trước</small>
                        </div>
                        <fa :icon="['fas', 'box']" class="fa-2x opacity-75" />
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card stats-card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="mb-1">{{ stats.totalOrders }}</h3>
                            <p class="mb-0 opacity-75">Đơn hàng tháng này</p>
                            <small class="opacity-75">+15% so với tháng trước</small>
                        </div>
                        <fa :icon="['fas', 'shopping-cart']" class="fa-2x opacity-75" />
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card stats-card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="mb-1">{{ stats.revenue }}</h3>
                            <p class="mb-0 opacity-75">Doanh thu (VNĐ)</p>
                            <small class="opacity-75">+22% so với tháng trước</small>
                        </div>
                        <fa :icon="['fas', 'chart-line']" class="fa-2x opacity-75" />
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Biểu đồ doanh thu 7 ngày qua</h5>
                </div>
                <div class="card-body">
                    <canvas id="revenueChart" height="100"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Danh mục bán chạy</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <small>Điện tử - Công nghệ</small>
                            <small>35%</small>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-primary" style="width: 35%"></div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <small>Sách & Học tập</small>
                            <small>28%</small>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-success" style="width: 28%"></div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <small>Thời trang</small>
                            <small>22%</small>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-warning" style="width: 22%"></div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <small>Đồ gia dụng</small>
                            <small>15%</small>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-info" style="width: 15%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-6">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Đơn hàng gần đây</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Mã đơn</th>
                                    <th>Khách hàng</th>
                                    <th>Tổng tiền</th>
                                    <th>Trạng thái</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="order in recentOrders" :key="order.id">
                                    <td>#{{ order.id }}</td>
                                    <td>{{ order.customer }}</td>
                                    <td>{{ order.total }} VNĐ</td>
                                    <td>
                                        <span class="badge" :class="getStatusBadge(order.status)">
                                            {{ order.status }}
                                        </span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Người dùng mới</h5>
                </div>
                <div class="card-body p-0">
                    <div id="newUsersList">
                        <div v-for="user in newUsers" :key="user.id" class="d-flex align-items-center p-3 border-bottom">
                            <img :src="user.avatar" class="rounded-circle me-3" width="40" height="40" alt="Avatar">
                            <div class="flex-grow-1">
                                <h6 class="mb-0">{{ user.name }}</h6>
                                <small class="text-muted">{{ user.university }}</small>
                            </div>
                            <small class="text-success">{{ user.joinDate | formatDate }}</small>
                        </div>
                        <div v-if="newUsers.length === 0" class="p-3 text-center text-muted">
                            Không có người dùng mới nào gần đây.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
/* Các style cần thiết từ AdminLayout để giữ giao diện nhất quán */

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

.card {
    border-radius: 10px;
}

.card-header {
    font-weight: 600;
    border-bottom: 1px solid #e9ecef;
}

/* Định dạng cho danh sách người dùng */
#newUsersList .border-bottom:last-child {
    border-bottom: none !important;
}

/* Định dạng cho Table */
.table thead th {
    font-weight: 600;
    color: #495057;
    background-color: #f1f2f6; 
}
</style>