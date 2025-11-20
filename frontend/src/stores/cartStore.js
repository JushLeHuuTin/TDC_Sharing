// /src/stores/cartStore.js
import { defineStore } from "pinia";
import axios from "axios";

export const useCartStore = defineStore("cartStore", {
  state: () => ({
    loading: false,
    successMessage: "",
    errorMessage: "",
    lastAddedItem: null,
    cartData: {
      shops: [],
      overall_summary: { subtotal: 0, shipping_fee: 0, discount: 0, total: 0 }, // Đặt giá trị mặc định an toàn
      is_cart_ready_for_checkout: false,
    },
    shippingOptions: {
      'default': 30000,
      'fast': 50000,
      'pickup': 0
    }
  }),

  getters: {
    formatPrice: () => (price) => {
      if (price === undefined || price === null) return '0 ₫';
      const floatPrice = parseFloat(price);
      return floatPrice.toLocaleString('vi-VN', {
        style: 'currency',
        currency: 'VND',
        minimumFractionDigits: 0,
        maximumFractionDigits: 0
      }).replace('₫', '').trim() + ' ₫';
    },
    overallTotal(state) {
      return state.cartData.overall_summary?.total || 0;
    },
    getSelectedSubtotal: (state) => (shop) => {
      if (!shop || !shop.items) return 1;
      const result = shop.items.reduce((sum, item) => {
        let itemValueToAdd = 0;
        if (item.is_selected) {
          const priceValue = parseFloat(item.price || 0);
          if (!isNaN(priceValue)) {
            itemValueToAdd = priceValue * item.quantity;
          }
        }
        return sum + itemValueToAdd;
      }, 0);
      return result;
    },

  },
  actions: {
    async fetchCartData() {
      this.loading = true;
      try {
        const response = await axios.get("http://127.0.0.1:8000/api/cart");

        // Cập nhật state với dữ liệu API
        this.cartData = response.data.data;
        console.log(this.cartData);
      } catch (error) {
        this.errorMessage = "Không thể lấy dữ liệu giỏ hàng. Vui lòng thử lại.";
        console.error("Lỗi Fetch Cart:", error);
      } finally {
        this.loading = false;
      }
    },
    async toggleItemSelected(cartItemId, isSelected) {
      this.loading = true; // Bật loading
      this.errorMessage = "";
      try {
        const endpoint = `http://127.0.0.1:8000/api/cart/item/${cartItemId}/toggle`;
        const response = await axios.put(endpoint, {
          is_selected: isSelected,
        });
        this.cartData = response.data.data;
        console.log(this.cartData);
      } catch (error) {
        console.error("Lỗi Toggle Item:", error);
        if (error.response && error.response.data && error.response.data.message) {
          this.errorMessage = error.response.data.message;
        } else {
          this.errorMessage = "Lỗi không xác định khi cập nhật trạng thái chọn.";
        }
      } finally {
        this.loading = false; // Tắt loading
        // alert(this.loading);
      }
    },
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
    async updateQuantity(cartItemId, newQuantity) {
      this.loading = true;
      this.errorMessage = "";
      this.successMessage = "";
      try {
        const endpoint = `http://127.0.0.1:8000/api/cart/item/${cartItemId}`;

        const response = await axios.put(endpoint, {
          quantity: newQuantity,
        });
        this.cartData = response.data.data;
        this.successMessage = response.data.message
      } catch (error) {
        console.error("Lỗi cập nhật số lượng:", error);
        if (error.response && error.response.data && error.response.data.message) {
          this.errorMessage = error.response.data.message;
        } else {
          this.errorMessage = "Không thể cập nhật số lượng. Vui lòng thử lại.";
        }
      } finally {
        this.loading = false;
      }
    },
    async removeItem(cartItemId) {
      this.loading = true;
      this.errorMessage = "";
      this.successMessage = "";
      try {
        let response = await axios.delete(`http://127.0.0.1:8000/api/cart/item/${cartItemId}`);
        // Sau đó fetch lại dữ liệu:
        await this.fetchCartData();
        this.successMessage = response.data.message
      } catch (e) {
        let message = "Không thể xóa sản phẩm, vui lòng thử lại.";
        if (e.response && e.response.data && e.response.data.message) {
          message = e.response.data.message;
        } else if (e.message) {
          message = e.message;
        }
        this.errorMessage = message;
      } finally {
        this.loading = false;
      }
    }
  },
});
