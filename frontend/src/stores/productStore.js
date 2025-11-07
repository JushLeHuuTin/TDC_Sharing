// stores/productStore.js
import { defineStore } from 'pinia';
import axios from 'axios';

export const useProductStore = defineStore('product', {
    state: () => ({
        featuredProducts: [],
        recentProducts: [],
        isLoadingFeatured: false,
        isLoadingRecent: false,
        featuredError: null,
        recentError: null,
    }),
    actions: {
        async fetchFeaturedProducts() {
            if (this.featuredProducts.length > 0) {
                // Tối ưu: Nếu đã có dữ liệu, không gọi API nữa
                return; 
            }
            
            this.isLoadingFeatured = true;
            this.featuredError = null;
            
            try {
                const url = 'http://127.0.0.1:8000/api/featured-products';
                const response = await axios.get(url);
                
                // Giả định API trả về mảng sản phẩm trong response.data.data
                this.featuredProducts = response.data.data || response.data;
                
            } catch (error) {
                this.featuredError = 'Lỗi tải sản phẩm nổi bật từ API.';
                console.error('Lỗi khi tải sản phẩm nổi bật:', error);
            } finally {
                this.isLoadingFeatured = false;
            }
        },
        
        async fetchRecentProducts() {
            if (this.recentProducts.length > 0) {
                // Tối ưu: Nếu đã có dữ liệu, không gọi API nữa
                return; 
            }
            
            this.isLoadingRecent = true;
            this.recentError = null;
            
            try {
                const url = 'http://127.0.0.1:8000/api/recent-products';
                const response = await axios.get(url);
                
                // Giả định API trả về mảng sản phẩm trong response.data.data
                this.recentProducts = response.data.data || response.data;
                
            } catch (error) {
                this.recentError = 'Lỗi tải sản phẩm mới nhất từ API.';
                console.error('Lỗi khi tải sản phẩm mới nhất:', error);
            } finally {
                this.isLoadingRecent = false;
            }
        },
    },
});