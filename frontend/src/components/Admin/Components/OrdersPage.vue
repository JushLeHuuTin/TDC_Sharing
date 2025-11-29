<script setup>
// ... (Giữ nguyên script setup cũ) ...
import { ref, onMounted } from 'vue';
import { storeToRefs } from 'pinia';
import { useAdminOrderStore } from '@/stores/adminOrderStore'; 
import Swal from 'sweetalert2';

// 1. Khởi tạo Store
const adminOrderStore = useAdminOrderStore();
const { orders, isLoading, currentOrder, pagination } = storeToRefs(adminOrderStore);

// Helper: Ngày đầu tháng và cuối tháng
const getFirstDayOfMonth = () => {
    const date = new Date();
    return new Date(date.getFullYear(), date.getMonth(), 1).toISOString().split('T')[0];
};
const getLastDayOfMonth = () => {
    const date = new Date();
    return new Date(date.getFullYear(), date.getMonth() + 1, 0).toISOString().split('T')[0];
};

// 2. State Filters
const filters = ref({
    status: '',
    search: '',
    from_date: getFirstDayOfMonth(),
    to_date: getLastDayOfMonth(),
    page: 1
});

const showDetailModal = ref(false);
const isDetailLoading = ref(false); 

// 3. Methods
function fetchData() {
    adminOrderStore.fetchOrders(filters.value);
}

function handleFilter() {
    filters.value.page = 1;
    fetchData();
}

function changePage(page) {
    if (page >= 1 && page <= pagination.value.lastPage) { // Sửa thành lastPage (camelCase) cho khớp với Store
        filters.value.page = page;
        fetchData();
    }
}

// ... (Các hàm openDetail, closeDetail, handleDelete, getStatusBadge, getStatusText giữ nguyên) ...
async function openDetail(orderId) {
    showDetailModal.value = true;
    isDetailLoading.value = true;
    await adminOrderStore.fetchOrderDetail(orderId);
    isDetailLoading.value = false;
}

function closeDetail() {
    showDetailModal.value = false;
    adminOrderStore.currentOrder = null;
}

