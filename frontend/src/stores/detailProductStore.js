import { defineStore } from "pinia";
import axios from "axios";

export const useDetailProductStore = defineStore("detailProduct", {
    state: () => ({
        product: null,
        mainImage: "",       // 🔥 Cho phép click đổi ảnh từ template
        isLoading: false,
        error: null,
        baseImageUrl: "http://127.0.0.1:8000/storage/" // Tuỳ backend
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
                    const firstImage = featured?.path ?? data.images[0].path;

                    this.mainImage = this.baseImageUrl + firstImage;
                } else {
                    this.mainImage = "/no-image.png"; // fallback
                }

            } catch (err) {
                this.error = err.response?.data?.message || "Lỗi tải sản phẩm";
                this.product = null;
            } finally {
                this.isLoading = false;
            }
        },

        // 🔥 Dùng khi click vào thumbnail
        setMainImage(path) {
            this.mainImage = this.baseImageUrl + path;
        }
    }
});
