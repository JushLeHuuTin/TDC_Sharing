<script setup>
import { ref, onMounted, computed } from 'vue';
import { storeToRefs } from 'pinia';
import { useSellerOrderStore } from '@/stores/sellerOrderStore';
import { useRouter } from 'vue-router';
import AppLayout from '@/Layouts/AppLayout.vue';

// --- INIT ---
const orderStore = useSellerOrderStore();
const { orders, isLoading, error, currentOrder, isDetailLoading } = storeToRefs(orderStore);
const router = useRouter();

const props = defineProps({
    showToast: Function,
});

// --- STATE ---
const filters = ref({ status: '', from_date: '', to_date: '', search: '' });
const showModal = ref(false); // Modal Xác nhận
const showDetailModal = ref(false); // Modal Chi tiết
const selectedOrder = ref(null);
const modalAction = ref(''); 

// --- COMPUTED ---
const filteredOrders = computed(() => {
    const apiFilteredList = orders.value || [];
    if (!filters.value.search) return apiFilteredList;

    const searchTerm = filters.value.search.toLowerCase().trim();
    return apiFilteredList.filter(order => {
        const code = String(order.order_code || '').toLowerCase();
        const name = String(order.customer_name || '').toLowerCase();
        return code.includes(searchTerm) || name.includes(searchTerm);
    });
});

// --- LIFECYCLE ---
onMounted(() => {
    orderStore.fetchOrders();
});

// --- METHODS ---
function handleApplyFilters() {
    const activeFilters = {};
    if (filters.value.status) activeFilters.status = filters.value.status;
    if (filters.value.from_date) activeFilters.from_date = filters.value.from_date;
    if (filters.value.to_date) activeFilters.to_date = filters.value.to_date;
    orderStore.fetchOrders(activeFilters);
}

// Modal Xác nhận
function openConfirmationModal(action, order) {
    selectedOrder.value = order;
    modalAction.value = action;
    showModal.value = true;
}

function closeModal() {
    showModal.value = false;
    selectedOrder.value = null;
    modalAction.value = '';
}

async function handleConfirmAction() {
    if (!selectedOrder.value) return;
    const orderId = selectedOrder.value.id || selectedOrder.value.order_code; 
    let success = false;

    if (modalAction.value === 'approve') {
        success = await orderStore.approveOrder(orderId);
        if (success && props.showToast) props.showToast('Đã duyệt đơn hàng!', 'success');
        else if (props.showToast) props.showToast(`Lỗi: ${orderStore.error}`, 'error');
    } else if (modalAction.value === 'reject') {
        success = await orderStore.rejectOrder(orderId);
         if (success && props.showToast) props.showToast('Đã từ chối đơn hàng.', 'warning');
        else if (props.showToast) props.showToast(`Lỗi: ${orderStore.error}`, 'error');
    }
    closeModal();
}

// --- XỬ LÝ NÚT XEM (MỞ MODAL) ---
async function handleViewOrder(orderId) {
    showDetailModal.value = true;
    await orderStore.fetchOrderDetail(orderId);
}

function closeDetailModal() {
    showDetailModal.value = false;
    orderStore.currentOrder = null;
}

// --- HELPER ---
function getStatusText(status) {
    const statusMap = { 'pending': 'Chờ duyệt', 'processing': 'Chờ duyệt', 'approved': 'Đã duyệt', 'shipped': 'Đang giao', 'delivered': 'Đã giao', 'rejected': 'Đã hủy', 'cancelled': 'Đã hủy' };
    return statusMap[status] || status;
}
function getStatusBadgeClass(status) {
    const statusClassMap = { 'pending': 'status-pending', 'processing': 'status-pending', 'approved': 'status-approved', 'shipped': 'status-processing', 'delivered': 'status-approved', 'rejected': 'status-rejected', 'cancelled': 'status-rejected' };
    return statusClassMap[status] || 'bg-gray-200 text-gray-800';
}
</script>

