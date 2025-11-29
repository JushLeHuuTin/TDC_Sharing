<script setup>
import { ref, onMounted, computed } from 'vue';
import { storeToRefs } from 'pinia';
import { useAdminNotificationStore } from '@/stores/adminNotificationStore';

// --- 1. KHỞI TẠO STORE & STATE ---
const notificationStore = useAdminNotificationStore();
const { notifications, isLoading, error, paginationData } = storeToRefs(notificationStore);

// State cho Modal (Tạo/Sửa)
const showModal = ref(false);
const isEditMode = ref(false);
const currentNotification = ref({
    id: null,
    user_ids: '', 
    type: 'promotion', 
    content: '',
    is_read: false
});

// State cho Modal Xóa
const showDeleteModal = ref(false);
const notificationToDelete = ref(null);

// State cho Phân trang
const currentPage = ref(1);

// --- 2. LIFECYCLE ---
onMounted(() => {
    notificationStore.fetchNotifications({ page: currentPage.value });
});

// --- 3. METHODS (CRUD) ---

// Mở modal Tạo
function openCreateModal() {
    isEditMode.value = false;
    currentNotification.value = { 
        id: null, 
        user_ids: '', 
        type: 'promotion', 
        content: '', 
        is_read: false // Mặc định là chưa đọc
    };
    notificationStore.error = null;
    showModal.value = true;
}

// Mở modal Sửa
function openEditModal(notification) {
    isEditMode.value = true;
    // Hiển thị ID để sửa
    const userIdsString = notification.user_id ? String(notification.user_id) : (notification.user_ids ? notification.user_ids.join(',') : '');
    
    currentNotification.value = { 
        ...notification, 
        user_ids: userIdsString 
    };
    notificationStore.error = null;
    showModal.value = true;
}

function closeModal() {
    showModal.value = false;
}

async function handleModalSubmit() {
    // 1. Chuẩn bị data
    const dataToSubmit = {
        type: currentNotification.value.type,
        content: currentNotification.value.content,
        is_read: currentNotification.value.is_read,
        user_ids: currentNotification.value.user_ids.split(',')
            .map(id => parseInt(id.trim()))
            .filter(id => !isNaN(id) && id > 0)
    };
    
    if (isEditMode.value || dataToSubmit.user_ids.length === 0) {
        delete dataToSubmit.user_ids;
    }

    let success = false;
    if (isEditMode.value) {
        // SỬA
        success = await notificationStore.updateNotification(currentNotification.value.id, dataToSubmit);
    } else {
        // TẠO MỚI (Luôn là chưa đọc, API mặc định false)
        delete dataToSubmit.is_read; 
        success = await notificationStore.createNotification(dataToSubmit);
    }

    if (success) {
        closeModal();
    }
}

// Modal Xóa
function openDeleteModal(notification) {
    notificationToDelete.value = notification;
    showDeleteModal.value = true;
}

function closeDeleteModal() {
    notificationToDelete.value = null;
    showDeleteModal.value = false;
}

async function confirmDelete() {
    if (notificationToDelete.value) {
        await notificationStore.deleteNotification(notificationToDelete.value.id);
        closeDeleteModal();
    }
}

// Hàm "Đánh dấu đã đọc"
async function handleMarkAsRead(notification) {
    const dataToUpdate = {
        type: notification.type,
        content: notification.content,
        is_read: true 
    };
    await notificationStore.updateNotification(notification.id, dataToUpdate);
}

// Phân trang
function changePage(page) {
    if (!paginationData.value || page < 1 || page > paginationData.value.last_page) return;
    currentPage.value = page;
    notificationStore.fetchNotifications({ page: currentPage.value });
}

// --- 5. HELPER FUNCTIONS (Style) ---
function getStatusBadgeClass(is_read) {
    return is_read ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700';
}
function getStatusText(is_read) {
    return is_read ? 'Đã đọc' : 'Chưa đọc';
}
function getTypeBadgeClass(type) {
    switch (type) {
        case 'system': return 'status-processing'; 
        case 'promotion': return 'status-pending'; 
        case 'order': return 'status-approved'; 
        case 'message': return 'status-processing'; 
        default: return 'bg-gray-100 text-gray-700';
    }
}
</script>

