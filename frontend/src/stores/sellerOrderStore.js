import { defineStore } from 'pinia';
import axios from 'axios';
import { useAuthStore } from './auth';

export const useSellerOrderStore = defineStore('sellerOrder', {
    state: () => ({
        orders: [],
        currentOrder: null,
        isLoading: false,
        isDetailLoading: false,
        error: null,
        pagination: { 
            currentPage: 1, 
            lastPage: 1, 
            total: 0,
            perPage: 10
        }
    }),

    actions: {
        async fetchOrders(params = {}) {
            this.isLoading = true;
            this.error = null;
            const authStore = useAuthStore();

            try {
                // Đảm bảo truyền tham số filters (bao gồm cả page) vào API
                const response = await axios.get('http://127.0.0.1:8000/api/seller/orders', {
                    headers: { Authorization: `Bearer ${authStore.token}` },
                    params: params 
                });

                if (response.data.success) {
                    this.orders = response.data.data;
                    if (response.data.meta) {
                        this.pagination = {
                            currentPage: response.data.meta.current_page,
                            lastPage: response.data.meta.last_page,
                            total: response.data.meta.total,
                            perPage: response.data.meta.per_page
                        };
                    }
                }
            } catch (err) {
                this.error = err.response?.data?.message || 'Lỗi tải danh sách đơn hàng.';
            } finally {
                this.isLoading = false;
            }
        },

        // ... (Các hàm approveOrder, shipOrder, rejectOrder, fetchOrderDetail giữ nguyên) ...
        async fetchOrderDetail(orderId) {
            this.isDetailLoading = true;
            this.currentOrder = null;
            const authStore = useAuthStore();
            try {
                const response = await axios.get(`http://127.0.0.1:8000/api/seller/orders/${orderId}`, {
                    headers: { Authorization: `Bearer ${authStore.token}` }
                });

                if (response.data.success) {
                    this.currentOrder = response.data.data;
                }
            } catch (err) {
                console.error("Lỗi xem chi tiết:", err);
            } finally {
                this.isDetailLoading = false;
            }
        },

        async approveOrder(orderId) {
            const authStore = useAuthStore();
            try {
                await axios.put(`http://127.0.0.1:8000/api/seller/orders/${orderId}/approve`, {}, {
                    headers: { Authorization: `Bearer ${authStore.token}` }
                });
                const index = this.orders.findIndex(o => o.id === orderId);
                if (index !== -1) this.orders[index].status = 'processing';
                if (this.currentOrder && this.currentOrder.id === orderId) this.currentOrder.status = 'processing';
                return { success: true };
            } catch (err) {
                return { success: false, message: err.response?.data?.message || 'Lỗi duyệt đơn.' };
            }
        },
        
        async shipOrder(orderId) {
            const authStore = useAuthStore();
            try {
                await axios.put(`http://127.0.0.1:8000/api/seller/orders/${orderId}/ship`, {}, {
                    headers: { Authorization: `Bearer ${authStore.token}` }
                });
                const index = this.orders.findIndex(o => o.id === orderId);
                if (index !== -1) this.orders[index].status = 'shipped';
                if (this.currentOrder && this.currentOrder.id === orderId) this.currentOrder.status = 'shipped';
                return { success: true };
            } catch (err) {
                return { success: false, message: err.response?.data?.message || 'Lỗi giao hàng.' };
            }
        },

        async rejectOrder(orderId) {
            const authStore = useAuthStore();
            try {
                await axios.put(`http://127.0.0.1:8000/api/seller/orders/${orderId}/reject`, {}, {
                    headers: { Authorization: `Bearer ${authStore.token}` }
                });
                const index = this.orders.findIndex(o => o.id === orderId);
                if (index !== -1) this.orders[index].status = 'cancelled';
                if (this.currentOrder && this.currentOrder.id === orderId) this.currentOrder.status = 'cancelled';
                return { success: true };
            } catch (err) {
                return { success: false, message: err.response?.data?.message || 'Lỗi từ chối đơn.' };
            }
        }
    }
});