// /src/stores/cartStore.js
import { defineStore } from "pinia";
import axios from "axios";

export const useCartStore = defineStore("cartStore", {
  state: () => ({
    loading: false,
    successMessage: "",
    errorMessage: "",
    lastAddedItem: null, 
  }),
  
  actions: {
    async addToCart(productId, quantity = 1) {
      this.loading = true;
      this.successMessage = "";
      this.errorMessage = "";
      this.lastAddedItem = null;

      try {
        const res = await axios.post("http://127.0.0.1:8000/api/cart/add", {
          product_id: productId,
          quantity,
        });

        this.successMessage = res.data.message;
      
        this.lastAddedItem = res.data.cart_item; 

        setTimeout(() => {
          this.successMessage = "";
          this.lastAddedItem = null;
        }, 3000);

      } catch (err) {
        console.error("Lỗi thêm giỏ hàng:", err);
        
        if (err.response && err.response.data && err.response.data.message) {
          this.errorMessage = err.response.data.message;
        } else {
          this.errorMessage = "Đã xảy ra lỗi không xác định khi thêm vào giỏ hàng.";
        }
        
        setTimeout(() => {
          this.errorMessage = "";
        }, 4000);
        
      } finally {
        this.loading = false;
      }
    },
  },
});