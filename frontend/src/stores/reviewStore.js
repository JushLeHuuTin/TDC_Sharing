// src/stores/reviewStore.js
import { defineStore } from 'pinia'
import axios from 'axios'

export const useReviewStore = defineStore('reviewStore', {
  state: () => ({
    summary: {
      total_reviews: 0,
      average_rating: 0,
      rating_counts: {}
    },
    reviews: [],
    loading: false,
    error: null
  }),

  actions: {
    async fetchReviews(productId) {
      this.loading = true
      this.error = null

      try {
        const res = await axios.get(`http://127.0.0.1:8000/api/products/${productId}/reviews`)
        if (res.data.success) {
          this.summary = res.data.data.summary
          this.reviews = res.data.data.reviews
        } else {
          this.error = 'Không thể tải danh sách đánh giá.'
        }
      } catch (err) {
        console.error(err)
        this.error = 'Lỗi kết nối tới server.'
      } finally {
        this.loading = false
      }
    }
  },

  getters: {
    // Trung bình rating làm tròn 1 chữ số thập phân
    formattedAverage: (state) => state.summary.average_rating?.toFixed(1) || '0.0',

    // Tổng số đánh giá
    totalReviews: (state) => state.summary.total_reviews,

    // Trả danh sách rating từ 5 → 1 sao
    ratingsBreakdown: (state) => {
      const counts = state.summary.rating_counts || {}
      return [5, 4, 3, 2, 1].map(star => ({
        star,
        count: counts[star] || 0
      }))
    }
  }
})
