<script setup>
import { ref, computed, watch, onMounted } from "vue";
import { RouterLink } from "vue-router";
import { useVoucherStore } from "@/stores/voucherStore";
import { usePromotionStore } from "@/stores/promotionStore";
import { storeToRefs } from "pinia";
import { getCurrentInstance } from "vue";
import BasePagination from "@/components/BasePagination.vue";

const instance = getCurrentInstance();
const $toast = instance.appContext.config.globalProperties.$toast;

let voucherModal = null;
const voucherStore = useVoucherStore();
const promotionStore = usePromotionStore();
const { vouchers, voucherPagination } = storeToRefs(voucherStore);
const { promotions,promotionPagination } = storeToRefs(promotionStore);
const activeTab = ref("promotions");
const isEditing = ref(false);
const newVoucher = ref({
  id: "",
  code: "",
  name: "",
  description: "",
  discount_type: "percentage",
  discount_value: 1,
  min_purchase: 0,
  usage_limit: 1, // 'voucherQuantity'

  max_discount: null, 
  per_customer_limit: 1, 
  target_audiences: [],
  category_ids: [],

  start_date: new Date().toISOString().split("T")[0], // Ngày hiện tại YYYY-MM-DD
  end_date: new Date(new Date().setFullYear(new Date().getFullYear() + 1))
    .toISOString()
    .split("T")[0], // Mặc định 1 năm sau
  is_active: true,
  user_limit: 1, // 'voucherUserLimit'// 'voucherTarget'
});
const maxLengths = {
  name: 100,
  code: 50,
  description: 500,
};
onMounted(() => {
  const modalEl = document.getElementById("addVoucherModal");
  voucherModal = new bootstrap.Modal(modalEl);
  promotionStore.fetchPromotions(1);

  // Áp dụng giá trị mặc định cho form ngay khi mount
  document.getElementById("voucherStartDate").value = newVoucher.value.start_date;
  document.getElementById("voucherEndDate").value = newVoucher.value.end_date;
});

watch(activeTab, (value) => {
  if (value === "vouchers") {
    voucherStore.fetchVouchers(1);
  }
  if(value === "promotions"){
    promotionStore.fetchPromotions(1);
  }
});

const isSaving = ref(false);
const formErrors = ref({});

const voucherSearchQuery = ref("");
const voucherFilterStatus = ref("");
const voucherFilterType = ref("");

// Biến lọc MỚI cho Promotions
const promotionSearchQuery = ref("");
const promotionFilterStatus = ref("");
const promotionFilterType = ref("");


const filteredVouchers = computed(() => {
  let list = voucherStore.vouchers;

  if (voucherSearchQuery.value) {
    const q = voucherSearchQuery.value.toLowerCase();
    list = list.filter(
      (v) => v.code.toLowerCase().includes(q) || v.name.toLowerCase().includes(q)
    );
  }

  if (voucherFilterStatus.value) {
    // Logic lọc theo trạng thái (cần đồng bộ với getVoucherStatus)
    const statusText = voucherFilterStatus.value;
    list = list.filter((v) => getVoucherStatus(v).text === statusText);
  }

  if (voucherFilterType.value) {
    list = list.filter((v) => v.type === voucherFilterType.value);
  }

  return list;
});

// Hàm computed MỚI cho Promotions
const filteredPromotions = computed(() => {
  let list = promotionStore.promotions;
  
  if (promotionSearchQuery.value) {
    const q = promotionSearchQuery.value.toLowerCase();
    list = list.filter(
      (p) => p.name.toLowerCase().includes(q)
    );
  }

  if (promotionFilterStatus.value) {
     // Logic lọc theo trạng thái (cần đồng bộ với getPromotionStatus)
    const statusText = promotionFilterStatus.value;
    list = list.filter((p) => getPromotionStatus(p).text === statusText);
  }

  if (promotionFilterType.value) {
    list = list.filter((p) => p.type === promotionFilterType.value);
  }

  return list;
});


