import { defineStore } from 'pinia';
import axios from 'axios';
import { useAuthStore } from '@/stores/auth';

// SỬA LỖI 1: Sửa lại IP thành 127.0.0.1
const API_URL = 'http://127.0.0.1:8000/api/seller/orders';

export const useSellerOrderStore = defineStore('sellerOrder', {
    state: () => ({
        orders: [],
        isLoading: false,
        error: null,
        
        // --- Cho Modal Chi tiết ---
        currentOrder: null, 
        isDetailLoading: false,
        detailError: null,
    }),

    actions: {
        // --- Action: Lấy danh sách đơn hàng ---
        async fetchOrders(filters = {}) {
            this.isLoading = true;
            this.error = null;
            const authStore = useAuthStore();
            const token = authStore.token;

            if (!token) {
                this.error = 'Bạn cần đăng nhập để xem đơn hàng.';
                this.isLoading = false;
                return;
            }

            try {
                const config = {
                    headers: { 'Authorization': `Bearer ${token}` },
                    params: filters
                };
                const response = await axios.get(API_URL, config);
                if (response.data && response.data.success) {
                    this.orders = response.data.data;
                } else {
                    this.orders = [];
                    this.error = 'Không thể tải danh sách đơn hàng.';
                }
            } catch (err) {
                this.error = 'Lỗi máy chủ khi tải đơn hàng.';
                console.error('Lỗi fetchOrders:', err);
            } finally {
                this.isLoading = false;
            }
        },

        // --- Action: Duyệt đơn hàng ---
        async approveOrder(orderCode) {
            this.isLoading = true; 
            this.error = null;
            const authStore = useAuthStore();
            const token = authStore.token;

            try {
                const config = { headers: { 'Authorization': `Bearer ${token}` } };
                const url = `${API_URL}/${orderCode}/approve`;
                const response = await axios.put(url, {}, config); 

                if (response.data && response.data.success) {
                    const index = this.orders.findIndex(o => o.order_code == orderCode);
                    if (index !== -1) {
                        this.orders[index] = response.data.data; 
                    }
                    return true;
                }
            } catch (err) {
                this.error = 'Lỗi khi duyệt đơn hàng.';
                console.error('Lỗi approveOrder:', err);
                return false;
            } finally {
                this.isLoading = false;
            }
      _ },

        // --- Action: Từ chối đơn hàng (Giả định API) ---
        async rejectOrder(orderCode) {
            // (Code này giữ nguyên)
        },
        
        // --- (TÍNH NĂNG 11) LẤY CHI TIẾT ĐƠN HÀNG ---
        async fetchOrderDetail(orderCode) {
            this.isDetailLoading = true;
            this.detailError = null;
            this.currentOrder = null;
            
            const authStore = useAuthStore();
            const token = authStore.token;

            try {
                const config = { headers: { 'Authorization': `Bearer ${token}` } };
                const url = `${API_URL}/${orderCode}`; 
                const response = await axios.get(url, config);

                if (response.data && response.data.success) {
                    this.currentOrder = response.data.data;
                } else {
                    this.detailError = 'Không tìm thấy chi tiết đơn hàng.';
                }
            } catch (err) {
                this.detailError = 'Lỗi máy chủ khi tải chi tiết đơn hàng.';
                console.error('Lỗi fetchOrderDetail:', err);
            } finally {
                this.isDetailLoading = false;
            }
        }
    }
});