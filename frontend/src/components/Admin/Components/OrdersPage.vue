<script setup>
import { ref, onMounted } from 'vue';
import { storeToRefs } from 'pinia';
import { useAdminOrderStore } from '@/stores/adminOrderStore'; 
import { useRouter } from 'vue-router';

// --- 1. KHỞI TẠO STORE & ROUTER ---
const orderStore = useAdminOrderStore();
const { orders, isLoading, error, paginationData } = storeToRefs(orderStore);
const router = useRouter();

// --- 2. STATE CỤC BỘ (BỘ LỌC) ---
const filters = ref({
    status: '',
    from_date: '',
    to_date: '',
    page: 1
});

// --- 3. LIFECYCLE ---
onMounted(() => {
    orderStore.fetchOrders();
});

// --- 4. METHODS (XỬ LÝ LOGIC) ---
function handleApplyFilters() {
    filters.value.page = 1; 
    const activeFilters = {};
    if (filters.value.status) activeFilters.status = filters.value.status;
    if (filters.value.from_date) activeFilters.from_date = filters.value.from_date;
    if (filters.value.to_date) activeFilters.to_date = filters.value.to_date;
    
    orderStore.fetchOrders(activeFilters);
}

// Nút placeholder
function handleViewOrder(orderId) {
    alert(`(Chưa code) Xem chi tiết đơn hàng ID: ${orderId}`);
}

// Nút placeholder
function handleEditOrder(orderId) {
    alert(`(Chưa code) Sửa đơn hàng ID: ${orderId}`);
}

// Nút placeholder
function handleDeleteOrder(orderId) {
    alert(`(Chưa code) Xóa đơn hàng ID: ${orderId}`);
}

function changePage(page) {
    if (!paginationData.value || page < 1 || page > paginationData.value.last_page) return;
    filters.value.page = page;
    orderStore.fetchOrders(filters.value);
}

// --- 5. HELPER FUNCTIONS ---
function getStatusText(status) {
    const statusMap = {
        'pending': 'Chờ duyệt',
        'processing': 'Chờ duyệt',
        'approved': 'Đã duyệt',
        'shipped': 'Đang giao',
        'delivered': 'Đã giao',
        'rejected': 'Đã hủy',
        'cancelled': 'Đã hủy'
    };
    return statusMap[status] || status;
}

function getStatusBadgeClass(status) {
    const statusClassMap = {
        'pending': 'status-pending',
        'processing': 'status-pending',
        'approved': 'status-approved',
        'shipped': 'status-processing',
        'delivered': 'status-approved',
        'rejected': 'status-rejected',
        'cancelled': 'status-rejected'
    };
    return statusClassMap[status] || 'bg-gray-200 text-gray-800';
}
</script>

<template>
    <div class="page-container p-6"> 
        <h2 class="text-2xl font-semibold text-gray-800 mb-6">Quản lý Đơn hàng (Admin)</h2>

        <div class="filter-card rounded-lg p-6 mb-8 table-shadow">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Bộ lọc</h3>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Trạng thái</label> 
                    <select v-model="filters.status" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Tất cả</option>
                        <option value="processing">Chờ duyệt</option>
                        <option value="pending">Chờ duyệt (Pending)</option>
                        <option value="shipped">Đang giao</option>
                        <option value="delivered">Đã giao</option>
                        <option value="cancelled">Đã hủy</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Từ ngày</label> 
                    <input v-model="filters.from_date" type="date" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Đến ngày</label> 
                    <input v-model="filters.to_date" type="date" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="flex items-end">
                    <button @click="handleApplyFilters" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-md transition duration-200 ease-in-out">
                        <span v-if="isLoading">Đang tải...</span>
                        <span v-else>Áp dụng</span>
                    </button>
                </div>
            </div>
            <div v-if="error" class="text-red-600 mt-4">{{ error }}</div>
        </div>

        <div class="bg-white rounded-lg table-shadow overflow-hidden">
            <div class="overflow-x-auto custom-scrollbar">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Mã đơn</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Khách hàng</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Seller</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tổng tiền</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Trạng thái</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ngày tạo</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Hành động</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr v-if="isLoading">
                            <td colspan="7" class="p-6 text-center text-gray-500">Đang tải dữ liệu...</td>
                        </tr>
                        <tr v-else-if="!orders || orders.length === 0">
                            <td colspan="7" class="p-6 text-center text-gray-500">Không tìm thấy đơn hàng nào.</td>
                        </tr>
                        
                        <tr v-for="order in orders" :key="order.order_id" class="table-row">
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900">#{{ order.order_id }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900">{{ order.customer_name || 'N/A' }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900">{{ order.seller_name || 'N/A' }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-semibold text-gray-900">{{ order.total_price }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="status-badge" :class="getStatusBadgeClass(order.status)">
                                    {{ getStatusText(order.status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-500">{{ order.order_date }}</div>
                            </td>
                            <td class="px-6 py-4 text-sm font-medium">
                                <div class="flex space-x-2">
                                    <button @click="handleViewOrder(order.order_id)" class="action-btn bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded-md">
                                        Xem
                                    </button>
                                    <button @click="handleEditOrder(order.order_id)" class="action-btn bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-1 rounded-md">
                                        Sửa
                                    </button>
                                    <button @click="handleDeleteOrder(order.order_id)" class="action-btn bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded-md">
                                        Xóa
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="bg-white px-6 py-3 border-t border-gray-200" v-if="!isLoading && paginationData && paginationData.last_page > 1">
                 <nav class="flex justify-center">
                    <ul class="flex items-center -space-x-px h-8 text-sm">
                        </ul>
                </nav>
            </div>
        </div>
    </div>
</template>

<style scoped>
/* (Giữ nguyên CSS của bạn) */
.custom-scrollbar::-webkit-scrollbar {
    width: 8px; height: 8px;
}
.custom-scrollbar::-webkit-scrollbar-track {
    background: #f1f5f9; border-radius: 4px;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    background: #cbd5e1; border-radius: 4px;
}
.table-shadow {
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
}
.filter-card {
    background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
    border: 1px solid #e2e8f0;
}
.status-badge {
    font-size: 0.75rem; font-weight: 600; padding: 0.25rem 0.75rem;
    border-radius: 9999px; text-transform: uppercase;
}
.status-pending {
    background-color: #fef3c7; color: #92400e;
}
.status-approved {
    background-color: #d1fae5; color: #065f46;
}
.status-rejected {
    background-color: #fee2e2; color: #991b1b;
}
.status-processing {
    background-color: #dbeafe; color: #1e40af;
}
.action-btn {
    transition: all 0.2s ease-in-out; font-weight: 500; font-size: 0.875rem;
}
.action-btn:hover {
    transform: translateY(-1px);
}
.table-row:hover {
    background-color: #f8fafc;
}
</style>