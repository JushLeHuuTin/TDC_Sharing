// store/AddressStore.js

import { defineStore } from 'pinia';
import axios from 'axios';

export const useAddressStore = defineStore('address', {
    state: () => ({
        userAddresses: [], 
        selectedAddressId: null, 
        isLoading: false,
    }),

    actions: {
        async fetchUserAddresses() {
            this.isLoading = true;
            try {
                const response = await axios.get("http://127.0.0.1:8000/api/user/addresses");
                this.userAddresses = response.data;
                if (this.userAddresses.length > 0) {
                    const defaultAddress = this.userAddresses.find(addr => addr.is_default) || this.userAddresses[0];
                    this.selectedAddressId = defaultAddress.id;
                }
            } catch (error) {
                console.error("Lỗi khi tải địa chỉ:", error);
            } finally {
                this.isLoading = false;
            }
        },

        setSelectedAddress(addressId) {
            this.selectedAddressId = addressId;
            // Tùy chọn: Gọi API để tính lại phí vận chuyển (nếu cần)
        }
    },

    getters: {
        // Lấy địa chỉ được chọn
        selectedAddress: (state) => {
            return state.userAddresses.find(addr => addr.id === state.selectedAddressId);
        }
    }
});