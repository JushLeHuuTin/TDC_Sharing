// src/stores/sellerOrderStore.js

import { defineStore } from 'pinia';
import axios from 'axios';
import { useAuthStore } from '@/stores/auth';

const API_URL = 'http://127.0.0.1:8000/api/seller/orders';

export const useSellerOrderStore = defineStore('sellerOrder', {
    state: () => ({
        orders: [],      // Danh sách đơn hàng
        isLoading: false,
        error: null,
        // paginationData: null // (Nếu API có trả về)
    }),

    actions: {
        // --- Action chính: Lấy danh sách đơn hàng (kèm bộ lọc) ---
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
                // 1. Chuẩn bị config (headers và params)
                const config = {
                    headers: { 'Authorization': `Bearer ${token}` },
                    params: filters // filters = { status: 'pending', from_date: '...' }
                };

                // 2. Gọi API
                const response = await axios.get(API_URL, config);

                // 3. Cập nhật state
                if (response.data && response.data.success) {
                    this.orders = response.data.data;
                    // this.paginationData = response.data.meta; // (Nếu có)
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
        async approveOrder(orderId) {
            this.isLoading = true; // (Có thể dùng 1 loading khác cho từng dòng)
            this.error = null;
            const authStore = useAuthStore();
            const token = authStore.token;

            try {
                const config = { headers: { 'Authorization': `Bearer ${token}` } };
                const url = `${API_URL}/${orderId}/approve`; // API Tính năng 13
                
                const response = await axios.put(url, {}, config); // PUT không cần body

                if (response.data && response.data.success) {
                    // Cập nhật trạng thái của đơn hàng đó trong state
                    const index = this.orders.findIndex(o => o.id === orderId);
                    if (index !== -1) {
                        // Giả định API trả về đơn hàng đã cập nhật
                        this.orders[index] = response.data.data; 
                    }
                    return true; // Báo thành công
                }
            } catch (err) {
                this.error = 'Lỗi khi duyệt đơn hàng.';
                console.error('Lỗi approveOrder:', err);
                return false; // Báo thất bại
            } finally {
                this.isLoading = false;
            }
        },

        // --- Action: Từ chối đơn hàng (Giả định API) ---
        async rejectOrder(orderId) {
            this.isLoading = true;
            this.error = null;
            const authStore = useAuthStore();
            const token = authStore.token;

            try {
                const config = { headers: { 'Authorization': `Bearer ${token}` } };
                const url = `${API_URL}/${orderId}/reject`; // GIẢ ĐỊNH API
                
                const response = await axios.put(url, {}, config);

                if (response.data && response.data.success) {
                    // Cập nhật trạng thái
                    const index = this.orders.findIndex(o => o.id === orderId);
                    if (index !== -1) {
                        this.orders[index] = response.data.data;
                    }
                    return true;
                }
            } catch (err) {
                this.error = 'Lỗi khi từ chối đơn hàng.';
                console.error('Lỗi rejectOrder:', err);
                return false;
            } finally {
                this.isLoading = false;
            }
        }
    }
});