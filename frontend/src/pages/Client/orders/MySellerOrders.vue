<script setup>
import { ref, onMounted, computed } from 'vue';
import { storeToRefs } from 'pinia';
import { useSellerOrderStore } from '@/stores/sellerOrderStore';
import { useRouter } from 'vue-router';
import AppLayout from '@/Layouts/AppLayout.vue';

// --- 1. KHỞI TẠO STORE & ROUTER ---
const orderStore = useSellerOrderStore();
const { orders, isLoading, error } = storeToRefs(orderStore);
const router = useRouter();

const props = defineProps({
    showToast: Function,
});

// --- 2. STATE CỤC BỘ (BỘ LỌC) ---
const filters = ref({
    status: '',
    from_date: '',
    to_date: '',
    search: '' // Cho chức năng lọc client-side
});

// --- 3. STATE CỤC BỘ (MODAL) ---
const showModal = ref(false);
const selectedOrder = ref(null);
const modalAction = ref(''); // 'approve' hoặc 'reject'

// --- 4. COMPUTED: LỌC 'TÌM KIẾM' (Client-side) ---
const filteredOrders = computed(() => {
    // Lấy danh sách orders đã được lọc bởi API (status, date)
    const apiFilteredList = orders.value;

    if (!filters.value.search) {
        return apiFilteredList; // Trả về nếu không có nội dung search
    }

    const searchTerm = filters.value.search.toLowerCase().trim();
    
    // Lọc theo Mã đơn hàng (order_code) hoặc Tên khách hàng (customer_name)
    return apiFilteredList.filter(order => {
        // Chuyển đổi an toàn sang chuỗi trước khi gọi toLowerCase
        const code = order.order_code ? String(order.order_code).toLowerCase() : '';
        const name = order.customer_name ? String(order.customer_name).toLowerCase() : '';
        
        return code.includes(searchTerm) || name.includes(searchTerm);
    });
});


// --- 5. LIFECYCLE ---
onMounted(() => {
    // Tải đơn hàng ngay khi component được tạo
    orderStore.fetchOrders();
});

// --- 6. METHODS (XỬ LÝ LOGIC) ---

// Gọi API để lọc (chỉ status, date)
function handleApplyFilters() {
    const activeFilters = {};
    if (filters.value.status) activeFilters.status = filters.value.status;
    if (filters.value.from_date) activeFilters.from_date = filters.value.from_date;
    if (filters.value.to_date) activeFilters.to_date = filters.value.to_date;
    
    // 'search' không cần gửi đi, vì nó được lọc bởi 'filteredOrders'
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
    // QUAN TRỌNG: API cần ID (ví dụ: 1, 2, 5), KHÔNG PHẢI Order Code (ORD-...)
    // Kiểm tra xem object order có trường 'id' không. Nếu không có, fallback về 'order_code' nhưng có thể sai.
    const orderId = selectedOrder.value.id || selectedOrder.value.order_code; 

    if (modalAction.value === 'approve') {
        success = await orderStore.approveOrder(orderId);
        if (success && props.showToast) {
            props.showToast('Đơn hàng đã được duyệt thành công!', 'success');
        } else if (props.showToast) {
            props.showToast(`Lỗi: ${orderStore.error}`, 'error');
        }

    } else if (modalAction.value === 'reject') {
        // Gọi API Từ chối
        success = await orderStore.rejectOrder(orderId);
         if (success && props.showToast) {
            props.showToast('Đơn hàng đã được từ chối.', 'warning');
        } else if (props.showToast) {
            props.showToast(`Lỗi: ${orderStore.error}`, 'error');
        }
    }
    
    closeModal();
}

// Chuyển sang trang chi tiết (Giai đoạn 4)
function handleViewOrder(orderId) {
    // Dùng ID để xem chi tiết (khớp với API chi tiết /api/seller/orders/{id})
    // Nếu router của bạn dùng ID thì truyền ID, dùng Slug thì truyền Slug. 
    // Ở đây giả định dùng ID.
    router.push({ name: 'seller.orders.detail', params: { id: orderId } });
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
            
            <!-- 1. BỘ LỌC TÌM KIẾM -->
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
    
            <!-- 2. BẢNG DỮ LIỆU -->
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
                            <!-- Dùng 'filteredOrders' thay vì 'orders' -->
                            <tr v-else-if="filteredOrders.length === 0">
                                <td colspan="7" class="p-6 text-center text-gray-500">Không tìm thấy đơn hàng nào.</td>
                            </tr>
                            <!-- Dùng 'filteredOrders' và 'order.id' làm key (cho chắc chắn) -->
                            <tr v-for="order in filteredOrders" :key="order.id" class="table-row">
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
                                            <!-- Nút Từ chối ĐÃ ĐƯỢC KÍCH HOẠT -->
                                            <button @click="openConfirmationModal('reject', order)" class="action-btn bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded-md">
                                                Từ chối
                                            </button>
                                        </template>
                                        <!-- Dùng 'order.id' (nếu router cần ID) -->
                                        <button @click="handleViewOrder(order.id)" class="action-btn bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded-md">
                                            Xem
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <!-- ... (Phần Phân trang giữ nguyên) ... -->
            </div>
        </main>
    
        <!-- 3. MODAL XÁC NHẬN -->
        <div v-if="showModal" class="fixed inset-0 bg-gray-600 bg-opacity-75 flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-lg p-8 max-w-md w-full mx-4 shadow-xl text-center">
                <!-- Icon -->
                <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-green-100 mb-4" v-if="modalAction === 'approve'">
                    <fa :icon="['fas', 'check']" class="h-8 w-8 text-green-600" />
                </div>
                <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-100 mb-4" v-else>
                    <fa :icon="['fas', 'times']" class="h-8 w-8 text-red-600" />
                </div>

                <!-- Tiêu đề -->
                <h3 class="text-2xl font-semibold text-gray-900 mb-2">
                    Xác nhận hành động
                </h3>
                <!-- Mô tả -->
                <p class="text-sm text-gray-500 mb-6">
                    Bạn có chắc chắn muốn 
                    <strong v-if="modalAction === 'approve'" class="font-bold text-green-600">duyệt</strong>
                    <strong v-if="modalAction === 'reject'" class="font-bold text-red-600">từ chối</strong>
                    đơn hàng này?
                </p>
                <!-- Thông tin đơn hàng (Box màu xám) -->
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
                <!-- Hàng nút bấm -->
                <div class="grid grid-cols-2 gap-4">
                    <button @click="closeModal" class="w-full px-4 py-3 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition duration-200 focus:outline-none focus:ring-2 focus:ring-gray-300">
                        Hủy
                    </button>
                    <button @click="handleConfirmAction" 
                            :class="[
                                'w-full px-4 py-3 text-sm font-medium text-white rounded-lg transition duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2',
                                modalAction === 'approve' ? 'bg-green-600 hover:bg-green-700 focus:ring-green-500' : 'bg-red-600 hover:bg-red-700 focus:ring-red-500'
                            ]">
                        Xác nhận
                    </button>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
/* (Giữ nguyên toàn bộ CSS của bạn) */
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