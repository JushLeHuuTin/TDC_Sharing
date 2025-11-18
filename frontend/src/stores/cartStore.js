// /src/stores/cartStore.js
import { defineStore } from "pinia";
import axios from "axios";

export const useCartStore = defineStore("cartStore", {
  state: () => ({
    items: [],
    loading: false,
    successMessage: "",
  }),

  actions: {
    async addToCart(productId, quantity = 1) {
      this.loading = true;
      this.successMessage = "";

      try {
        const res = await axios.post("http://127.0.0.1:8000/api/cart/add", {
          product_id: productId,
          quantity,
        });

        this.items = res.data.cart_items;

        // 🔥 Hiệu ứng thông báo
        this.successMessage = "Đã thêm vào giỏ hàng!";
        setTimeout(() => {
          this.successMessage = "";
        }, 2000);

      } catch (err) {
        console.error("Lỗi thêm giỏ hàng:", err);
      } finally {
        this.loading = false;
      }
    },
  },
});