async function handleDelete(order) {
    if (showDetailModal.value) showDetailModal.value = false;
    const result = await Swal.fire({
        title: 'Xóa đơn hàng?',
        text: `Hành động này sẽ xóa vĩnh viễn đơn hàng #${order.order_code}.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Xóa ngay',
        cancelButtonText: 'Hủy',
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6b7280'
    });

    if (result.isConfirmed) {
        const res = await adminOrderStore.deleteOrder(order.order_id || order.id);
        if (res.success) {
            Swal.fire('Đã xóa!', 'Đơn hàng đã được xóa thành công.', 'success');
            fetchData(); 
        } else {
            Swal.fire('Lỗi!', res.message, 'error');
        }
    }
}

function getStatusBadge(status) {
    switch(status) {
        case 'pending': return 'bg-yellow-100 text-yellow-800 border border-yellow-200';
        case 'processing': return 'bg-blue-50 text-blue-700 border border-blue-200';
        case 'shipped': return 'bg-purple-100 text-purple-700 border border-purple-200';
        case 'delivered': 
        case 'completed': return 'bg-green-100 text-green-700 border border-green-200';
        case 'cancelled': 
        case 'rejected': return 'bg-red-50 text-red-700 border border-red-200';
        default: return 'bg-gray-100 text-gray-600 border border-gray-200';
    }
}

function getStatusText(status) {
    const map = { 
        'pending': 'Chờ duyệt', 
        'processing': 'Đang xử lý', 
        'shipped': 'Đang giao', 
        'delivered': 'Hoàn thành', 
        'cancelled': 'Đã hủy',
        'rejected': 'Đã từ chối'
    };
    return map[status] || status;
}

onMounted(() => {
    fetchData();
});
</script>

<template>
    <div class="p-6 bg-gray-50 min-h-screen">
        <!-- ... (Phần Header và Bộ lọc giữ nguyên) ... -->
        <div class="max-w-7xl mx-auto">
            <div class="mb-8 flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 flex items-center">
                        <fa :icon="['fas', 'tasks']" class="mr-3 text-indigo-600" />
                        Quản lý tất cả đơn hàng
                    </h1>
                    <p class="text-gray-500 text-sm mt-1">Theo dõi toàn bộ giao dịch trên hệ thống.</p>
                </div>
            </div>

            <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100 mb-6">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                    <div class="md:col-span-2">
                        <label class="block text-xs font-semibold text-gray-500 mb-1">Tìm kiếm</label>
                        <div class="relative">
                             <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <fa :icon="['fas', 'search']" class="text-gray-400" />
                            </div>
                            <input v-model="filters.search" type="text" 
                                class="block w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm transition duration-150 ease-in-out"
                                placeholder="Mã đơn, khách hàng..." 
                                @keyup.enter="handleFilter">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 mb-1">Trạng thái</label>
                        <select v-model="filters.status" class="block w-full pl-3 pr-10 py-2.5 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-lg bg-white">
                            <option value="">Tất cả</option>
                            <option value="pending">Chờ duyệt</option>
                            <option value="processing">Đang xử lý</option>
                            <option value="shipped">Đang giao</option>
                            <option value="delivered">Hoàn thành</option>
                            <option value="cancelled">Đã hủy</option>
                        </select>
                    </div>
                    <div>
                        <button @click="handleFilter" class="w-full bg-indigo-600 text-white py-2.5 rounded-lg hover:bg-indigo-700 transition-colors flex justify-center items-center h-[42px]">
                            <fa :icon="['fas', 'filter']" class="mr-2" /> Lọc
                        </button>
                    </div>
                </div>
            </div>

            <!-- 2. DANH SÁCH ĐƠN HÀNG -->
            <div class="bg-white rounded-xl shadow-sm border overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <!-- ... (Phần thead và tbody giữ nguyên) ... -->
                         <thead class="bg-gray-50 text-gray-700 uppercase font-semibold text-xs">
                            <tr>
                                <th class="px-6 py-3 text-left tracking-wider">Mã đơn</th>
                                <th class="px-6 py-3 text-left tracking-wider">Khách hàng</th>
                                <th class="px-6 py-3 text-left tracking-wider">Người bán</th> 
                                <th class="px-6 py-3 text-left tracking-wider">Tổng tiền</th>
                                <th class="px-6 py-3 text-center tracking-wider">Trạng thái</th>
                                <th class="px-6 py-3 text-left tracking-wider">Ngày tạo</th>
                                <th class="px-6 py-3 text-right tracking-wider">Hành động</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <tr v-if="isLoading">
                                <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                    <fa :icon="['fas', 'spinner']" spin class="text-3xl text-indigo-500" />
                                    <p class="mt-2 text-sm">Đang tải dữ liệu...</p>
                                </td>
                            </tr>
                            <tr v-else-if="!orders || orders.length === 0">
                                <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                    <fa :icon="['far', 'folder-open']" class="text-4xl text-gray-300 mb-3" />
                                    <p>Không có dữ liệu đơn hàng nào.</p>
                                </td>
                            </tr>
                            
                            <tr v-for="order in orders" :key="order.order_id" class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-bold text-indigo-600">#{{ order.order_code || order.order_id }}</div>
                                    <div class="text-xs text-gray-400 mt-0.5">{{ order.items_count }} sản phẩm</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ order.customer_name || 'N/A' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <fa :icon="['fas', 'store']" class="text-gray-400 mr-2" />
                                        <span class="text-sm text-gray-700">{{ order.seller_name || 'N/A' }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-bold text-gray-900">{{ order.total_price || order.final_amount }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <span :class="['inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium', getStatusBadge(order.status)]">
                                        {{ getStatusText(order.status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ order.order_date || order.created_date }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex justify-end space-x-2">
                                        <button @click="openDetail(order.order_id)" class="text-gray-400 hover:text-indigo-600 p-1.5 rounded-md hover:bg-indigo-50 transition-colors" title="Xem chi tiết">
                                            <fa :icon="['fas', 'eye']" />
                                        </button>
                                        <button @click="handleDelete(order)" class="text-gray-400 hover:text-red-600 p-1.5 rounded-md hover:bg-red-50 transition-colors" title="Xóa đơn hàng">
                                            <fa :icon="['fas', 'trash-alt']" />
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- --- PHÂN TRANG (PAGINATION) --- -->
                <div v-if="pagination && pagination.lastPage > 1" class="bg-white px-4 py-3 border-t border-gray-200 flex items-center justify-between sm:px-6">
                     <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                        <div>
                            <p class="text-sm text-gray-700">
                                Hiển thị trang <span class="font-medium">{{ pagination.currentPage }}</span> / <span class="font-medium">{{ pagination.lastPage }}</span>
                                (Tổng {{ pagination.total }} đơn)
                            </p>
                        </div>
                        <div>
                            <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                                <!-- Nút Trước -->
                                <button @click="changePage(pagination.currentPage - 1)" 
                                    :disabled="pagination.currentPage === 1" 
                                    class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed">
                                    <span class="sr-only">Previous</span>
                                    <fa :icon="['fas', 'chevron-left']" class="h-4 w-4" />
                                </button>
                                
                                <!-- Số trang -->
                                <span class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700">
                                    {{ pagination.currentPage }}
                                </span>

                                <!-- Nút Sau -->
                                <button @click="changePage(pagination.currentPage + 1)" 
                                    :disabled="pagination.currentPage === pagination.lastPage" 
                                    class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed">
                                    <span class="sr-only">Next</span>
                                    <fa :icon="['fas', 'chevron-right']" class="h-4 w-4" />
                                </button>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>

            <!-- MODAL CHI TIẾT (Giữ nguyên code modal cũ) -->
            <!-- ... (Phần modal chi tiết giữ nguyên từ file cũ, chỉ cần copy paste lại phần template của modal) ... -->
            <div v-if="showDetailModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black bg-opacity-50" @click.self="closeDetail">
                <div class="bg-white rounded-xl shadow-2xl w-full max-w-3xl max-h-[90vh] overflow-hidden flex flex-col">
                    <div class="p-5 border-b flex justify-between items-center bg-gray-50">
                        <h3 class="text-lg font-bold">Chi tiết đơn hàng #{{ currentOrder?.order_code }}</h3>
                        <button @click="closeDetail" class="text-gray-500 hover:text-gray-700 text-xl">&times;</button>
                    </div>
                    <div class="p-6 overflow-y-auto" v-if="currentOrder">
                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div>
                                <p class="text-sm text-gray-500">Khách hàng</p>
                                <p class="font-medium">{{ currentOrder.customer_name }}</p>
                                <p class="text-sm">{{ currentOrder.customer_phone }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Người bán</p>
                                <p class="font-medium">{{ currentOrder.seller_name }}</p>
                            </div>
                        </div>
                        <div class="border rounded-lg overflow-hidden">
                            <table class="min-w-full text-sm text-left">
                                <thead class="bg-gray-100">
                                    <tr>
                                        <th class="p-3">Sản phẩm</th>
                                        <th class="p-3 text-center">SL</th>
                                        <th class="p-3 text-right">Giá</th>
                                        <th class="p-3 text-right">Thành tiền</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y">
                                    <tr v-for="item in currentOrder.items" :key="item.id">
                                        <td class="p-3">{{ item.product_name }}</td>
                                        <td class="p-3 text-center">{{ item.quantity }}</td>
                                        <td class="p-3 text-right">{{ Number(item.price).toLocaleString() }} đ</td>
                                        <td class="p-3 text-right font-bold">{{ Number(item.subtotal).toLocaleString() }} đ</td>
                                    </tr>
                                </tbody>
                                <tfoot class="bg-gray-50 font-bold">
                                    <tr>
                                        <td colspan="3" class="p-3 text-right">Tổng cộng:</td>
                                        <td class="p-3 text-right text-red-600 text-lg">{{ currentOrder.final_amount }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>