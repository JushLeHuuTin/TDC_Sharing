<script setup>
import { RouterView } from 'vue-router';
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