<template>
    <div class="page-container p-6">
         <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <RouterLink to="/admin/dashboard">Dashboard</RouterLink>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">Quản lý thông báo</li>
                    </ol>
                </nav>
                <h2 class="mb-0">Quản lý thông báo</h2>
                <p class="text-muted mb-0">Tổ chức và quản lý thông báo</p>
            </div>
            <button class="btn btn-primary" @click="openAddModal()">
                <fa :icon="['fas', 'plus']" class="me-2" />Thêm danh mục
            </button>
        </div>
    </div>
        <!-- Header -->
        <!-- <div class="flex justify-between items-center mb-6">
            <div>
                <h2 class="text-2xl font-semibold text-gray-800">Quản lý Thông báo</h2>
                <p class="text-sm text-gray-500">Gửi và quản lý thông báo hệ thống</p>
            </div>
            <button @click="openCreateModal" class="action-btn bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md font-medium flex items-center">
                <fa :icon="['fas', 'plus']" class="h-4 w-4 mr-2" />
                Tạo thông báo
            </button>
        </div> -->
        
        <!-- BẢNG DỮ LIỆU -->
        <div class="bg-white rounded-lg table-shadow overflow-hidden">
            <div class="overflow-x-auto custom-scrollbar">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Người nhận</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nội dung</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Loại</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Trạng thái</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ngày tạo</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Hành động</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr v-if="isLoading && !showModal && !showDeleteModal">
                            <td colspan="7" class="p-6 text-center text-gray-500">Đang tải dữ liệu...</td>
                        </tr>
                        <tr v-else-if="!notifications || notifications.length === 0">
                            <td colspan="7" class="p-6 text-center text-gray-500">Không tìm thấy thông báo nào.</td>
                        </tr>

                        <tr v-for="noti in notifications" :key="noti.id" class="table-row">
                            <td class="px-6 py-4">#{{ noti.id }}</td>
                            <td class="px-6 py-4">{{ noti.recipient_name || 'N/A' }}</td>
                            <td class="px-6 py-4 max-w-sm truncate" :title="noti.content">
                                {{ noti.content }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="status-badge" :class="getTypeBadgeClass(noti.type)">{{ noti.type }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="status-badge" :class="getStatusBadgeClass(noti.is_read)">
                                    {{ getStatusText(noti.is_read) }}
                                </span>
                            </td>
                            <td class="px-6 py-4">{{ noti.created_at }}</td>
                            
                            <td class="px-6 py-4 text-sm font-medium">
                                <div class="flex space-x-2">
                                    <button v-if="!noti.is_read" @click="handleMarkAsRead(noti)" class="action-btn bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded-md" title="Đánh dấu đã đọc">
                                        <fa :icon="['fas', 'eye']" class="h-4 w-4" />
                                    </button>
                                    <button v-else class="action-btn bg-gray-300 text-gray-500 px-3 py-1 rounded-md cursor-not-allowed" title="Đã đọc" disabled>
                                        <fa :icon="['fas', 'eye-slash']" class="h-4 w-4" />
                                    </button>
                                    <button @click="openEditModal(noti)" class="action-btn bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-1 rounded-md" title="Sửa">
                                        <fa :icon="['fas', 'pencil-alt']" class="h-4 w-4" />
                                    </button>
                                    <button @click="openDeleteModal(noti)" class="action-btn bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded-md" title="Xóa">
                                        <fa :icon="['fas', 'trash']" class="h-4 w-4" />
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Phân trang -->
            <div class="bg-white px-6 py-3 border-t border-gray-200" v-if="!isLoading && paginationData && paginationData.last_page > 1">
                 <nav class="flex justify-center">
                    <ul class="flex items-center -space-x-px h-8 text-sm">
                        <!-- (Code phân trang) -->
                    </ul>
                </nav>
            </div>
        </div>

        <!-- MODAL TẠO/SỬA -->
        <div v-if="showModal" class="fixed inset-0 bg-gray-900 bg-opacity-75 flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-lg shadow-xl max-w-lg w-full">
                <div class="flex justify-between items-center p-5 border-b">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <fa :icon="['fas', 'edit']" class="text-gray-500 mr-3" v-if="isEditMode" />
                        <fa :icon="['fas', 'plus']" class="text-gray-500 mr-3" v-else />
                        {{ isEditMode ? 'Chỉnh sửa Thông báo' : 'Tạo Thông báo mới' }}
                    </h3>
                    <button @click="closeModal" class="text-gray-400 hover:text-gray-600">
                        <fa :icon="['fas', 'times']" class="h-5 w-5" />
                    </button>
                </div>

                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Người nhận (User IDs) <span class="text-red-500">*</span></label>
                        <input v-model="currentNotification.user_ids" type="text" 
                               :placeholder="isEditMode ? 'Nhập ID để sửa (1,2,3)' : 'Nhập User IDs (1,2,3)...'"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <small class="text-gray-500">Nhập ID người dùng (vì chưa có API lấy danh sách User).</small>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Loại thông báo <span class="text-red-500">*</span></label>
                        <select v-model="currentNotification.type" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="promotion">Khuyến mãi</option>
                            <option value="system">Hệ thống</option>
                            <option value="order">Đơn hàng</option>
                            <option value="message">Tin nhắn</option>
                        </select>
                    </div>
                    <div class="relative">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nội dung thông báo <span class="text-red-500">*</span></label>
                        <textarea v-model="currentNotification.content" rows="4" placeholder="Nhập nội dung thông báo..."
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                        <small class="absolute bottom-2 right-2 text-gray-400">
                            {{ currentNotification.content.length }}/255 ký tự
                        </small>
                    </div>
                    
                    <div class="flex justify-between">
                        <!-- CHECKBOX TRẠNG THÁI: Disabled nếu là Tạo mới -->
                        <label class="flex items-center cursor-pointer">
                            <input v-model="currentNotification.is_read" type="checkbox" class="sr-only peer" :disabled="!isEditMode">
                            <div class="relative w-11 h-6 bg-gray-200 rounded-full peer peer-checked:bg-blue-600 transition-colors peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all" :class="{'opacity-50 cursor-not-allowed': !isEditMode}"></div>
                            <span class="ml-3 text-sm font-medium text-gray-700">
                                {{ !isEditMode ? 'Mặc định: Chưa đọc' : (currentNotification.is_read ? 'Trạng thái: Đã đọc' : 'Trạng thái: Chưa đọc') }}
                            </span>
                        </label>
                        
                        <label class="flex items-center cursor-pointer">
                            <input type="checkbox" checked class="sr-only peer" disabled> 
                            <div class="relative w-11 h-6 bg-gray-200 rounded-full peer peer-checked:bg-blue-600 transition-colors peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all"></div>
                            <span class="ml-3 text-sm font-medium text-gray-700">Gửi ngay</span>
                        </label>
                    </div>

                    <div v-if="error" class="text-red-600 text-sm p-3 bg-red-50 rounded-md">
                        Lỗi: {{ error }}
                    </div>
                </div>

                <div class="p-4 bg-gray-50 border-t flex justify-end space-x-3">
                    <button @click="closeModal" class="px-5 py-2 text-sm font-medium text-gray-700 bg-white border rounded-lg hover:bg-gray-100">
                        Hủy
                    </button>
                    <button @click="handleModalSubmit" :disabled="isLoading" class="px-5 py-2 text-sm font-medium text-white bg-green-600 rounded-lg hover:bg-green-700 disabled:opacity-50 flex items-center">
                        <fa :icon="['fas', 'save']" class="h-4 w-4 mr-2" />
                        <span v-if="isLoading">Đang lưu...</span>
                        <span v-else>Lưu thông báo</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- MODAL XÁC NHẬN XÓA -->
        <div v-if="showDeleteModal && notificationToDelete" class="fixed inset-0 bg-gray-900 bg-opacity-75 flex items-center justify-center z-50 p-4">
            <!-- (Giữ nguyên code modal xóa) -->
             <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
                <div class="flex justify-between items-center p-4 border-b border-red-200 bg-red-50 rounded-t-lg">
                    <h3 class="text-lg font-semibold text-red-700 flex items-center">
                        <fa :icon="['fas', 'exclamation-triangle']" class="h-5 w-5 text-red-600 mr-2" />
                        Xác nhận xóa thông báo
                    </h3>
                    <button @click="closeDeleteModal" class="text-gray-400 hover:text-gray-600">
                        <fa :icon="['fas', 'times']" class="h-5 w-5" />
                    </button>
                </div>
                <div class="p-6">
                    <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg" role="alert">
                        <strong class="font-bold">CẢNH BÁO: Hành động không thể hoàn tác!</strong>
                        <span class="block sm:inline">Bạn có chắc chắn muốn xóa thông báo này?</span>
                    </div>
                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 text-left mt-4 text-sm">
                        <p class="mb-2"><strong class="text-gray-600 w-24 inline-block">ID:</strong> <span class="font-medium text-gray-900">#{{ notificationToDelete.id }}</span></p>
                        <p class="mb-2"><strong class="text-gray-600 w-24 inline-block">Người nhận:</strong> <span class="font-medium text-gray-900">{{ notificationToDelete.recipient_name }}</span></p>
                        <p><strong class="text-gray-600 w-24 inline-block">Nội dung:</strong> <span class="text-gray-800">{{ notificationToDelete.content }}</span></p>
                    </div>
                </div>
                <div class="p-4 bg-gray-50 border-t flex justify-end space-x-3 rounded-b-lg">
                    <button @click="closeDeleteModal" class="px-5 py-2 text-sm font-medium text-gray-700 bg-white border rounded-lg hover:bg-gray-100">
                        Hủy bỏ
                    </button>
                    <button @click="confirmDelete" :disabled="isLoading" class="px-5 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 disabled:opacity-50 flex items-center">
                        <fa :icon="['fas', 'trash']" class="h-4 w-4 mr-2" />
                        Xác nhận xóa
                    </button>
                </div>
            </div>
        </div>

    </div>
</template>

<style scoped>
.custom-scrollbar::-webkit-scrollbar { width: 8px; height: 8px; }
.custom-scrollbar::-webkit-scrollbar-track { background: #f1f5f9; border-radius: 4px; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
.table-shadow { box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06); }
.filter-card { background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%); border: 1px solid #e2e8f0; }
.status-badge { font-size: 0.75rem; font-weight: 600; padding: 0.25rem 0.75rem; border-radius: 9999px; text-transform: uppercase; }
.status-pending { background-color: #fef3c7; color: #92400e; }
.status-approved { background-color: #d1fae5; color: #065f46; }
.status-rejected { background-color: #fee2e2; color: #991b1b; }
.status-processing { background-color: #dbeafe; color: #1e40af; }
.action-btn { transition: all 0.2s ease-in-out; font-weight: 500; font-size: 0.875rem; padding-top: 0.25rem; padding-bottom: 0.25rem; }
.action-btn:hover { transform: translateY(-1px); }
.table-row:hover { background-color: #f8fafc; }

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