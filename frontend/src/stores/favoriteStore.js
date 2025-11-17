import { defineStore } from 'pinia';
import axios from 'axios';
import { useAuthStore } from './auth';


export const useFavoriteStore = defineStore('favoriteStore', {
    state: () => ({
        favorites: [],     // data[]
        links: {},         // links {}
        meta: {},          // meta {}
        isLoading: false,
        submissionError: null,
        totalFavorites: 0
    }),

    actions: {
        async fetchFavorites(page = 1) {
            this.isLoading = true;
            this.submissionError = null;

            const authStore = useAuthStore();
            const token = authStore.token;

            if (!token) {
                this.isLoading = false;
                this.submissionError = { general: ['Phiên đăng nhập hết hạn.'] };
                throw new Error('Unauthorized');
            }

            try {
                const res = await axios.get(
                    `http://127.0.0.1:8000/api/favorites?page=${page}`,
                    {
                        headers: {
                            Authorization: `Bearer ${token}`
                        }
                    }
                );

                // API trả về đúng format bạn gửi
                this.favorites = res.data.data ?? [];
                this.links = res.data.links ?? {};
                this.meta = res.data.meta ?? {};
                this.totalFavorites = res.data.meta.total ?? 0;

            } catch (error) {
                console.error("Lỗi khi load favorites:", error);

                if (error.response?.status === 401) {
                    authStore.logout();
                }

                this.submissionError = error.response?.data?.errors || {
                    general: ['Không thể tải danh sách yêu thích.']
                };
            } finally {
                this.isLoading = false;
            }
        }
    }
});
