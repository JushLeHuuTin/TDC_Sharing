<script setup>
import { ref, computed, watch, onMounted } from "vue";
import { RouterLink } from "vue-router";
import { useVoucherStore } from "@/stores/voucherStore";
import { storeToRefs } from "pinia";
import { getCurrentInstance } from "vue";

const instance = getCurrentInstance();
const $toast = instance.appContext.config.globalProperties.$toast;

let voucherModal = null;

const voucherStore = useVoucherStore();
const { vouchers } = storeToRefs(voucherStore);
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

  // Áp dụng giá trị mặc định cho form ngay khi mount
  document.getElementById("voucherStartDate").value = newVoucher.value.start_date;
  document.getElementById("voucherEndDate").value = newVoucher.value.end_date;
});

watch(activeTab, (value) => {
  if (value === "vouchers") {
    voucherStore.fetchVouchers(1);
  }
});

const isSaving = ref(false);
const formErrors = ref({});

const voucherSearchQuery = ref("");
const voucherFilterStatus = ref("");
const voucherFilterType = ref("");

const filteredVouchers = computed(() => {
  let list = voucherStore.vouchers;

  if (voucherSearchQuery.value) {
    const q = voucherSearchQuery.value.toLowerCase();
    list = list.filter(
      (v) => v.code.toLowerCase().includes(q) || v.name.toLowerCase().includes(q)
    );
  }

  if (voucherFilterStatus.value) {
    list = list.filter((v) => v.status_text === voucherFilterStatus.value);
  }

  if (voucherFilterType.value) {
    list = list.filter((v) => v.type === voucherFilterType.value);
  }

  return list;
});

async function saveVoucher() {
  isSaving.value = true;
  formErrors.value = {}; // Reset lỗi // Tương tự như code hiện tại, chuẩn bị payload

  const payload = {
    code: newVoucher.value.code.toUpperCase(),
    name: newVoucher.value.name,
    description: newVoucher.value.description,

    discount_type: newVoucher.value.discount_type,
    discount_value: parseFloat(newVoucher.value.discount_value),
    max_value: newVoucher.value.max_value ? parseFloat(newVoucher.value.max_value) : null,
    min_purchase: parseFloat(newVoucher.value.min_purchase),
    usage_limit: parseInt(newVoucher.value.usage_limit),
    start_date: newVoucher.value.start_date,
    end_date: newVoucher.value.end_date,
    is_active: newVoucher.value.is_active ? 1 : 0, 
    user_limit: newVoucher.value.user_limit, // Thêm các trường thiếu trong payload cũ
    target_audience: newVoucher.value.target_audience, // Thêm các trường thiếu trong payload cũ
  };

  if (newVoucher.value.type === "shipping") {
    payload.discount_value = 0;
  }

  let result;
  if (isEditing.value) {
    // Gọi hàm CẬP NHẬT
    result = await voucherStore.updateVoucher(newVoucher.value.id, payload);
  } else {
    // Gọi hàm TẠO MỚI (code hiện tại của bạn)
    result = await voucherStore.createVoucher(payload);
  }

  if (result.success) {
    $toast.success(`Đã lưu voucher: ${result.voucher.code}`);
    resetVoucherForm();
    voucherModal.hide(); // Không cần fetch lại toàn bộ nếu store đã tự cập nhật,
    // nhưng nếu backend không gửi lại toàn bộ danh sách,
    // bạn có thể giữ lại fetchVouchers để đảm bảo dữ liệu mới nhất.
    voucherStore.fetchVouchers(1);
  } else {
    // Xử lý lỗi validation từ server
    if (result.errors) {
      formErrors.value = result.errors;
      $toast.error("Lưu voucher thất bại! Vui lòng kiểm tra các trường đã nhập.");
    } else {
      $toast.error(result.message || "Lỗi không xác định khi thao tác với voucher.");
    }
  }

  isSaving.value = false;
}
async function deleteVoucher(id) {
  if (confirm("Bạn có chắc chắn muốn xóa voucher này?")) {
    const result = await voucherStore.deleteVoucher(id);

    if (result.success) {
      $toast.success(result.message || `Đã xóa Voucher #${id} thành công!`);
    } else {
      $toast.error(result.message || `Không thể xóa Voucher #${id}.`);
    }
  }
}
function resetVoucherFilters() {
  voucherSearchQuery.value = "";
  voucherFilterStatus.value = "";
  voucherFilterType.value = "";
  props.showToast("Đã đặt lại bộ lọc Voucher", "info");
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

// Cập nhật hàm createVoucher để reset form
function createVoucher() {
  resetVoucherForm(); // Đảm bảo form sạch sẽ khi mở
  document.getElementById("voucherModalTitle").innerText = "Thêm voucher giảm giá";
  voucherModal.show();
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
      is_active: !!vou.is_active,
    user_limit: 1,
  };
  document.getElementById("voucherModalTitle").innerText = "Cập nhật voucher giảm giá";
  voucherModal.show();
}

