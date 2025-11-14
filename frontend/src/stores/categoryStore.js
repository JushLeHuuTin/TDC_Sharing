// stores/categoryStore.js
import { defineStore } from 'pinia';
import axios from 'axios';


// Hàm đệ quy để làm phẳng cây danh mục
const flattenCategories = (categoriesTree, level = 0, flatList = []) => {
    categoriesTree.forEach(cat => {
        // Tạo chuỗi ký tự phân cấp (Ví dụ: "— — ")
        const indent = '— '.repeat(level);

        flatList.push({
            ...cat,
            name: indent + cat.name,
            level: level,
            isParent: cat.children && cat.children.length > 0
        });

        if (cat.children && cat.children.length > 0) {
            // Đệ quy cho danh mục con
            flattenCategories(cat.children, level + 1, flatList);
        }
    });
    return flatList;
};

export const useCategoryStore = defineStore('category', {
    state: () => ({
        // 🎯 Chỉ cần lưu trữ data dạng cây (vì nó chứa tất cả thông tin)
        categoriesTree: [],
        isLoading: false,
        error: null,
        isLoadingAttributes: true,
        dynamicAttributes: [],
        form_attributes: [],
        breadcrumb: [],
        products: [],
        loading: false,
        error: null,
        pagination: {
            currentPage: 1,
            perPage: 8,
            totalItems: 0,
            totalPages: 1,
            links: [] // Links chi tiết (First, Last, Next, Previous)
        }

    }),
    actions: {
        async fetchCategories(isTree = false) { // Sử dụng một action chung với cờ isTree
            // Sử dụng categoriesTree để cache data lớn nhất
            if (this.categoriesTree.length > 0) {
                return;
            }

            this.isLoading = true;
            this.error = null;

            try {
                // Nếu isTree là false, mặc định sẽ gọi API top-five
                const endpoint = isTree
                    ? 'http://127.0.0.1:8000/api/categories' // Lấy full tree
                    : 'http://127.0.0.1:8000/api/categories/top-five'; // Lấy top 5 (dạng cây)

                const response = await axios.get(endpoint);

                // 🎯 LƯU TRỮ VÀO categoriesTree
                this.categoriesTree = response.data.data || response.data;
                console.log(this.categoriesTree);
            } catch (error) {
                this.error = 'Lỗi tải danh mục từ API.';
                console.error('Lỗi khi tải danh mục:', error);
            } finally {
                this.isLoading = false;
            }
        },
        async fetchDynamicAttributes(categoryId) {
            try {
                const url = `http://127.0.0.1:8000/api/categories/${categoryId}/attributes`;
                const response = await axios.get(url);

                const attributes = response.data.data || [];
                this.dynamicAttributes = attributes;
                const mappedAttributes = attributes.map(attr => ({
                    // Dùng id và giá trị rỗng cho binding
                    attribute_id: attr.id,
                    value: ''
                }));

                return mappedAttributes;
            } catch (error) {
                console.error(`Lỗi tải thuộc tính cho category ${categoryId}:`, error);
                alert('Không thể tải thuộc tính chi tiết cho danh mục này. Vui lòng thử lại.');
            } finally {
                this.isLoadingAttributes = false;
            }
        },
        async fetchProductsBySlug(slug = null, page = 1, filters = {}) {
            this.loading = true;
            this.error = null;
            try {
                const queryParams = new URLSearchParams({
                    page,
                    ...(filters.priceRange && filters.priceRange.min ? { price_min: filters.priceRange.min } : {}),
                    ...(filters.priceRange && filters.priceRange.max ? { price_max: filters.priceRange.max } : {}),
                }).toString();
                alert(queryParams);
                console.log(queryParams);
                const url = slug
                    ? `http://127.0.0.1:8000/api/categories/${slug}/products?${queryParams}`
                    : `http://127.0.0.1:8000/api/products?${queryParams}`;

                const response = await axios.get(url);
                this.products = response.data.data || [];
                const meta = response.data.meta;
                if (meta) {
                    this.pagination.currentPage = meta.current_page;
                    this.pagination.totalPages = meta.last_page;
                    this.pagination.totalItems = meta.total;
                    this.pagination.perPage = meta.per_page;
                    this.pagination.links = meta.links;
                } else {
                    this.pagination.currentPage = 1;
                    this.pagination.totalPages = 1;
                    this.pagination.totalItems = 0;
                    this.pagination.perPage = 8; this.pagination.links = [];
                }
            } catch (err) {
                this.error = err.response?.data?.message || 'Lỗi khi tải dữ liệu';
                console.error('Fetch category products failed:', err);
            } finally {
                this.loading = false;
            }
        },
    },
    getters: {
        topFiveCategories: (state) => state.categoriesTree,
        flattenedCategories: (state) => {
            return flattenCategories(state.categoriesTree);
        },
        breadcrumbNames: (state) => state.breadcrumb.map(b => b.name).join(' / '),
        activeProducts: (state) => state.products.filter(p => p.status === 'active'),
        soldProducts: (state) => state.products.filter(p => p.status === 'sold'),
    }
});