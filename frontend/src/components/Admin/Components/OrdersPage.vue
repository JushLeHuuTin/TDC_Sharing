<script setup>
import { ref, onMounted, computed } from "vue"; // Thêm computed
import { storeToRefs } from "pinia";
import { useAdminOrderStore } from "@/stores/adminOrderStore";
import Swal from "sweetalert2";

// 1. Khởi tạo Store
const adminOrderStore = useAdminOrderStore();
const { orders, isLoading, currentOrder, pagination } =
  storeToRefs(adminOrderStore);

// Helper: Ngày đầu tháng và cuối tháng
const formatDateLocal = (date) => {
  const year = date.getFullYear();
  const month = String(date.getMonth() + 1).padStart(2, "0");
  const day = String(date.getDate()).padStart(2, "0");
  return `${year}-${month}-${day}`;
};

const getFirstDayOfMonth = () => {
  const date = new Date();
  return formatDateLocal(new Date(date.getFullYear(), 9, 1));
};

const getCurrentDate = () => {
  return formatDateLocal(new Date());
};

// --- TÍNH TOÁN HIỂN THỊ PHÂN TRANG ---
const fromEntry = computed(() => {
  if (!orders.value || orders.value.length === 0) return 0;
  if (pagination.value && pagination.value.from) return pagination.value.from;
  // Fallback nếu API chưa trả về 'from'
  return ((pagination.value?.currentPage || 1) - 1) * 10 + 1;
});

const toEntry = computed(() => {
  if (!orders.value || orders.value.length === 0) return 0;
  if (pagination.value && pagination.value.to) return pagination.value.to;
  // Fallback nếu API chưa trả về 'to'
  return fromEntry.value + orders.value.length - 1;
});

