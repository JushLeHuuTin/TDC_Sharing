<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import AppLayout from '@/Layouts/AppLayout.vue'; 
// Giả định bạn đã import Pinia Store và Axios
import { useAuthStore } from '@/stores/auth'; 
// import axios from 'axios'; 

// --- SETUP VÀ STATE ---
const router = useRouter();
const authStore = useAuthStore();

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

    if (!form.value.email || !form.value.password) {
        form.value.error = 'Vui lòng nhập Email và Mật khẩu.';
        form.value.loading = false;
        return;
    }

    try {
        // --- 🎯 LOGIC GỌI API THỰC TẾ (Sử dụng Axios) ---
        
        // ⚠️ Bỏ comment khối này khi tích hợp Backend thực tế:
        /*
        const response = await axios.post('/api/login', {
            email: form.value.email,
            password: form.value.password
        });
        
        const { token, user } = response.data;
        authStore.setAuth(token, user); // Lưu Token và User vào Pinia/LocalStorage
        
        // Chuyển hướng người dùng đã xác thực
        router.push({ name: user.role === 'admin' ? 'admin.dashboard' : 'user.profile' });
        */
        
        // --- MOCK LOGIC (Giả lập thành công cho mục đích demo) ---
        await new Promise(resolve => setTimeout(resolve, 1000)); // Delay giả lập mạng
        
        if (form.value.email === 'admin@market.com' && form.value.password === '123456') {
            const mockToken = 'mock-jwt-admin-token-12345';
            const mockUser = { id: 1, name: 'Admin User', role: 'admin' };
            authStore.setAuth(mockToken, mockUser);
            router.push({ name: 'admin.dashboard' }); 
        } else   if (form.value.email === 'user1@market.com' && form.value.password === '123456') {
            const mockToken = 'mock-jwt-admin-token-12345';
            const mockUser = { id: 1, name: 'User1', role: 'customer' };
            authStore.setAuth(mockToken, mockUser);
            router.push({ name: 'home' }); 
        }else {
             form.value.error = 'Thông tin đăng nhập không hợp lệ hoặc tài khoản không tồn tại.';
        }
        // --- KẾT THÚC MOCK LOGIC ---

    } catch (e) {
        form.value.error = e.response?.data?.message || 'Đã xảy ra lỗi hệ thống. Vui lòng thử lại.';
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