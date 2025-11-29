import { defineStore } from 'pinia';
import axios from 'axios';
import { useAuthStore } from './auth';

export const useSellerOrderStore = defineStore('sellerOrder', {
    state: () => ({
        orders: [],
        currentOrder: null, // Đơn hàng đang xem chi tiết
        isLoading: false,
        isDetailLoading: false,
        error: null,
        pagination: {
            currentPage: 1,
            lastPage: 1,
            total: 0
        }
    }),

    actions: {
        async fetchOrders(params = {}) {
            this.isLoading = true;
            this.error = null;
            const authStore = useAuthStore();

            try {
                const response = await axios.get('http://127.0.0.1:8000/api/seller/orders', {
                    headers: { Authorization: `Bearer ${authStore.token}` },
                    params: params // Gửi kèm filter (page, status, search...)
                });

                if (response.data.success) {
                    this.orders = response.data.data;
                    this.pagination = response.data.meta;
                }
            } catch (err) {
                this.error = err.response?.data?.message || 'Lỗi tải danh sách đơn hàng.';
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
                // Gọi API xem chi tiết
                const response = await axios.get(`http://127.0.0.1:8000/api/seller/orders/${orderId}`, {
                    headers: { Authorization: `Bearer ${authStore.token}` }
                });

                if (response.data.success) {
                    this.currentOrder = response.data.data;
                }
            } catch (err) {
                console.error("Lỗi xem chi tiết đơn hàng:", err);
                // Không cần set error global để tránh hiện thông báo đỏ ở danh sách
            } finally {
                this.isDetailLoading = false;
            }
        },

        async approveOrder(orderId) {
            const authStore = useAuthStore();
            try {
                const response = await axios.put(`http://127.0.0.1:8000/api/seller/orders/${orderId}/approve`, {}, {
                    headers: { Authorization: `Bearer ${authStore.token}` }
                });
                
                // Cập nhật lại trạng thái trong danh sách local (Optimistic UI)
                const index = this.orders.findIndex(o => o.id === orderId);
                if (index !== -1) {
                    this.orders[index].status = 'shipped';
                }
                
                return { success: true };
            } catch (err) {
                return { success: false, message: err.response?.data?.message || 'Lỗi duyệt đơn.' };
            }
        },

        async rejectOrder(orderId) {
            const authStore = useAuthStore();
            try {
                const response = await axios.put(`http://127.0.0.1:8000/api/seller/orders/${orderId}/reject`, {}, {
                    headers: { Authorization: `Bearer ${authStore.token}` }
                });
                
                const index = this.orders.findIndex(o => o.id === orderId);
                if (index !== -1) {
                    this.orders[index].status = 'cancelled';
                }
                
                return { success: true };
            } catch (err) {
                return { success: false, message: err.response?.data?.message || 'Lỗi từ chối đơn.' };
            }
        }
    }
});