// 2. State Filters
const filters = ref({
  status: "",
  search: "",
  from_date: getFirstDayOfMonth(),
  to_date: getCurrentDate(),
  page: 1,
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
  if (pagination.value && page >= 1 && page <= pagination.value.lastPage) {
    filters.value.page = page;
    fetchData();
    window.scrollTo({ top: 0, behavior: "smooth" });
  }
}

// --- XEM CHI TIẾT ---
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

// --- XÓA ĐƠN HÀNG ---
async function handleDelete(order) {
  if (showDetailModal.value) showDetailModal.value = false;

  const result = await Swal.fire({
    title: "Xóa đơn hàng?",
    text: `Hành động này sẽ xóa vĩnh viễn đơn hàng #${order.order_code}.`,
    icon: "warning",
    showCancelButton: true,
    confirmButtonText: "Xóa ngay",
    cancelButtonText: "Hủy",
    confirmButtonColor: "#ef4444",
    cancelButtonColor: "#6b7280",
  });

  if (result.isConfirmed) {
    const res = await adminOrderStore.deleteOrder(order.order_id || order.id);
    if (res.success) {
      Swal.fire("Đã xóa!", "Đơn hàng đã được xóa thành công.", "success");
      fetchData();
    } else {
      Swal.fire("Lỗi!", res.message, "error");
    }
  }
}

// Helper Badge
function getStatusBadge(status) {
  switch (status) {
    case "pending":
      return "bg-amber-100 text-amber-700 border border-amber-200";
    case "processing":
      return "bg-blue-100 text-blue-700 border border-blue-200";
    case "shipped":
      return "bg-cyan-100 text-cyan-700 border border-cyan-200";
    case "delivered":
    case "completed":
      return "bg-emerald-100 text-emerald-700 border border-emerald-200";
    case "cancelled":
    case "rejected":
      return "bg-red-50 text-red-700 border border-red-200";
    default:
      return "bg-gray-100 text-gray-600 border border-gray-200";
  }
}

function getStatusText(status) {
  const map = {
    pending: "Chờ duyệt",
    paid: "đã thanh toán",
    processing: "đã duyệt",
    shipped: "Đang giao",
    delivered: "Hoàn thành",
    cancelled: "Đã hủy",
    rejected: "Đã từ chối",
  };
  return map[status] || status;
}

onMounted(() => {
  fetchData();
});
</script>

<template>
  <div class="p-6 bg-gray-50 min-h-screen">
    <div class="max-w-7xl mx-auto">
      <div class="mb-8 flex justify-between items-center">
        <div>
          <h1 class="text-2xl font-bold text-gray-900 flex items-center">
            <fa :icon="['fas', 'tasks']" class="mr-3 text-blue-600" />
            Quản lý tất cả đơn hàng
          </h1>
          <p class="text-gray-500 text-sm mt-1">
            Theo dõi toàn bộ giao dịch trên hệ thống.
          </p>
        </div>
        <button
          class="bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-50 shadow-sm transition-colors flex items-center text-sm font-medium"
        >
          <fa :icon="['fas', 'download']" class="mr-2" /> Xuất báo cáo
        </button>
      </div>

      <!-- 1. BỘ LỌC (Dàn hàng ngang) -->
      <div
        class="bg-white p-5 rounded-xl shadow-sm border border-gray-100 mb-6"
      >
        <div
          class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-12 gap-4 items-end"
        >
          <!-- Tìm kiếm (4 cột) -->
          <div class="lg:col-span-4">
            <label
              class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5"
              >Tìm kiếm</label
            >
            <div class="relative">
              <div
                class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"
              >
                <fa :icon="['fas', 'search']" class="text-gray-400" />
              </div>
              <input
                v-model="filters.search"
                type="text"
                class="block w-full pl-12 pr-3 py-2.5 border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm transition duration-150 ease-in-out"
                placeholder="Mã đơn, tên khách hàng..."
                @keyup.enter="handleFilter"
              />
            </div>
          </div>

          <!-- Trạng thái (2 cột) -->
          <div class="lg:col-span-2">
            <label
              class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5"
              >Trạng thái</label
            >
            <select
              v-model="filters.status"
              class="block w-full pl-3 pr-8 py-2.5 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-lg bg-white"
            >
              <option value="">Tất cả</option>
              <option value="pending">Chờ duyệt</option>
              <option value="processing">đã duyệt</option>
              <option value="shipped">Đang giao</option>
              <option value="delivered">Hoàn thành</option>
              <option value="cancelled">Đã hủy</option>
            </select>
          </div>

          <!-- Từ ngày (2 cột) -->
          <div class="lg:col-span-2">
            <label
              class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5"
              >Từ ngày</label
            >
            <input
              v-model="filters.from_date"
              type="date"
              class="block w-full px-3 py-2.5 border-gray-300 focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-lg"
            />
          </div>

          <!-- Đến ngày (2 cột) -->
          <div class="lg:col-span-2">
            <label
              class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5"
              >Đến ngày</label
            >
            <input
              v-model="filters.to_date"
              type="date"
              class="block w-full px-3 py-2.5 border-gray-300 focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-lg"
            />
          </div>

          <!-- Nút Lọc (2 cột) -->
          <div class="lg:col-span-2">
            <button
              @click="handleFilter"
              class="w-full bg-blue-600 text-white py-2.5 rounded-lg hover:bg-blue-700 transition-colors flex justify-center items-center h-[42px] shadow-sm"
            >
              <fa :icon="['fas', 'filter']" class="mr-2" /> Lọc
            </button>
          </div>
        </div>
      </div>

      <!-- 2. DANH SÁCH ĐƠN HÀNG -->
      <div
        class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden"
      >
        <div class="overflow-x-auto custom-scrollbar">
          <table class="min-w-full divide-y divide-gray-200">
            <thead
              class="bg-gray-50 text-gray-600 uppercase font-semibold text-xs"
            >
              <tr>
                <th class="px-6 py-4 text-left tracking-wider">Mã đơn</th>
                <!-- Đã xóa cột Sản phẩm ở đây -->
                <th class="px-6 py-4 text-left tracking-wider">
                  <div class="flex items-center">
                    <!-- Icon User -->
                    <fa :icon="['fas', 'user']" class="mr-2 text-gray-400" />
                    Khách hàng
                  </div>
                </th>
                <th class="px-6 py-4 text-left tracking-wider">
                  <div class="flex items-center">
                    <!-- Icon Store -->
                    <fa :icon="['fas', 'store']" class="mr-2 text-gray-400" />
                    Người bán
                  </div>
                </th>
                <th class="px-6 py-4 text-left tracking-wider">Tổng tiền</th>
                <th class="px-6 py-4 text-center tracking-wider">Trạng thái</th>
                <th class="px-6 py-4 text-left tracking-wider">Ngày tạo</th>
                <th class="px-6 py-4 text-right tracking-wider">Hành động</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 bg-white">
              <tr v-if="isLoading">
                <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                  <fa
                    :icon="['fas', 'spinner']"
                    spin
                    class="text-3xl text-blue-500"
                  />
                  <p class="mt-2 text-sm">Đang tải dữ liệu...</p>
                </td>
              </tr>
              <tr v-else-if="!orders || orders.length === 0">
                <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                  <fa
                    :icon="['far', 'folder-open']"
                    class="text-4xl text-gray-300 mb-3"
                  />
                  <p>Không tìm thấy đơn hàng nào.</p>
                </td>
              </tr>

              <tr
                v-for="order in orders"
                :key="order.order_id"
                class="hover:bg-gray-50 transition-colors"
              >
                <!-- Mã đơn -->
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="text-sm font-bold text-blue-600">
                    #{{ order.order_code || order.order_id }}
                  </div>
                  <div class="text-xs text-gray-400 mt-1">
                    {{ order.items_count }} món
                  </div>
                </td>

                <!-- Khách hàng (Avatar + Tên + SĐT) -->
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="flex items-center">
                    <div
                      class="flex-shrink-0 h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center text-gray-500 text-sm font-bold uppercase"
                    >
                      {{
                        order.customer_name
                          ? order.customer_name.charAt(0)
                          : "?"
                      }}
                    </div>
                    <div class="ml-3">
                      <div class="text-sm font-medium text-gray-900">
                        {{ order.customer_name || "N/A" }}
                      </div>
                      <div class="text-xs text-gray-500">
                        {{ order.customer_phone }}
                      </div>
                    </div>
                  </div>
                </td>

                <!-- Người bán -->
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="text-sm text-gray-700">
                    {{ order.seller_name || "N/A" }}
                  </div>
                </td>

                <!-- Tổng tiền -->
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="text-sm font-bold text-gray-900">
                    {{ order.total_price || order.final_amount }}
                  </div>
                </td>

                <!-- Trạng thái -->
                <td class="px-6 py-4 whitespace-nowrap text-center">
                  <span
                    :class="[
                      'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
                      getStatusBadge(order.status),
                    ]"
                  >
                    {{ getStatusText(order.status) }}
                  </span>
                </td>

                <!-- Ngày tạo -->
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                  {{ order.order_date || order.created_date }}
                </td>

                <!-- Hành động -->
                <td
                  class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium"
                >
                  <div class="flex justify-end space-x-3">
                    <button
                      @click="openDetail(order.order_id)"
                      class="text-gray-400 hover:text-blue-600 transition-colors"
                      title="Xem chi tiết"
                    >
                      <fa :icon="['fas', 'eye']" />
                    </button>
                    <button
                      @click="handleDelete(order)"
                      class="text-gray-400 hover:text-red-600 transition-colors"
                      title="Xóa đơn hàng"
                    >
                      <fa :icon="['fas', 'trash-alt']" />
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- 3. PHÂN TRANG (LUÔN HIỂN THỊ) -->
        <div
          v-if="pagination && (pagination.lastPage >= 1 || orders.length > 0)"
          class="bg-white px-4 py-3 border-t border-gray-200 flex items-center justify-between sm:px-6"
        >
          <div
            class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between"
          >
            <div>
              <p class="text-sm text-gray-700">
                Hiển thị trang
                <span class="font-medium">{{ pagination.currentPage }}</span> /
                <span class="font-medium">{{ pagination.lastPage }}</span> (Tổng
                {{ pagination.total }} đơn)
              </p>
            </div>
            <div>
              <nav
                class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px"
                aria-label="Pagination"
              >
                <button
                  @click="changePage(pagination.currentPage - 1)"
                  :disabled="pagination.currentPage === 1"
                  class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
                >
                  <span class="sr-only">Previous</span>
                  <fa :icon="['fas', 'chevron-left']" class="h-3 w-3" />
                </button>

                <span
                  class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700"
                >
                  {{ pagination.currentPage }}
                </span>

                <button
                  @click="changePage(pagination.currentPage + 1)"
                  :disabled="pagination.currentPage === pagination.lastPage"
                  class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
                >
                  <span class="sr-only">Next</span>
                  <fa :icon="['fas', 'chevron-right']" class="h-3 w-3" />
                </button>
              </nav>
            </div>
          </div>
        </div>
      </div>

      <!-- MODAL CHI TIẾT -->
      <div
        v-if="showDetailModal"
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black bg-opacity-50 backdrop-blur-sm"
        @click.self="closeDetail"
      >
        <div
          class="bg-white rounded-xl shadow-2xl w-full max-w-3xl max-h-[90vh] overflow-hidden flex flex-col animate__animated animate__fadeInUp"
        >
          <!-- Header Modal -->
          <div class="p-5 border-b flex justify-between items-center bg-white">
            <h3 class="text-xl font-bold text-gray-800 flex items-center">
              <fa
                :icon="['fas', 'file-invoice-dollar']"
                class="mr-2 text-blue-600"
              />
              Chi tiết đơn hàng #{{ currentOrder?.order_code }}
            </h3>
            <button
              @click="closeDetail"
              class="text-gray-400 hover:text-gray-600 text-xl"
            >
              &times;
            </button>
          </div>

          <!-- Body Modal -->
          <div class="p-6 overflow-y-auto flex-1" v-if="currentOrder">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
              <!-- Khách hàng -->
              <div class="bg-blue-50 p-4 rounded-lg border border-blue-100">
                <h4
                  class="font-bold text-blue-800 mb-3 uppercase text-xs tracking-wider"
                >
                  Thông tin khách hàng
                </h4>
                <div class="space-y-2 text-sm text-gray-700">
                  <p>
                    <span class="font-medium text-gray-500">Họ tên:</span>
                    {{ currentOrder.customer_name }}
                  </p>
                  <p>
                    <span class="font-medium text-gray-500">Email:</span>
                    {{ currentOrder.customer_email }}
                  </p>
                  <p>
                    <span class="font-medium text-gray-500">SĐT:</span>
                    {{ currentOrder.customer_phone }}
                  </p>
                  <p>
                    <span class="font-medium text-gray-500">Địa chỉ:</span>
                    {{ currentOrder.shipping_address }}
                  </p>
                </div>
              </div>

              <!-- Đơn hàng -->
              <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                <h4
                  class="font-bold text-gray-800 mb-3 uppercase text-xs tracking-wider"
                >
                  Thông tin đơn hàng
                </h4>
                <div class="space-y-2 text-sm text-gray-700">
                  <p>
                    <span class="font-medium text-gray-500">Người bán:</span>
                    <span class="text-blue-600 font-medium">{{
                      currentOrder.seller_name
                    }}</span>
                  </p>
                  <p>
                    <span class="font-medium text-gray-500">Ngày đặt:</span>
                    {{ currentOrder.order_date }}
                  </p>
                  <p>
                    <span class="font-medium text-gray-500">Thanh toán:</span>
                    {{ currentOrder.payment_method }}
                  </p>
                  <div class="flex items-center">
                    <span class="font-medium text-gray-500 mr-2"
                      >Trạng thái:</span
                    >
                    <span
                      :class="[
                        'px-2 py-0.5 rounded text-xs font-bold uppercase',
                        getStatusBadge(currentOrder.status),
                      ]"
                    >
                      {{ getStatusText(currentOrder.status) }}
                    </span>
                  </div>
                </div>
              </div>
            </div>

            <!-- Bảng sản phẩm -->
            <h4
              class="font-bold text-gray-800 mb-3 text-sm uppercase tracking-wider"
            >
              Sản phẩm ({{ currentOrder.items?.length || 0 }})
            </h4>
            <div class="border rounded-lg overflow-hidden">
              <table class="min-w-full text-sm text-left">
                <thead
                  class="bg-gray-100 text-gray-600 font-medium border-b text-sm"
                >
                  <tr>
                    <th class="p-3">Sản phẩm</th>
                    <th class="p-3 text-center">SL</th>
                    <th class="p-3 text-right">Giá</th>
                    <th class="p-3 text-right">Thành tiền</th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-sm">
                  <tr v-for="item in currentOrder.items" :key="item.id">
                    <td class="p-3 font-medium text-gray-900">
                      {{ item.product_name }}
                    </td>
                    <td class="p-3 text-center text-gray-500">
                      {{ item.quantity }}
                    </td>
                    <td class="p-3 text-right text-gray-500">
                      {{ Number(item.price).toLocaleString() }} đ
                    </td>
                    <td class="p-3 text-right font-bold text-gray-900">
                      {{ Number(item.subtotal).toLocaleString() }} đ
                    </td>
                  </tr>
                </tbody>
                <tfoot class="bg-gray-50 font-bold">
                  <tr>
                    <td
                      colspan="3"
                      class="p-3 text-right text-sm text-gray-500"
                    >
                      Tổng cộng:
                    </td>
                    <td class="p-3 text-right text-lg text-red-600">
                      {{ currentOrder.final_amount }}
                    </td>
                  </tr>
                </tfoot>
              </table>
            </div>
          </div>

          <div v-else-if="isDetailLoading" class="p-10 text-center">
            <fa
              :icon="['fas', 'spinner']"
              spin
              class="text-4xl text-blue-500"
            />
          </div>

          <div class="p-4 border-t bg-gray-50 flex justify-end space-x-2">
            <button
              @click="handleDelete(currentOrder)"
              class="px-4 py-2 bg-red-50 text-red-700 border border-red-200 rounded-lg hover:bg-red-100 text-sm font-medium transition-colors flex items-center"
            >
              <fa :icon="['fas', 'trash-alt']" class="mr-1.5" /> Xóa đơn này
            </button>
            <button
              @click="closeDetail"
              class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 text-sm font-medium transition-colors"
            >
              Đóng
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.custom-scrollbar::-webkit-scrollbar {
  width: 6px;
}
.custom-scrollbar::-webkit-scrollbar-track {
  background: #f1f1f1;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
  background: #cbd5e1;
  border-radius: 10px;
}
.table-shadow {
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1),
    0 2px 4px -1px rgba(0, 0, 0, 0.06);
}
</style>
