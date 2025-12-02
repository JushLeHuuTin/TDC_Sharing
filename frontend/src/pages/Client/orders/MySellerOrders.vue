<script setup>
import { ref, onMounted, computed } from 'vue';
import { storeToRefs } from 'pinia';
import { useSellerOrderStore } from '@/stores/sellerOrderStore';
import { useRouter } from 'vue-router';
import Swal from 'sweetalert2';
import AppLayout from '@/Layouts/AppLayout.vue';
import BasePagination from '@/components/BasePagination.vue';


// 1. Khởi tạo Store & Router
const sellerOrderStore = useSellerOrderStore();
const { orders, isLoading, currentOrder, isDetailLoading, pagination } = storeToRefs(sellerOrderStore);
const router = useRouter();

// Helper: Lấy ngày đầu tháng và cuối tháng
const getFirstDayOfMonth = () => {
    const date = new Date();
    return new Date(date.getFullYear(), date.getMonth(), 1).toISOString().split('T')[0];
};

const getLastDayOfMonth = () => {
    const date = new Date();
    return new Date(date.getFullYear(), date.getMonth() + 1, 0).toISOString().split('T')[0]; // Ngày cuối tháng
};

// 2. Định nghĩa State (Filters) - Mặc định từ đầu tháng đến cuối tháng
const filters = ref({
    status: '',
    search: '',
    from_date: '',
    to_date: '',
    page: 1
});
const handlePageChange = (page) => {
    // 1. Cập nhật khóa 'page' trong đối tượng filters
    filters.value.page = page; 

    // 2. Chỉ truyền đối tượng filters (đã có page mới)
    sellerOrderStore.fetchOrders(filters.value); 
};
const showDetailModal = ref(false);

// 3. Định nghĩa các hàm (Methods)

function fetchData() {
    sellerOrderStore.fetchOrders(filters.value);
}

function handleFilter() {
    filters.value.page = 1;
    fetchData();
}

function changePage(page) {
    if (page >= 1 && page <= pagination.value.last_page) {
        filters.value.page = page;
        fetchData();
    }
}

async function openDetail(orderId) {
    showDetailModal.value = true;
    await sellerOrderStore.fetchOrderDetail(orderId);
}

function closeDetail() {
    showDetailModal.value = false;
    sellerOrderStore.currentOrder = null;
}

// 1. DUYỆT ĐƠN (Pending -> Processing)
async function handleApprove(order) {
    const result = await Swal.fire({
        title: 'Duyệt đơn hàng?',
        text: `Xác nhận duyệt đơn #${order.order_code || order.id}? Trạng thái sẽ chuyển sang "đã duyệt".`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Duyệt ngay',
        cancelButtonText: 'Hủy',
        confirmButtonColor: '#3b82f6', // Xanh dương
        cancelButtonColor: '#6b7280'
    });

    if (result.isConfirmed) {
        // Gọi API duyệt đơn
        const res = await sellerOrderStore.approveOrder(order.id);
        
        if (res.success) {
            Swal.fire({
                title: 'Thành công!', 
                text: 'Đơn hàng đang được xử lý (Processing).', 
                icon: 'success',
                timer: 1500,
                showConfirmButton: false
            });
            // Cập nhật lại danh sách để thấy trạng thái mới
            fetchData(); 
        } else {
            Swal.fire('Lỗi!', res.message, 'error');
        }
    }
}

