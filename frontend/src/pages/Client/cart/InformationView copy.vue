<script setup>
import { onMounted, computed, ref } from "vue";
import { storeToRefs } from "pinia";
import { useRouter } from "vue-router";
import AppLayout from "@/Layouts/AppLayout.vue";
import { useAuthStore } from "@/stores/auth";
import { useCartStore } from "@/stores/cartStore";
import { useAddressStore } from "@/stores/addressStore"; // Đã import

const addressStore = useAddressStore();
const router = useRouter();
const authStore = useAuthStore();
const cartStore = useCartStore();

// Lấy dữ liệu giỏ hàng và địa chỉ từ Store
const { cartData, formatPrice } = storeToRefs(cartStore);
const { userProfile } = storeToRefs(authStore);
const { userAddresses, selectedAddressId, isLoading: loadingAddresses } = storeToRefs(
  addressStore
);

// --- STATE FORM CỤC BỘ ---
// Chúng ta sẽ giữ form này để hiển thị chi tiết địa chỉ được chọn, KHÔNG dùng để nhập.
const formData = ref({
  fullName: "",
  phoneNumber: "",
  addressDetail: "", // Thông tin chi tiết địa chỉ
  note: "",
});

// Computed để lấy chi tiết địa chỉ đang được chọn
const selectedAddressDetails = computed(() => {
  return userAddresses.value.find((addr) => addr.id === selectedAddressId.value) || null;
});

const selectedItems = computed(() => {
  if (!cartData.value?.shops) return [];
  return cartData.value.shops.flatMap((shop) =>
    shop.items.filter((item) => item.is_selected)
  );
});

const overallSummary = computed(() => {
  if (!cartData.value?.overall_summary) {
    return { subtotal: 0, shipping_fee: 0, discount: 0, total: 0 };
  }
  return cartData.value.overall_summary;
});

const totalSelected = computed(() => selectedItems.value.length);

// Xử lý khi nhấn nút Tiếp tục
const continueToPayment = () => {
  if (!selectedAddressId.value) {
    alert("Vui lòng chọn địa chỉ giao hàng.");
    return;
  }
  // Ghi chú: Bạn có thể lưu formData.note vào store nếu cần
  router.push({ name: "checkout-payment" });
};

onMounted(async () => {
  // 1. Tải Profile và Địa chỉ cùng lúc
  await Promise.all([
    cartStore.fetchCartData(),
    authStore.fetchUserProfile(),
    addressStore.fetchUserAddresses(), // Tải danh sách địa chỉ
  ]);

  if (userProfile.value ) {
    const profile = userProfile.value;
    formData.value.fullName = profile.name || "";
    formData.value.phoneNumber = profile.phone || "";
    formData.value.address = profile.full_shipping_address || "";
  }
});

// Theo dõi khi địa chỉ được chọn thay đổi để cập nhật formData (chỉ để hiển thị)
// watch(selectedAddressDetails, (newDetails) => {
//     if (newDetails) {
//         formData.value.fullName = newDetails.full_name;
//         formData.value.phoneNumber = newDetails.phone;
//         // Có thể tạo một trường hiển thị địa chỉ đầy đủ từ newDetails
//         formData.value.addressDetail = `${newDetails.street_address}, ${newDetails.ward}, ${newDetails.district}, ${newDetails.city}`;
//     }
// }, { immediate: true });
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

        <div class="col-span-12 lg:col-span-8">
          <h2 class="text-2xl font-bold text-gray-900 mb-6">Thông tin giao hàng</h2>

          <div class="bg-white rounded-lg shadow-sm p-6 space-y-6">
            <h3 class="font-semibold text-gray-900 mb-4">Chọn địa chỉ giao hàng</h3>

            <div>
              <label for="addressSelect" class="block text-sm font-medium text-gray-700"
                >Địa chỉ đã lưu</label
              >

              <div v-if="loadingAddresses" class="mt-1 text-blue-600">
                Đang tải địa chỉ...
              </div>

              <select
                v-else-if="userAddresses.length > 0"
                id="addressSelect"
                :value="selectedAddressId"
                @change="addressStore.setSelectedAddress(parseInt($event.target.value))"
                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
              >
                <option v-for="addr in userAddresses" :key="addr.id" :value="addr.id">
                  {{ addr.full_name }} | {{ addr.phone }} | {{ addr.street_address }},
                  {{ addr.city }}
                </option>
              </select>

              <div
                v-else
                class="mt-2 p-3 bg-yellow-50 border-l-4 border-yellow-500 text-yellow-700"
              >
                Bạn chưa có địa chỉ nào được lưu.
              </div>

              <div class="mt-4">
                <button class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                  + Thêm địa chỉ mới (Chuyển đến trang quản lý)
                </button>
              </div>
            </div>

            <div v-if="selectedAddressDetails" class="p-4 bg-gray-50 rounded-lg border">
              <h4 class="font-semibold text-gray-800">Thông tin nhận hàng:</h4>
              <p class="text-sm mt-1">
                **{{ selectedAddressDetails.full_name }}** |
                {{ selectedAddressDetails.phone }}
              </p>
              <p class="text-sm text-gray-600">
                {{ selectedAddressDetails.street_address }},
                {{ selectedAddressDetails.ward }}, {{ selectedAddressDetails.district }},
                {{ selectedAddressDetails.city }}
              </p>
            </div>
            <div
              v-else-if="userAddresses.length > 0"
              class="p-4 bg-red-50 text-red-700 rounded-lg"
            >
              Vui lòng chọn một địa chỉ từ danh sách.
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
                @click="continueToPayment"
                :disabled="!selectedAddressId"
                class="bg-blue-600 text-white py-3 px-4 rounded-lg transition-all font-medium hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed"
              >
                Tiếp tục đến thanh toán
                <fa :icon="['fas', 'arrow-right']" class="ml-2 fa-sm" />
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
