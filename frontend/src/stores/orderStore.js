import { defineStore } from 'pinia';
import axios from 'axios';
import { useVoucherStore } from './voucherStore'; 

export const useOrderStore = defineStore('order', {
    state: () => ({
        isProcessing: false,
    }),

    actions: {
        async handleCheckoutPayment (payload, paymentMethod, router) {
            this.isProcessing = true;
            const voucherStore = useVoucherStore();

            try {
                const response = await axios.post("http://127.0.0.1:8000/api/orders", payload);
                const data = response.data;
                if (data.success) {
                    // 1. COD (Thành công ngay lập tức)
                    if (paymentMethod === 'cod') {
                        return { success: true, message: data.message };
                    } 
                    
                    else if (paymentMethod === 'momo' || paymentMethod === 'bank') {
                        const payUrl = data.redirect_url;
                        if (payUrl) {
                            window.location.href = payUrl; // Chuyển hướng
                            return { success: true, redirect: true };
                        }
                    }
                }
                
                throw new Error(data.message || 'Lỗi đặt hàng không xác định.');

            } catch (err) {
                console.error("Order API Error:", err.response?.data || err.message);
                return { 
                    success: false, 
                    message: err.response?.data?.message || err.message || 'Có lỗi xảy ra khi tạo đơn hàng.'
                };
            } finally {
                this.isProcessing = false;
            }
        },
    },
});