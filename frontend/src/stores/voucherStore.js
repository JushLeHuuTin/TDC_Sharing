import { defineStore } from 'pinia';

export const useVoucherStore = defineStore('voucher', {
    state: () => ({
        // Trạng thái lưu trữ mức giảm giá đã áp dụng
        appliedDiscount: 0,
        // Lưu trữ mã voucher đã áp dụng thành công
        appliedVoucherCode: null, 
        // Trạng thái tải (để ngăn người dùng nhấn nút nhiều lần)
        isLoading: false, 
        // Trạng thái lỗi (nếu có)
        voucherError: null,
    }),

    actions: {
        async validateAndApplyVoucher(voucherCode) {
            // 1. Kiểm tra đầu vào
            if (!voucherCode) {
                this.appliedDiscount = 0;
                this.appliedVoucherCode = null;
                this.voucherError = 'Vui lòng nhập mã giảm giá.';
                return;
            }

            this.isLoading = true;
            this.voucherError = null; // Reset lỗi cũ

            try {
                const response = await fetch('http://127.0.0.1:8000/api/checkout/validate-voucher', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ voucher_code: voucherCode }),
                });

                const data = await response.json();

                if (data.valid) {
                    // Thành công: Cập nhật trạng thái Store
                    this.appliedDiscount = data.discount_amount;
                    this.appliedVoucherCode = data.voucher_code;
                    // Tùy chọn: trả về true hoặc mức giảm
                    return true; 
                } else {
                    // Thất bại: Cập nhật lỗi và reset giảm giá
                    this.appliedDiscount = 0;
                    this.appliedVoucherCode = null;
                    this.voucherError = data.message || 'Mã giảm giá không hợp lệ.';
                    return false;
                }
            } catch (error) {
                // Lỗi kết nối
                this.voucherError = 'Lỗi kết nối kiểm tra voucher.';
                this.appliedDiscount = 0;
                return false;
            } finally {
                this.isLoading = false;
            }
        },
        
        // Action để xóa mã giảm giá
        removeVoucher() {
            this.appliedDiscount = 0;
            this.appliedVoucherCode = null;
            this.voucherError = null;
        }
    },
});