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
        const data = res.data.data;

        // summary
        this.summary = data.summary;

        // reviews list
        this.reviews = data.reviews.map(r => ({
          id: r.id,
          user_name: r.reviewer_name,
          rating: r.rating,
          comment: r.comment,
          date: r.created_at,
          images: r.images ?? [],     // nếu API có hình sau này
          helpful_count: r.helpful_count ?? 0,
          user_avatar: r.avatar ?? "/default-avatar.png",
          verified: r.verified ?? false,
          seller_reply: r.seller_reply ?? null,
        }));

      } catch (err) {
        this.error = "Không thể tải đánh giá!";
      } finally {
        this.isLoading = false;
      }
    }
  },
  

  getters: {
    filteredReviews(state) {
      return state.reviews;
    },
    // Trung bình rating làm tròn 1 chữ số thập phân
    formattedAverage: (state) => state.summary.average_rating?.toFixed(1) || '0.0',

    formattedSummary(state) {
      if (!state.summary) return null;

      const s = state.summary;

      const breakdown = Object.keys(s.rating_counts).map(stars => ({
        stars: Number(stars),
        count: s.rating_counts[stars],
        percentage: s.total_reviews
          ? Math.round((s.rating_counts[stars] / s.total_reviews) * 100)
          : 0,
      })).sort((a,b) => b.stars - a.stars);

      return {
        total: s.total_reviews,
        avg: s.average_rating,
        breakdown
      };
    }
  }
})

