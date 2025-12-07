<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import AppLayout from '@/Layouts/AppLayout.vue'; 
// Giả định bạn đã import Pinia Store và Axios
import { useAuthStore } from '@/stores/auth'; 
import axios from 'axios'; 

// --- SETUP VÀ STATE ---
const router = useRouter();
const authStore = useAuthStore();
axios.defaults.baseURL = 'http://127.0.0.1:8000';
const form = ref({
    email: '',
    password: '',
    remember: false,
    loading: false,
    error: null,
});

// --- ACTION HANDLERS ---

/**
 * Xử lý việc gửi thông tin đăng nhập lên server.
 */

 async function handleLogin() {
    form.value.error = null;
    form.value.loading = true;

    try {
        const response = await axios.post('/api/login', {
            email: form.value.email,
            password: form.value.password
        });

        const { token, user } = response.data;

        // Cập nhật store
        authStore.setAuth(token, user);

        // Không cần set axios.defaults nữa
        // axios sẽ tự lấy token nhờ interceptor
        user.role == 'admin' ? router.push({ name: 'admin' }) : router.push({ name: 'home.index' });

    } catch (error) {
        form.value.error =
            error.response?.data?.message || 'Đăng nhập thất bại.';
    } finally {
        form.value.loading = false;
    }
}


</script>

<template>
    <AppLayout title="StudentMarket - Đăng nhập">

        <section class="d-flex justify-content-center align-items-center py-5 login-page-bg">
            <div class="card shadow-lg login-card">
                <div class="card-body p-5">
                    
                    <div class="text-center mb-4">
                        <fa :icon="['fas', 'lock']" class="fa-3x text-primary mb-3" />
                        <h4 class="fw-bold text-dark">Đăng nhập tài khoản</h4>
                        <p class="text-muted">Dành cho sinh viên đã đăng ký</p>
                    </div>

                    <form @submit.prevent="handleLogin">
                        
                        <div v-if="form.error" class="alert alert-danger p-2 small" role="alert">
                            {{ form.error }}
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label fw-bold">Email (tài khoản sinh viên)</label>
                            <div class="input-group">
                                <span class="input-group-text"><fa :icon="['fas', 'envelope']" /></span>
                                <input 
                                    type="email" 
                                    id="email" 
                                    class="form-control" 
                                    placeholder="your.email@university.edu.vn"
                                    v-model="form.email" 
                                    required 
                                    autofocus
                                >
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label fw-bold">Mật khẩu</label>
                            <div class="input-group">
                                <span class="input-group-text"><fa :icon="['fas', 'key']" /></span>
                                <input 
                                    type="password" 
                                    id="password" 
                                    class="form-control" 
                                    placeholder="Nhập mật khẩu"
                                    v-model="form.password" 
                                    required
                                >
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="remember" v-model="form.remember">
                                <label class="form-check-label" for="remember">
                                    Ghi nhớ tôi
                                </label>
                            </div>
                            <RouterLink to="/forgot-password" class="text-primary small">Quên mật khẩu?</RouterLink>
                        </div>

                        <button type="submit" class="btn btn-primary btn-lg w-100" :disabled="form.loading">
                            <span v-if="form.loading" class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                            {{ form.loading ? 'Đang xác thực...' : 'Đăng nhập' }}
                        </button>
                    </form>
                    
                    <div class="mt-4 text-center">
                        <p class="text-muted">Chưa có tài khoản? 
                            <RouterLink to="/register" class="text-success fw-bold">Đăng ký ngay</RouterLink>
                        </p>
                    </div>

                </div>
            </div>
        </section>

    </AppLayout>
</template>

<style scoped>
/* CSS cho trang Login */

.login-page-bg {
    min-height: 100vh;
    background: linear-gradient(135deg, #f0f4f8 0%, #e0e9f0 100%);
}

.login-card {
    max-width: 450px;
    width: 90%;
    border-radius: 15px;
    border: none;
}

.form-control:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 0.25rem rgba(102, 126, 234, 0.25);
}

.input-group-text {
    background-color: #f8f9fa;
    border-right: none;
    color: #6c757d;
}

.btn-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
    transition: all 0.3s ease;
}

.btn-primary:hover {
    box-shadow: 0 4px 10px rgba(102, 126, 234, 0.3);
}
</style>