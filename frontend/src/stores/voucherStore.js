import { defineStore } from 'pinia';
import axios from 'axios';
import { useAuthStore } from './auth';


export const useVoucherStore = defineStore('voucher', {
    state: () => ({
        appliedDiscount: 0,
        appliedVoucherCode: null,
        isLoading: false,
        voucherError: null,

        // --- Voucher list ---
        vouchers: [],
        stats: {},
        pagination: {
            current_page: 1,
            last_page: 1,
            total: 0,
            per_page: 0,
        },
        loadingVouchers: false,
        fetchError: null,
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
        },
        async fetchVouchers(page = 1) {
            this.loadingVouchers = true;
            this.fetchError = null;

            try {
                const res = await axios.get(`http://127.0.0.1:8000/api/vouchers?page=${page}`);

                // Lấy danh sách voucher
                this.vouchers = res.data.data.data;
                // Lấy stats
                this.stats = res.data.stats;

                // Lấy thông tin phân trang
                this.pagination = {
                    current_page: res.data.data.current_page,
                    last_page: res.data.data.last_page,
                    total: res.data.data.total,
                    per_page: res.data.data.per_page,
                };

            } catch (err) {
                this.fetchError = err.response?.data?.message || "Không tải được danh sách voucher.";
            } finally {
                this.loadingVouchers = false;
            }
        },
        async createVoucher(voucherData) {
            this.loadingVouchers = true; // Sử dụng trạng thái loading của danh sách
            this.fetchError = null; // Reset lỗi

            try {
                const res = await axios.post('http://127.0.0.1:8000/api/vouchers', voucherData);
                return { success: true, message: res.data.message, voucher: res.data.voucher };

            } catch (err) {
                // Xử lý lỗi từ server (ví dụ: lỗi validation 422)
                const errorMessage = err.response?.data?.message || "Lỗi không xác định khi tạo voucher.";
                const validationErrors = err.response?.data?.errors;

                this.fetchError = errorMessage;

                return { success: false, message: errorMessage, errors: validationErrors };

            } finally {
                this.loadingVouchers = false;
            }
        },
        formatPrice(value) {
            if (value === null || value === undefined) return '0₫';

            const number = parseFloat(value); // chuyển decimal về number

            if (isNaN(number)) return '0₫';

            return new Intl.NumberFormat('vi-VN', {
                style: 'currency',
                currency: 'VND',
                minimumFractionDigits: 0,
                maximumFractionDigits: 0
            }).format(number);
        },
        async updateVoucher(id, voucherData) {
            const authStore = useAuthStore();
            const token = authStore.token;
            this.loadingVouchers = true;
            this.fetchError = null;

            try {
                console.log("=== Debug: updateVoucher start ===");
                console.log("Voucher ID:", id);
                console.log("Original voucherData:", voucherData);

                // Chuyển object JSON thành FormData
                const formData = new FormData();
                for (const key in voucherData) {
                    if (voucherData[key] !== null && voucherData[key] !== undefined) {
                        formData.append(key, voucherData[key]);
                    }
                }
                formData.append('_method', 'PUT');

                console.log("FormData to send:");
                for (let pair of formData.entries()) {
                    console.log(pair[0], ":", pair[1]);
                }

                const res = await axios.post(
                    `http://127.0.0.1:8000/api/vouchers/${id}`,
                    formData,
                    {
                        headers: {
                            Authorization: `Bearer ${token}`, // hoặc lấy từ store nếu bạn quản lý token ở đó
                            'Content-Type': 'multipart/form-data',
                        },
                    }
                );

                console.log("=== Debug: Response from server ===");
                console.log(res.data);

                return { success: true, message: res.data.message, voucher: res.data.data };
            } catch (err) {
                console.log("=== Debug: Caught error ===");
                console.log(err);
                const errorMessage =
                    err?.response?.data?.message || `Lỗi không xác định khi cập nhật voucher #${id}.`;
                const validationErrors = err?.response?.data?.errors || null;

                this.fetchError = errorMessage;

                console.log("Error message:", errorMessage);
                console.log("Validation errors:", validationErrors);

                return { success: false, message: errorMessage, errors: validationErrors };
            } finally {
                this.loadingVouchers = false;
                console.log("=== Debug: updateVoucher end ===");
            }
        }
        ,

        async deleteVoucher(id) {
            this.loadingVouchers = true;
            this.fetchError = null;

            try {
                const res = await axios.delete(`http://127.0.0.1:8000/api/vouchers/${id}`);
                this.vouchers = this.vouchers.filter(v => v.id !== id);
                return { success: true, message: res.data.message };
            } catch (err) {
                const errorMessage = err.response?.data?.message || `Lỗi không xác định khi xóa voucher #${id}.`;

                this.fetchError = errorMessage;

                return { success: false, message: "Voucher không tồn tại !" };
            } finally {
                this.loadingVouchers = false;
            }
        },

    },
});