async function saveDiscount() {
  isSaving.value = true;
  formErrors.value = {}; // Reset lỗi // Tương tự như code hiện tại, chuẩn bị payload

  let result;
  
  // Cấu trúc Payload chung dựa trên activeTab
  let payload = {
      name: newVoucher.value.name,
      description: newVoucher.value.description,
      discount_type: newVoucher.value.discount_type,
      discount_value: parseFloat(newVoucher.value.discount_value),
      min_purchase: parseFloat(newVoucher.value.min_purchase),
      is_active: newVoucher.value.is_active ? 1 : 0, 
      start_date: newVoucher.value.start_date,
      end_date: newVoucher.value.end_date,
      updated_at: newVoucher.value.updated_at,
  };
  
  // Xử lý giá trị float/null an toàn
  const maxDiscountValue = newVoucher.value.max_discount ? parseFloat(newVoucher.value.max_discount) : null;
  const usageLimitValue = newVoucher.value.usage_limit ? parseInt(newVoucher.value.usage_limit) : null;
  const perCustomerLimitValue = newVoucher.value.per_customer_limit ? parseInt(newVoucher.value.per_customer_limit) : null;
  if (activeTab.value === 'promotions') {
      // --- Logic cho Promotion ---
      payload = {
          ...payload,
          max_discount: maxDiscountValue,
          usage_limit: usageLimitValue,
          per_customer_limit: perCustomerLimitValue, 
          target_audiences: newVoucher.value.target_audiences, // Giả định là mảng
          category_ids: newVoucher.value.category_ids, // Giả định là mảng
      };

      if (isEditing.value) {
          result = await promotionStore.updatePromotion(newVoucher.value.id, payload);
      } else {
          result = await promotionStore.createPromotion(payload);
      }
      
  } else {
      // --- Logic cho Voucher ---
      payload = {
          ...payload,
          code: newVoucher.value.code.toUpperCase(), // Bắt buộc
          max_value: maxDiscountValue,
          usage_limit: usageLimitValue, // Voucher thường dùng tên cột là usage_limit hoặc quantity
          user_limit: newVoucher.value.per_customer_limit, // Voucher thường dùng tên cột user_limit
          target_audience: newVoucher.value.target_audience, // Voucher thường có target audience
      };
      if (newVoucher.value.discount_type === "shipping") {
          payload.discount_value = 0;
      }
      
      if (isEditing.value) {
          result = await voucherStore.updateVoucher(newVoucher.value.id, payload);
      } else {
          result = await voucherStore.createVoucher(payload);
      }
    
    }
    if (result && result.success) {
    $toast.success(result.message || `Đã lưu thành công!`);
    voucherModal.hide(); 
    
    // Refresh danh sách tương ứng
    if (activeTab.value === 'promotions') {
        promotionStore.fetchPromotions(1);
    } else {
        voucherStore.fetchVouchers(1);
    }
  } else {
    // Xử lý lỗi validation từ server
    if (result && result.errors) {
      formErrors.value = result.errors;
      $toast.error((result && result.message));
    } else {
      $toast.error((result && result.message) || "Lỗi không xác định khi thao tác.");
    }
  }
  isSaving.value = false;
}
async function deleteVoucher(id) {
  if (!confirm("Bạn có chắc chắn muốn xóa voucher này?")) {
    return; // Dừng nếu người dùng hủy
  }
  const voucherCode = vouchers.value.find(v => v.id === id)?.code || `#${id}`;

  try {
    const result = await voucherStore.deleteVoucher(id);
    $toast.success(result.message || `Đã xóa Voucher ${voucherCode} thành công!`);
    
  } catch (error) {

    let messageToDisplay = 'Xóa thất bại. Vui lòng thử lại.';
    if (error && error.message) {
        messageToDisplay = error.message;
    }  if (error && error.errors) {
    }
    
    $toast.error(messageToDisplay);
  }
}
function resetVoucherFilters() {
  voucherSearchQuery.value = "";
  voucherFilterStatus.value = "";
  voucherFilterType.value = "";
  $toast.info("Đã đặt lại bộ lọc Voucher"); // Sửa props.showToast thành $toast.info
}

// Hàm reset MỚI cho Promotions
function resetPromotionFilters() {
  promotionSearchQuery.value = "";
  promotionFilterStatus.value = "";
  promotionFilterType.value = "";
  $toast.info("Đã đặt lại bộ lọc Chương trình Khuyến mãi"); 
}

function generateVoucherCode() {
  const chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
  let result = "";
  for (let i = 0; i < 8; i++) {
    result += chars.charAt(Math.floor(Math.random() * chars.length));
  }
  newVoucher.value.code = result;
}

// Hàm cập nhật nhãn Giá trị giảm
function updateVoucherValueLabel() {
  const type = newVoucher.value.type;
  const labelEl = document.getElementById("voucherValueLabel");
  if (type === "percentage") {
    labelEl.innerText = "Giá trị giảm (%)";
    newVoucher.value.discount_value = 1; // Đảm bảo giá trị min
  } else if (type === "fixed") {
    labelEl.innerText = "Giá trị giảm (VNĐ)";
    newVoucher.value.discount_value = 1000; // Đảm bảo giá trị min
  } else {
    labelEl.innerText = "Giá trị giảm";
    newVoucher.value.discount_value = 0; // Miễn phí vận chuyển thường không có giá trị
  }
}

// Hàm reset form
function resetVoucherForm() {
  isEditing.value = false;
  formErrors.value = {};
  newVoucher.value = {
    code: "",
    name: "",
    description: "",
    discount_type: "percentage",
    discount_value: 1,

    min_purchase: 0,
    usage_limit: 1,
    start_date: new Date().toISOString().split("T")[0],
    end_date: new Date(new Date().setFullYear(new Date().getFullYear() + 1))
      .toISOString()
      .split("T")[0],
    is_active: true,
    user_limit: 1,
    target_audience: "all",
  };
  // Cập nhật lại giá trị cho date inputs
  document.getElementById("voucherStartDate").value = newVoucher.value.start_date;
  document.getElementById("voucherEndDate").value = newVoucher.value.end_date;
  updateVoucherValueLabel();
}
function resetPromotionForm() {
  isEditing.value = false;
  formErrors.value = {};
  newVoucher.value = { 
    id: "",
    code: null, // KHÔNG có mã code
    name: "",
    description: "",
    discount_type: "percentage",
    discount_value: 5,
    min_purchase: 0,
    usage_limit: 100, 
    max_discount: null,
    per_customer_limit: 1, 
    target_audiences: [],
    category_ids: [],

    start_date: new Date().toISOString().split("T")[0],
    end_date: new Date(new Date().setFullYear(new Date().getFullYear() + 1))
      .toISOString()
      .split("T")[0],
    is_active: true,
  };
  // Cập nhật lại giá trị cho date inputs
  const startDateEl = document.getElementById("voucherStartDate");
  const endDateEl = document.getElementById("voucherEndDate");
  if (startDateEl) startDateEl.value = newVoucher.value.start_date;
  if (endDateEl) endDateEl.value = newVoucher.value.end_date;
  updateVoucherValueLabel();
}