// Thêm hàm Dummy cho các nút thao tác mới (nếu bạn chưa có)
function viewVoucherUsage(id) {
  props.showToast(`Xem lượt sử dụng Voucher #${id}`, "info");
}

function duplicateVoucher(id) {
  props.showToast(`Nhân bản Voucher #${id}`, "info");
}
function getUsagePercent(v) {
  return (v.used_count / v.quantity) * 100;
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

  if (v.used_count >= v.quantity) return { class: "bg-danger", text: "Hết lượt" };
  if (v.is_active == false) return { class: "bg-secondary", text: "Không hoạt động" };

  if (now > end) return { class: "bg-secondary", text: "Hết hạn" };
  

  if (now >= start && now <= end && v.is_active == true) return { class: "bg-success", text: "Hoạt động" };

  return { class: "bg-warning", text: "Chờ kích hoạt" };
}
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
              <h6 class="mb-0 text-dark fw-bold">Danh sách chương trình khuyến mãi</h6>
              <button class="btn btn-primary btn-sm" @click="createPromotion">
                <fa :icon="['fas', 'plus']" class="me-2" />Tạo khuyến mãi
              </button>
            </div>

            <div class="table-responsive bg-white rounded shadow-sm">
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
                  <!-- <tr v-for="promo in mockPromotions" :key="promo.id">
                    <td>{{ promo.name }}</td>
                    <td>
                      <span
                        class="badge"
                        :class="promo.type === 'percentage' ? 'bg-info' : 'bg-success'"
                      >
                        {{ promo.type === "percentage" ? "Phần trăm" : "Cố định" }}
                      </span>
                    </td>
                    <td>
                      {{ promo.value }}{{ promo.type === "percentage" ? "%" : " VNĐ" }}
                    </td>
                    <td>{{ promo.time }}</td>
                    <td>{{ promo.usage }} lần</td>
                    <td>
                      <span class="badge" :class="getStatusBadge(promo.status)">
                        {{ promo.status }}
                      </span>
                    </td>
                    <td>
                      <div class="btn-group btn-group-sm">
                        <button
                          class="btn btn-outline-primary"
                          @click="editPromotion(promo.id)"
                          title="Chỉnh sửa"
                        >
                          <fa :icon="['fas', 'edit']" />
                        </button>
                        <button
                          class="btn btn-outline-danger"
                          @click="deletePromotion(promo.id)"
                          title="Xóa"
                        >
                          <fa :icon="['fas', 'trash']" />
                        </button>
                      </div>
                    </td>
                  </tr> -->
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
                  <option value="active">Đang hoạt động</option>
                  <option value="expired">Đã hết hạn</option>
                  <option value="used">Đã sử dụng hết</option>
                  <option value="inactive">Không hoạt động</option>
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
        </div>
      </div>
    </div>
    <!-- Add/Edit Voucher Modal -->
    <div class="modal fade" id="addVoucherModal" tabindex="-1">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="voucherModalTitle">Thêm voucher giảm giá</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>

          <form id="voucherForm" @submit.prevent="saveVoucher">
            <div class="modal-body">
              <input type="hidden" id="voucherId" :value="newVoucher.id" />

              <div class="row">
                <div class="col-md-4 mb-3">
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
                    {{ formErrors.code[0] }}
                  </div>
                </div>

                <div class="col-md-8 mb-3">
                  <label class="form-label"
                    >Tên voucher <span class="text-danger">*</span></label
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

              <div class="mb-3">
                <label class="form-label">Mô tả voucher</label>
                <textarea
                  class="form-control"
                  id="voucherDescription"
                  rows="3"
                  v-model="newVoucher.description"
                  :maxlength="maxLengths.description"
                ></textarea>
                <div class="form-text text-end">
                  <!-- {{ newVoucher.description.length }}/{{ maxLengths.description }} -->
                </div>
                <div v-if="formErrors.description" class="text-danger small mt-1">
                  {{ formErrors.description[0] }}
                </div>
              </div>

              <div class="row">
                <div class="col-md-4 mb-3">
                  <label class="form-label">Loại voucher</label>
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

              <div class="row">
                <div class="col-md-6 mb-3">
                  <label class="form-label"
                    >Số lượng voucher <span class="text-danger">*</span></label
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
              </div>

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
              <div class="form-check">
                <input
                  class="form-check-input"
                  type="checkbox"
                  id="voucherActive"
                  v-model="newVoucher.is_active"
                />
                <label class="form-check-label" for="voucherActive">
                  Kích hoạt voucher ngay
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
                {{ isSaving ? "Đang lưu..." : "Lưu voucher" }}
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
