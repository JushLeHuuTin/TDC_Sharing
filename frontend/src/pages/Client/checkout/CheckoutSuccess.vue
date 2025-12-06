<script setup>
import { computed } from "vue";
import { useRoute, useRouter } from "vue-router";
import AppLayout from "@/Layouts/AppLayout.vue";
import { useCartStore } from "@/stores/cartStore";


const route = useRoute();
const router = useRouter();

// --- LẤY ORDER IDS TRẢ VỀ ---
const orderIdsString = route.query.orderIds || "";
const confirmedOrderIds = computed(() =>
  orderIdsString ? orderIdsString.split(",") : []
);
// --- LẤY TRANSACTION INFO TỪ MOMO ---
const momoTransId = route.query.transId || null;
const momoOrderId = route.query.orderId || null;
const resultCode = Number(route.query.resultCode) || 0;

const handleNavigate = (name) => {
  router.push({ name });
};
</script>

<template>
<AppLayout>
  <div class="min-h-screen flex items-center justify-center bg-gray-50 px-4">
    <div class="w-full max-w-lg bg-white rounded-lg shadow p-8 text-center border-t-8 border-green-500">
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

      <h1 class="text-3xl md:text-4xl font-extrabold text-gray-900 mb-2">
        Thanh toán thành công!
      </h1>
      <p class="text-gray-600 mb-4">
        Đơn hàng của bạn đã được thanh toán và đang chờ người bán xử lý.
      </p>

      <div class="bg-gray-50 p-6 rounded-xl text-left space-y-3 border border-gray-100">
        <p class="text-sm font-semibold text-gray-700">
          Giao dịch:
          <span class="font-normal text-gray-900">{{ momoOrderId ? "Momo" : "Không có / Thanh toán COD"}}</span>
        </p>
        <p class="text-sm font-semibold text-gray-700">
          Mã giao dịch MoMo::
          <span class="font-normal text-gray-900">{{ momoOrderId || "Không có" }}</span>
        </p>
        <p class="text-sm font-semibold text-gray-700">
          Trạng thái:
          <span :class="resultCode === 0 ? 'text-green-600 font-bold' : 'text-red-600 font-bold'">
            {{ resultCode === 0 ? "Thành công" : "Lỗi" }}
          </span>
        </p>

        <div v-if="confirmedOrderIds.length > 0" class="mt-4">
          <p class="text-sm font-semibold text-gray-700">
            Đã tạo {{ confirmedOrderIds.length }} đơn hàng:
          </p>
          <div class="flex flex-wrap gap-2 mt-2">
            <span v-for="id in confirmedOrderIds" :key="id" class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-xs font-mono">
              #ORDER-{{ id }}
            </span>
          </div>
        </div>
      </div>

      <div class="mt-8 flex flex-col md:flex-row justify-center gap-4">
        <button
          @click="handleNavigate('products.index')"
          class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-100 transition duration-150"
        >
          Tiếp tục mua sắm
        </button>
        <button
          @click="handleNavigate('user-orders')"
          class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition duration-150"
        >
          Xem đơn hàng của tôi
        </button>
      </div>
    </div>
  </div>
</AppLayout>

</template>
