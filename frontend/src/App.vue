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
    if (authStore.isLoggedIn && 
        (router.currentRoute.value.name === 'login' || router.currentRoute.value.name === 'register')) {
        
        router.push({ name: 'home.index' });
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