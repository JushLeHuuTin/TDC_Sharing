<script setup>
import { ref, computed } from "vue";
import { storeToRefs } from "pinia";
import { useRouter } from "vue-router";
import AppLayout from "@/Layouts/AppLayout.vue";
import { useCartStore } from "@/stores/cartStore";

const router = useRouter();
const cartStore = useCartStore();
const { cartData, formatPrice } = storeToRefs(cartStore);

// --- STATE ---
const paymentMethod = ref("cod");
const loadingPayment = ref(false);

// Discount logic
const discountCode = ref("");
const appliedDiscount = ref(null);
const discountList = ref([
  { code: "STUDENT10", type: "percent", value: 10 },
  { code: "NEWBIE30", type: "fixed", value: 30000 },
]);

// Selected items
const selectedItems = computed(() => {
  if (!cartData.value?.shops) return [];
  return cartData.value.shops.flatMap((shop) =>
    shop.items.filter((item) => item.is_selected)
  );
});

// Overall summary with discount applied
const overallSummary = computed(() => {
  const base = cartData.value?.overall_summary || {
    subtotal: 0,
    shipping_fee: 0,
    discount: 0,
    total: 0,
  };
  let discount = 0;
  if (appliedDiscount.value) {
    if (appliedDiscount.value.type === "percent") {
      discount =
        ((base.subtotal + base.shipping_fee) * appliedDiscount.value.value) / 100;
    } else if (appliedDiscount.value.type === "fixed") {
      discount = appliedDiscount.value.value;
    }
  }
  const total = Math.max(base.subtotal + base.shipping_fee - discount, 0);
  return { ...base, discount, total };
});

// --- FUNCTIONS ---
const applyDiscount = () => {
  const found = discountList.value.find(
    (d) => d.code === discountCode.value.toUpperCase()
  );
  if (found) {
    appliedDiscount.value = found;
    alert(`Áp dụng mã ${found.code} thành công!`);
  } else {
    alert("Mã giảm giá không hợp lệ");
    appliedDiscount.value = null;
  }
};

const selectDiscount = (code) => {
  const found = discountList.value.find((d) => d.code === code);
  appliedDiscount.value = found || null;
};

const handlePayment = async () => {
  if (selectedItems.value.length === 0) {
    alert("Bạn chưa chọn sản phẩm nào.");
    return;
  }

  loadingPayment.value = true;

  try {
    if (paymentMethod.value === "cod") {
      router.push({ name: "checkout-success" });
    } else if (paymentMethod.value === "momo") {
      const response = await fetch("/api/momo/create-payment", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
          items: selectedItems.value,
          total: overallSummary.value.total,
        }),
      });
      const data = await response.json();
      if (data.payUrl) window.location.href = data.payUrl;
      else alert("Không thể tạo thanh toán MoMo. Vui lòng thử lại.");
    } else if (paymentMethod.value === "bank") {
      alert("Vui lòng chuyển khoản theo hướng dẫn. (Chưa implement API)");
    }
  } catch (err) {
    console.error(err);
    alert("Có lỗi xảy ra khi tạo thanh toán.");
  } finally {
    loadingPayment.value = false;
  }
};
</script>

