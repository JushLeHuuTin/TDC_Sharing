<script setup>
import { onMounted, computed, ref } from "vue";
import { storeToRefs } from "pinia";
import { useRouter } from "vue-router";
import AppLayout from "@/Layouts/AppLayout.vue";
import { useAuthStore } from "@/stores/auth";
import { useCartStore } from "@/stores/cartStore";

const router = useRouter();
const authStore = useAuthStore();
const cartStore = useCartStore();
const { cartData, formatPrice } = storeToRefs(cartStore);
// --- STATE FORM CỤC BỘ ---
const formData = ref({
  fullName: "",
  phoneNumber: "",
  address: "", // Địa chỉ đầy đủ từ API
  note: "",
});
const { userProfile, loadingProfile } = storeToRefs(authStore);
const selectedItems = computed(() => {
  if (!cartData.value || !cartData.value.shops) return [];

  return cartData.value.shops.flatMap((shop) =>
    shop.items.filter((item) => item.is_selected)
  );
});
const overallSummary = computed(() => {
  if (!cartData.value || !cartData.value.overall_summary) {
    return { subtotal: 0, shipping_fee: 0, discount: 0, total: 0 };
  }
  return cartData.value.overall_summary;
});
const totalSelected = computed(() => selectedItems.value.length);
onMounted(async () => {
  cartStore.fetchCartData();
  // 1. Fetch Profile
  await authStore.fetchUserProfile();
  console.log(userProfile);
  // 2. Điền dữ liệu vào form khi profile có sẵn
  if (userProfile.value) {
    const profile = userProfile.value;

    // Điền dữ liệu từ API JSON vào formData
    formData.value.fullName = profile.name || "";
    formData.value.phoneNumber = profile.phone || "";
    formData.value.address = profile.full_shipping_address || "";
  }
});
</script>
<template>
  <AppLayout>
    <div class="max-w-7xl mx-auto py-8">
      <div class="grid grid-cols-12 gap-8">
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
            <div class="flex-1">
              <div
                class="w-10 h-10 bg-blue-600 rounded-full text-white flex items-center justify-center font-bold mx-auto mb-1"
              >
                2
              </div>
              <p class="text-sm font-medium">Thông tin</p>
            </div>
            <div class="flex-1 border-t-2 border-gray-300 mx-[-16px]"></div>
            <div class="flex-1 text-gray-500">
              <div
                class="w-10 h-10 bg-gray-200 rounded-full flex items-center justify-center font-bold mx-auto mb-1"
              >
                3
              </div>
              <p class="text-sm">Thanh toán</p>
            </div>
          </div>

          <h2 class="text-2xl font-bold text-gray-900 mb-6">Thông tin giao hàng</h2>

          <div class="bg-white rounded-lg shadow-sm p-6 space-y-6">
            <h3 class="font-semibold text-gray-900 mb-4">Địa chỉ giao hàng</h3>

            <div>
              <label for="fullName" class="block text-sm font-medium text-gray-700"
                >Họ và tên</label
              >
              <input
                type="text"
                id="fullName"
                v-model="formData.fullName"
                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                placeholder="Nguyễn Văn A"
              />
            </div>

            <div>
              <label for="phoneNumber" class="block text-sm font-medium text-gray-700"
                >Số điện thoại</label
              >
              <input
                type="tel"
                id="phoneNumber"
                v-model="formData.phoneNumber"
                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                placeholder="09xxxxxxxx"
              />
            </div>

            <div>
              <label for="address" class="block text-sm font-medium text-gray-700"
                >Địa chỉ</label
              >
              <input
                type="text"
                id="address"
                v-model="formData.address"
                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                placeholder="Số nhà, tên đường, phường/xã, quận/huyện, tỉnh/thành phố"
              />
            </div>

            <div>
              <label for="note" class="block text-sm font-medium text-gray-700"
                >Ghi chú (tùy chọn)</label
              >
              <textarea
                id="note"
                v-model="formData.note"
                rows="3"
                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                placeholder="Ví dụ: Giao hàng giờ hành chính..."
              ></textarea>
            </div>

            <h3 class="font-semibold text-gray-900 mt-8 mb-4">Thông tin thêm</h3>
            <div>
              <label for="invoice" class="flex items-center">
                <input
                  type="checkbox"
                  id="invoice"
                  class="rounded text-blue-600 focus:ring-blue-500"
                />
                <span class="ml-2 text-sm text-gray-700">Yêu cầu xuất hóa đơn VAT</span>
              </label>
            </div>

            <div class="flex justify-between mt-8">
              <button
               @click="router.push({ name: 'cart' })"
                class="bg-gray-200 text-gray-700 hover:bg-gray-300 px-4 py-2 rounded-lg text-sm font-medium transition-all"
              >
                <fa :icon="['fas', 'arrow-left']" class="mr-2 fa-sm" /> Quay lại giỏ hàng
              </button>
              <button
                @click="router.push({ name: 'checkout-payment' })"
                class="bg-blue-600 text-white py-3 px-4 rounded-lg transition-all font-medium hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed"
              >
                Tiếp tục đến thanh toán
                <fa :icon="['fas', 'arrow-right']" class="ml-2 fa-sm" />
              </button>
            </div>
          </div>
        </div>

        <div class="col-span-12 lg:col-span-4 sticky top-24 space-y-6">
          <div class="bg-white rounded-lg shadow-sm p-6">
            <h3 class="text-xl font-bold text-gray-900 mb-4">Tổng đơn hàng</h3>
            <div class="space-y-2 text-sm text-gray-600 border-b pb-3">
              <div class="flex justify-between">
                <span>Tạm tính ({{ totalSelected }} sản phẩm):</span>
                <span class="font-medium text-gray-900">
                  {{ formatPrice(overallSummary.subtotal) }}
                </span>
              </div>

              <div class="flex justify-between">
                <span>Phí vận chuyển:</span>
                <span class="font-medium text-gray-900">
                  {{ formatPrice(overallSummary.shipping_fee) }}
                </span>
              </div>

              <div class="flex justify-between">
                <span>Giảm giá:</span>
                <span class="font-medium text-red-600">
                  - {{ formatPrice(overallSummary.discount) }}
                </span>
              </div>
            </div>

            <div class="flex justify-between items-center text-lg font-bold pt-3">
              <span>Tổng cộng:</span>
              <span class="text-red-600">
                {{ formatPrice(overallSummary.total) }}
              </span>
            </div>

          </div>
          <div class="bg-white rounded-lg shadow-sm p-6">
            <h3 class="font-semibold text-gray-900 mb-4">
              Sản phẩm đã chọn ({{ totalSelected }})
            </h3>

            <div v-if="selectedItems.length === 0" class="text-gray-500 text-sm">
              Bạn chưa chọn sản phẩm nào.
            </div>

            <div
              v-for="item in selectedItems"
              :key="item.cart_item_id"
              class="flex items-center space-x-3 mb-3 border-b pb-3 last:border-b-0"
            >
              <img
                :src="item.image_url"
                :alt="item.title"
                class="w-12 h-12 object-cover rounded border"
              />

              <div class="flex-1">
                <p class="text-sm font-medium text-gray-900 line-clamp-1">
                  {{ item.title }}
                </p>
                <p class="text-xs text-gray-500">x {{ item.quantity }}</p>
              </div>

              <span class="text-sm font-semibold text-gray-900">
                {{ formatPrice(item.price * item.quantity) }}
              </span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
