import { defineStore } from 'pinia';
import axios from 'axios';
import { useAuthStore } from '@/stores/auth';

const API_URL = 'http://127.0.0.1:8000/api/seller/orders';

export const useSellerOrderStore = defineStore('sellerOrder', {
    state: () => ({
        orders: [],
        isLoading: false,
        error: null,
        
        // --- STATE CHO MODAL CHI TIẾT ---
        currentOrder: null, 
        isDetailLoading: false,
        // --------------------------------
    }),

    actions: {
        // 1. Lấy danh sách
     async fetchOrders(filters = {}) {
            this.isLoading = true;
            this.error = null;
            const authStore = useAuthStore();
            const token = authStore.token;

            if (!token) {
                this.error = 'Bạn cần đăng nhập.';
                this.isLoading = false;
                return;
            }

            try {
                const config = { headers: { 'Authorization': `Bearer ${token}` }, params: filters };
                const response = await axios.get(API_URL, config);

                if (response.data && response.data.success) {
                    this.orders = response.data.data;
                } else {
                    this.orders = [];
                    // Xử lý trường hợp backend trả về success=false nhưng không phải 422
                }
            } catch (err) {
                this.error = 'Lỗi tải danh sách đơn hàng.';
                console.error(err);

                // --- BẮT LỖI VALIDATION (STATUS CODE 422) ---
                if (err.response && err.response.status === 422 && err.response.data.errors) {
                    const validationErrors = err.response.data.errors;
                    let errorMessages = [];

                    // Lặp qua từng trường lỗi (from_date, to_date,...)
                    for (const field in validationErrors) {
                        if (validationErrors.hasOwnProperty(field)) {
                            // Thêm tất cả thông báo lỗi của trường đó vào mảng
                            errorMessages = errorMessages.concat(validationErrors[field]);
                        }
                    }

                    // Nối các thông báo lỗi lại thành một chuỗi duy nhất để hiển thị
                    if (errorMessages.length > 0) {
                        this.error = 'Dữ liệu lọc không hợp lệ:\n' + errorMessages.join('\n');
                    } else {
                        // Trường hợp 422 nhưng không có errors (rất hiếm)
                        this.error = err.response.data.message || 'Lỗi validation không xác định.';
                    }
                } 
                // --- KẾT THÚC BẮT LỖI VALIDATION ---

            } finally {
                this.isLoading = false;
            }
        },

        // 2. Duyệt đơn
        async approveOrder(orderId) {
            const authStore = useAuthStore();
            const token = authStore.token;
            try {
                const config = { headers: { 'Authorization': `Bearer ${token}` } };
                await axios.put(`${API_URL}/${orderId}/approve`, {}, config);
                this.fetchOrders(); 
                return true;
            } catch (err) { return false; }
        },

        // 3. Từ chối đơn
        async rejectOrder(orderId) {
            const authStore = useAuthStore();
            const token = authStore.token;
            try {
                const config = { headers: { 'Authorization': `Bearer ${token}` } };
                await axios.put(`${API_URL}/${orderId}/reject`, {}, config);
                
                const index = this.orders.findIndex(o => o.id == orderId || o.order_code == orderId);
                if (index !== -1) this.orders[index].status = 'cancelled'; 
                return true;
            } catch (err) { return false; }
        },
        
        // 4. HÀM LẤY CHI TIẾT (QUAN TRỌNG)
        async fetchOrderDetail(orderId) {
            this.isDetailLoading = true;
            this.currentOrder = null;
            const authStore = useAuthStore();
            const token = authStore.token;

            try {
                const config = { headers: { 'Authorization': `Bearer ${token}` } };
                // API: GET /api/seller/orders/{id}
                const response = await axios.get(`${API_URL}/${orderId}`, config);

                if (response.data && response.data.success) {
                    this.currentOrder = response.data.data;
                }
            } catch (err) {
                console.error('Lỗi lấy chi tiết:', err);
            } finally {
                this.isDetailLoading = false;
            }
        }
    }
});