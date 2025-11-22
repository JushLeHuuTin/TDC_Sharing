<script setup>
import { useCartStore } from '@/stores/cartStore';
import { storeToRefs } from 'pinia';
import { useRouter } from 'vue-router';

const router = useRouter();
const cartStore = useCartStore();
const { successMessage, errorMessage, lastAddedItem } = storeToRefs(cartStore);

// Hàm format tiền tệ (cần thiết nếu chưa có global helper)
const formatPrice = (price) => {
    if (!price) return '0₫';
    return parseFloat(price).toLocaleString('vi-VN') + '₫';
};
</script>

<template>
    <div 
        class="fixed top-5 right-5 z-50 transition-all duration-500 ease-in-out"
        :class="{
            'translate-x-0 opacity-100': successMessage || errorMessage,
            'translate-x-full opacity-0': !successMessage && !errorMessage
        }"
    >
        
        <div 
            v-if="successMessage && lastAddedItem" 
            class="max-w-xs w-full bg-white shadow-xl rounded-lg p-4 border-t-4 border-green-500 flex flex-col space-y-3"
            role="alert"
        >
            <div class="flex items-center space-x-3">
                <i class="fas fa-check-circle text-green-500 text-xl"></i>
                <h4 class="font-bold text-green-700 text-lg">{{ successMessage }}</h4>
            </div>

            <div class="flex space-x-3 border-t pt-3 border-gray-100">
                <img 
                    :src="lastAddedItem.product.image_url || 'default-image.jpg'" 
                    :alt="lastAddedItem.product.title"
                    class="w-16 h-16 object-cover rounded-md flex-shrink-0"
                />
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-gray-900 truncate">{{ lastAddedItem.product.title }}</p>
                    <p class="text-xs text-gray-500">Số lượng: {{ lastAddedItem.quantity }}</p>
                    <p class="text-sm font-semibold text-red-600">{{ formatPrice(lastAddedItem.price) }}</p>
                </div>
            </div>
            
            <button 
                class="w-full bg-green-500 text-white text-sm font-medium py-2 rounded-lg hover:bg-green-600 transition-colors"

                @click="router.push({ name: 'cart' })"
            >
                Xem Giỏ Hàng
            </button>
        </div>
        
        <div 
            v-if="errorMessage && !successMessage" 
            class="max-w-xs w-full bg-white shadow-xl rounded-lg p-4 border-t-4 border-red-500 flex items-start space-x-3"
            role="alert"
        >
        <fa :icon="['fas', 'exclamation-triangle']" class="text-red-500 text-xl flex-shrink-0 mt-0.5" />
            <div class="flex-1">
                <h4 class="font-bold text-red-700 text-lg">Lỗi Thao Tác</h4>
                <p class="text-sm text-gray-700">{{ errorMessage }}</p>
            </div>
            <button @click="errorMessage = ''" class="text-gray-400 hover:text-gray-600 flex-shrink-0">
               <fa :icon="['fas', 'times']" class="fa-sm" />
            </button>
        </div>
    </div>
</template>