<script setup>
import { ref, computed } from 'vue';
import { RouterLink } from 'vue-router'; 

// --- PROPS VÀ UTILITY ---
const props = defineProps({
    showToast: Function,
    formatDate: {
        type: Function,
        default: (date) => new Date(date).toLocaleDateString('vi-VN')
    }
});

// --- STATE CỤC BỘ ---
const activeFilter = ref('all');
const mockNotifications = ref([
    { id: 1, title: 'Chào mừng sinh viên mới', type: 'system', status: 'sent', readCount: 234, totalSent: 300, date: '2024-02-20' },
    { id: 2, title: 'Khuyến mãi sách giáo khoa', type: 'promotion', status: 'pending', readCount: 0, totalSent: 0, date: '2024-02-21' },
    { id: 3, title: 'Cập nhật chính sách bảo mật', type: 'update', status: 'sent', readCount: 156, totalSent: 200, date: '2024-02-19' },
    { id: 4, title: 'Thông báo bảo trì hệ thống', type: 'system', status: 'draft', readCount: 0, totalSent: 0, date: '2024-02-22' }
]);

// --- COMPUTED PROPERTIES ---

const notificationStats = computed(() => {
    // Giá trị cứng từ Blade gốc
    return {
        totalNotifications: 89,
        sentNotifications: 67,
        pendingNotifications: 12,
        readRate: '78%'
    };
});

const filteredNotifications = computed(() => {
    if (activeFilter.value === 'all') {
        return mockNotifications.value;
    }
    // Lọc theo trạng thái
    return mockNotifications.value.filter(n => n.status === activeFilter.value);
});

// --- HELPER FUNCTIONS ---

function getStatusBadge(status) {
    switch (status) {
        case 'sent': return 'bg-success';
        case 'pending': return 'bg-warning';
        case 'draft': return 'bg-secondary';
        default: return 'bg-info';
    }
}

function getTypeBadge(type) {
     switch (type) {
        case 'system': return 'bg-primary';
        case 'promotion': return 'bg-success';
        case 'update': return 'bg-info';
        default: return 'bg-secondary';
    }
}

// --- ACTION HANDLERS ---

function createNotification() {
    props.showToast('Mở form tạo Thông báo mới...', 'info');
}

function editNotification(id) {
    props.showToast(`Chỉnh sửa Thông báo #${id}`, 'info');
}

function sendNotification(id) {
    props.showToast(`Gửi Thông báo #${id} thành công!`, 'success');
}

function deleteNotification(id) {
    if (confirm('Bạn có chắc chắn muốn xóa thông báo này?')) {
        props.showToast(`Đã xóa Thông báo #${id}`, 'success');
        // Logic xóa data
    }
}

function sendBulkNotification() {
    props.showToast('Mở form gửi Thông báo hàng loạt...', 'info');
}

function scheduleNotification() {
    props.showToast('Mở form Lên lịch gửi...', 'info');
}

function filterNotifications(filterName) {
    activeFilter.value = filterName;
    props.showToast(`Lọc theo: ${filterName}`, 'info');
}
</script>