<template>
  <AppLayout>
    <div class="max-w-7xl mx-auto py-8">
      <div class="grid grid-cols-12 gap-8">
        <!-- MAIN -->
        <div class="col-span-12 lg:col-span-8">
          <!-- STEPPER -->
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
            <div class="flex-1">
              <div
                class="w-10 h-10 bg-blue-600 rounded-full text-white flex items-center justify-center font-bold mx-auto mb-1"
              >
                3
              </div>
              <p class="text-sm font-medium">Thanh toán</p>
            </div>
          </div>

          <h2 class="text-2xl font-bold text-gray-900 mb-6">Thanh toán đơn hàng</h2>

          <!-- Payment Methods -->
          <div class="bg-white rounded-lg shadow-sm p-6 space-y-6">
            <h3 class="font-semibold text-gray-900 mb-4">Chọn phương thức thanh toán</h3>
            <div class="flex flex-col space-y-3">
              <label
                class="flex items-center p-3 border rounded-lg cursor-pointer"
                :class="{ 'border-blue-600 bg-blue-50': paymentMethod === 'cod' }"
              >
                <input type="radio" value="cod" v-model="paymentMethod" class="mr-3" />
                <span>Thanh toán khi nhận hàng (COD)</span>
              </label>
              <label
                class="flex items-center p-3 border rounded-lg cursor-pointer"
                :class="{ 'border-blue-600 bg-blue-50': paymentMethod === 'momo' }"
              >
                <input type="radio" value="momo" v-model="paymentMethod" class="mr-3" />
                <span>Ví MoMo</span>
              </label>
              <label
                class="flex items-center p-3 border rounded-lg cursor-pointer"
                :class="{ 'border-blue-600 bg-blue-50': paymentMethod === 'bank' }"
              >
                <input type="radio" value="bank" v-model="paymentMethod" class="mr-3" />
                <span>Chuyển khoản ngân hàng</span>
              </label>
            </div>

            <!-- Main area payment section -->
            <div class="text-right mt-6 flex justify-between items-center">
              <!-- Button quay lại Step 2 -->
              <button
                @click="router.push({ name: 'checkout-information' })"
                class="bg-gray-200 text-gray-800 px-6 py-3 rounded-lg hover:bg-gray-300"
              >
             <fa :icon="['fas', 'arrow-left']" class="mr-2 fa-sm" /> Quay lại 
              </button>

              <!-- Button thanh toán -->
              <button
                @click="handlePayment"
                :disabled="loadingPayment"
                class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 disabled:opacity-50"
              >
                Thanh toán {{ formatPrice(overallSummary.total) }}
              </button>
            </div>
          </div>
        </div>

        <!-- SIDEBAR -->
        <div class="col-span-12 lg:col-span-4 sticky top-24 space-y-6">
          <!-- Tổng đơn hàng -->
          <div class="bg-white rounded-lg shadow-sm p-6">
            <h3 class="text-xl font-bold text-gray-900 mb-4">Tổng đơn hàng</h3>
            <div class="space-y-2 text-sm text-gray-600 border-b pb-3">
              <div class="flex justify-between">
                <span>Tạm tính:</span>
                <span class="font-medium text-gray-900">{{
                  formatPrice(overallSummary.subtotal)
                }}</span>
              </div>
              <div class="flex justify-between">
                <span>Phí vận chuyển:</span>
                <span class="font-medium text-gray-900">{{
                  formatPrice(overallSummary.shipping_fee)
                }}</span>
              </div>
              <div class="flex justify-between" v-if="appliedDiscount">
                <span>Giảm giá ({{ appliedDiscount.code }}):</span>
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
          <div class="bg-white rounded-lg shadow-sm p-6 space-y-4">
            <h3 class="font-semibold text-gray-900">Mã giảm giá</h3>
            <div class="flex space-x-2">
              <input
                type="text"
                placeholder="Nhập mã giảm giá"
                v-model="discountCode"
                class="flex-1 border border-gray-300 rounded-lg text-sm p-2.5 focus:border-blue-500 focus:ring-blue-500"
              />
              <button
                @click="applyDiscount"
                class="bg-blue-600 text-white px-4 py-2 rounded-lg font-medium text-sm hover:bg-blue-700"
              >
                Áp dụng
              </button>
            </div>

            <div class="space-y-2 text-sm">
              <div
                v-for="d in discountList"
                :key="d.code"
                class="flex items-center justify-between text-gray-700"
              >
                <p
                  class="font-mono bg-green-100 text-green-700 px-2 py-0.5 rounded text-xs"
                >
                  {{ d.code }} ({{
                    d.type === "percent" ? d.value + "%" : "-" + formatPrice(d.value)
                  }})
                </p>
                <button
                  @click="selectDiscount(d.code)"
                  class="text-blue-600 hover:text-blue-700"
                >
                  Chọn
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
