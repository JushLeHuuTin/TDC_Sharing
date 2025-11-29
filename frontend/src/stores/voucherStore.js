import { defineStore } from 'pinia';
import axios from 'axios';
import { useAuthStore } from './auth';
import { faCommentsDollar } from '@fortawesome/free-solid-svg-icons';


export const useVoucherStore = defineStore('voucher', {
    state: () => ({
        appliedDiscount: 0,
        appliedVoucherCode: null,
        isLoading: false,
        voucherError: null,

        // --- Voucher list ---
        vouchers: [],
        stats: {},
        voucherPagination: {
            currentPage: 1,
            perPage: 8,
            totalItems: 0,
            totalPages: 1,
            links: [] // Links chi tiết (First, Last, Next, Previous)
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
                const res = await fetch('http://127.0.0.1:8000/api/checkout/validate-voucher', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ voucher_code: voucherCode }),
                });

                const data = await res.json();
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
                this.voucherPagination.currentPage = res.data.data.current_page;
                this.voucherPagination.totalPages = res.data.data.last_page;
                this.voucherPagination.totalItems = res.data.data.total;
                this.voucherPagination.perPage = res.data.data.per_page;
                this.voucherPagination.links = res.data.data.links;

            } catch (err) {
                this.fetchError = err.res?.data?.message || "Không tải được danh sách voucher.";
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
                const errorMessage = err.res?.data?.message || "Lỗi không xác định khi tạo voucher.";
                const validationErrors = err.res?.data?.errors;

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
                const formData = new FormData();
                for (const key in voucherData) {
                    if (voucherData[key] !== null && voucherData[key] !== undefined) {
                        formData.append(key, voucherData[key]);
                    }
                }
            formData.append('_method', 'PUT');
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
                return { success: true, message: res.data.message, voucher: res.data.data };
            } catch (err) {
                console.log(err.response);
                const errorMessage =
                    err?.response?.data?.message || `Lỗi không xác định khi cập nhật voucher #${id}.`;
                const validationErrors = err?.response?.data?.errors || null;
                this.fetchError = errorMessage;
                return { success: false, message: errorMessage, errors: validationErrors };
            } finally {
                this.loadingVouchers = false;
            }
        },
        async deleteVoucher(id) {
            try {
                const res = await axios.delete(`http://127.0.0.1:8000/api/vouchers/${id}`);
                return { success: true, message: res.data.message };
            } catch (error) {
                let errorMessage = 'Lỗi hệ thống không xác định.';
                let errorData = null;
                if (error.response) {
                    const status = error.response.status;
                    const data = error.response.data;
                    if (status === 403) {
                        errorMessage = data.message || 'Bạn không có quyền thực hiện thao tác này.';
                    } else if (status === 400 || status === 404) {
                        errorMessage = data.message || 'Lỗi dữ liệu.';
                    } else if (status === 401) {
                        errorMessage = 'Phiên đăng nhập đã hết hạn. Vui lòng đăng nhập lại.';
                    } else {
                        errorMessage = data.message || `Lỗi server (${status}).`;
                    }
                    errorData = data.errors; // Lấy lỗi validation nếu có
                }
                throw { success: false, message: errorMessage, errors: errorData };
            } 
            finally {
                this.loadingVouchers = false;
            }
        },
        handleBackendError(error, authStore) {
            if (error.res) {
                // Backend trả về lỗi validation
                const data = error.res.data;
                if (data.errors) {
                    this.submissionError = data.errors;
                } else if (data.message) {
                    this.submissionError = { general: [data.message] };
                }
            } else {
                this.submissionError = { general: ['Lỗi kết nối hoặc không xác định'] };
            }

        },

    },
});