<template>
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><RouterLink to="/admin/dashboard">Dashboard</RouterLink></li>
                        <li class="breadcrumb-item active" aria-current="page">Thông báo</li>
                    </ol>
                </nav>
                <h2 class="mb-0">Quản lý thông báo</h2>
                <p class="text-muted mb-0">Gửi và quản lý thông báo hệ thống</p>
            </div>
            <button class="btn btn-primary" @click="createNotification">
                <fa :icon="['fas', 'plus']" class="me-2" />Tạo thông báo
            </button>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card stats-card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="mb-1">{{ notificationStats.totalNotifications }}</h3>
                            <p class="mb-0 opacity-75">Tổng thông báo</p>
                        </div>
                        <fa :icon="['fas', 'bell']" class="fa-2x opacity-75" />
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card stats-card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="mb-1">{{ notificationStats.sentNotifications }}</h3>
                            <p class="mb-0 opacity-75">Đã gửi</p>
                        </div>
                        <fa :icon="['fas', 'paper-plane']" class="fa-2x opacity-75" />
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card stats-card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="mb-1">{{ notificationStats.pendingNotifications }}</h3>
                            <p class="mb-0 opacity-75">Chờ gửi</p>
                        </div>
                        <fa :icon="['fas', 'clock']" class="fa-2x opacity-75" />
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card stats-card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="mb-1">{{ notificationStats.readRate }}</h3>
                            <p class="mb-0 opacity-75">Tỷ lệ đọc</p>
                        </div>
                        <fa :icon="['fas', 'eye']" class="fa-2x opacity-75" />
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card shadow-sm notifications-list-card">
                <div class="card-header bg-white border-bottom-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Danh sách thông báo</h5>
                        <div class="btn-group btn-group-sm">
                            <button class="btn btn-outline-primary" :class="{ 'active': activeFilter === 'all' }" @click="filterNotifications('all')">Tất cả</button>
                            <button class="btn btn-outline-primary" :class="{ 'active': activeFilter === 'sent' }" @click="filterNotifications('sent')">Đã gửi</button>
                            <button class="btn btn-outline-primary" :class="{ 'active': activeFilter === 'pending' }" @click="filterNotifications('pending')">Chờ gửi</button>
                            <button class="btn btn-outline-primary" :class="{ 'active': activeFilter === 'draft' }" @click="filterNotifications('draft')">Nháp</button>
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div id="notificationsList">
                        <div v-for="notif in filteredNotifications" :key="notif.id" class="notification-item border-bottom p-3">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="flex-grow-1">
                                    <h6 class="mb-1 fw-bold">{{ notif.title }}</h6>
                                    <div class="d-flex align-items-center gap-2 mb-2">
                                        <span class="badge" :class="getTypeBadge(notif.type)">
                                            {{ notif.type === 'system' ? 'Hệ thống' : notif.type === 'promotion' ? 'Khuyến mãi' : 'Cập nhật' }}
                                        </span>
                                        <span class="badge" :class="getStatusBadge(notif.status)">
                                            {{ notif.status === 'sent' ? 'Đã gửi' : notif.status === 'pending' ? 'Chờ gửi' : 'Nháp' }}
                                        </span>
                                    </div>
                                    <small class="text-muted">
                                        {{ notif.status === 'sent' ? `${notif.readCount}/${notif.totalSent} đã đọc` : 'Chưa gửi' }} • {{ props.formatDate(new Date(notif.date)) }}
                                    </small>
                                </div>
                                <div class="btn-group btn-group-sm notification-actions">
                                    <button class="btn btn-outline-primary" @click="editNotification(notif.id)" title="Chỉnh sửa">
                                        <fa :icon="['fas', 'edit']" />
                                    </button>
                                    <button class="btn btn-outline-success" @click="sendNotification(notif.id)" title="Gửi ngay">
                                        <fa :icon="['fas', 'paper-plane']" />
                                    </button>
                                    <button class="btn btn-outline-danger" @click="deleteNotification(notif.id)" title="Xóa">
                                        <fa :icon="['fas', 'trash']" />
                                    </button>
                                </div>
                            </div>
                        </div>
                         <div v-if="filteredNotifications.length === 0" class="p-4 text-center text-muted">
                            Không có thông báo nào phù hợp với bộ lọc.
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h6 class="mb-0 fw-bold">Thống kê thông báo</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <small>Thông báo hệ thống</small>
                            <small>45</small>
                        </div>
                        <div class="progress" style="height: 6px;">
                            <div class="progress-bar bg-primary" style="width: 60%"></div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <small>Khuyến mãi</small>
                            <small>23</small>
                        </div>
                        <div class="progress" style="height: 6px;">
                            <div class="progress-bar bg-success" style="width: 30%"></div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <small>Cập nhật</small>
                            <small>21</small>
                        </div>
                        <div class="progress" style="height: 6px;">
                            <div class="progress-bar bg-info" style="width: 28%"></div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card mt-3 shadow-sm">
                <div class="card-header bg-light">
                    <h6 class="mb-0 fw-bold">Thao tác nhanh</h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <button class="btn btn-primary" @click="createNotification">
                            <fa :icon="['fas', 'plus']" class="me-2" />Tạo thông báo mới
                        </button>
                        <button class="btn btn-outline-info" @click="sendBulkNotification">
                            <fa :icon="['fas', 'broadcast-tower']" class="me-2" />Gửi hàng loạt
                        </button>
                        <button class="btn btn-outline-success" @click="scheduleNotification">
                            <fa :icon="['fas', 'calendar']" class="me-2" />Lên lịch gửi
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
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

.breadcrumb-item a {
    color: #667eea;
    text-decoration: none;
}

.notifications-list-card {
    border-radius: 10px;
}

.notifications-list-card .card-header {
    border-radius: 10px 10px 0 0;
}

.notification-item {
    transition: background-color 0.2s ease;
    cursor: pointer;
}

.notification-item:hover {
    background-color: #f8f9fa;
}

.notification-actions {
    opacity: 0;
    transition: opacity 0.2s ease;
}

.notification-item:hover .notification-actions {
    opacity: 1;
}

.card-header-tabs .nav-link.active {
    background-color: #f8f9fa !important; 
    border-bottom: 3px solid #667eea !important;
    color: #667eea;
}
.card-header-tabs .nav-link {
    border: none;
}
</style>