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

            try {
                const res = await axios.get(
                    `http://127.0.0.1:8000/api/products/${slug}`
                );

                const data = res.data.data;
                this.product = data;

                // Nếu API trả ảnh → set mainImage
                if (data.images?.length) {
                    const featured = data.images.find(img => img.is_featured);
                    const firstImagePath = featured?.path ?? data.images[0].path;

                    // ✨ FIX: Sử dụng helper để tạo URL tuyệt đối
                    this.mainImage = getFullImageUrl(firstImagePath, this.baseImageUrl);
                    
                    // ✨ Cập nhật đường dẫn cho tất cả ảnh thumbnail để template dễ dùng
                    this.product.images = data.images.map(img => ({
                        ...img,
                        full_path: getFullImageUrl(img.path, this.baseImageUrl)
                    }));

                } else {
                    // ✨ FIX: Sử dụng ảnh mặc định nội bộ
                    this.mainImage = getFullImageUrl(null, this.baseImageUrl); 
                }

            } catch (err) {
                this.error = err.response?.data?.message || "Lỗi tải sản phẩm";
                this.product = null;
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