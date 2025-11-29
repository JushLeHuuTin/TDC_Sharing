<script setup>
import { ref, computed, onMounted } from "vue";
import { storeToRefs } from "pinia";
import { useRouter } from "vue-router";
import AppLayout from "@/Layouts/AppLayout.vue";
import { useCartStore } from "@/stores/cartStore";
import { useOrderStore } from "@/stores/orderStore";
import { useVoucherStore } from "@/stores/voucherStore";
import { useAddressStore } from "@/stores/addressStore";
import { getCurrentInstance } from "vue";
import momoLogo from "@/assets/momo_logo.png";

const instance = getCurrentInstance();
const $toast = instance.appContext.config.globalProperties.$toast;

const router = useRouter();
const cartStore = useCartStore();
const orderStore = useOrderStore();
const voucherStore = useVoucherStore();
const addressStore = useAddressStore();
const { selectedAddressId } = storeToRefs(addressStore);
const { isProcessing: loading } = storeToRefs(orderStore);
const { cartData, formatPrice } = storeToRefs(cartStore);
const { appliedVoucherCode, appliedDiscount: storeAppliedDiscount } = storeToRefs(
  voucherStore
);
// onMounted(async () => {
//   await addressStore.fetchUserAddresses();
// });
const voucherInput = ref("");
const paymentMethod = ref("cod");
// const loading = ref(false);
const showSuccessModal = ref(false);

async function applyVoucher() {
  const success = await voucherStore.validateAndApplyVoucher(voucherInput.value);
  if (success) {
    $toast.success(
      "Áp dụng mã giảm giá thành công! Mức giảm: " +
        storeAppliedDiscount.value.toLocaleString("vi-VN") +
        " VNĐ",
      {
        position: "top-right",
        timeout: 5000,
      }
    );
  } else if (voucherStore.voucherError) {
    $toast.error(voucherStore.voucherError, {
      position: "top-right",
      timeout: 5000,
    });
  }
}
const discountList = ref([
  { code: "SALE500K", type: "percent", value: 10 },
  { code: "NEWBIE30", type: "fixed", value: 30000 },
]);

const selectedItems = computed(() => {
  if (!cartData.value?.shops) return [];
  return cartData.value.shops.flatMap((shop) =>
    shop.items.filter((item) => item.is_selected)
  );
});

const overallSummary = computed(() => {
  const base = cartData.value?.overall_summary || {
    subtotal: 0,
    shipping_fee: 0,
    discount: 0,
    total: 0,
  };

  const storeDiscountAmount = storeAppliedDiscount.value;

  const finalDiscount = storeDiscountAmount;

  const total = Math.max(base.subtotal + base.shipping_fee - finalDiscount, 0);
  return {
    ...base,
    discount: finalDiscount,
    total,
  };
});

const selectDiscount = async (code) => {
  voucherInput.value = code;
  await applyVoucher();
};

const handlePayment = async () => {
  if (selectedItems.value.length === 0) {
    alert("Bạn chưa chọn sản phẩm nào.");
    return;
  }

  // loading.value = true; // Không cần set nữa, Store tự quản lý isProcessing

  const finalVoucherCode = appliedVoucherCode.value;
  const payload = {
    voucher_code: finalVoucherCode,
    address_id: selectedAddressId.value,
    payment_method: paymentMethod.value,
  };

  // GỌI ACTION TỪ PINIA STORE
  const result = await orderStore.handleCheckoutPayment(
    payload,
    paymentMethod.value,
    router
  );

  if (result.success && !result.redirect) {
    // Áp dụng cho COD
    showSuccessModal.value = true;

    // Tự động đóng modal và chuyển về home sau 5 giây
    setTimeout(() => {
      showSuccessModal.value = false;
      router.push({ name: "home" });
    }, 5000);
  } else if (!result.success) {
    // Xử lý lỗi
    console.error(result.message);
    $toast.error(result.message, { position: "top-right", timeout: 5000 });
  }
};
</script>

