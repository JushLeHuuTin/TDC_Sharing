import { defineStore } from 'pinia';
import axios from 'axios';
import { useAuthStore } from '@/stores/auth';

const API_URL = 'http://127.0.0.1:8000/api/seller/orders';

export const useSellerOrderStore = defineStore('sellerOrder', {
    state: () => ({
        orders: [],
        isLoading: false,
        error: null,
    }),

    actions: {
        // --- Lấy danh sách đơn hàng ---
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

        // --- Duyệt đơn hàng ---
        async approveOrder(orderCode) {
            this.isLoading = true;
            this.error = null;
            const authStore = useAuthStore();
            const token = authStore.token;

            try {
                const config = { headers: { 'Authorization': `Bearer ${token}` } };
                // Lưu ý: API của bạn dùng ID (orderCode ở đây thực chất là ID nếu bạn truyền ID vào)
                // Nếu API nhận ID database (ví dụ: 1), hãy đảm bảo truyền số 1 vào đây
                const url = `${API_URL}/${orderCode}/approve`;
                
                const response = await axios.put(url, {}, config);

                if (response.data && response.data.success) {
                    // Cập nhật state cục bộ
                    const index = this.orders.findIndex(o => o.order_code == orderCode || o.id == orderCode);
                    if (index !== -1) {
                        // Cập nhật trạng thái mới (shipped)
                        this.orders[index].status = 'shipped'; 
                        // Hoặc reload lại toàn bộ list nếu cần: await this.fetchOrders();
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
        },

        // --- SỬA LỖI: Từ chối đơn hàng (Đã có API) ---
        async rejectOrder(orderId) { // Đổi tên tham số thành orderId cho rõ ràng (API cần ID)
            this.isLoading = true;
            this.error = null;
            const authStore = useAuthStore();
            const token = authStore.token;

            try {
                const config = { headers: { 'Authorization': `Bearer ${token}` } };
                // API Mới: /api/seller/orders/{id}/reject
                const url = `${API_URL}/${orderId}/reject`;
                
                const response = await axios.put(url, {}, config);

                if (response.data && response.data.success) {
                    // Cập nhật state cục bộ
                    // Tìm đơn hàng theo ID (hoặc order_code nếu API trả về order_code)
                    // Ở đây ta tìm lỏng lẻo (==) để bắt cả chuỗi lẫn số
                    const index = this.orders.findIndex(o => o.id == orderId || o.order_code == orderId);
                    
                    if (index !== -1) {
                        this.orders[index].status = 'cancelled'; // Cập nhật trạng thái thành 'cancelled'
                    }
                    return true;
                }
            } catch (err) {
                this.error = 'Lỗi khi từ chối đơn hàng. Vui lòng thử lại.';
                console.error('Lỗi rejectOrder:', err);
                return false;
            } finally {
                this.isLoading = false;
            }
        }
    }
});