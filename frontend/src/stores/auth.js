// stores/auth.js
import { defineStore } from 'pinia';
import router from '@/router'; // Giả định import router instance
import axios from 'axios';
export const useAuthStore = defineStore('auth', {
    state: () => ({
        token: null,
        user: null, 
        isLoggedIn: false,
        loadingProfile: false,
        userProfile:[]
    }),
    actions: {
        // 1. Hàm KHỞI TẠO (Lấy lại trạng thái từ LocalStorage khi F5)
        initializeStore() {
            const token = localStorage.getItem('auth_token');
            const userJson = localStorage.getItem('user_info');
        
            if (token && userJson) {
                this.token = token;
                this.user = JSON.parse(userJson);
                this.isLoggedIn = true;
        
                // 🔥 QUAN TRỌNG: Gắn token lại cho axios sau F5
                axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
            } else {
                this.token = null;
                this.user = null;
                this.isLoggedIn = false;
            }
        },

        // 2. Hàm ĐĂNG NHẬP THÀNH CÔNG (Cập nhật Store và LocalStorage)
        setAuth(token, userData) {
            this.token = token;
            this.user = userData;
            this.isLoggedIn = true;
            
            // LƯU vào LocalStorage để duy trì trạng thái
            localStorage.setItem('auth_token', token);
            localStorage.setItem('user_info', JSON.stringify(userData));
        },

        // 3. Hàm ĐĂNG XUẤT (Xóa khỏi Store và LocalStorage)
        async logout() {
            try {
                // Gọi API logout (không cần truyền token vì axios đã có sẵn bearer)
                await axios.post('/api/logout');
            } catch (error) {
                console.warn("Logout API error:", error);
            }
        
            // XÓA TRONG PINIA
            this.token = null;
            this.user = null;
            this.isLoggedIn = false;
        
            // XÓA LOCALSTORAGE
            localStorage.removeItem('auth_token');
            localStorage.removeItem('user_info');
        
        },
        async fetchUserProfile() {
            this.loadingProfile = true;
            try {
                // Endpoint lấy profile chi tiết (đã xác nhận)

                const response = await axios.get("http://127.0.0.1:8000/api/user/profile"); 
                this.userProfile = response.data.data; 
          
            } catch (error) {
                console.error("Lỗi fetching profile:", error);
                // Xử lý lỗi (ví dụ: hiển thị toast)
            } finally {
                this.loadingProfile = false;
            }
        },
    },
    getters: {
        // Ví dụ: kiểm tra quyền admin
        isAdmin: (state) => state.user?.role === 'admin'
    }
});