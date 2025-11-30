import { defineStore } from 'pinia'
import axios from 'axios'
import { useAuthStore } from './auth'

export const useReviewStore = defineStore('reviewStore', {
  state: () => ({
    summary: {
      total_reviews: 0,
      average_rating: 0,
      rating_counts: {}
    },
    reviews: [],
    userReview: null,
    loading: false,
    error: null
  }),

  actions: {
    // 1. Lấy danh sách (Giữ nguyên)
    async fetchReviews(productId) {
      this.loading = true;
      this.error = null;
      try {
        const res = await axios.get(`http://127.0.0.1:8000/api/products/${productId}/reviews`);
        const data = res.data.data;
        this.summary = data.summary;
        
        this.reviews = data.reviews.map(r => ({
          id: r.id,
          user_id: r.reviewer_id,
          user_name: r.reviewer_name,
          rating: r.rating,
          comment: r.comment,
          date: r.created_at,
          images: r.images ?? [],
          user_avatar: r.avatar || `https://ui-avatars.io/api/?name=${r.reviewer_name}&background=random&color=fff`,
          seller_reply: r.seller_reply ?? null,
        }));

        const authStore = useAuthStore();
        if (authStore.user) {
            this.userReview = this.reviews.find(r => r.user_id === authStore.user.id) || null;
        }

      } catch (err) {
        this.error = "Không thể tải đánh giá!";
      } finally {
        this.loading = false;
      }
    },

    // 2. THÊM ĐÁNH GIÁ
    async addReview(reviewData) {
        this.loading = true;
        const authStore = useAuthStore();
        try {
            const res = await axios.post('http://127.0.0.1:8000/api/reviews', reviewData, {
                headers: { Authorization: `Bearer ${authStore.token}` }
            });
            
            const newR = res.data.data; 
            
            const newReviewFrontend = {
                id: newR.id,
                user_id: newR.reviewer_id,
                user_name: newR.reviewer_name,
                rating: newR.rating,
                comment: newR.comment,
                date: newR.created_at,
                images: [],
                user_avatar: newR.avatar || `https://ui-avatars.io/api/?name=${newR.reviewer_name}&background=random&color=fff`,
                seller_reply: null
            };

            this.reviews.unshift(newReviewFrontend);
            
            return { success: true };
        } catch (err) {
            const msg = err.response?.data?.message || 'Lỗi khi gửi đánh giá';
            return { success: false, message: msg };
        } finally {
            this.loading = false;
        }
    },

    // 3. SỬA ĐÁNH GIÁ
    async updateReview(reviewId, reviewData) {
        const authStore = useAuthStore();
        
        // 1. Backup
        const index = this.reviews.findIndex(r => r.id === reviewId);
        if (index === -1) return { success: false };
        const originalReview = { ...this.reviews[index] };

        // 2. Optimistic Update
        this.reviews[index].rating = reviewData.rating;
        this.reviews[index].comment = reviewData.comment;

        // 3. API Call
        try {
            await axios.put(`http://127.0.0.1:8000/api/reviews/${reviewId}`, reviewData, {
                headers: { Authorization: `Bearer ${authStore.token}` }
            });
            return { success: true };
        } catch (err) {
            this.reviews[index] = originalReview;
            return { success: false, message: err.response?.data?.message || 'Lỗi cập nhật' };
        }
    },

    // 4. XÓA ĐÁNH GIÁ (Đã xóa dòng confirm cũ)
    async deleteReview(reviewId, productId) {
        // --- ĐÃ XÓA DÒNG NÀY: if(!confirm(...)) ---
        // Vì component ReviewSection.vue đã dùng Swal để hỏi rồi, Store cứ thế mà xóa thôi.
        
        const authStore = useAuthStore();
        
        const index = this.reviews.findIndex(r => r.id === reviewId);
        const originalReview = this.reviews[index];

        this.reviews = this.reviews.filter(r => r.id !== reviewId);

        try {
            await axios.delete(`http://127.0.0.1:8000/api/reviews/${reviewId}`, {
                headers: { Authorization: `Bearer ${authStore.token}` }
            });
            return { success: true };
        } catch (err) {
            if (originalReview) this.reviews.splice(index, 0, originalReview);
            return { success: false, message: 'Lỗi xóa đánh giá' };
        }
    }
  },

  getters: {
    formattedAverage: (state) => state.summary.average_rating?.toFixed(1) || '0.0',
    formattedSummary(state) {
      if (!state.summary) return null;
      const s = state.summary;
      const breakdown = Object.keys(s.rating_counts).map(stars => ({
        stars: Number(stars),
        count: s.rating_counts[stars],
        percentage: s.total_reviews ? Math.round((s.rating_counts[stars] / s.total_reviews) * 100) : 0,
      })).sort((a,b) => b.stars - a.stars);
      
      return { total: s.total_reviews, avg: s.average_rating, breakdown };
    }
  }
})