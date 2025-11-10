// stores/productStore.js
import { defineStore } from 'pinia';
import axios from 'axios';
import { useAuthStore } from './auth';

export const useProductStore = defineStore('product', {
    state: () => ({
        featuredProducts: [],
        recentProducts: [],
        isLoadingFeatured: false,
        isLoadingRecent: false,
        featuredError: null,
        recentError: null,
        submissionError: null,
        isCreating: false,
        myProducts: [],
        isLoadingMyProducts: false,
        myProductsError: null
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
        async createProduct(formData) {
            this.isCreating = true;
            this.submissionError = null;
            
            const authStore = useAuthStore();
            const token = authStore.token;

            if (!token) {
                this.isCreating = false;
                this.submissionError = 'Phiên làm việc đã hết hạn. Vui lòng đăng nhập lại.';
                return;
            }

            try {
                // 🎯 GỌI API THỰC TẾ TỪ STORE
                const response = await axios.post('http://127.0.0.1:8000/api/products', formData, {
                    headers: {
                        'Authorization': `Bearer ${token}`,
                    }
                });
                
                this.isCreating = false;
                return response.data.data; // Trả về object sản phẩm vừa tạo
                
            } catch (error) {
                this.isCreating = false;
                
                if (error.response && error.response.status === 422) {
                    // Lỗi Validation
                    this.submissionError = error.response.data.errors;
                    throw new Error('Validation Failed'); // Ném lỗi để Component bắt và hiển thị
                }
                if (error.response && error.response.status === 401) {
                    // Lỗi Unauthorized
                    authStore.logout(); // Kích hoạt logout ngay lập tức
                    throw new Error('Unauthorized');
                }
                this.submissionError = { general: ['Lỗi hệ thống khi đăng sản phẩm.'] };
                throw new Error('System Error');
            }
        },
        async updateProduct(productId, formData) { 
            this.isUpdating = true;
            this.submissionError = null;
            
            const authStore = useAuthStore();
            const token = authStore.token;

            if (!token) {
                this.isUpdating = false;
                this.submissionError = 'Phiên làm việc đã hết hạn. Vui lòng đăng nhập lại.';
                throw new Error('Unauthorized'); 
            }
            formData.append('_method', 'PUT');
            try {
                const url = `http://127.0.0.1:8000/api/products/${productId}`;
                const response = await axios.post(url, formData, {
                    headers: {
                        'Authorization': `Bearer ${token}`,
                    }
                });
                
                this.isUpdating = false;
                return response.data.data; 
                
            } catch (error) {
                this.isUpdating = false;
                
                if (error.response) {
                    if (error.response.status === 422) {
                        this.submissionError = error.response.data.errors;
                        console.log(submissionError);
                        throw new Error('Validation Failed');
                    }
                    if (error.response.status === 401) {
                        authStore.logout();
                        throw new Error('Unauthorized');
                    }
                }
                this.submissionError = { general: [error.response?.data?.message || 'Lỗi hệ thống không xác định.'] };
                throw new Error('System Error');
            }
        },
        updateProductInList(updatedProduct) {
            // 1. Tìm index của sản phẩm trong mảng
            const index = this.myProducts.findIndex(p => p.id === updatedProduct.id);
    
            if (index !== -1) {
                Object.assign(this.myProducts[index], updatedProduct);
            }
        },
        async fetchMyProducts() {
            if (this.myProducts.length > 0) {
                return; 
            }    
            this.isLoadingMyProducts = true;
            this.myProductsError = null;        
            try {
                const url = 'http://127.0.0.1:8000/api/products';
                const response = await axios.get(url);
                this.myProducts = response.data.data || response.data;
            } catch (error) {
                console.error('Lỗi khi tải sản phẩm mới nhất:', error);
            } finally {
                this.isLoadingMyProducts = false;
            }
        },
    },
});