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
        myProductsError: null,
        myProductsStatusCounts: {
            active: 0,
            draft: 0,
            pending: 0,
            sold: 0,
            hidden: 0,
        },
        pagination: {
            currentPage: 1,
            perPage: 8,
            totalItems: 0,
            totalPages: 1,
            links: [] // Links chi tiết (First, Last, Next, Previous)
        }
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
        async fetchMyProducts(status = 'active', page = 1, sort = 'newest') {
            // if (this.myProducts.length > 0) {
            //     return; 
            // }    
            try {
                const url = `http://127.0.0.1:8000/api/products/my?status=${status}&page=${page}&sort_by=${sort}`;
                const response = await axios.get(url);
                this.pagination.currentPage = response.data.meta.current_page;
                this.pagination.totalPages = response.data.meta.last_page;
                this.pagination.totalItems = response.data.meta.total;
                this.pagination.perPage = response.data.meta.per_page;
                this.pagination.links = response.data.meta.links;
                this.myProducts = response.data.data;

            } catch (error) {
                // ...
            } finally {
                this.isLoadingMyProducts = false;
            }
        },
        async goToPage(page) {
            if (page < 1 || page > this.pagination.totalPages) return;
            this.fetchMyProducts(this.currentStatus, page, this.sortBy, this.pagination.perPage);
        },
        async fetchMyProductsStatusCounts() {
            try {
                const response = await axios.get('http://127.0.0.1:8000/api/user/products/counts');
                this.myProductsStatusCounts = response.data.data;
            } catch (e) {
                console.error('Không thể tải status counts.');
            }
        },
        async deleteProduct(id) {
            try {
              const res = await axios.delete(`http://127.0.0.1:8000/api/products/${id}`);
              // Cập nhật store, loại bỏ sản phẩm khỏi danh sách
              this.myProducts = this.myProducts.filter(p => p.id !== id);
              return res.data; // { success: true, message: '...' }
            } catch (error) {
              // Ném error ra component để xử lý toast
              throw error;
            }
          },
        async deleteProduct(id) {
            try {
              const res = await axios.delete(`http://127.0.0.1:8000/api/products/${id}`);
              // Cập nhật store, loại bỏ sản phẩm khỏi danh sách
              this.myProducts = this.myProducts.filter(p => p.id !== id);
              return res.data; // { success: true, message: '...' }
            } catch (error) {
              // Ném error ra component để xử lý toast
              throw error;
            }
          }
    },
});