// 2. GIAO HÀNG (Processing -> Shipped)
async function handleShip(order) {
    const result = await Swal.fire({
        title: 'Giao hàng?',
        text: `Xác nhận giao đơn #${order.order_code || order.id} cho đơn vị vận chuyển? Trạng thái sẽ chuyển sang "Đang giao".`,
        icon: 'info',
        showCancelButton: true,
        confirmButtonText: 'Xác nhận giao',
        cancelButtonText: 'Hủy',
        confirmButtonColor: '#8b5cf6', // Màu tím
        cancelButtonColor: '#6b7280'
    });

    if (result.isConfirmed) {
        // Gọi API giao hàng (cần đảm bảo store có hàm shipOrder)
        // Nếu store chưa có shipOrder, bạn có thể dùng tạm approveOrder nếu backend tự xử lý logic chuyển tiếp
        // Nhưng tốt nhất là có endpoint riêng hoặc tham số riêng.
        // Giả sử store có hàm shipOrder như đã bàn trước đó:
        const res = await sellerOrderStore.shipOrder(order.id);
        
        if (res.success) {
            Swal.fire({
                title: 'Đang giao!', 
                text: 'Đơn hàng đã chuyển sang trạng thái giao hàng (Shipped).', 
                icon: 'success',
                timer: 1500,
                showConfirmButton: false
            });
            fetchData();
        } else {
            Swal.fire('Lỗi!', res.message, 'error');
        }
    }
}

