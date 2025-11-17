<script setup>
import { ref, onMounted, computed } from 'vue';
import { storeToRefs } from 'pinia';
import { useSellerOrderStore } from '@/stores/sellerOrderStore';
import { useRouter } from 'vue-router';
import AppLayout from '@/Layouts/AppLayout.vue';

// --- 1. KHỞI TẠO STORE & ROUTER ---
const orderStore = useSellerOrderStore();
const { 
    orders, 
    isLoading, 
    error, 
    currentOrder, 
    isDetailLoading, 
    detailError 
} = storeToRefs(orderStore);
const router = useRouter();

const props = defineProps({
    showToast: Function,
});

// --- 2. STATE CỤC BỘ (BỘ LỌC) ---
const filters = ref({
    status: '',
    from_date: '',
    to_date: '',
    search: ''
});

// --- 3. STATE CỤC BỘ (MODAL) ---
const showModal = ref(false); // Modal Xác nhận (Duyệt/Từ chối)
const selectedOrder = ref(null);
const modalAction = ref('');

const showDetailModal = ref(false); // Modal Chi Tiết Đơn Hàng

// --- 4. COMPUTED: LỌC 'TÌM KIẾM' (Client-side) ---
const filteredOrders = computed(() => {
    const apiFilteredList = orders.value;
    if (!filters.value.search) {
        return apiFilteredList;
    }
    const searchTerm = filters.value.search.toLowerCase().trim();
    return apiFilteredList.filter(order => {
        const codeMatch = order.order_code?.toString().toLowerCase().includes(searchTerm);
        const customerMatch = order.customer_name?.toLowerCase().includes(searchTerm);
        return codeMatch || customerMatch;
    });
});

// --- 5. LIFECYCLE ---
onMounted(() => {
    orderStore.fetchOrders();
});

// --- 6. METHODS (XỬ LÝ LOGIC) ---
function handleApplyFilters() {
    const activeFilters = {};
    if (filters.value.status) activeFilters.status = filters.value.status;
    if (filters.value.from_date) activeFilters.from_date = filters.value.from_date;
    if (filters.value.to_date) activeFilters.to_date = filters.value.to_date;
    orderStore.fetchOrders(activeFilters);
}

// Mở modal xác nhận
function openConfirmationModal(action, order) {
    selectedOrder.value = order;
    modalAction.value = action;
    showModal.value = true;
}

// Đóng modal
function closeModal() {
    showModal.value = false;
    selectedOrder.value = null;
    modalAction.value = '';
}

// Xác nhận hành động (Duyệt / Từ chối)
async function handleConfirmAction() {
    if (!selectedOrder.value) return;
    let success = false;
    if (modalAction.value === 'approve') {
        success = await orderStore.approveOrder(selectedOrder.value.order_code);
        if (success && props.showToast) {
            props.showToast('Đơn hàng đã được duyệt thành công!', 'success');
        } else if (props.showToast) {
            props.showToast(`Lỗi: ${orderStore.error}`, 'error');
        }
    } else if (modalAction.value === 'reject') {
        success = await orderStore.rejectOrder(selectedOrder.value.order_code);
         if (success && props.showToast) {
            props.showToast('Đơn hàng đã được từ chối.', 'warning');
        } else if (props.showToast) {
            props.showToast(`Lỗi: ${orderStore.error}`, 'error');
        }
    }
    closeModal();
}

// Hàm XEM
async function handleViewOrder(orderCode) {
    // 1. Gọi API để lấy chi tiết
    await orderStore.fetchOrderDetail(orderCode);
    
    // 2. Nếu không lỗi, mở modal
    if (!orderStore.detailError) {
        showDetailModal.value = true;
    } else {
        // Báo lỗi nếu có
        if (props.showToast) {
            props.showToast(orderStore.detailError, 'error');
        }
    }
}

// Đóng modal chi tiết
function closeDetailModal() {
    showDetailModal.value = false;
    orderStore.currentOrder = null;
    orderStore.detailError = null;
}

