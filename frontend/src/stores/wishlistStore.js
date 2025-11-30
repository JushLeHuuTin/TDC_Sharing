import { defineStore } from "pinia";
import axios from "axios";
// Giả định bạn có useAuthStore để lấy token
import { useAuthStore } from "./auth"; 

const API_BASE = "http://127.0.0.1:8000/api";

export const useWishlistStore = defineStore("wishlist", {
    state: () => ({
        products: [],
        isLoading: false,       // Loading khi tải dữ liệu trang
        isLoadingError: null,   // Lỗi khi tải dữ liệu trang
        pagination: {
            currentPage: 1,
            perPage: 4,
            totalItems: 0,
            totalPages: 1,
            links: []
        },
        
        isAddingOrRemoving: false, // Loading khi thực hiện thao tác (toggle)
        toggleError: null,         // Lỗi riêng cho thao tác Add/Remove
        processingId: null, 
    }),

    getters: {
        totalItems: (state) => state.pagination.totalItems || 0,
        // Kiểm tra nhanh xem một sản phẩm có trong danh sách yêu thích không
        isFavorited: (state) => (productId) => {
            return state.products.some(p => p.id === productId);
        },
        // Lấy danh sách ID các sản phẩm yêu thích (dùng cho các trang danh sách)
        favoritedIds: (state) => state.products.map(p => p.id),
    },

    actions: {
        async fetchFavorites(page = 1) {
            this.isLoading = true;
            this.isLoadingError = null; // Reset lỗi tải trang
            const authStore = useAuthStore();
            const token = authStore.token;

            if (!token) {
                 this.isLoadingError = "Bạn cần đăng nhập để xem danh sách yêu thích.";
                 this.isLoading = false;
                 return;
            }

            try {
                const response = await axios.get(`${API_BASE}/favorites?page=${page}`, {
                    headers: { Authorization: `Bearer ${token}` }
                });

                this.products = response.data.data;
                
                // Cập nhật Pagination chi tiết
                this.pagination.currentPage = response.data.meta.current_page;
                this.pagination.totalPages = response.data.meta.last_page;
                this.pagination.totalItems = response.data.meta.total;
                this.pagination.perPage = response.data.meta.per_page;
                this.pagination.links = response.data.meta.links;
                
            } catch (err) {
                this.isLoadingError = err.response?.data?.message || "Lỗi tải danh sách yêu thích.";
                this.products = [];
                // Nếu lỗi là 401, logout
                if (err.response?.status === 401) authStore.logout();

            } finally {
                this.isLoading = false;
            }
        },

        async toggleFavorite(productId) {
            this.isAddingOrRemoving = true;
            this.processingId = productId;
            this.toggleError = null; // Reset lỗi thao tác
            
            const authStore = useAuthStore();
            const token = authStore.token;
            
            if (!token) {
                alert("Vui lòng đăng nhập để thực hiện chức năng này.");
                this.isAddingOrRemoving = false;
                this.processingId = null;
                // Ném lỗi để component biết thao tác thất bại
                throw new Error('Unauthorized'); 
            }

            try {
                const isCurrentlyFavorited = this.isFavorited(productId);
                
                if (isCurrentlyFavorited) {
                    // Xóa khỏi danh sách
                    await axios.delete(`${API_BASE}/favorites/${productId}`, {
                        headers: { Authorization: `Bearer ${token}` }
                    });
                    
                    // Cập nhật state local
                    this.products = this.products.filter(p => p.id !== productId);
                    
                } else {
                    // Thêm vào danh sách
                    const response = await axios.post(`${API_BASE}/favorites/${productId}`, {}, {
                        headers: { Authorization: `Bearer ${token}` }
                    });
                    
                    if (response.data.data) {
                         // Cập nhật state local
                         this.products.unshift(response.data.data);
                    }
                }
                
            } catch (err) {
                // Xử lý lỗi riêng cho thao tác
                this.toggleError = err.response?.data?.message || "Lỗi khi cập nhật yêu thích.";
                console.error("Wishlist toggle error:", err);
                
                // Ném lỗi để component biết thao tác thất bại
                throw err; 
            } finally {
                this.isAddingOrRemoving = false;
                this.processingId = null;
                
                // Đồng bộ hóa phân trang nếu đang ở trang Wishlist
                if (window.location.pathname.includes('/favorites')) {
                     this.fetchFavorites(this.pagination.currentPage || 1); 
                }
            }
        }
    }
});