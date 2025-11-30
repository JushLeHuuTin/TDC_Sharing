<script setup>
import { ref, onMounted, reactive, computed } from 'vue';
import { storeToRefs } from 'pinia';
import { useAdminNotificationStore } from '@/stores/adminNotificationStore';
import Swal from 'sweetalert2';

// 1. Init Store
const notificationStore = useAdminNotificationStore();
const { notifications, users, isLoading, paginationData } = storeToRefs(notificationStore);

// 2. State
const filters = ref({ search: '', type: '', page: 1 });
const showModal = ref(false);
const isEditMode = ref(false);

const form = reactive({
    id: null,
    user_id: '',
    type: 'system',
    content: '',
    is_read: false
});

// --- TÍNH TOÁN HIỂN THỊ PHÂN TRANG ---
const fromEntry = computed(() => {
    if (!notifications.value || notifications.value.length === 0) return 0;
    if (paginationData.value && paginationData.value.from) return paginationData.value.from;
    return ((paginationData.value?.current_page || 1) - 1) * (paginationData.value?.per_page || 10) + 1;
});

const toEntry = computed(() => {
    if (!notifications.value || notifications.value.length === 0) return 0;
    if (paginationData.value && paginationData.value.to) return paginationData.value.to;
    
    // Fallback tính toán
    const calculatedTo = fromEntry.value + notifications.value.length - 1;
    return paginationData.value?.total && calculatedTo > paginationData.value.total ? paginationData.value.total : calculatedTo;
});

// 3. Methods
function fetchData() { 
    notificationStore.fetchNotifications(filters.value); 
}

function handleFilter() {
    filters.value.page = 1;
    fetchData();
}

function changePage(page) {
    if (paginationData.value && page >= 1 && page <= paginationData.value.last_page) {
        filters.value.page = page;
        fetchData();
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }
}

// --- MODAL ---
function openCreateModal() {
    isEditMode.value = false;
    Object.assign(form, { id: null, user_id: '', type: 'system', content: '', is_read: false });
    showModal.value = true;
}

function openEditModal(item) {
    isEditMode.value = true;
    Object.assign(form, { 
        id: item.id, 
        user_id: item.user_id ? item.user_id : '', // Nếu null (toàn hệ thống) thì set rỗng để select default
        type: item.type, 
        content: item.content, 
        is_read: Boolean(item.is_read) 
    });
    showModal.value = true;
}

async function handleSave() {
    if (!form.content.trim()) {
        Swal.fire('Lỗi', 'Vui lòng nhập nội dung thông báo', 'warning');
        return;
    }

    const payload = { 
        ...form, 
        user_id: form.user_id === '' ? null : form.user_id 
    };

    const result = isEditMode.value 
        ? await notificationStore.updateNotification(form.id, payload)
        : await notificationStore.createNotification(payload);

    if (result.success) {
        showModal.value = false;
        Swal.fire({
            icon: 'success',
            title: 'Thành công!',
            text: isEditMode.value ? 'Đã cập nhật thông báo.' : 'Đã tạo thông báo mới.',
            timer: 1500,
            showConfirmButton: false
        });
        fetchData();
    } else {
        Swal.fire('Lỗi', result.message, 'error');
    }
}

async function handleDelete(id) {
    const res = await Swal.fire({
        title: 'Xóa thông báo?', icon: 'warning', showCancelButton: true,
        confirmButtonText: 'Xóa', cancelButtonText: 'Hủy', confirmButtonColor: '#ef4444'
    });
    if (res.isConfirmed) {
        const result = await notificationStore.deleteNotification(id);
        if (result.success) {
            Swal.fire('Đã xóa', '', 'success');
            if(notifications.value.length === 0 && filters.value.page > 1) {
                filters.value.page--;
                fetchData();
            } else {
                fetchData();
            }
        } else {
             Swal.fire('Lỗi', result.message, 'error');
        }
    }
}

// Helpers
const typeLabels = { system: 'Hệ thống', promotion: 'Khuyến mãi', order: 'Đơn hàng', warning: 'Cảnh báo', message: 'Tin nhắn' };
function getTypeText(t) { return typeLabels[t] || t; }

