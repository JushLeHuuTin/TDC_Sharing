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
        expandedCategories: [],
        isLoading: false,
        error: null,
        isLoadingAttributes: true,
        dynamicAttributes: [],
        form_attributes: [],
        breadcrumb: [],
        products: [],
        loading: false,
        error: null,
        selectedCategoryId: null,
        pagination: {
            currentPage: 1,
            perPage: 8,
            totalItems: 0,
            totalPages: 1,
            links: [] // Links chi tiết (First, Last, Next, Previous)
        },
        filters: {
            search: '',
            priceRange: null,
            categories: [],
            conditions: [],
            location: '',
            negotiable: false,
            hasImages: false,
            verified: false
        },

    }),
    actions: {
        async fetchCategories(isTree = false) { // Sử dụng một action chung với cờ isTree
            // Sử dụng categoriesTree để cache data lớn nhất
            this.expandedCategories = [];
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
        async fetchProductsBySlug(slug = null, page = 1) {
            this.isLoading = true;
            this.error = null;

            try {
                const queryParams = new URLSearchParams({
                    page,
                    q: this.filters.search,
                    ...(this.filters.priceRange && this.filters.priceRange.min ? { price_min: this.filters.priceRange.min } : {}),
                    ...(this.filters.priceRange && this.filters.priceRange.max ? { price_max: this.filters.priceRange.max } : {}),
                }).toString();;
                const url = slug
                    ? `http://127.0.0.1:8000/api/categories/${slug}/products?${queryParams}`
                    : `http://127.0.0.1:8000/api/products?${queryParams}`;

                const response = await axios.get(url);
                this.products = response.data.data || [];
                const meta = response.data.meta || {};
                this.pagination = {
                    currentPage: meta.current_page || 1,
                    totalPages: meta.last_page || 1,
                    totalItems: meta.total || 0,
                    perPage: meta.per_page || 8,
                    links: meta.links || [],
                };
            } catch (err) {
                this.error = err.response?.data?.message || 'Lỗi khi tải dữ liệu';
                console.error(err);
            } finally {
                this.isLoading = false;
            }
        },
        selectCategory(id){
            this.selectedCategoryId = id;
        },
        toggleExpand(id){
            if(!this.expandedCategories) this.expandedCategories = [];
            if(this.expandedCategories.includes(id)){
                this.expandedCategories = this.expandedCategories.filter(x=>x!==id);
            }else{
                this.expandedCategories.push(id);
            }
        },
        expandAll() {
            const expanded = [];
            const expand = (cats) => {
              cats.forEach(c => {
                if(c.children && c.children.length > 0){
                  expanded.push(c.id);
                  expand(c.children);
                }
              });
            };
            expand(this.categoriesTree);
            this.expandedCategories = expanded;
          },                    
        collapseAll(){
            this.expandedCategories = [];
        },
        hasSubCategories(id){
            const category = this.categoriesTree.find(c => c.id === id);
            return category && category.children && category.children.length > 0;
        }
    },
    getters: {
        topFiveCategories: (state) => state.categoriesTree,
        flattenedCategories: (state) => {
            return flattenCategories(state.categoriesTree);
        },
        breadcrumbNames: (state) => state.breadcrumb.map(b => b.name).join(' / '),
        activeProducts: (state) => state.products.filter(p => p.status === 'active'),
        soldProducts: (state) => state.products.filter(p => p.status === 'sold'),
        categoriesArray: (state) => state.categoriesTree || [],
        categoryStats: (state) => {
            // Nếu categoriesTree đã flatten
            const level1Categories = state.categoriesTree.filter(c => c.level === 1);
            const level2Categories = state.categoriesTree.filter(c => c.level === 2);
        
            // Tổng sản phẩm, bao gồm tất cả category (có product_count)
            const totalProducts = state.categoriesTree.reduce((sum, c) => sum + (c.product_count || 0), 0);
        
            return {
                totalCategories: state.categoriesTree.length,
                level1Categories: level1Categories.length,
                level2Categories: level2Categories.length,
                totalProducts: totalProducts, // raw number
                totalProductsFormatted: totalProducts.toLocaleString('en-US') // hiển thị
            };
        },        
        categoryTreeData: (state) => {
            const flat = [];

            const expanded = state.expandedCategories || [];
    
            const flatten = (cats, level = 1, parentId = null) => {
                cats.forEach(cat => {
                    const newCat = {
                        ...cat,
                        level,
                        parent_id: parentId,
                    };
                    flat.push(newCat);
    
                    // Nếu category có children và đang mở rộng
                    if(cat.children && cat.children.length > 0 && expanded.includes(cat.id)){
                        flatten(cat.children, level + 1, cat.id);
                    }
                });
            };
    
            flatten(state.categoriesTree);
    
            return flat;
        }
        ,
        selectedCategoryInfo: (state) => {
            if (!state.selectedCategoryId) return null;
        
            const flat = state.categoryTreeData; // dùng dữ liệu flatten
        
            const category = flat.find(c => c.id === state.selectedCategoryId);
            if (!category) return null;
        
            const parentCategory = category.parent_id
                ? flat.find(c => c.id === category.parent_id)
                : null;
        
            const subCategories = flat.filter(c => c.parent_id === category.id);
        
            return {
                ...category,
                parentCategory,
                subCategories
            };
        }        
    }
});