<script setup>
import { RouterView } from 'vue-router';
import AdminSidebar from '@/components/Admin/Components/AdminSidebar.vue';
</script>

<template>
    <div class="admin-layout d-flex">
        
        <AdminSidebar />

        <main class="main-content flex-grow-1">
            <div class="container-fluid">
                
                <RouterView />
                
            </div>
        </main>
    </div>
</template>

<style scoped>
/* CSS để đẩy nội dung sang bên phải */
.main-content {
    margin-left: 280px; /* Phải bằng độ rộng của Sidebar */
    padding: 20px;
}

/* CSS cho responsive (khi màn hình nhỏ) */
@media (max-width: 768px) {
    .main-content {
        margin-left: 0;
    }
}
</style>