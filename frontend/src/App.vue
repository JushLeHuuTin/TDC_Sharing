<script setup>
import { RouterView } from 'vue-router';
import { onBeforeMount } from 'vue'; 
import { useAuthStore } from '@/stores/auth';
import { useRouter } from 'vue-router'; 

const authStore = useAuthStore();
const router = useRouter();

onBeforeMount(() => {
    console.log('App is mounting. Initializing Auth Store...');
    
    authStore.initializeStore(); 
    
    // [Tùy chọn] Kiểm tra và điều hướng nếu cần
    // Nếu người dùng đang ở trang Login/Register nhưng đã có token, chuyển họ về trang chính
    if (authStore.isLoggedIn && 
        (router.currentRoute.value.name === 'login' || router.currentRoute.value.name === 'register')) {
        
        router.push({ name: 'home' });
    }
});
</script>

<template>
    <RouterView />
</template>

<style>
body {
    background-color: #f9fafb; 
    box-sizing: border-box;
}

.product-card {
    transition: all 0.3s ease;
}
.product-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 24px rgba(0,0,0,0.15);
}

</style>