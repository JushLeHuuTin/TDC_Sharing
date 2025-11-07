// stores/categoryStore.js
import { defineStore } from 'pinia';
import axios from 'axios';

export const useCategoryStore = defineStore('category', {
    state: () => ({
        categories: [],
        isLoading: false,
        error: null,
    }),
    actions: {
        async fetchCategories() {
            if (this.categories.length > 0) {
                // Tối ưu: Nếu đã có dữ liệu, không gọi API nữa (Cache)
                return; 
            }
            
            this.isLoading = true;
            this.error = null;
            
            try {
                const url = 'http://127.0.0.1:8000/api/categories/top-five';
                const response = await axios.get(url);
                
                // Cập nhật State toàn cục
                this.categories = response.data.data || response.data;
                
            } catch (error) {
                this.error = 'Lỗi tải danh mục từ API.';
                console.error('Lỗi khi tải danh mục:', error);
            } finally {
                this.isLoading = false;
            }
        },
        
        // (Bạn có thể thêm các action khác: addCategory, deleteCategory, v.v.)
    },
    getters: {
        topFiveCategories: (state) => state.categories,
    }
});