// --- 7. HELPER FUNCTIONS (Copy từ code của bạn) ---
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
    <AppLayout>
        <main class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
            
            <div class="filter-card rounded-lg p-6 mb-8 table-shadow">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Bộ lọc tìm kiếm</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Trạng thái</label> 
                        <select v-model="filters.status" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Tất cả</option>
                            <option value="processing">Chờ duyệt</option>
                            <option value="shipped">Đang giao</option>
                            <option value="delivered">Đã giao</option>
                            <option value="cancelled">Đã hủy</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Từ ngày</label> 
                        <input v-model="filters.from_date" type="date" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Đến ngày</label> 
                        <input v-model="filters.to_date" type="date" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tìm kiếm</label> 
                        <input v-model="filters.search" type="text" placeholder="Mã đơn hàng, khách hàng..." class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div class="flex items-end">
                        <button @click="handleApplyFilters" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-md transition duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
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
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mã đơn hàng</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Khách hàng</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sản phẩm</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tổng tiền</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Trạng thái</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ngày tạo</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hành động</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-if="isLoading">
                                <td colspan="7" class="p-6 text-center text-gray-500">Đang tải dữ liệu...</td>
                            </tr>
                            <tr v-else-if="filteredOrders.length === 0">
                                <td colspan="7" class="p-6 text-center text-gray-500">Không tìm thấy đơn hàng nào.</td>
                            </tr>
                            <tr v-for="order in filteredOrders" :key="order.order_code" class="table-row">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">#{{ order.order_code }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ order.customer_name || 'N/A' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ order.items ? order.items.length : 1 }} sản phẩm</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-semibold text-gray-900">{{ (order.final_amount) }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="status-badge" :class="getStatusBadgeClass(order.status)">
                                        {{ getStatusText(order.status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-500">{{ (order.created_date) }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <template v-if="order.status === 'processing'">
                                            <button @click="openConfirmationModal('approve', order)" class="action-btn bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded-md">
                                                Duyệt
                                            </button>
                                            <button @click="openConfirmationModal('reject', order)" class="action-btn bg-red-300 text-white px-3 py-1 rounded-md cursor-not-allowed" disabled>
                                                Từ chối
                                            </button>
                                        </template>
                                        <button @click="handleViewOrder(order.order_code)" class="action-btn bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded-md">
                                            Xem
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    
        <div v-if="showModal" class="fixed inset-0 bg-gray-600 bg-opacity-75 flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-lg p-8 max-w-md w-full mx-4 shadow-xl text-center">
                <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-green-100 mb-4">
                    <fa :icon="['fas', 'check']" class="h-8 w-8 text-green-600" />
                </div>
                <h3 class="text-2xl font-semibold text-gray-900 mb-2">
                    Xác nhận đơn hàng
                </h3>
                <p class="text-sm text-gray-500 mb-6">
                    Bạn có chắc chắn muốn 
                    <strong v-if="modalAction === 'approve'" class="font-bold text-green-600">xác nhận</strong>
                    <strong v-if="modalAction === 'reject'" class="font-bold text-red-600">từ chối</strong>
                    đơn hàng này?
                </p>
                <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 text-left mb-8">
                    <div class="flex justify-between text-sm mb-3">
                        <span class="text-gray-600">Mã đơn hàng:</span>
                        <span class="font-semibold text-gray-900">#{{ selectedOrder.order_code }}</span>
                    </div>
                    <div class="flex justify-between text-sm mb-3">
                        <span class="text-gray-600">Khách hàng:</span>
                        <span class="font-semibold text-gray-900">{{ selectedOrder.customer_name }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Giá trị:</span>
                        <span class="font-semibold text-red-600">{{ selectedOrder.final_amount }}</span>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <button @click="closeModal" class="w-full px-4 py-3 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition duration-200 focus:outline-none focus:ring-2 focus:ring-gray-300">
                        Hủy
                    </button>
                    <button @click="handleConfirmAction" :class="[
                                'w-full px-4 py-3 text-sm font-medium text-white rounded-lg transition duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2',
                                modalAction === 'approve' ? 'bg-green-600 hover:bg-green-700 focus:ring-green-500' : 'bg-red-600 hover:bg-red-700 focus:ring-red-500'
                            ]">
                        Xác nhận
                    </button>
                </div>
            </div>
        </div>


        <div v-if="showDetailModal" class="fixed inset-0 bg-gray-900 bg-opacity-75 flex items-center justify-center z-50 p-4">
            
            <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full max-h-[90vh] flex flex-col">
                
                <div class="flex justify-between items-center p-4 border-b">
                    <div class="flex items-center">
                        <fa :icon="['fas', 'file-invoice']" class="h-5 w-5 text-gray-700 mr-2" />
                        <h3 class="text-lg font-semibold text-gray-900">
                            Chi tiết đơn hàng 
                            <span v-if="!isDetailLoading && currentOrder" class="text-blue-600">
                                #{{ currentOrder.order_code }}
                            </span>
                        </h3>
                    </div>
                    <button @click="closeDetailModal" class="text-gray-400 hover:text-gray-600">
                        <fa :icon="['fas', 'times']" class="h-5 w-5" />
                    </button>
                </div>

                <div class="p-6 overflow-y-auto">
                    <div v-if="isDetailLoading" class="text-center py-10">
                        <p class="text-gray-500 mt-2">Đang tải chi tiết...</p>
                    </div>

                    <div v-if="!isDetailLoading && detailError" class="text-center py-10">
                        <p class="text-red-600">{{ detailError }}</p>
                    </div>
                    
                    <div v-if="!isDetailLoading && currentOrder">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <h4 class="text-md font-semibold text-gray-800 mb-3 border-b pb-2">Thông tin khách hàng</h4>
                                <div class="space-y-2 text-sm">
                                    <p><strong class="text-gray-600 w-20 inline-block">Tên:</strong> {{ currentOrder.customer_name }}</p>
                                    <p><strong class="text-gray-600 w-20 inline-block">Email:</strong> {{ currentOrder.customer_email }}</p>
                                    <p><strong class="text-gray-600 w-20 inline-block">Điện thoại:</strong> {{ currentOrder.customer_phone }}</p>
                                    <p><strong class="text-gray-600 w-20 inline-block">Địa chỉ:</strong> {{ currentOrder.shipping_address }}</p>
                                </div>
                            </div>
                            <div>
                                <h4 class="text-md font-semibold text-gray-800 mb-3 border-b pb-2">Thông tin đơn hàng</h4>
                                <div class="space-y-2 text-sm">
                                    <p><strong class="text-gray-600 w-32 inline-block">Mã đơn:</strong> #{{ currentOrder.order_code }}</p>
                                    <p><strong class="text-gray-600 w-32 inline-block">Ngày đặt:</strong> {{ currentOrder.order_date }}</p>
                                    <p class="flex items-center">
                                        <strong class="text-gray-600 w-32 inline-block">Trạng thái:</strong> 
                                        <span class="status-badge" :class="getStatusBadgeClass(currentOrder.status)">
                                            {{ getStatusText(currentOrder.status) }}
                                        </span>
                                    </p>
                                    <p><strong class="text-gray-600 w-32 inline-block">Thanh toán:</strong> {{ currentOrder.payment_method }}</p>
                                </div>
                            </div>
                        </div>

                        <div>
                            <h4 class="text-md font-semibold text-gray-800 mb-3 border-b pb-2">Sản phẩm</h4>
                            <div class="border rounded-lg overflow-hidden">
                                <table class="min-w-full text-sm">
                                    <thead class="bg-gray-50">
                                        <tr class="text-left">
                                            <th class="p-3 font-medium text-gray-600">Sản phẩm</th>
                                            <th class="p-3 font-medium text-gray-600 text-center">Số lượng</th>
                                            <th class="p-3 font-medium text-gray-600 text-right">Đơn giá</th>
                                            <th class="p-3 font-medium text-gray-600 text-right">Thành tiền</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y">
                                        <tr v-for="(product, index) in currentOrder.products" :key="index">
                                            <td class="p-3">{{ product.product_name }}</td>
                                            <td class="p-3 text-center">{{ product.quantity }}</td>
                                            <td class="p-3 text-right">{{ product.price }}</td>
                                            <td class="p-3 text-right font-semibold">{{ product.subtotal }}</td>
                                        </tr>
                                    </tbody>
                                    <tfoot class="bg-gray-50 border-t-2">
                                        <tr>
                                            <td colspan="3" class="p-3 text-right font-bold text-gray-800">Tổng cộng:</td>
                                            <td class="p-3 text-right font-bold text-red-600 text-base">{{ currentOrder.total_amount }}</td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="p-4 bg-gray-50 border-t flex justify-end space-x-3">
                    <button @click="closeDetailModal" 
                            class="px-5 py-2 text-sm font-medium text-gray-700 bg-white border rounded-lg hover:bg-gray-100 transition duration-200">
                        Đóng
                    </button>
                    <button class="px-5 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition duration-200 flex items-center">
                        <fa :icon="['fas', 'print']" class="h-4 w-4 mr-2" />
                        In đơn hàng
                    </button>
                </div>
            </div>
        </div>

    </AppLayout>
</template>

<style scoped>
/* (ĐÃ SỬA LỖI CSS) */
.custom-scrollbar::-webkit-scrollbar {
    width: 8px;
    height: 8px;
}
.custom-scrollbar::-webkit-scrollbar-track {
    background: #f1f5f9;
    border-radius: 4px;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 4px;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}
.table-shadow {
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
}
.filter-card {
    background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
    border: 1px solid #e2e8f0;
}
.status-badge {
    font-size: 0.75rem;
    font-weight: 600;
    padding: 0.25rem 0.75rem;
    border-radius: 9999px;
    text-transform: uppercase;
    letter-spacing: 0.025em;
}
.status-pending {
    background-color: #fef3c7;
    color: #92400e;
}
.status-approved {
    background-color: #d1fae5;
    color: #065f46;
}
.status-rejected {
    background-color: #fee2e2;
    color: #991b1b;
}
.status-processing {
    background-color: #dbeafe;
    color: #1e40af;
}
.action-btn {
    transition: all 0.2s ease-in-out;
    font-weight: 500;
    font-size: 0.875rem;
}
.action-btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
}
.table-row:hover {
    background-color: #f8fafc;
    transition: background-color 0.15s ease-in-out;
}
.stats-card {
    background: linear-gradient(135deg, #ffffff 0%, #f1f5f9 100%);
    border-left: 4px solid #3b82f6;
}
</style>