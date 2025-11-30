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
        logout() {
            this.token = null;
            this.user = null;
            this.isLoggedIn = false;

            localStorage.removeItem('auth_token');
            localStorage.removeItem('user_info');
            
            // Chuyển hướng về trang đăng nhập
            router.push({ name: 'login' });
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