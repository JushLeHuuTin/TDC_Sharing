// src/stores/dashboardStore.js
import { defineStore } from 'pinia';
import axios from 'axios';
import { useAuthStore } from '@/stores/auth'; // Cần cái này để lấy Token

export const useDashboardStore = defineStore('dashboard', {
    state: () => ({
        stats: {
            total_users: 0,
            total_products: 0,
            total_orders: 0
        },
        isLoading: false,
        error: null,
    }),
    
    actions: {
        async fetchDashboardStats() {
            // Không cần cache vì số liệu admin thay đổi liên tục, nên cứ gọi mới
            
            this.isLoading = true;
            this.error = null;
            
            try {
                // 1. Lấy token từ store Auth (vì đây là trang Admin bảo mật)
                const authStore = useAuthStore();
                const token = authStore.token;

                // 2. Cấu hình URL và Header (Token)
                const url = 'http://127.0.0.1:8000/api/admin/dashboard/stats';
                const config = {
                    headers: {
                        'Authorization': `Bearer ${token}`
                    }
                };

                // 3. Gọi API trực tiếp bằng axios
                const response = await axios.get(url, config);
                
                // 4. Cập nhật State
                // Kiểm tra kỹ cấu trúc trả về (response.data.data hay response.data)
                if (response.data.success) {
                    this.stats = response.data.data;
                }
                
            } catch (error) {
                this.error = 'Lỗi tải thống kê Dashboard.';
                console.error('Lỗi khi tải thống kê:', error);
            } finally {
                this.isLoading = false;
            }
        },
    },
    
    getters: {
        // Getter nếu cần (hiện tại chưa cần xử lý gì thêm)
        getStats: (state) => state.stats,
    }
});