function getTypeBadge(t) {
    const map = { 
        system: 'bg-blue-100 text-blue-800',
        promotion: 'bg-red-100 text-red-800 border border-red-200', 
        order: 'bg-green-100 text-green-800', 
        warning: 'bg-yellow-100 text-yellow-800', 
        message: 'bg-gray-100 text-gray-800' 
    };
    return map[t] || 'bg-gray-100 text-gray-600';
}
function getReadText(isRead) { return isRead ? 'Đã xem' : 'Chưa xem'; }
function getReadBadge(isRead) { return isRead ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600'; }

onMounted(async () => {
    await Promise.all([fetchData(), notificationStore.fetchUsers()]);
});
</script>

<template>
    <div class="p-6 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto">
            <!-- Header -->
            <div class="mb-8 flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 flex items-center">
                        <fa :icon="['fas', 'bell']" class="mr-3 text-indigo-600" /> Quản lý Thông báo
                    </h1>
                    <p class="text-gray-500 text-sm mt-1">Gửi thông báo cho người dùng hoặc toàn hệ thống.</p>
                </div>
                <button @click="openCreateModal" class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 shadow-sm flex items-center font-medium transition-colors">
                    <fa :icon="['fas', 'plus']" class="mr-2" /> Tạo mới
                </button>
            </div>

            <!-- Bộ lọc -->
            <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100 mb-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
                    <div class="md:col-span-2">
                        <label class="block text-xs font-semibold text-gray-500 mb-1">Tìm kiếm nội dung/người nhận</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <fa :icon="['fas', 'magnifying-glass']" class="text-gray-400" />
                            </div>
                            <input v-model="filters.search" type="text" class="pl-10 w-full border rounded-lg p-2.5 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition text-sm" placeholder="Nhập nội dung hoặc tên người nhận..." @keyup.enter="handleFilter">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 mb-1">Loại</label>
                        <select v-model="filters.type" class="w-full border rounded-lg p-2.5 focus:ring-indigo-500 focus:border-indigo-500 text-sm bg-white" @change="handleFilter">
                            <option value="">Tất cả</option>
                            <option v-for="(label, key) in typeLabels" :key="key" :value="key">{{ label }}</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Danh sách -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="overflow-x-auto custom-scrollbar">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50 text-gray-600 text-xs uppercase font-semibold">
                            <tr>
                                <th class="px-6 py-4 text-left tracking-wider">ID</th>
                                <th class="px-6 py-4 text-left tracking-wider">Người nhận</th>
                                <th class="px-6 py-4 text-left tracking-wider">Loại</th>
                                <th class="px-6 py-4 text-left tracking-wider" style="width: 30%">Nội dung</th>
                                <th class="px-6 py-4 text-center tracking-wider">Trạng thái</th>
                                <th class="px-6 py-4 text-left tracking-wider">Ngày tạo</th>
                                <th class="px-6 py-4 text-right tracking-wider">Hành động</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            <tr v-if="isLoading">
                                <td colspan="7" class="p-8 text-center text-gray-500">
                                    <div class="flex justify-center items-center">
                                        <fa :icon="['fas', 'spinner']" spin class="text-3xl text-indigo-500 mr-2" />
                                        <span>Đang tải dữ liệu...</span>
                                    </div>
                                </td>
                            </tr>
                            <tr v-else-if="!notifications.length">
                                <td colspan="7" class="p-8 text-center text-gray-500">
                                    <div class="flex flex-col items-center">
                                        <fa :icon="['far', 'bell-slash']" class="text-4xl text-gray-300 mb-3" />
                                        <p>Chưa có thông báo nào.</p>
                                    </div>
                                </td>
                            </tr>
                            
                            <tr v-for="item in notifications" :key="item.id" class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 font-bold text-indigo-600 text-sm">#{{ item.id }}</td>
                                <td class="px-6 py-4">
                                    <!-- HIỂN THỊ TÊN NGƯỜI DÙNG RÕ RÀNG -->
                                    <div class="text-sm font-bold text-gray-900">
                                        {{ item.recipient_name }}
                                    </div>
                                    <!-- Hiển thị ID bên dưới để tiện tra cứu -->
                                    <div v-if="item.user_id" class="text-xs text-gray-500 mt-0.5">
                                        ID: {{ item.user_id }}
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span :class="['inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold uppercase tracking-wide', getTypeBadge(item.type)]">
                                        {{ getTypeText(item.type) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600 truncate max-w-xs" :title="item.content">
                                    {{ item.content }}
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span v-if="item.is_read" class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-green-100 text-green-700 border border-green-200">
                                        <fa :icon="['fas', 'check']" class="mr-1" /> Đã đọc
                                    </span>
                                    <span v-else class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-gray-100 text-gray-600 border border-gray-200">
                                        Chưa đọc
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">{{ item.created_at }}</td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex justify-end space-x-2">
                                        <button @click="openEditModal(item)" class="text-blue-600 hover:bg-blue-50 p-2 rounded-md transition-colors" title="Sửa">
                                            <fa :icon="['fas', 'edit']" />
                                        </button>
                                        <button @click="handleDelete(item.id)" class="text-red-600 hover:bg-red-50 p-2 rounded-md transition-colors" title="Xóa">
                                            <fa :icon="['fas', 'trash-alt']" />
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                
                <!-- Phân trang -->
                <div v-if="paginationData" class="bg-white px-6 py-4 border-t border-gray-200 flex items-center justify-between">
                    <div class="text-sm text-gray-700">
                        tổng số <span class="font-medium">{{ paginationData.total }}</span> thông báo
                    </div>
                    
                    <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                        <button @click="changePage(paginationData.current_page - 1)" 
                            :disabled="paginationData.current_page === 1" 
                            class="relative inline-flex items-center px-3 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed">
                            <span class="sr-only">Previous</span>
                            <fa :icon="['fas', 'chevron-left']" class="h-3 w-3" />
                        </button>
                        
                        <span class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700">
                            Trang {{ paginationData.current_page }} / {{ paginationData.last_page }}
                        </span>

                        <button @click="changePage(paginationData.current_page + 1)" 
                            :disabled="paginationData.current_page === paginationData.last_page" 
                            class="relative inline-flex items-center px-3 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed">
                            <span class="sr-only">Next</span>
                            <fa :icon="['fas', 'chevron-right']" class="h-4 w-4" />
                        </button>
                    </nav>
                </div>
            </div>

            <!-- Modal -->
            <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black bg-opacity-50 backdrop-blur-sm" @click.self="showModal=false">
                <div class="bg-white rounded-lg shadow-xl w-full max-w-lg animate__animated animate__fadeInDown">
                    <div class="p-5 border-b flex justify-between items-center bg-white rounded-t-lg">
                        <h3 class="text-lg font-bold text-gray-800">{{ isEditMode ? 'Sửa thông báo' : 'Tạo thông báo mới' }}</h3>
                        <button @click="showModal=false" class="text-gray-500 hover:text-gray-700 transition-colors text-xl">&times;</button>
                    </div>
                    <div class="p-6 space-y-4">
                        <!-- CHỌN USER (Dropdown) -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Người nhận <span class="text-red-500">*</span></label>
                            <select v-model="form.user_id" class="w-full border border-gray-300 rounded-lg p-2.5 focus:ring-indigo-500 focus:border-indigo-500 text-sm bg-white transition">
                                <option v-for="u in users" :key="u.id" :value="u.id">{{ u.full_name }} (ID: {{ u.id }})</option>
                            </select>
                            <p class="text-xs text-gray-400 mt-1">Chọn người dùng cụ thể hoặc để trống để gửi cho tất cả.</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Loại thông báo <span class="text-red-500">*</span></label>
                            <select v-model="form.type" class="w-full border border-gray-300 rounded-lg p-2.5 focus:ring-indigo-500 focus:border-indigo-500 text-sm bg-white transition">
                                <option v-for="(label, key) in typeLabels" :key="key" :value="key">{{ label }}</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nội dung <span class="text-red-500">*</span></label>
                            <textarea v-model="form.content" rows="4" class="w-full border border-gray-300 rounded-lg p-2.5 focus:ring-indigo-500 focus:border-indigo-500 text-sm transition" placeholder="Nhập nội dung thông báo..."></textarea>
                        </div>
                         <div v-if="isEditMode" class="flex items-center mt-2">
                             <input id="read" v-model="form.is_read" type="checkbox" class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500 cursor-pointer">
                             <label for="read" class="ml-2 text-sm font-medium text-gray-900 cursor-pointer">Đánh dấu là đã đọc</label>
                        </div>
                    </div>
                    <div class="p-4 border-t flex justify-end space-x-2 bg-gray-50 rounded-b-lg">
                        <button @click="showModal=false" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 bg-white hover:bg-gray-50 transition-colors text-sm font-medium">Hủy</button>
                        <button @click="handleSave" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 shadow-sm transition-colors text-sm font-medium flex items-center">
                            <fa v-if="isLoading" :icon="['fas', 'spinner']" spin class="mr-2" />
                            <span>{{ isEditMode ? 'Cập nhật' : 'Lưu lại' }}</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.custom-scrollbar::-webkit-scrollbar { width: 6px; }
.custom-scrollbar::-webkit-scrollbar-track { background: #f1f1f1; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
.table-shadow { box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); }
</style>