// Cập nhật hàm createVoucher để reset form
function createVoucher() {
  resetVoucherForm(); // Đảm bảo form sạch sẽ khi mở
  document.getElementById("voucherModalTitle").innerText = "Thêm voucher giảm giá";
  voucherModal.show();
}
function createPromotion() {
  resetPromotionForm(); 
  activeTab.value = 'promotions'; 
  document.getElementById("voucherModalTitle").innerText = "Tạo Chương trình Khuyến mãi"; 
  if (voucherModal) voucherModal.show(); 
}
function updateVoucher(id) {
  isEditing.value = true;
  const vou = vouchers.value.find((c) => c.id === id);
  newVoucher.value = {
    id: vou.id,
    code: vou.code,
    name: vou.name,
    description: vou.description,
    discount_type: vou.type,
    discount_value: vou.value,
    min_purchase: vou.min_purchase,
    usage_limit: vou.quantity,
    start_date: vou.start_date,
    end_date: vou.end_date,
    updated_at: vou.updated_at,
      is_active: !!vou.is_active,
    user_limit: 1,
  };
  document.getElementById("voucherModalTitle").innerText = "Cập nhật voucher giảm giá";
  voucherModal.show();
}

function viewVoucherUsage(id) {
  $toast.info(`Xem lượt sử dụng Voucher #${id}`); 
}

function duplicateVoucher(id) {
  $toast.info(`Nhân bản Voucher #${id}`); 
}
function getUsagePercent(v) {
  const limit = v.quantity || v.max_uses_per_user; 
  const count = v.used_count || v.usage_count; 

  if (!limit || limit === 0) return 0;
  return (count / limit) * 100;
}
function getProgressColor(p) {
  if (p >= 90) return "bg-danger";
  if (p >= 70) return "bg-warning";
  return "bg-success";
}
function getVoucherStatus(v) {
  const now = new Date();
  const start = new Date(v.start_date);
  const end = new Date(v.end_date);

  if (v.used_count >= v.usage_limit) return { class: "bg-danger", text: "Hết lượt" }; // Sửa v.quantity thành v.usage_limit
  if (v.is_active == false) return { class: "bg-secondary", text: "Không hoạt động" };

  if (now > end) return { class: "bg-secondary", text: "Hết hạn" };
  

  if (now >= start && now <= end && v.is_active == true) return { class: "bg-success", text: "Hoạt động" };

  return { class: "bg-warning", text: "Chờ kích hoạt" };
}
function getPromotionStatus(p) { // Sửa tên biến từ v thành p
  const now = new Date();
  const start = new Date(p.time_start);
  const end = new Date(p.time_end);

  if (p.usage_count >= p.usage_limit) return { class: "bg-danger", text: "Hết lượt" }; // Sửa v.quantity thành v.usage_limit
  if (p.status === 'inactive') return { class: "bg-secondary", text: "Không hoạt động" }; // Dùng trường status từ API

  if (now > end) return { class: "bg-secondary", text: "Hết hạn" };
  

  if (p.status === 'active') return { class: "bg-success", text: "Hoạt động" }; // Dùng trường status từ API

  return { class: "bg-warning", text: "Chờ kích hoạt" };
}
const handlePageChange = (page) => {
  (activeTab.value == "promotions") ? promotionStore.fetchPromotions( page):voucherStore.fetchVouchers( page);
};
</script>

