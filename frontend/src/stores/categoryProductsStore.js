import { defineStore } from 'pinia';
import axios from 'axios';

export const useCategoryProductsStore = defineStore('categoryProducts', {
    state: () => ({
        breadcrumb: [],
        products: [],
        loading: false,
        error: null,
    }),

    actions: {
        async fetchProductsBySlug(slug) {
            this.loading = true;
            this.error = null;

            try {
                const response = await axios.get(`http://127.0.0.1:8000/api/categories/${slug}/products`);
                this.breadcrumb = response.data.breadcrumb || [];
                this.products = response.data.products || [];
            } catch (err) {
                this.error = err.response?.data?.message || 'Lỗi khi tải dữ liệu';
                console.error('Fetch category products failed:', err);
            } finally {
                this.loading = false;
            }
        },
    },

    getters: {
        breadcrumbNames: (state) => state.breadcrumb.map(b => b.name).join(' / '),
        activeProducts: (state) => state.products.filter(p => p.status === 'active'),
        soldProducts: (state) => state.products.filter(p => p.status === 'sold'),
    }
});
