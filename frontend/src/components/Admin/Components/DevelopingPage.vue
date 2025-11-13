<script setup>
import { onMounted } from 'vue';

// Props nhận từ AdminLayout (showToast là hữu ích để thông báo)
const props = defineProps({
    showToast: Function,
    // RouterView tự động truyền các props khác (ví dụ: categories) nhưng không cần dùng ở đây
});
// props.showToast(`trang "${category.name}" dang phat trien...`, 'info'); 
</script>

<template>
    <div class="container-fluid py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8 col-md-10">
                
                <div class="card shadow-lg border-0 rounded-4 develop-card">
                    <div class="card-body p-5 text-center">
                        
                        <div class="mb-4">
                            <fa :icon="['fas', 'cogs']" class="fa-6x text-gradient" /> 
                        </div>

                        <h1 class="card-title fw-bold text-dark mb-3">
                            Tính năng đang được phát triển
                        </h1>
                        
                        <p class="text-muted lead mb-4">
                            Trang này hiện đang được đội ngũ kỹ thuật của chúng tôi xây dựng và hoàn thiện.
                            Xin vui lòng quay lại sau!
                        </p>
                        
                        <div class="text-start mx-auto" style="max-width: 400px;">
                            <ul class="list-unstyled text-muted develop-list">
                                <li class="mb-2"><fa :icon="['fas', 'check-circle']" class="text-success me-2" /> Thiết kế giao diện (UI) đã hoàn tất</li>
                                <li class="mb-2"><fa :icon="['fas', 'spinner']" class="fa-spin text-warning me-2" /> Tích hợp API Backend đang tiến hành</li>
                                <li class="mb-2"><fa :icon="['fas', 'code']" class="text-info me-2" /> Viết logic xử lý nghiệp vụ</li>
                            </ul>
                        </div>
                        
                        <hr class="my-4">
                        
                        <button class="btn btn-lg btn-primary shadow-sm" @click="props.showToast('Chuyển về trang chủ Admin...', 'info')">
                            <fa :icon="['fas', 'tachometer-alt']" class="me-2" />Quay lại Dashboard
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
/* Thêm các icon cogs, spinner, check-circle, code vào thư viện FA trong main.js */

.develop-card {
    background-color: #ffffff;
    border: 1px solid #e0e0e0;
}

.text-gradient {
    /* Tạo gradient màu dựa trên sidebar của bạn */
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}

.develop-list li {
    font-size: 1.05rem;
    color: #495057;
}
</style>