<template>
  <AppLayout>
    <div class="max-w-7xl mx-auto py-8 grid grid-cols-12 gap-8">
      <!-- MAIN -->
      <div class="col-span-12 lg:col-span-8">
        <div class="flex justify-between items-center mb-6 text-center">
          <div class="flex-1 text-gray-500">
            <div
              class="w-10 h-10 bg-gray-200 rounded-full flex items-center justify-center font-bold mx-auto mb-1"
            >
              1
            </div>
            <p class="text-sm">Giỏ hàng</p>
          </div>
          <div class="flex-1 border-t-2 border-gray-300 mx-[-16px]"></div>
          <div class="flex-1 text-gray-500">
            <div
              class="w-10 h-10 bg-gray-200 rounded-full flex items-center justify-center font-bold mx-auto mb-1"
            >
              2
            </div>
            <p class="text-sm">Thông tin</p>
          </div>
          <div class="flex-1 border-t-2 border-gray-300 mx-[-16px]"></div>
          <div class="flex-1 text-gray-500">
            <div
              class="w-10 h-10 bg-blue-600 rounded-full text-white flex items-center justify-center font-bold mx-auto mb-1"
            >
              3
            </div>
            <p class="text-sm font-medium">Thanh toán</p>
          </div>
        </div>
        <h2 class="text-2xl font-bold mb-6">Thanh toán đơn hàng</h2>
        <div class="bg-white rounded-lg shadow p-6 space-y-6">
          <h3 class="font-semibold mb-4">Chọn phương thức thanh toán</h3>
          <div class="flex flex-col space-y-3">
            <!-- 1. THANH TOÁN KHI NHẬN HÀNG (COD) -->
            <label
              class="flex items-start p-3 border rounded-lg cursor-pointer transition-all duration-150"
              :class="{
                'border-blue-600 bg-blue-50 ring-2 ring-blue-500':
                  paymentMethod === 'cod',
              }"
            >
              <!-- Hidden Radio Button -->
              <input type="radio" value="cod" v-model="paymentMethod" class="hidden" />

              <!-- Icon Container -->
              <div
                class="flex items-center justify-center w-10 h-10 bg-green-500 rounded-lg flex-shrink-0 mr-3"
              >
                <!-- Icon: Cash/Payment -->
                <fa :icon="['fas', 'money-bill-wave']" class="text-white text-lg" />
              </div>

              <!-- Content -->
              <div>
                <p class="font-semibold text-gray-800">Thanh toán khi nhận hàng (COD)</p>
                <p class="text-xs text-gray-500 mt-0.5">
                  Trả tiền mặt khi giao hàng. Phí COD: 15.000 VNĐ
                </p>
              </div>
            </label>
            <!-- 2. VÍ MOMO -->
            <label
              class="flex items-start p-3 border rounded-lg cursor-pointer transition-all duration-150"
              :class="{
                'border-blue-600 bg-blue-50 ring-2 ring-blue-500':
                  paymentMethod === 'momo',
              }"
            >
              <!-- Hidden Radio Button -->
              <input type="radio" value="momo" v-model="paymentMethod" class="hidden" />

              <!-- Icon Container (Dùng logo đã import) -->
              <div class="flex items-center justify-center w-10 h-10 flex-shrink-0 mr-3">
                <!-- SỬ DỤNG HÌNH ẢNH ĐÃ IMPORT -->
                <img
                  :src="momoLogo"
                  alt="Ví MoMo Logo"
                  class="w-10 h-10 object-cover rounded-lg"
                />
              </div>

              <!-- Content -->
              <div>
                <p class="font-semibold text-gray-800">Ví MoMo</p>
                <p class="text-xs text-gray-500 mt-0.5">
                  Thanh toán qua ví điện tử MoMo. Miễn phí
                </p>
              </div>
            </label>

            <!-- 3. CHUYỂN KHOẢN NGÂN HÀNG (Bank) -->
            <label
              class="flex items-start p-3 border rounded-lg cursor-pointer transition-all duration-150"
              :class="{
                'border-blue-600 bg-blue-50 ring-2 ring-blue-500':
                  paymentMethod === 'bank',
              }"
            >
              <!-- Hidden Radio Button -->
              <input type="radio" value="bank" v-model="paymentMethod" class="hidden" />

              <!-- Icon Container (Màu xanh ngân hàng) -->
              <div
                class="flex items-center justify-center w-10 h-10 bg-indigo-500 rounded-lg flex-shrink-0 mr-3"
              >
                <!-- Icon: University/Bank -->
                <fa :icon="['fas', 'university']" class="text-white text-lg" />
              </div>

              <!-- Content -->
              <div>
                <p class="font-semibold text-gray-800">Chuyển khoản ngân hàng</p>
                <p class="text-xs text-gray-500 mt-0.5">
                  Chuyển khoản trước khi giao hàng. Miễn phí
                </p>
              </div>
            </label>
          </div>

          <div class="flex justify-between items-center mt-6">
            <button
              @click="router.push({ name: 'checkout-information' })"
              class="bg-gray-200 text-gray-800 px-6 py-3 rounded hover:bg-gray-300"
            >
              <fa :icon="['fas', 'arrow-left']" class="mr-2 fa-sm" />
              Quay lại
            </button>
            <button
              @click="handlePayment"
              :disabled="loading"
              class="bg-blue-600 text-white px-6 py-3 rounded hover:bg-blue-700 disabled:opacity-50"
            >
              Thanh toán {{ formatPrice(overallSummary.total) }}
            </button>
          </div>
        </div>
      </div>

      <!-- SIDEBAR -->
      <div class="col-span-12 lg:col-span-4 sticky top-24 space-y-6">
        <div class="bg-white rounded-lg shadow p-6">
          <h3 class="text-xl font-bold mb-4">Tổng đơn hàng</h3>
          <div class="space-y-2 text-sm text-gray-600 border-b pb-3">
            <div class="flex justify-between">
              <span>Tạm tính:</span>
              <span class="font-medium">{{ formatPrice(overallSummary.subtotal) }}</span>
            </div>
            <div class="flex justify-between">
              <span>Phí vận chuyển:</span>
              <span class="font-medium">{{
                formatPrice(overallSummary.shipping_fee)
              }}</span>
            </div>
            <div class="flex justify-between" v-if="storeAppliedDiscount">
              <span>Giảm giá:</span>
              <span class="font-medium text-red-600"
                >- {{ formatPrice(overallSummary.discount) }}</span
              >
            </div>
          </div>
          <div class="flex justify-between items-center text-lg font-bold pt-3">
            <span>Tổng cộng:</span>
            <span class="text-red-600">{{ formatPrice(overallSummary.total) }}</span>
          </div>
        </div>

        <!-- Mã giảm giá -->
        <div class="bg-white rounded-xl shadow-lg p-5 space-y-4 border border-gray-100">
          <h3 class="text-lg font-bold text-gray-800 border-b pb-2">Mã giảm giá</h3>

          <div class="space-y-3">
            <div class="flex space-x-2 items-center">
              <input
                type="text"
                v-model="voucherInput"
                :disabled="voucherStore.isLoading"
                placeholder="Nhập mã giảm giá"
                class="flex-grow p-2 border rounded-lg focus:ring-blue-500 focus:border-blue-500 disabled:bg-gray-100"
              />
              <button
                @click="applyVoucher"
                :disabled="voucherStore.isLoading || !voucherInput"
                class="px-4 py-2 text-white font-medium rounded-lg transition duration-150"
                :class="{
                  'bg-blue-600 hover:bg-blue-700': !voucherStore.isLoading,
                  'bg-blue-400 cursor-not-allowed': voucherStore.isLoading,
                }"
              >
                {{ voucherStore.isLoading ? "Đang kiểm tra..." : "Áp dụng" }}
              </button>
            </div>

            <div
              v-if="voucherStore.appliedDiscount > 0"
              class="flex justify-between p-2 rounded-lg bg-green-50 text-green-700 font-semibold border border-green-200"
            >
              <span>Giảm giá đã áp dụng:</span>
              <span>-{{ voucherStore.appliedDiscount.toLocaleString("vi-VN") }} VNĐ</span>
            </div>

            <div
              v-else-if="voucherStore.voucherError"
              class="p-2 text-sm text-red-600 bg-red-50 rounded-lg border border-red-200"
            >
              {{ voucherStore.voucherError }}
            </div>
          </div>

          <div class="space-y-3 pt-3 border-t border-gray-100">
            <p class="text-gray-600 text-sm font-semibold">Mã giảm giá có thể sử dụng:</p>
            <div class="space-y-2">
              <div
                v-for="d in discountList"
                :key="d.code"
                class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition duration-150"
              >
                <div class="flex flex-col">
                  <p
                    class="font-mono bg-indigo-100 text-indigo-700 px-2 py-0.5 rounded text-xs font-semibold"
                  >
                    {{ d.code }}
                  </p>
                  <p class="text-gray-500 text-xs mt-1">
                    Giảm {{ d.type === "percent" ? d.value + "%" : formatPrice(d.value) }}
                  </p>
                </div>

                <button
                  @click="selectDiscount(d.code)"
                  class="px-3 py-1 text-sm bg-transparent border border-blue-600 text-blue-600 hover:bg-blue-600 hover:text-white rounded-full transition duration-150"
                >
                  Chọn
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <transition name="fade">
      <div
        v-if="showSuccessModal"
        class="fixed inset-0 bg-black/50 flex items-center justify-center z-50"
      >
        <div class="bg-white rounded-xl shadow-xl p-8 max-w-md w-full text-center">
          <svg
            class="w-16 h-16 mx-auto text-green-500 mb-4"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
            />
          </svg>
          <h2 class="text-2xl font-bold mb-2">Thanh toán thành công!</h2>
          <p class="text-gray-600 mb-4">
            Đơn hàng của bạn đã được tạo và đang chờ người bán xử lý.
          </p>

          <div class="flex justify-center gap-4">
            <button
              @click="
                () => {
                  showSuccessModal = false;
                  router.push({ name: 'home' });
                }
              "
              class="px-6 py-2 border border-gray-300 rounded hover:bg-gray-100"
            >
              Tiếp tục mua sắm
            </button>
            <button
              @click="
                () => {
                  showSuccessModal = false;
                  router.push({ name: 'user-orders' });
                }
              "
              class="px-6 py-2 bg-blue-600 text-white rounded hover:bg-blue-700"
            >
              Xem đơn hàng
            </button>
          </div>
        </div>
      </div>
    </transition>
  </AppLayout>
</template>