<template>
    <AppLayout>
        <main class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
            
            <!-- Lời chào Seller -->
            <div class="mb-8">
                <SellerWelcome />
            </div>

            <!-- BỘ LỌC TÌM KIẾM -->
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
    
            <!-- BẢNG DỮ LIỆU -->
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
                            <tr v-for="order in filteredOrders" :key="order.id || order.order_code" class="table-row">
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
                                            <button @click="openConfirmationModal('reject', order)" class="action-btn bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded-md">
                                                Từ chối
                                            </button>
                                        </template>
                                        <button @click="handleViewOrder(order.id || order.order_code)" class="action-btn bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded-md">
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
    
        <!-- 1. MODAL XÁC NHẬN (GIAO DIỆN ĐẸP - GIỐNG ẢNH) -->
        <div v-if="showModal" class="fixed inset-0 bg-gray-900 bg-opacity-75 flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-xl shadow-2xl max-w-md w-full overflow-hidden transform transition-all scale-100">
                <div class="p-6 text-center">
                    <!-- Icon -->
                    <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full mb-5"
                         :class="modalAction === 'approve' ? 'bg-green-100' : 'bg-red-100'">
                        <fa v-if="modalAction === 'approve'" :icon="['fas', 'check']" class="h-8 w-8 text-green-600" />
                        <fa v-else :icon="['fas', 'times']" class="h-8 w-8 text-red-600" />
                    </div>
                    
                    <!-- Tiêu đề -->
                    <h3 class="text-2xl font-bold text-gray-900 mb-2">
                        {{ modalAction === 'approve' ? 'Xác nhận đơn hàng' : 'Từ chối đơn hàng' }}
                    </h3>
                    
                    <!-- Mô tả -->
                    <p class="text-gray-500 mb-6">
                        Bạn có chắc chắn muốn 
                        <strong :class="modalAction === 'approve' ? 'text-green-600' : 'text-red-600'">
                            {{ modalAction === 'approve' ? 'xác nhận' : 'từ chối' }}
                        </strong> 
                        đơn hàng này không?
                    </p>

                    <!-- Box thông tin đơn hàng -->
                    <div class="bg-gray-50 rounded-lg p-4 text-left mb-6 border border-gray-100">
                        <div class="flex justify-between mb-2">
                            <span class="text-gray-500 text-sm">Mã đơn hàng:</span>
                            <span class="font-bold text-gray-800">#{{ selectedOrder.order_code }}</span>
                        </div>
                        <div class="flex justify-between mb-2">
                            <span class="text-gray-500 text-sm">Khách hàng:</span>
                            <span class="font-bold text-gray-800">{{ selectedOrder.customer_name }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500 text-sm">Giá trị:</span>
                            <span class="font-bold text-blue-600">{{ selectedOrder.final_amount || selectedOrder.total_amount }}</span>
                        </div>
                    </div>

                    <!-- Nút bấm -->
                    <div class="grid grid-cols-2 gap-4">
                        <button @click="closeModal" 
                                class="w-full py-3 px-4 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold rounded-lg transition-colors">
                            Hủy
                        </button>
                        <button @click="handleConfirmAction" 
                                :class="[
                                    'w-full py-3 px-4 text-white font-bold rounded-lg transition-colors shadow-md',
                                    modalAction === 'approve' ? 'bg-green-600 hover:bg-green-700 shadow-green-200' : 'bg-red-600 hover:bg-red-700 shadow-red-200'
                                ]">
                            {{ modalAction === 'approve' ? 'Xác nhận' : 'Từ chối' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. MODAL CHI TIẾT ĐƠN HÀNG (GIAO DIỆN ĐẸP) -->
        <div v-if="showDetailModal" class="fixed inset-0 bg-gray-900 bg-opacity-75 flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-lg shadow-2xl max-w-3xl w-full max-h-[90vh] flex flex-col overflow-hidden">
                <!-- ... (Giữ nguyên code modal chi tiết của bạn) ... -->
                <!-- Header Modal -->
                <div class="flex justify-between items-center px-6 py-4 border-b bg-white">
                    <h3 class="text-lg font-bold text-gray-800 flex items-center">
                        <fa :icon="['fas', 'file-invoice']" class="mr-2 text-gray-600" />
                        Chi tiết đơn hàng {{ currentOrder?.order_code }}
                    </h3>
                    <button @click="closeDetailModal" class="text-gray-400 hover:text-gray-600 text-2xl">&times;</button>
                </div>
                
                <!-- Body Modal -->
                <div class="p-6 overflow-y-auto bg-gray-50">
                    <div v-if="isDetailLoading" class="text-center py-10">
                        <div class="spinner-border text-blue-600" role="status"><span class="visually-hidden">Loading...</span></div>
                        <p class="text-gray-500 mt-2">Đang tải thông tin...</p>
                    </div>
                    
                    <div v-else-if="currentOrder">
                        <!-- Thông tin chung (2 Cột) -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <!-- Cột Trái: Khách hàng -->
                            <div class="p-4 bg-white shadow-sm rounded-lg">
                                <h4 class="text-sm font-bold text-gray-700 uppercase mb-3 pb-1 border-b">Thông tin khách hàng</h4>
                                <div class="space-y-2 text-sm text-gray-600">
                                    <p><strong class="text-gray-900">Tên:</strong> {{ currentOrder.customer_name }}</p>
                                    <p><strong class="text-gray-900">Email:</strong> {{ currentOrder.customer_email }}</p>
                                    <p><strong class="text-gray-900">SĐT:</strong> {{ currentOrder.customer_phone }}</p>
                                    <p><strong class="text-gray-900">Địa chỉ:</strong> {{ currentOrder.shipping_address }}</p>
                                </div>
                            </div>

                            <!-- Cột Phải: Đơn hàng -->
                            <div class="p-4 bg-white shadow-sm rounded-lg">
                                <h4 class="text-sm font-bold text-gray-700 uppercase mb-3 pb-1 border-b">Thông tin đơn hàng</h4>
                                <div class="space-y-2 text-sm text-gray-600">
                                    <p><strong class="text-gray-900">Mã đơn:</strong> #{{ currentOrder.order_code }}</p>
                                    <p><strong class="text-gray-900">Ngày đặt:</strong> {{ currentOrder.order_date }}</p>
                                    <p><strong class="text-gray-900">Thanh toán:</strong> {{ currentOrder.payment_method }}</p>
                                    <p class="flex items-center">
                                        <strong class="text-gray-900 mr-2">Trạng thái:</strong> 
                                        <span class="px-2 py-0.5 rounded text-xs font-bold uppercase" :class="getStatusBadgeClass(currentOrder.status)">
                                            {{ getStatusText(currentOrder.status) }}
                                        </span>
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Danh sách sản phẩm -->
                        <div class="bg-white shadow-sm rounded-lg overflow-hidden mb-6">
                            <div class="px-4 py-3 bg-gray-100 border-b border-gray-200 font-bold text-gray-700 text-sm">Sản phẩm</div>
                            <table class="w-full text-sm text-left">
                                <thead class="bg-gray-50 text-gray-600 font-medium border-b">
                                    <tr>
                                        <th class="p-3 text-left">Sản phẩm</th>
                                        <th class="p-3 text-center">Số lượng</th>
                                        <th class="p-3 text-right">Đơn giá</th>
                                        <th class="p-3 text-right">Thành tiền</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    <tr v-for="(item, idx) in currentOrder.products" :key="idx">
                                        <td class="p-3 font-medium text-gray-900">{{ item.product_name }}</td>
                                        <td class="p-3 text-center">{{ item.quantity }}</td>
                                        <td class="p-3 text-right">{{ item.price }}</td>
                                        <td class="p-3 text-right font-bold text-gray-900">{{ item.subtotal }}</td>
                                    </tr>
                                </tbody>
                                <tfoot>
                                    <tr class="bg-gray-50 font-bold">
                                        <td colspan="3" class="p-3 text-right">Tổng cộng:</td>
                                        <td class="p-3 text-right text-red-600 text-lg">{{ currentOrder.total_amount }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                    
                    <div v-else class="text-center text-red-500 py-10">
                        Không tìm thấy thông tin chi tiết.
                    </div>
                </div>

                <!-- Footer Modal -->
                <div class="p-4 border-t bg-white text-right">
                    <button @click="closeDetailModal" class="bg-gray-600 text-white px-6 py-2 rounded hover:bg-gray-700">Đóng</button>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
/* CSS giữ nguyên */
.custom-scrollbar::-webkit-scrollbar { width: 8px; height: 8px; }
.custom-scrollbar::-webkit-scrollbar-track { background: #f1f5f9; border-radius: 4px; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
.table-shadow { box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06); }
.filter-card { background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%); border: 1px solid #e2e8f0; }
.status-badge { font-size: 0.75rem; font-weight: 600; padding: 0.25rem 0.75rem; border-radius: 9999px; text-transform: uppercase; letter-spacing: 0.025em; }
.status-pending { background-color: #fef3c7; color: #92400e; }
.status-approved { background-color: #d1fae5; color: #065f46; }
.status-rejected { background-color: #fee2e2; color: #991b1b; }
.status-processing { background-color: #dbeafe; color: #1e40af; }
.action-btn { transition: all 0.2s ease-in-out; font-weight: 500; font-size: 0.875rem; }
.action-btn:hover { transform: translateY(-1px); box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15); }
.table-row:hover { background-color: #f8fafc; transition: background-color 0.15s ease-in-out; }
.stats-card { background: linear-gradient(135deg, #ffffff 0%, #f1f5f9 100%); border-left: 4px solid #3b82f6; }
</style>