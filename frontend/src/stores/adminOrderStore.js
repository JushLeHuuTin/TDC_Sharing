import { defineStore } from 'pinia';
import axios from 'axios';
import { useAuthStore } from './auth';

export const useAdminOrderStore = defineStore('adminOrder', {
    state: () => ({
        orders: [],
        currentOrder: null,
        isLoading: false,
        isDetailLoading: false,
        error: null,
        pagination: { currentPage: 1, lastPage: 1, total: 0 }
    }),

    actions: {
        async fetchOrders(params = {}) {
            this.isLoading = true;
            const authStore = useAuthStore();
            try {
                // Gọi API Admin
                const response = await axios.get('http://127.0.0.1:8000/api/admin/orders', {
                    headers: { Authorization: `Bearer ${authStore.token}` },
                    params
                });
                if (response.data.success) {
                    this.orders = response.data.data;
                    if (response.data.meta) {
                         this.pagination = {
                            currentPage: response.data.meta.current_page,
                            lastPage: response.data.meta.last_page,
                            total: response.data.meta.total
                        };
                    }
                }
            } catch (err) {
                this.error = 'Không thể tải danh sách đơn hàng.';
                console.error(err);
            } finally {
                this.isLoading = false;
            }
        },

        async fetchOrderDetail(orderId) {
            this.isDetailLoading = true;
            this.currentOrder = null;
            const authStore = useAuthStore();
            try {
                // Gọi API chi tiết Admin
                const response = await axios.get(`http://127.0.0.1:8000/api/admin/orders/${orderId}`, {
                    headers: { Authorization: `Bearer ${authStore.token}` }
                });
                if (response.data.success) {
                    this.currentOrder = response.data.data;
                }
            } catch (err) {
                console.error(err);
            } finally {
                this.isDetailLoading = false;
            }
        },

        async deleteOrder(orderId) {
            const authStore = useAuthStore();
            try {
                await axios.delete(`http://127.0.0.1:8000/api/admin/orders/${orderId}`, {
                    headers: { Authorization: `Bearer ${authStore.token}` }
                });
                // Xóa khỏi danh sách local sau khi xóa thành công trên server
                this.orders = this.orders.filter(o => o.order_id !== orderId);
                return { success: true };
            } catch (err) {
                return { success: false, message: 'Lỗi xóa đơn hàng.' };
            }
        }
    }
});