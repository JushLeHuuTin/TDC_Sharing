import { defineStore } from "pinia";
import axios from "axios";

// Hàm helper (Có thể đặt trong file riêng nhưng giữ ở đây cho tiện)
const getFullImageUrl = (path, baseUrl) => {
    if (!path) {
        return baseUrl + 'products/default-product.jpg';
    }
    const cleanedPath = path.startsWith('/') ? path.substring(1) : path;
    return baseUrl.endsWith('/') ? baseUrl + cleanedPath : baseUrl + '/' + cleanedPath;
};


export const useDetailProductStore = defineStore("detailProduct", {
    state: () => ({
        product: null,
        mainImage: "",      
        isLoading: false,
        error: null,
        // Dùng biến env hoặc đường dẫn cố định, nhưng thêm dấu / ở cuối để dễ nối chuỗi
        baseImageUrl: "http://127.0.0.1:8000/storage/" 
    }),

    getters: {
        hasProduct: (state) => !!state.product,

        formattedPrice(state) {
            if (!state.product?.price) return "";
            return state.product.price; // vì API trả "14.059.732đ"
        }
    },

    actions: {
        async fetchProduct(slug) {
            this.isLoading = true;
            this.error = null;
            this.product = null; // Luôn reset product trước khi fetch
    
            try {
                const res = await axios.get(
                    `http://127.0.0.1:8000/api/products/${slug}`
                );
    
                const data = res.data.data;
                this.product = data;
    
                // ... (Logic xử lý hình ảnh giữ nguyên) ...
                if (data.images?.length) {
                    const featured = data.images.find(img => img.is_featured);
                    const firstImagePath = featured?.path ?? data.images[0].path;
                    
                    this.mainImage = getFullImageUrl(firstImagePath, this.baseImageUrl);
                    
                    this.product.images = data.images.map(img => ({
                        ...img,
                        full_path: getFullImageUrl(img.path, this.baseImageUrl)
                    }));
                } else {
                    this.mainImage = getFullImageUrl(null, this.baseImageUrl); 
                }
    
            } catch (err) {
                // --- ĐÃ SỬA: XỬ LÝ LỖI 404 CỤ THỂ ---
                if (err.response && err.response.status === 404) {
                    // Nếu là lỗi 404, set error là chuỗi đặc biệt để component dễ kiểm tra
                    this.error = '404_NOT_FOUND';
                } else {
                    // Xử lý các lỗi khác (500, mạng, v.v.)
                    this.error = err.response?.data?.message || "Lỗi tải dữ liệu sản phẩm.";
                }
                this.product = null; // Đảm bảo product luôn là null khi có lỗi
            } finally {
                this.isLoading = false;
            }
        },

        // 🔥 Dùng khi click vào thumbnail
        setMainImage(fullPath) { // Nhận fullPath đã được xử lý
            this.mainImage = fullPath;
        }
    }
});