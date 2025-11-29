<script setup>
import { computed } from "vue";
import { useRoute, useRouter } from "vue-router";
import AppLayout from "@/Layouts/AppLayout.vue";

const route = useRoute();
const router = useRouter();

// Lấy thông báo lỗi từ session flash data (nếu Backend gửi) hoặc từ URL
// Giả định backend gửi lỗi qua session flash data (như bạn dùng with('error', ...))
// Nếu bạn sử dụng Inertia.js, bạn có thể truy cập props.error
// Ở đây tôi dùng placeholder cho session flash data
const backendFlashError = computed(() => {
    // Thay thế bằng logic truy cập flash data thực tế
    return route.query.error || 'Thanh toán đã bị hủy hoặc gặp lỗi không mong muốn.';
});

const handleNavigate = (name) => {
    router.push({ name: name });
};
</script>

<template>
  <AppLayout>
    <div class="max-w-4xl mx-auto py-16 px-4">
      <div class="bg-white rounded-xl shadow-2xl p-8 md:p-12 text-center border-t-8 border-red-500">
        
        <!-- STEPPER -->
        <div class="flex justify-center items-center mb-6">
            <div class="flex-1 text-gray-500 text-center">
                <div aclass="w-10 h-10 bg-gray-200 rounded-full flex items-center justify-center font-bold mx-auto mb-1">1</div>
                <p class="text-sm">Giỏ hàng</p>
            </div>
            <div class="flex-1 border-t-2 border-gray-300 mx-[-16px]"></div>
            <div class="flex-1 text-gray-500 text-center">
                <div class="w-10 h-10 bg-gray-200 rounded-full flex items-center justify-center font-bold mx-auto mb-1">2</div>
                <p class="text-sm">Thông tin</p>
            </div>
            <div class="flex-1 border-t-2 border-gray-300 mx-[-16px]"></div>
            <div class="flex-1 text-center">
                <div class="w-10 h-10 bg-red-500 rounded-full text-white flex items-center justify-center font-bold mx-auto mb-1">3</div>
                <p class="text-sm font-medium">Thất bại</p>
            </div>
        </div>

        <!-- THÔNG BÁO CHÍNH -->
        <div class="mb-8">
            <svg class="w-16 h-16 mx-auto text-red-500 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <h1 class="text-3xl font-extrabold text-gray-900 mb-3">Thanh toán thất bại!</h1>
            <p class="text-gray-700 font-semibold mt-4 p-4 bg-red-50 border border-red-200 rounded-lg">
                Chi tiết lỗi: {{ backendFlashError }}
            </p>
        </div>

        <!-- NÚT HÀNH ĐỘNG -->
        <div class="mt-8 flex justify-center space-x-4">
            <button 
                @click="handleNavigate('cart')" 
                class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-100 transition duration-150"
            >
                Quay lại giỏ hàng
            </button>
            <button 
                @click="handleNavigate('checkout-payment')" 
                class="px-6 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 transition duration-150"
            >
                Thử thanh toán lại
            </button>
        </div>

      </div>
    </div>
  </AppLayout>
</template>