<template>
  <div class="promotion-manager-wrapper">
    <div class="page-header">
      <div class="d-flex justify-content-between align-items-center">
        <div>
          <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
              <li class="breadcrumb-item">
                <RouterLink to="/admin/dashboard">Dashboard</RouterLink>
              </li>
              <li class="breadcrumb-item active" aria-current="page">
                Khuyến mãi & Voucher
              </li>
            </ol>
          </nav>
          <h2 class="mb-0">Quản lý khuyến mãi & Voucher</h2>
          <p class="text-muted mb-0">Tạo và quản lý các chương trình khuyến mãi</p>
        </div>
        <div class="dropdown">
          <button
            class="btn btn-primary dropdown-toggle"
            type="button"
            data-bs-toggle="dropdown"
          >
            <fa :icon="['fas', 'plus']" class="me-2" />Tạo mới
          </button>
          <ul class="dropdown-menu">
            <li>
              <a class="dropdown-item" href="#" @click.prevent="createPromotion">
                <fa :icon="['fas', 'percentage']" class="me-2" />Chương trình khuyến mãi
              </a>
            </li>
            <li>
              <a class="dropdown-item" href="#" @click.prevent="createVoucher">
                <fa :icon="['fas', 'ticket-alt']" class="me-2" />Voucher giảm giá
              </a>
            </li>
          </ul>
        </div>
      </div>
    </div>

    <div class="row mb-4">
      <div class="col-md-3 mb-3">
        <div class="card stats-card bg-primary text-white">
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
              <div>
                <h3 class="mb-1">{{ voucherStore.stats.active_vouchers }}</h3>
                <p class="mb-0 opacity-75">Tổng khuyến mãi</p>
              </div>
              <fa :icon="['fas', 'percentage']" class="fa-2x opacity-75" />
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-3 mb-3">
        <div class="card stats-card bg-success text-white">
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
              <div>
                <h3 class="mb-1">{{ voucherStore.stats.active_vouchers }}</h3>
                <p class="mb-0 opacity-75">Đang hoạt động</p>
              </div>
              <fa :icon="['fas', 'play-circle']" class="fa-2x opacity-75" />
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-3 mb-3">
        <div class="card stats-card bg-warning text-white">
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
              <div>
                <h3 class="mb-1">{{ voucherStore.stats.total_vouchers }}</h3>
                <p class="mb-0 opacity-75">Voucher đã tạo</p>
              </div>
              <fa :icon="['fas', 'ticket-alt']" class="fa-2x opacity-75" />
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-3 mb-3">
        <div class="card stats-card bg-info text-white">
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
              <div>
                <h3 class="mb-1">{{ voucherStore.stats.used_vouchers }}</h3>
                <p class="mb-0 opacity-75">Đã sử dụng hết</p>
              </div>
              <fa :icon="['fas', 'check-circle']" class="fa-2x opacity-75" />
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="card shadow-sm promotion-card">
      <div class="card-header bg-white border-bottom-0">
        <ul class="nav nav-tabs card-header-tabs" id="promotionTabs">
          <li class="nav-item">
            <button
              class="nav-link"
              :class="{ active: activeTab === 'promotions' }"
              @click="activeTab = 'promotions'"
            >
              <fa :icon="['fas', 'percentage']" class="me-2" />Chương trình khuyến mãi
            </button>
          </li>
          <li class="nav-item">
            <button
              class="nav-link"
              :class="{ active: activeTab === 'vouchers' }"
              @click="activeTab = 'vouchers'"
            >
              <fa :icon="['fas', 'ticket-alt']" class="me-2" />Voucher giảm giá
            </button>
          </li>
        </ul>
      </div>
      <div class="card-body bg-light-gray">
        <div id="promotionTabContent">
          <div v-if="activeTab === 'promotions'">
          
            <div class="d-flex justify-content-between align-items-center mb-3">
              <h6 class="mb-0 text-dark fw-bold">
                Danh sách chương trình khuyến mãi ({{ filteredPromotions.length }} /
                {{ promotionStore.total }}) 
              </h6>
              <button class="btn btn-primary btn-sm" @click="createPromotion">
                <fa :icon="['fas', 'plus']" class="me-2" />Tạo chương trình khuyến mãi
              </button>
            </div>
            
            <!-- BỘ LỌC MỚI CHO PROMOTIONS -->
            <div class="row mb-3 p-3 bg-white rounded shadow-sm align-items-center">
              <div class="col-md-4">
                <input
                  type="text"
                  class="form-control"
                  placeholder="Tìm kiếm tên chương trình..."
                  v-model="promotionSearchQuery"
                />
              </div>
              <div class="col-md-3">
                <select class="form-select" v-model="promotionFilterStatus">
                  <option value="">Tất cả trạng thái</option>
                  <option value="Hoạt động">Đang hoạt động</option>
                  <option value="Hết hạn">Đã hết hạn</option>
                  <option value="Hết lượt">Đã sử dụng hết</option>
                  <option value="Không hoạt động">Không hoạt động</option>
                  <option value="Chờ kích hoạt">Chờ kích hoạt</option>
                </select>
              </div>
              <div class="col-md-3">
                <select class="form-select" v-model="promotionFilterType">
                  <option value="">Tất cả loại</option>
                  <option value="percentage">Phần trăm</option>
                  <option value="fixed">Số tiền cố định</option>
                  <!-- Promotion thường không có shipping, nhưng giữ để đồng bộ -->
                  <option value="shipping">Miễn phí vận chuyển</option> 
                </select>
              </div>
              <div class="col-md-2">
                <button
                  class="btn btn-outline-secondary w-100"
                  @click="resetPromotionFilters"
                >
                  <fa :icon="['fas', 'undo']" /> Reset
                </button>
              </div>
            </div>
            <!-- KẾT THÚC BỘ LỌC MỚI -->

            <div class="table-responsive bg-white rounded shadow-sm mt-3">
              <table class="table table-hover mb-0">
                <thead class="table-light">
                  <tr>
                    <th>Tên chương trình</th>
                    <th>Loại giảm giá</th>
                    <th>Giá trị</th>
                    <th>Thời gian</th>
                    <th>Sử dụng</th>
                    <th>Trạng thái</th>
                    <th>Thao tác</th>
                  </tr>
                </thead>

                <tbody>
                  <tr v-for="promotion in filteredPromotions" :key="promotion.id">
                    <!-- Fix: Lọc theo filteredPromotions -->
                    <!-- Ten -->
                    <td class="fw-bold text-primary">
                      <code>{{ promotion.name }}</code>
                    </td>
                    <!-- Type -->
                    <td>
                      <span
                        class="badge"
                        :class="
                          promotion.type === 'percentage'
                            ? 'bg-info'
                            : promotion.type === 'fixed'
                            ? 'bg-warning'
                            : 'bg-success'
                        "
                      >
                        {{promotion.type }}
                      </span>
                    </td>

                    <td>
                      <small>
                        {{ promotion.value_display }}
                      </small>
                    </td>
                       <!-- Thời hạn -->
                       <td>
                      <small>
                        {{ new Date(promotion.time_start).toLocaleDateString("vi-VN") }}
                        →
                        {{ new Date(promotion.time_end).toLocaleDateString("vi-VN") }}
                      </small>
                    </td>
                    <!-- Usage -->
                    <td>
                      <div>
                        <span
                          :class="
                            promotion.usage_count >= promotion.usage_limit
                              ? 'text-danger fw-bold'
                              : promotion.usage_count / promotion.usage_limit >= 0.9
                              ? 'text-danger'
                              : promotion.usage_count / promotion.usage_limit >= 0.7
                              ? 'text-warning'
                              : 'text-success'
                          "
                        >
                        </span>
                        {{ promotion.usage_limit }}
                      </div>

                      <!-- Progress bar -->
                      <div class="progress mt-1" style="height: 4px">
                        <div
                          class="progress-bar"
                          :class="getProgressColor(getUsagePercent(promotion))"
                          :style="{ width: getUsagePercent(promotion) + '%' }"
                        ></div>
                      </div>
                    </td>

                 

                    <!-- Trạng thái -->
                    <td>
                      <span class="badge" :class="getPromotionStatus(promotion).class">
                        {{ getPromotionStatus(promotion).text }}
                      </span>
                    </td>

                    <!-- Thao tác -->
                    <td>
                      <div class="btn-group btn-group-sm">
                        <button
                          class="btn btn-outline-primary"
                          @click="updateVoucher(promotion.id)"
                        >
                          <fa :icon="['fas', 'edit']" />
                        </button>

                        <button
                          class="btn btn-outline-info"
                          @click="viewVoucherUsage(promotion.id)"
                        >
                          <fa :icon="['fas', 'users']" />
                        </button>

                        <button
                          class="btn btn-outline-warning"
                          @click="duplicateVoucher(promotion.id)"
                        >
                          <fa :icon="['fas', 'copy']" />
                        </button>

                        <button
                          class="btn btn-outline-danger"
                          @click="deleteVoucher(promotion.id)"
                        >
                          <fa :icon="['fas', 'trash']" />
                        </button>
                      </div>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

          <div v-else-if="activeTab === 'vouchers'">
            <div class="d-flex justify-content-between align-items-center mb-3">
              <h6 class="mb-0 text-dark fw-bold">
                Danh sách voucher giảm giá ({{ filteredVouchers.length }} /
                {{ vouchers.length }})
              </h6>
              <button class="btn btn-primary btn-sm" @click="createVoucher">
                <fa :icon="['fas', 'plus']" class="me-2" />Tạo voucher
              </button>
            </div>

            <div class="row mb-3 p-3 bg-white rounded shadow-sm align-items-center">
              <div class="col-md-4">
                <input
                  type="text"
                  class="form-control"
                  placeholder="Tìm kiếm mã voucher..."
                  v-model="voucherSearchQuery"
                />
              </div>
              <div class="col-md-3">
                <select class="form-select" v-model="voucherFilterStatus">
                  <option value="">Tất cả trạng thái</option>
                  <option value="Hoạt động">Đang hoạt động</option>
                  <option value="Hết hạn">Đã hết hạn</option>
                  <option value="Hết lượt">Đã sử dụng hết</option>
                  <option value="Không hoạt động">Không hoạt động</option>
                  <option value="Chờ kích hoạt">Chờ kích hoạt</option>
                </select>
              </div>
              <div class="col-md-3">
                <select class="form-select" v-model="voucherFilterType">
                  <option value="">Tất cả loại</option>
                  <option value="percentage">Phần trăm</option>
                  <option value="fixed">Số tiền cố định</option>
                  <option value="shipping">Miễn phí vận chuyển</option>
                </select>
              </div>
              <div class="col-md-2">
                <button
                  class="btn btn-outline-secondary w-100"
                  @click="resetVoucherFilters"
                >
                  <fa :icon="['fas', 'undo']" /> Reset
                </button>
              </div>
            </div>

            <div class="table-responsive bg-white rounded shadow-sm mt-3">
              <table class="table table-hover mb-0">
                <thead class="table-light">
                  <tr>
                    <th>Mã voucher</th>
                    <th>Tên voucher</th>
                    <th>Loại & Giá trị</th>
                    <th>Số lượng</th>
                    <th>Thời hạn</th>
                    <th>Trạng thái</th>
                    <th>Thao tác</th>
                  </tr>
                </thead>

                <tbody>
                  <tr v-for="voucher in filteredVouchers" :key="voucher.id">
                    <!-- Mã voucher -->
                    <td class="fw-bold text-primary">
                      <code>{{ voucher.code }}</code>
                    </td>

                    <!-- Tên -->
                    <td>{{ voucher.name }}</td>

                    <!-- Type -->
                    <td>
                      <span
                        class="badge"
                        :class="
                          voucher.type === 'percentage'
                            ? 'bg-info'
                            : voucher.type === 'fixed'
                            ? 'bg-warning'
                            : 'bg-success'
                        "
                      >
                        {{
                          voucher.type === "percentage"
                            ? voucher.value + "%"
                            : voucherStore.formatPrice(voucher.value)
                        }}
                      </span>

                      <div v-if="voucher.max_value">
                        <small class="text-muted"
                          >Tối đa {{ voucherStore.formatPrice(voucher.max_value) }}</small
                        >
                      </div>
                    </td>

                    <!-- Usage -->
                    <td>
                      <div>
                        <span
                          :class="
                            voucher.used_count >= voucher.quantity
                              ? 'text-danger fw-bold'
                              : voucher.used_count / voucher.quantity >= 0.9
                              ? 'text-danger'
                              : voucher.used_count / voucher.quantity >= 0.7
                              ? 'text-warning'
                              : 'text-success'
                          "
                        >
                          {{ voucher.used_count }}
                        </span>
                        /
                        {{ voucher.quantity }}
                      </div>

                      <!-- Progress bar -->
                      <div class="progress mt-1" style="height: 4px">
                        <div
                          class="progress-bar"
                          :class="getProgressColor(getUsagePercent(voucher))"
                          :style="{ width: getUsagePercent(voucher) + '%' }"
                        ></div>
                      </div>
                    </td>

                    <!-- Thời hạn -->
                    <td>
                      <small>
                        {{ new Date(voucher.start_date).toLocaleDateString("vi-VN") }}
                        →
                        {{ new Date(voucher.end_date).toLocaleDateString("vi-VN") }}
                      </small>
                    </td>

                    <!-- Trạng thái -->
                    <td>
                      <span class="badge" :class="getVoucherStatus(voucher).class">
                        {{ getVoucherStatus(voucher).text }}
                      </span>
                    </td>

                    <!-- Thao tác -->
                    <td>
                      <div class="btn-group btn-group-sm">
                        <button
                          class="btn btn-outline-primary"
                          @click="updateVoucher(voucher.id)"
                        >
                          <fa :icon="['fas', 'edit']" />
                        </button>

                        <button
                          class="btn btn-outline-info"
                          @click="viewVoucherUsage(voucher.id)"
                        >
                          <fa :icon="['fas', 'users']" />
                        </button>

                        <button
                          class="btn btn-outline-warning"
                          @click="duplicateVoucher(voucher.id)"
                        >
                          <fa :icon="['fas', 'copy']" />
                        </button>

                        <button
                          class="btn btn-outline-danger"
                          @click="deleteVoucher(voucher.id)"
                          >
                          <fa :icon="['fas', 'trash']" />
                        </button>
                      </div>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
           
          </div>
             <!-- PHÂN TRANG CHO PROMOTION -->
             <BasePagination 
              v-if="activeTab === 'promotions'"
              :pagination="promotionPagination" 
              :on-page-change="handlePageChange" 
            />
            <!-- PHÂN TRANG CHO VOUCHER -->
            <BasePagination 
              v-if="activeTab === 'vouchers'"
              :pagination="voucherPagination" 
              :on-page-change="handlePageChange" 
            />
        </div>
      </div>
    </div>
   <!-- Add/Edit Discount Modal -->
   <div class="modal fade" id="addVoucherModal" tabindex="-1">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <!-- Binding tiêu đề Modal động -->
            <h5 class="modal-title" id="voucherModalTitle">
              {{ isEditing ? 'Cập nhật' : 'Thêm' }} {{ activeTab === 'vouchers' ? 'Voucher giảm giá' : 'Chương trình khuyến mãi' }}
            </h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>

          <!-- Sử dụng hàm chung saveDiscount -->
          <form id="voucherForm" @submit.prevent="saveDiscount"> 
            <div class="modal-body">
              <input type="hidden" id="voucherId" :value="newVoucher.id" />

              <div class="row">
                <!-- Mã Voucher (Chỉ hiện khi activeTab là Voucher) -->
                <div class="col-md-4 mb-3" v-if="activeTab === 'vouchers'">
                  <label class="form-label"
                    >Mã voucher <span class="text-danger">*</span></label
                  >
                  <div class="input-group">
                    <input
                      type="text"
                      class="form-control"
                      id="voucherCode"
                      style="text-transform: uppercase"
                      v-model="newVoucher.code"
                    />
                    <button
                      type="button"
                      class="btn btn-outline-secondary"
                      @click="generateVoucherCode"
                    >
                      <fa :icon="['fas', 'random']" />
                    </button>
                  </div>
                  <div class="form-text">Mã voucher phải là duy nhất</div>
                  <div v-if="formErrors.code" class="text-danger small mt-1">
                    {{ formErrors.code ? formErrors.code[0] : '' }}
                  </div>
                </div>

                <!-- Tên Voucher/Promotion -->
                <div 
                  :class="activeTab === 'vouchers' ? 'col-md-8' : 'col-md-12'" 
                  class="mb-3"
                >
                  <label class="form-label"
                    >Tên {{ activeTab === 'vouchers' ? 'voucher' : 'chương trình' }} <span class="text-danger">*</span></label
                  >
                  <input
                    type="text"
                    class="form-control"
                    id="voucherName"
                    v-model="newVoucher.name"
                  />
                  <div v-if="formErrors.name" class="text-danger small mt-1">
                    {{ formErrors.name[0] }}
                  </div>
                </div>
              </div>

              <!-- Mô tả -->
              <div class="mb-3">
                <label class="form-label">Mô tả {{ activeTab === 'vouchers' ? 'voucher' : 'chương trình' }}</label>
                <textarea
                  class="form-control"
                  id="voucherDescription"
                  rows="3"
                  v-model="newVoucher.description"
                  :maxlength="maxLengths.description"
                ></textarea>
                <div class="form-text text-end">
                  <!-- {{ newVoucher.description.length || 0 }}/{{ maxLengths.description }} -->
                </div>
                <div v-if="formErrors.description" class="text-danger small mt-1">
                  {{ formErrors.description[0] }}
                </div>
              </div>

              <!-- Loại, Giá trị, Đơn hàng tối thiểu -->
              <div class="row">
                <div class="col-md-4 mb-3">
                  <label class="form-label">Loại {{ activeTab === 'vouchers' ? 'voucher' : 'giảm giá' }}</label>
                  <select
                    class="form-select"
                    id="voucherType"
                    v-model="newVoucher.discount_type"
                    @change="updateVoucherValueLabel"
                  >
                    <option value="percentage">Phần trăm (%)</option>
                    <option value="fixed">Số tiền cố định</option>
                    <option value="shipping">Miễn phí vận chuyển</option>
                  </select>
                  <div v-if="formErrors.type" class="text-danger small mt-1">
                    {{ formErrors.type[0] }}
                  </div>
                </div>

                <div class="col-md-4 mb-3">
                  <label class="form-label" id="voucherValueLabel"
                    >Giá trị giảm (%)</label
                  >
                  <input
                    type="number"
                    class="form-control"
                    id="voucherValue"
                    min="0"
                    v-model.number="newVoucher.discount_value"
                  />
                  <div v-if="formErrors.discount_value" class="text-danger small mt-1">
                    {{ formErrors.discount_value[0] }}
                  </div>
                </div>

                <div class="col-md-4 mb-3">
                  <label class="form-label">Đơn hàng tối thiểu (VNĐ)</label>
                  <input
                    type="number"
                    class="form-control"
                    id="voucherMinOrder"
                    min="0"
                    v-model.number="newVoucher.min_purchase"
                  />
                  <div v-if="formErrors.min_purchase" class="text-danger small mt-1">
                    {{ formErrors.min_purchase[0] }}
                  </div>
                </div>
              </div>

              <!-- Giới hạn sử dụng và Khách hàng -->
              <div class="row">
                <div :class="newVoucher.discount_type === 'percentage' ? 'col-md-4' : 'col-md-6'" class="mb-3">
                  <label class="form-label"
                    >Giới hạn sử dụng (Tổng) <span class="text-danger">*</span></label
                  >
                  <input
                    type="number"
                    class="form-control"
                    id="voucherQuantity"
                    min="1"
                    v-model.number="newVoucher.usage_limit"
                  />
                  <div v-if="formErrors.usage_limit" class="text-danger small mt-1">
                    {{ formErrors.usage_limit[0] }}
                  </div>
                </div>
                
                <!-- Giới hạn sử dụng/Khách hàng (Dùng chung cho cả Voucher và Promotion) -->
                <div :class="newVoucher.discount_type === 'percentage' ? 'col-md-4' : 'col-md-6'" class="mb-3">
                  <label class="form-label">Giới hạn/Khách hàng</label>
                  <input
                    type="number"
                    class="form-control"
                    id="perCustomerLimit"
                    min="1"
                    v-model.number="newVoucher.per_customer_limit"
                  />
                  <div class="form-text">Số lần tối đa 1 khách hàng có thể sử dụng</div>
                  <div v-if="formErrors.per_customer_limit" class="text-danger small mt-1">
                    {{ formErrors.per_customer_limit[0] }}
                  </div>
                </div>
                
                <!-- Giới hạn giảm tối đa (Chỉ hiện khi là %) -->
                <div class="col-md-4 mb-3" v-if="newVoucher.discount_type === 'percentage'">
                  <label class="form-label">Giới hạn giảm tối đa (VNĐ)</label>
                  <input
                    type="number"
                    class="form-control"
                    id="maxDiscount"
                    min="0"
                    v-model.number="newVoucher.max_discount"
                  />
                  <div class="form-text">Áp dụng cho giảm theo %</div>
                  <div v-if="formErrors.max_discount" class="text-danger small mt-1">
                    {{ formErrors.max_discount[0] }}
                  </div>
                </div>
              </div>

              <!-- Thời hạn -->
              <div class="row">
                <div class="col-md-6 mb-3">
                  <label class="form-label"
                    >Ngày bắt đầu <span class="text-danger">*</span></label
                  >
                  <input
                    type="date"
                    class="form-control"
                    id="voucherStartDate"
                    v-model="newVoucher.start_date"
                  />
                  <div v-if="formErrors.start_date" class="text-danger small mt-1">
                    {{ formErrors.start_date[0] }}
                  </div>
                </div>

                <div class="col-md-6 mb-3">
                  <label class="form-label"
                    >Ngày hết hạn <span class="text-danger">*</span></label
                  >
                  <input
                    type="date"
                    class="form-control"
                    id="voucherEndDate"
                    v-model="newVoucher.end_date"
                  />
                  <div v-if="formErrors.end_date" class="text-danger small mt-1">
                    {{ formErrors.end_date[0] }}
                  </div>
                </div>
              </div>
              
              <!-- Áp dụng sản phẩm/danh mục (chỉ hiện cho Promotion) -->
              <div v-if="activeTab === 'promotions'" class="mb-3 border p-3 rounded-lg bg-light">
                  <h6 class="text-primary mt-0 mb-3">Ràng buộc áp dụng</h6>
                  
                  <!-- 1. Ràng buộc khách hàng (target_audiences) -->
                  <div class="mb-3">
                      <label class="form-label">Nhóm khách hàng áp dụng</label>
                      <select 
                          class="form-select mb-2" 
                          v-model="newVoucher.targetAudienceType"
                      >
                          <option value="all">Áp dụng cho TẤT CẢ khách hàng</option>
                          <option value="specific_groups">Áp dụng cho NHÓM khách hàng cụ thể</option>
                      </select>
                      
                      <div v-if="newVoucher.targetAudienceType === 'specific_groups'">
                           <!-- Giả định dùng multiple select cho groups ID -->
                          <select 
                              class="form-select"
                              multiple
                              v-model="newVoucher.target_audiences"
                          >
                              <option v-for="group in mockData.userGroups" :key="group.id" :value="group.id">
                                  {{ group.name }}
                              </option>
                          </select>
                          <div class="form-text">Chọn một hoặc nhiều nhóm khách hàng.</div>
                          <div v-if="formErrors.target_audiences" class="text-danger small mt-1">
                              {{ formErrors.target_audiences[0] }}
                          </div>
                      </div>
                  </div>

                  <!-- 2. Ràng buộc danh mục (category_ids) -->
                   <div class="mb-3">
                      <label class="form-label">Danh mục sản phẩm áp dụng</label>
                      <select 
                          class="form-select mb-2" 
                          v-model="newVoucher.categoryApplyType"
                      >
                          <option value="all">Áp dụng cho TẤT CẢ sản phẩm</option>
                          <option value="specific_categories">Áp dụng cho DANH MỤC cụ thể</option>
                      </select>
                      
                      <div v-if="newVoucher.categoryApplyType === 'specific_categories'">
                          <!-- Giả định dùng multiple select cho categories ID -->
                           <select 
                              class="form-select"
                              multiple
                              v-model="newVoucher.applicable_categories"
                          >
                              <option v-for="cat in mockData.categories" :key="cat.id" :value="cat.id">
                                  {{ cat.name }}
                              </option>
                          </select>
                          <div class="form-text">Chọn một hoặc nhiều danh mục sản phẩm.</div>
                          <div v-if="formErrors.category_ids" class="text-danger small mt-1">
                              {{ formErrors.category_ids[0] }}
                          </div>
                      </div>
                  </div>
                  
                  <!-- 3. Ràng buộc Sản phẩm cụ thể (applicable_products) -->
                  <!-- <div class="form-text mt-2">
                      <RouterLink to="#" class="small text-decoration-none">
                          <fa :icon="['fas', 'tag']" class="me-1" />
                          Quản lý áp dụng cho sản phẩm cụ thể ({{ newVoucher.applicable_products.length }} sản phẩm)
                      </RouterLink>
                  </div> -->

              </div>
              
              <div class="form-check">
                <input
                  class="form-check-input"
                  type="checkbox"
                  id="voucherActive"
                  v-model="newVoucher.is_active"
                />
                <label class="form-check-label" for="voucherActive">
                  Kích hoạt {{ activeTab === 'vouchers' ? 'voucher' : 'chương trình' }} ngay
                </label>
              </div>
            </div>

            <div class="modal-footer">
              <button
                type="button"
                class="btn btn-secondary"
                data-bs-dismiss="modal"
                :disabled="isSaving"
              >
                Hủy
              </button>

              <button type="submit" class="btn btn-primary" :disabled="isSaving">
                <fa :icon="['fas', 'save']" class="me-2" />
                {{ isSaving ? "Đang lưu..." : "Lưu" }} {{ activeTab === 'vouchers' ? 'voucher' : 'khuyến mãi' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.page-header {
  border-radius: 10px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
  background: white;
  margin-bottom: 20px;
  padding: 20px;
}

.breadcrumb-item a {
  color: #667eea;
  text-decoration: none;
}
.breadcrumb-item.active {
  color: #495057;
}

.stats-card {
  border: none;
  border-radius: 15px;
  box-shadow: 0 6px 18px rgba(0, 0, 0, 0.1);
  transition: transform 0.3s ease;
}

.stats-card:hover {
  transform: translateY(-5px);
}

/* 3. Main Card (Chứa Tabs và Content) */
.promotion-card {
  border-radius: 10px;
  border: 1px solid #dee2e6;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
}

/* 4. Tabs Navigation: Phân biệt rõ ràng */
.card-header-tabs {
  border-bottom: none;
}

.card-header-tabs .nav-link {
  color: #6c757d; /* Màu chữ xám mặc định */
  font-weight: 500;
  padding: 10px 15px;
  border: none;
  border-bottom: 3px solid transparent;
  transition: all 0.2s;
}

.card-header-tabs .nav-link:hover {
  color: #0d6efd;
}

.card-header-tabs .nav-link.active {
  background-color: transparent !important;
  color: #667eea;
  border-bottom: 3px solid #667eea;
  font-weight: 600;
}

.card-body {
  padding: 20px;
}

.table-responsive {
  border: 1px solid #e9ecef;
}

.table thead th {
  font-weight: 600;
  color: #495057;
  background-color: #f1f2f6;
  border-bottom: 2px solid #dee2e6;
}
</style>