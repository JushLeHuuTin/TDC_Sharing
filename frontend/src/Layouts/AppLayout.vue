<script setup>
import { onMounted, ref, watch } from 'vue';

// --- CẬP NHẬT IMPORT: Sử dụng AppHeader và AppFooter từ thư mục hiện tại ---
import AppHeader from '../components/User/Layouts/AppHeader.vue'; 
import AppFooter from '../components/User/Layouts/AppFooter.vue'; 

// --- XỬ LÝ MESSAGE/ALERT (THAY THẾ SESSION FLASH) ---
const props = defineProps({
    flash: {
        type: Object,
        default: () => ({ success: null, error: null })
    },
    // Nếu sử dụng Inertia.js, bạn có thể truyền thêm user props vào layout
    user: {
        type: Object,
        default: null,
    }
});
const successMessage = ref(props.flash.success);
const errorMessage = ref(props.flash.error);

watch(() => props.flash, (newFlash) => {
    successMessage.value = newFlash.success;
    errorMessage.value = newFlash.error;

    if (newFlash.success || newFlash.error) {
        setTimeout(() => {
            successMessage.value = null;
            errorMessage.value = null;
        }, 5000);
    }
}, { deep: true });

onMounted(() => {
    // Khởi tạo thư viện JS nếu cần

});
</script>

<template>
    <div class="bg-gray-50 min-h-screen">
        
        <!-- AppHeader Component (Thay thế @include('components.header')) -->
        <!-- Truyền user prop vào Header nếu cần hiển thị thông tin đăng nhập -->
        <AppHeader :user="user" />

        <!-- Main Content -->
        <main class=" mx-auto px-4 sm:px-6 lg:px-8 py-8">
            
            <!-- Alert Display -->
            <div v-if="successMessage || errorMessage" class="mb-6">
                <div 
                    v-if="successMessage" 
                    class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative shadow-md"
                    role="alert"
                >
                    <strong class="font-bold">Thành công!</strong>
                    <span class="block sm:inline">{{ successMessage }}</span>
                </div>
                <div 
                    v-else-if="errorMessage" 
                    class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative shadow-md"
                    role="alert"
                >
                    <strong class="font-bold">Lỗi!</strong>
                    <span class="block sm:inline">{{ errorMessage }}</span>
                </div>
            </div>

            <slot />
        </main>

        <!-- AppFooter Component (Thay thế @include('components.footer')) -->
        <AppFooter />
        
        <!-- Floating Action Button -->

    </div>
</template>

<style scoped>
/* Giữ lại các style tùy chỉnh cho Layout */
.product-card {
    transition: all 0.3s ease;
}
.product-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 24px rgba(0,0,0,0.15);
}
.skeleton {
    background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
    background-size: 200% 100%;
    animation: loading 1.5s infinite;
}
@keyframes loading {
    0% { background-position: 200% 0; }
    100% { background-position: -200% 0; }
}
.price-badge {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}
.condition-badge {
    backdrop-filter: blur(10px);
    background: rgba(255, 255, 255, 0.9);
}
</style>