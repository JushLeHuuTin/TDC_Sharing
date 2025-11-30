import { defineStore } from 'pinia';
import axios from 'axios';
import { useAuthStore } from '@/stores/auth';

const API_URL = 'http://127.0.0.1:8000/api/admin/orders';

export const useAdminOrderStore = defineStore('adminOrder', {
    state: () => ({
        orders: [],      // Danh sách tất cả đơn hàng
        isLoading: false,
        error: null,
        paginationData: null 
    }),

    actions: {
        /**
         * Lấy danh sách TẤT CẢ đơn hàng (Admin - Tính năng 10)
         * @param {object} filters - { status, from_date, to_date, page }
         */
        async fetchOrders(filters = {}) {
            this.isLoading = true;
            this.error = null;
            const authStore = useAuthStore();
            const token = authStore.token;

            if (!token || !authStore.isAdmin) { // Kiểm tra admin
                this.error = 'Bạn không có quyền truy cập.';
                this.isLoading = false;
                return;
            }

            try {
                const config = {
                    headers: { 'Authorization': `Bearer ${token}` },
                    params: filters 
                };

                const response = await axios.get(API_URL, config);

                // Giả định API trả về có .data và .meta (giống API seller)
                if (response.data && response.data.data) {
                    this.orders = response.data.data;
                    this.paginationData = response.data.meta || null;
                } else {
                    this.orders = [];
                    this.error = 'Không thể tải danh sách đơn hàng.';
                }

            } catch (err) {
                this.error = 'Lỗi máy chủ khi tải đơn hàng.';
                console.error('Lỗi fetchOrders (Admin):', err);
            } finally {
                this.isLoading = false;
            }
        },
    }
});