import { defineStore } from 'pinia';
import axios from 'axios';
import { useAuthStore } from '@/stores/auth';

const BASE_URL = 'http://127.0.0.1:8000/api';

export const useReviewStore = defineStore('review', {
    state: () => ({
        reviews: [],
        isLoading: false,
        error: null,
    }),

    actions: {
        /**
         * Lấy danh sách đánh giá cho 1 sản phẩm (Tính năng 2)
         * @param {number} productId - ID của sản phẩm
         */
        async fetchReviews(productId) {
            this.isLoading = true;
            this.error = null;
            try {
                // API này công khai, không cần token
                const response = await axios.get(`${BASE_URL}/products/${productId}/reviews`);
                if (response.data && response.data.data) {
                    this.reviews = response.data.data;
                }
            } catch (err) {
                this.error = 'Lỗi khi tải đánh giá.';
                console.error('Lỗi fetchReviews:', err);
            } finally {
                this.isLoading = false;
            }
        },

        /**
         * Thêm đánh giá mới (Tính năng 1)
         * @param {object} data - { product_id, rating, comment }
         */
        async addReview(data) {
            this.isLoading = true;
            this.error = null;
            const authStore = useAuthStore();
            const token = authStore.token;

            if (!token) {
                this.error = "Bạn cần đăng nhập để đánh giá.";
                return false;
            }

            try {
                const config = { headers: { 'Authorization': `Bearer ${token}` } };
                const response = await axios.post(`${BASE_URL}/reviews`, data, config);

                if (response.data && response.data.data) {
                    // Thêm vào đầu danh sách (nhanh, không cần fetch)
                    this.reviews.unshift(response.data.data);
                    return true;
                }
            } catch (err) {
                this.error = 'Lỗi khi gửi đánh giá.';
                console.error('Lỗi addReview:', err);
                return false;
            } finally {
                this.isLoading = false;
            }
        },

        /**
         * Xóa đánh giá (Tính năng 3)
         * @param {number} reviewId - ID của đánh giá
         */
        async deleteReview(reviewId) {
            this.isLoading = true;
            this.error = null;
            const authStore = useAuthStore();
            const token = authStore.token;

            if (!token) {
                this.error = "Bạn cần đăng nhập.";
                return false;
            }

            try {
                const config = { headers: { 'Authorization': `Bearer ${token}` } };
                await axios.delete(`${BASE_URL}/reviews/${reviewId}`, config);
                
                // Xóa khỏi state (nhanh)
                this.reviews = this.reviews.filter(r => r.id !== reviewId);
                return true;

            } catch (err) {
                this.error = 'Lỗi khi xóa đánh giá.';
                console.error('Lỗi deleteReview:', err);
                return false;
            } finally {
                this.isLoading = false;
            }
        },

        /**
         * Cập nhật đánh giá (Tính năng 4)
         * @param {number} reviewId - ID của đánh giá
         * @param {object} data - { rating, comment }
         */
        async updateReview(reviewId, data) {
            this.isLoading = true;
            this.error = null;
            const authStore = useAuthStore();
            const token = authStore.token;

            if (!token) {
                this.error = "Bạn cần đăng nhập.";
                return false;
            }

            try {
                const config = { headers: { 'Authorization': `Bearer ${token}` } };
                const response = await axios.put(`${BASE_URL}/reviews/${reviewId}`, data, config);

                if (response.data && response.data.data) {
                    // Cập nhật trong state (nhanh)
                    const index = this.reviews.findIndex(r => r.id == reviewId);
                    if (index !== -1) {
                        this.reviews[index] = response.data.data;
                    }
                    return true;
                }
            } catch (err) {
                this.error = 'Lỗi khi cập nhật đánh giá.';
                console.error('Lỗi updateReview:', err);
                return false;
            } finally {
                this.isLoading = false;
            }
        }
    }
});