import { defineStore } from 'pinia';
import axios from 'axios';
import { useAuthStore } from './auth';


export const usePromotionStore = defineStore('promotion', {
    state: () => ({
        appliedDiscount: 0,
        appliedPromotionCode: null,
        isLoading: false,
        promotionError: null,

        // --- Promotion list ---
        promotions: [],
        total: 0,
        promotionPagination: {
            currentPage: 1,
            perPage: 8,
            totalItems: 0,
            totalPages: 1,
            links: [] // Links chi tiết (First, Last, Next, Previous)
        },
        loadingPromotions: false,
        fetchError: null,
    }),

    actions: {
        // Action để xóa mã giảm giá
        removePromotion() {
            this.appliedDiscount = 0;
            this.appliedPromotionCode = null;
            this.promotionError = null;
        },
        async fetchPromotions(page = 1) {
            this.loadingPromotions = true;
            this.fetchError = null;

            try {
                const res = await axios.get(`http://127.0.0.1:8000/api/promotions?page=${page}`);

                // Lấy danh sách promotion
                this.promotions = res.data.data.data;
                // Lấy stats
                this.total = res.data.data.total;

                // Lấy thông tin phân trang
                this.promotionPagination.currentPage = res.data.data.current_page;
                this.promotionPagination.totalPages = res.data.data.last_page;
                this.promotionPagination.totalItems = res.data.data.total;
                this.promotionPagination.perPage = res.data.data.per_page;
                this.promotionPagination.lastPage = res.data.data.last_page;
                this.promotionPagination.links = res.data.data.links;

            } catch (err) {
                this.fetchError = err.response?.data?.message || "Không tải được danh sách promotion.";
            } finally {
                this.loadingPromotions = false;
            }
        },
        async createPromotion(promotionData) {
            this.loadingPromotions = true; // Sử dụng trạng thái loading của danh sách
            this.fetchError = null; // Reset lỗi

            try {
                const res = await axios.post('http://127.0.0.1:8000/api/promotions', promotionData);
                return { success: true, message: res.data.message, promotion: res.data.promotion };
                
            } catch (err) {
                // Xử lý lỗi từ server (ví dụ: lỗi validation 422)
                const errorMessage = err.response?.data?.message || "Lỗi không xác định khi tạo promotion.";
                const validationErrors = err.response?.data?.errors;
                console.log(validationErrors);

                this.fetchError = errorMessage;

                return { success: false, message: errorMessage, errors: validationErrors };

            } finally {
                this.loadingPromotions = false;
            }
        },
        // formatPrice(value) {
        //     if (value === null || value === undefined) return '0₫';

        //     const number = parseFloat(value); // chuyển decimal về number

        //     if (isNaN(number)) return '0₫';

        //     return new Intl.NumberFormat('vi-VN', {
        //         style: 'currency',
        //         currency: 'VND',
        //         minimumFractionDigits: 0,
        //         maximumFractionDigits: 0
        //     }).format(number);
        // },
        // async updatePromotion(id, promotionData) {
        //     const authStore = useAuthStore();
        //     const token = authStore.token;
        //     this.loadingPromotions = true;
        //     this.fetchError = null;

        //     try {
        //         console.log("=== Debug: updatePromotion start ===");
        //         console.log("Promotion ID:", id);
        //         console.log("Original promotionData:", promotionData);

        //         // Chuyển object JSON thành FormData
        //         const formData = new FormData();
        //         for (const key in promotionData) {
        //             if (promotionData[key] !== null && promotionData[key] !== undefined) {
        //                 formData.append(key, promotionData[key]);
        //             }
        //         }
        //         formData.append('_method', 'PUT');

        //         console.log("FormData to send:");
        //         for (let pair of formData.entries()) {
        //             console.log(pair[0], ":", pair[1]);
        //         }

        //         const res = await axios.post(
        //             `http://127.0.0.1:8000/api/promotions/${id}`,
        //             formData,
        //             {
        //                 headers: {
        //                     Authorization: `Bearer ${token}`, // hoặc lấy từ store nếu bạn quản lý token ở đó
        //                     'Content-Type': 'multipart/form-data',
        //                 },
        //             }
        //         );

        //         console.log("=== Debug: Response from server ===");
        //         console.log(res.data);

        //         return { success: true, message: res.data.message, promotion: res.data.data };
        //     } catch (err) {
        //         console.log("=== Debug: Caught error ===");
        //         console.log(err);
        //         const errorMessage =
        //             err?.response?.data?.message || `Lỗi không xác định khi cập nhật promotion #${id}.`;
        //         const validationErrors = err?.response?.data?.errors || null;

        //         this.fetchError = errorMessage;

        //         console.log("Error message:", errorMessage);
        //         console.log("Validation errors:", validationErrors);

        //         return { success: false, message: errorMessage, errors: validationErrors };
        //     } finally {
        //         this.loadingPromotions = false;
        //         console.log("=== Debug: updatePromotion end ===");
        //     }
        // }
        // ,

        // async deletePromotion(id) {
        //     this.loadingPromotions = true;
        //     this.fetchError = null;

        //     try {
        //         const res = await axios.delete(`http://127.0.0.1:8000/api/promotions/${id}`);
        //         this.promotions = this.promotions.filter(v => v.id !== id);
        //         return { success: true, message: res.data.message };
        //     } catch (err) {
        //         const errorMessage = err.response?.data?.message || `Lỗi không xác định khi xóa promotion #${id}.`;

        //         this.fetchError = errorMessage;

        //         return { success: false, message: "Promotion không tồn tại !" };
        //     } finally {
        //         this.loadingPromotions = false;
        //     }
        // },

    },
});