// 3. TỪ CHỐI (Pending -> Cancelled)
async function handleReject(order) {
    const result = await Swal.fire({
        title: 'Từ chối đơn hàng?',
        text: `Hủy đơn hàng #${order.order_code || order.id}? Hành động này không thể hoàn tác.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Từ chối',
        cancelButtonText: 'Quay lại',
        confirmButtonColor: '#ef4444', // Đỏ
        cancelButtonColor: '#6b7280'
    });

    if (result.isConfirmed) {
        const res = await sellerOrderStore.rejectOrder(order.id);
        if (res.success) {
            Swal.fire({
                title: 'Đã hủy!', 
                text: 'Đơn hàng đã bị từ chối.', 
                icon: 'success',
                timer: 1500,
                showConfirmButton: false
            });
            fetchData();
        } else {
            Swal.fire('Lỗi!', res.message, 'error');
        }
    }
}

// Helper: Badge màu trạng thái
function getStatusBadge(status) {
    switch(status) {
        case 'pending': return 'bg-amber-100 text-amber-800 border border-amber-200'; // Vàng cam
        case 'processing': return 'bg-blue-50 text-blue-700 border border-blue-200'; // Xanh nhạt
        case 'shipped': 
        case 'delivering': return 'bg-purple-100 text-purple-700 border border-purple-200'; // Tím
        case 'delivered': 
        case 'completed': return 'bg-emerald-100 text-emerald-700 border border-emerald-200'; // Xanh lá
        case 'cancelled': 
        case 'rejected': return 'bg-red-50 text-red-700 border border-red-200'; // Đỏ nhạt
        default: return 'bg-gray-100 text-gray-600 border border-gray-200';
    }
}

function getStatusText(status) {
    const map = {
        'pending': 'Chờ duyệt',
        'processing': 'đã duyệt',
        'shipped': 'Đang giao',
        'delivered': 'Hoàn thành',
        'cancelled': 'Đã hủy',
        'rejected': 'Đã từ chối'
    };
    return map[status] || status;
}

// 4. Lifecycle Hooks
onMounted(() => {
    fetchData();
});
</script>

<template>
    <AppLayout>
        <div class="bg-gray-50 min-h-screen pb-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-8">
                
                <!-- Header -->
                <div class="mb-8">
                    <h1 class="text-2xl font-bold text-gray-900 flex items-center">
                        <fa :icon="['fas', 'box-open']" class="mr-3 text-blue-600" />
                        Quản lý đơn hàng của tôi (Seller)
                    </h1>
                    <p class="text-gray-500 text-sm mt-1">Theo dõi và xử lý các đơn hàng khách mua từ shop của bạn.</p>
                </div>

                <!-- 1. BỘ LỌC (Filter Bar) -->
                <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100 mb-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-12 gap-4 items-end">
                        
                        <!-- Tìm kiếm -->
                        <div class="lg:col-span-4">
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Tìm kiếm</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <fa :icon="['fas', 'search']" class="text-gray-400" />
                                </div>
                                <input v-model="filters.search" type="text" 
                                    class="block w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-lg leading-5 bg-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm transition duration-150 ease-in-out"
                                    placeholder="Mã đơn, tên khách hàng..."
                                    @keyup.enter="handleFilter">
                            </div>
                        </div>

                        <!-- Trạng thái -->
                        <div class="lg:col-span-2">
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Trạng thái</label>
                            <select v-model="filters.status" 
                                class="block w-full pl-3 pr-10 py-2.5 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-lg bg-gray-50">
                                <option value="">Tất cả</option>
                                <option value="pending">Chờ duyệt</option>
                                <option value="processing">đã duyệt</option>
                                <option value="shipped">Đang giao</option>
                                <option value="delivered">Hoàn thành</option>
                                <option value="cancelled">Đã hủy</option>
                            </select>
                        </div>

                        <!-- Từ ngày -->
                        <div class="lg:col-span-2">
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Từ ngày</label>
                            <input v-model="filters.from_date" type="date" class="block w-full pl-3 pr-3 py-2.5 border-gray-300 focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-lg">
                        </div>

                        <!-- Đến ngày -->
                        <div class="lg:col-span-2">
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Đến ngày</label>
                            <input v-model="filters.to_date" type="date" class="block w-full pl-3 pr-3 py-2.5 border-gray-300 focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-lg">
                        </div>

                        <!-- Nút Lọc -->
                        <div class="lg:col-span-2">
                            <button @click="handleFilter" 
                                class="w-full flex justify-center py-2.5 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors h-[42px]">
                                <fa :icon="['fas', 'filter']" class="mr-2" /> Lọc
                            </button>
                        </div>
                    </div>
                </div>

                <!-- 2. DANH SÁCH ĐƠN HÀNG -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50 text-gray-700 uppercase font-semibold text-xs">
                                <tr>
                                    <th class="px-6 py-3 text-left tracking-wider">Mã đơn</th>
                                    <th class="px-6 py-3 text-left tracking-wider">Khách hàng</th>
                                    <th class="px-6 py-3 text-left tracking-wider">Ngày đặt</th>
                                    <th class="px-6 py-3 text-left tracking-wider">Tổng tiền</th>
                                    <th class="px-6 py-3 text-center tracking-wider">Trạng thái</th>
                                    <th class="px-6 py-3 text-right tracking-wider">Hành động</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-if="isLoading">
                                    <td colspan="6" class="px-6 py-12 text-center">
                                        <fa :icon="['fas', 'spinner']" spin class="text-3xl text-blue-500" />
                                        <p class="mt-2 text-sm text-gray-500">Đang tải dữ liệu...</p>
                                    </td>
                                </tr>
                                <tr v-else-if="orders.length === 0">
                                    <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                        <fa :icon="['far', 'folder-open']" class="text-4xl text-gray-300 mb-3" />
                                        <p>Không tìm thấy đơn hàng nào.</p>
                                    </td>
                                </tr>
                                
                                <tr v-for="order in orders" :key="order.id" class="hover:bg-gray-50 transition-colors group">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-bold text-blue-600">#{{ order.order_code || order.id }}</div>
                                        <div class="text-xs text-gray-400 mt-0.5">{{ order.items_count }} sản phẩm</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-8 w-8 rounded-full bg-gray-200 flex items-center justify-center text-gray-500 text-xs font-bold">
                                                {{ order.customer_name ? order.customer_name.charAt(0).toUpperCase() : '?' }}
                                            </div>
                                            <div class="ml-3">
                                                <div class="text-sm font-medium text-gray-900">{{ order.customer_name || 'Khách lẻ' }}</div>
                                                <div class="text-xs text-gray-500">{{ order.customer_phone }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ order.created_date }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-bold text-gray-900">{{ order.final_amount }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <span :class="['inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium', getStatusBadge(order.status)]">
                                            {{ getStatusText(order.status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex justify-end space-x-2">
                                            <!-- Nút Xem -->
                                            <button @click="openDetail(order.id)" 
                                                class="text-gray-400 hover:text-blue-600 p-1.5 rounded-md hover:bg-blue-50 transition-colors" 
                                                title="Xem chi tiết">
                                                <fa :icon="['fas', 'eye']" />
                                            </button>

                                            <!-- Hành động cho đơn Pending -->
                                            <template v-if="order.status === 'pending'">
                                                <button @click="handleApprove(order)" 
                                                    class="text-blue-500 hover:text-blue-700 p-1.5 rounded-md hover:bg-blue-50 transition-colors" 
                                                    title="Duyệt đơn (Chuyển sang đã duyệt)">
                                                    <fa :icon="['fas', 'check']" />
                                                </button>
                                                <button @click="handleReject(order)" 
                                                    class="text-red-400 hover:text-red-600 p-1.5 rounded-md hover:bg-red-50 transition-colors" 
                                                    title="Từ chối">
                                                    <fa :icon="['fas', 'times']" />
                                                </button>
                                            </template>

                                            <!-- Hành động cho đơn Processing (Mới thêm) -->
                                            <template v-if="order.status === 'processing'">
                                                <button @click="handleShip(order)" 
                                                    class="text-purple-500 hover:text-purple-700 p-1.5 rounded-md hover:bg-purple-50 transition-colors" 
                                                    title="Giao hàng (Chuyển sang Đang giao)">
                                                    <fa :icon="['fas', 'truck']" />
                                                </button>
                                            </template>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                           

                    <!-- Phân trang -->
                    <!-- <div v-if="pagination.last_page > 1" class="bg-white px-4 py-3 border-t border-gray-200 flex items-center justify-between sm:px-6">
                        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                            <div>
                                <p class="text-sm text-gray-700">
                                    Hiển thị trang <span class="font-medium">{{ pagination.currentPage }}</span> trong <span class="font-medium">{{ pagination.lastPage }}</span>
                                </p>
                            </div>
                            <div>
                                <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                                    <button @click="changePage(pagination.currentPage - 1)" :disabled="pagination.currentPage === 1" 
                                        class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 disabled:opacity-50">
                                        <span class="sr-only">Previous</span>
                                        <fa :icon="['fas', 'chevron-left']" class="h-3 w-3" />
                                    </button>
                                    
                                    <span class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700">
                                        {{ pagination.currentPage }}
                                    </span>

                                    <button @click="changePage(pagination.currentPage + 1)" :disabled="pagination.currentPage === pagination.lastPage" 
                                        class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 disabled:opacity-50">
                                        <span class="sr-only">Next</span>
                                        <fa :icon="['fas', 'chevron-right']" class="h-3 w-3" />
                                    </button>
                                </nav>
                            </div>
                        </div>
                    </div> -->
                </div>
                <BasePagination :pagination="pagination" :on-page-change="handlePageChange" />
            </div>
        </div>

        <!-- 3. MODAL CHI TIẾT ĐƠN HÀNG -->
        <div v-if="showDetailModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black bg-opacity-50 backdrop-blur-sm" @click.self="closeDetail">
            <div class="bg-white rounded-xl shadow-2xl w-full max-w-3xl max-h-[90vh] overflow-hidden flex flex-col animate__animated animate__fadeInUp">
                <!-- Header Modal -->
                <div class="p-5 border-b flex justify-between items-center bg-gray-50">
                    <h3 class="text-xl font-bold text-gray-800 flex items-center">
                        <fa :icon="['fas', 'file-invoice-dollar']" class="mr-2 text-blue-600" />
                        Chi tiết đơn hàng #{{ currentOrder?.order_code || '...' }}
                    </h3>
                    <button @click="closeDetail" class="text-gray-400 hover:text-gray-600 text-2xl">&times;</button>
                </div>

                <!-- Body Modal -->
                <div class="p-6 overflow-y-auto flex-1" v-if="currentOrder">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <!-- Thông tin khách hàng -->
                        <div class="bg-blue-50 p-4 rounded-lg border border-blue-100">
                            <h4 class="font-bold text-blue-800 mb-3 uppercase text-xs tracking-wider">Thông tin khách hàng</h4>
                            <div class="space-y-2 text-sm text-gray-700">
                                <p><span class="font-medium text-gray-500">Họ tên:</span> {{ currentOrder.customer_name }}</p>
                                <p><span class="font-medium text-gray-500">SĐT:</span> {{ currentOrder.customer_phone }}</p>
                                <p><span class="font-medium text-gray-500">Email:</span> {{ currentOrder.customer_email }}</p>
                                <p><span class="font-medium text-gray-500">Địa chỉ:</span> {{ currentOrder.shipping_address }}</p>
                            </div>
                        </div>

                        <!-- Thông tin đơn hàng -->
                        <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                            <h4 class="font-bold text-gray-800 mb-3 uppercase text-xs tracking-wider">Thông tin đơn hàng</h4>
                            <div class="space-y-2 text-sm text-gray-700">
                                <p><span class="font-medium text-gray-500">Ngày đặt:</span> {{ currentOrder.created_date }}</p>
                                <p><span class="font-medium text-gray-500">Thanh toán:</span> {{ currentOrder.payment_method === 'cod' ? 'Tiền mặt (COD)' : 'Chuyển khoản' }}</p>
                                <div class="flex items-center">
                                    <span class="font-medium text-gray-500 mr-2">Trạng thái:</span> 
                                    <span :class="['px-2 py-0.5 rounded text-xs font-bold uppercase', getStatusBadge(currentOrder.status)]">
                                        {{ getStatusText(currentOrder.status) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Danh sách sản phẩm -->
                    <h4 class="font-bold text-gray-800 mb-3">Sản phẩm ({{ currentOrder.items?.length || 0 }})</h4>
                    <div class="border rounded-lg overflow-hidden">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-100 text-gray-700">
                                <tr>
                                    <th class="p-3 text-left text-xs font-medium uppercase">Sản phẩm</th>
                                    <th class="p-3 text-center text-xs font-medium uppercase">SL</th>
                                    <th class="p-3 text-right text-xs font-medium uppercase">Đơn giá</th>
                                    <th class="p-3 text-right text-xs font-medium uppercase">Thành tiền</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y">
                                <tr v-for="item in currentOrder.items" :key="item.id">
                                    <td class="p-3 text-sm font-medium text-gray-900">{{ item.product_name }}</td>
                                    <td class="p-3 text-sm text-center text-gray-500">{{ item.quantity }}</td>
                                    <td class="p-3 text-sm text-right text-gray-500">{{ Number(item.price).toLocaleString() }} đ</td>
                                    <td class="p-3 text-sm text-right font-bold text-gray-900">{{ Number(item.subtotal).toLocaleString() }} đ</td>
                                </tr>
                            </tbody>
                            <tfoot class="bg-gray-50 font-bold">
                                <tr>
                                    <td colspan="3" class="p-3 text-right text-sm">Tổng cộng:</td>
                                    <td class="p-3 text-right text-lg text-red-600">{{ currentOrder.final_amount }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
                
                <div v-else-if="isDetailLoading" class="p-10 text-center">
                     <fa :icon="['fas', 'spinner']" spin class="text-4xl text-blue-500" />
                </div>

                <!-- Footer Modal -->
                <div class="p-4 border-t bg-gray-50 flex justify-end space-x-3" v-if="currentOrder">
                    <button @click="closeDetail" class="px-4 py-2 bg-white border rounded-lg text-gray-700 hover:bg-gray-100 transition-colors text-sm font-medium">Đóng</button>
                    
                    <!-- Hành động trong Modal -->
                    <template v-if="currentOrder.status === 'pending'">
                        <button @click="handleReject(currentOrder)" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors text-sm font-medium shadow-sm">Từ chối</button>
                        <button @click="handleApprove(currentOrder)" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm font-medium shadow-sm">Duyệt đơn</button>
                    </template>
                    
                    <template v-if="currentOrder.status === 'processing'">
                        <button @click="handleShip(currentOrder)" class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors text-sm font-medium shadow-sm">Giao hàng</button>
                    </template>
                </div>
            </div>
        </div>
    </AppLayout>
</template> 