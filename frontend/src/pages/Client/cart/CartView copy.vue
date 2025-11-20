<script setup>
import { onMounted, computed, watch } from "vue";
import { storeToRefs } from "pinia";
import AppLayout from "@/Layouts/AppLayout.vue";
import { useCartStore } from "@/stores/cartStore";
import { getCurrentInstance } from 'vue';
import { useRouter } from "vue-router";
const instance = getCurrentInstance();
const $toast = instance.appContext.config.globalProperties.$toast;
const cartStore = useCartStore();
const router = useRouter();
// ĐẢM BẢO IMPORT CÁC GETTERS TÍNH TOÁN TỔNG MỚI
const { 
    cartData, 
    loading, 
    formatPrice, 
    // Các Getters tính toán theo Item được chọn
    getSelectedSubtotal, 
    errorMessage,
    successMessage,
    getSelectedTotal, 
    clientOverallSumgetSelectedTotalmary 
} = storeToRefs(cartStore);

onMounted(() => {
    cartStore.fetchCartData();
    // alert(clientOverallSummary.subtotal);
    // console.log(cartData);
});
watch(errorMessage, (newError) => {
    if (newError) {
        // 1. Hiển thị toast lỗi
        $toast.error(newError, {
            position: 'top-right', // Hoặc vị trí bạn muốn
            timeout: 5000 
        });
    }else{
        
    }
});
watch(successMessage, (newSuccess) => {
    if (newSuccess) {
        $toast.success(newSuccess, {
            position: 'top-right', // Hoặc vị trí bạn muốn
            timeout: 5000 
        });
    }else{
        
    }
});

// Logic xử lý khi checkbox được click
const handleToggleItem = (item) => {
    // Gọi action trong store để gửi trạng thái ngược lại lên API
    cartStore.toggleItemSelected(item.cart_item_id, !item.is_selected);
};

// Hàm xử lý tăng/giảm số lượng
const handleUpdateQuantity = (item, delta) => {
    const newQuantity = item.quantity + delta;
    if (newQuantity >= 1) {
        cartStore.updateQuantity(item.cart_item_id, newQuantity);
    }
};

// Hàm xử lý xoá item
const handleRemoveItem = (cartItemId) => {
    if (confirm('Bạn có chắc chắn muốn xóa sản phẩm này khỏi giỏ hàng?')) {
        cartStore.removeItem(cartItemId);
    }
};

// Giả lập logic chọn phương thức vận chuyển (giữ nguyên)
const getShippingFee = (shopSummary) => {
    return shopSummary.shipping_fee; 
}

// Computed property để tính tổng số lượng sản phẩm được chọn (cho tiêu đề)
const totalSelectedItemsCount = computed(() => {
    return cartData.value.shops.reduce((count, shop) => 
        count + shop.items.filter(i => i.is_selected).length
    , 0);
});
const handleSubmitOrder = () => {
    // if (clientOverallSummary.value.subtotal === 0) {
    //     $toast.error("Giỏ hàng của bạn đang trống. Vui lòng chọn sản phẩm để đặt hàng.", { position: "top-right" });
    //     return; // Ngăn không cho chuyển trang nếu giỏ hàng trống
    // }

    // Ở đây bạn có thể thêm các bước validation khác
    // Hoặc gọi API để xác nhận giỏ hàng trước khi chuyển trang
    // Ví dụ: cartStore.confirmCartForCheckout(selectedShipping.value, selectedPayment.value);
    
    // Sau khi các bước xử lý thành công, chuyển hướng đến trang thông tin
    router.push({ name: 'checkout-information' }); // <-- Sử dụng tên route đã định nghĩa
};
const totalShopCount = computed(() => cartData.value.shops.length);

</script>

<template>
  <AppLayout>
    <div class="max-w-7xl mx-auto py-8">
      <div class="grid grid-cols-12 gap-8">
        
        <div class="col-span-12 lg:col-span-8">
          
          <div class="flex justify-between items-center mb-6 text-center">
            <div class="flex-1">
              <div class="w-10 h-10 bg-blue-600 rounded-full text-white flex items-center justify-center font-bold mx-auto mb-1">
                1
              </div>
              <p class="text-sm font-medium">Giỏ hàng</p>
            </div>
            <div class="flex-1 border-t-2 border-gray-300 mx-[-16px]"></div>
            <div class="flex-1 text-gray-500">
              <div class="w-10 h-10 bg-gray-200 rounded-full flex items-center justify-center font-bold mx-auto mb-1">
                2
              </div>
              <p class="text-sm">Thông tin</p>
            </div>
            <div class="flex-1 border-t-2 border-gray-300 mx-[-16px]"></div>
            <div class="flex-1 text-gray-500">
              <div class="w-10 h-10 bg-gray-200 rounded-full flex items-center justify-center font-bold mx-auto mb-1">
                3
              </div>
              <p class="text-sm">Thanh toán</p>
            </div>
          </div>

          <div class="mb-6">
            <h2 class="text-2xl font-bold text-gray-900">Giỏ hàng của bạn</h2>
            <p class="text-gray-500 text-sm">
                Bạn có {{ totalSelectedItemsCount }} sản phẩm từ {{ totalShopCount }} nguồn bán
            </p>
          </div>

          <div v-if="loading" class="text-center py-10 bg-white rounded-lg shadow-sm">
              <fa :icon="['fas', 'spinner']" class="animate-spin text-blue-600 text-2xl" />
              <p class="mt-2 text-gray-600">Đang tải giỏ hàng...</p>
          </div>

          <div v-else-if="!cartData.shops || cartData.shops.length == 0 " class="bg-white rounded-lg shadow-sm p-6 text-center">
              <fa :icon="['fas', 'box-open']" class="text-gray-400 text-4xl mb-3" />
              <p class="text-lg text-gray-600">Giỏ hàng của bạn đang trống.</p>
              <router-link to="/" class="mt-4 inline-block text-blue-600 hover:underline">
                Tiếp tục mua sắm
              </router-link>
          </div>
          
          <div v-else class="space-y-6">
            <div v-for="shop in cartData.shops" :key="shop.seller_id" class="bg-white rounded-lg shadow-sm p-6">
              <div class="flex justify-between items-center border-b pb-4 mb-4">
                <div class="flex items-center space-x-3">
                  <fa :icon="['fas', 'store']" class="text-blue-600" />
                  <span class="font-semibold text-gray-900">{{ shop.shop_name }}</span>
                  <div class="flex items-center space-x-1 text-yellow-500 text-sm">
                    <fa :icon="['fas', 'star']" />
                    <span>4.8</span>
                    <span class="text-gray-400 text-xs">(1.8k đánh giá)</span>
                  </div>
                </div>
                <button class="text-blue-600 hover:text-blue-700 text-sm font-medium border border-blue-200 px-3 py-1 rounded-md">
                    <fa :icon="['fas', 'comment-dots']" class="mr-1 fa-sm" />
                    Nhắn shop
                </button>
              </div>

              <div v-for="item in shop.items" :key="item.cart_item_id" class="flex space-x-4 py-4 border-b border-gray-100 last:border-b-0">
                <div class="flex items-start">
                    <input 
                        type="checkbox" 
                        :checked="item.is_selected"
                        @change="handleToggleItem(item)" 
                        class="w-5 h-5 text-blue-600 rounded mt-1 mr-3 focus:ring-blue-500" 
                    />
                    <img 
                        :src="item.image_url || 'https://via.placeholder.com/64?text=Product'" 
                        :alt="item.title" 
                        class="w-24 h-24 object-cover rounded-lg flex-shrink-0 border border-gray-200"
                    />
                </div>
                
                <div class="flex-1 min-w-0">
                    <p class="font-medium text-gray-900 truncate mb-1">{{ item.title }}</p>
                    <p class="text-red-600 font-semibold text-lg mb-1">{{ formatPrice(item.price) }}</p>
                    
                    <p class="text-xs text-gray-500 mb-2">Tình trạng: 95% như mới | BH 1 năm</p>

                    <div class="flex items-center justify-between mt-3">
                        <div class="flex items-center space-x-2 border border-gray-300 rounded-md">
                            <button @click="handleUpdateQuantity(item, -1)" 
                                :disabled="item.quantity <= 1 || loading"
                                class="w-8 h-8 text-gray-600 hover:bg-gray-100 rounded-l-md disabled:opacity-50">
                                <fa :icon="['fas', 'minus']" class="fa-xs" />
                            </button>
                            <input 
                                type="text" 
                                :value="item.quantity" 
                                class="w-8 text-center border-none p-0 text-sm focus:ring-0" 
                                readonly 
                            />
                            <button @click="handleUpdateQuantity(item, 1)" 
                                :disabled="loading"
                                class="w-8 h-8 text-gray-600 hover:bg-gray-100 rounded-r-md disabled:opacity-50">
                                <fa :icon="['fas', 'plus']" class="fa-xs" />
                            </button>
                        </div>
                        
                        <button @click="handleRemoveItem(item.cart_item_id)" :disabled="loading" class="text-gray-400 hover:text-red-600 text-sm disabled:opacity-50">
                            <fa :icon="['fas', 'trash']" class="mr-1 fa-sm" />
                            Xóa
                        </button>
                    </div>
                </div>
              </div>
              
              <div class="border-t pt-4 mt-4 space-y-3">
                <h4 class="font-semibold text-gray-900 mb-2">Phương thức vận chuyển</h4>
                
                <div class="flex justify-between items-center p-3 border border-blue-600 bg-blue-50 rounded-lg cursor-pointer">
                    <div class="flex items-center">
                        <input type="radio" name="shipping" :id="`standard-${shop.seller_id}`" checked class="text-blue-600 mr-3">
                        <label :for="`standard-${shop.seller_id}`">
                            <p class="font-medium">Giao hàng tiêu chuẩn</p>
                            <p class="text-xs text-gray-500">2-3 ngày</p>
                        </label>
                    </div>
                    <span class="font-semibold text-sm">{{ formatPrice(30000) }}</span>
                </div>

                <div class="flex justify-between items-center p-3 border rounded-lg hover:bg-blue-50/50 cursor-pointer">
                    <div class="flex items-center">
                        <input type="radio" name="shipping" :id="`pickup-${shop.seller_id}`" class="text-blue-600 mr-3">
                        <label :for="`pickup-${shop.seller_id}`">
                            <p class="font-medium">Tự đến lấy</p>
                            <p class="text-xs text-gray-500">Hôm nay</p>
                        </label>
                    </div>
                    <span class="font-semibold text-green-600 text-sm">Miễn phí</span>
                </div>
              </div>

              <div class="border-t pt-4 mt-4 text-right">
                <div class="text-sm text-gray-600 mb-1">
                    Tạm tính {{ shop.items.filter(i => i.is_selected).length }} sản phẩm: 
                    <span class="font-medium text-gray-900">
                        {{ formatPrice(getSelectedSubtotal(shop)) }}
                    </span>
                </div>
                <div class="text-sm text-gray-600 mb-1">
                    Phí vận chuyển: 
                    <span class="font-medium text-gray-900">{{ formatPrice(shop.summary.shipping_fee) }}</span>
                </div>
                <div class="text-lg font-bold text-gray-900 mt-2">
                    Tổng từ {{ shop.shop_name }}: 
                    <span class="text-red-600">
                        {{ formatPrice(shop.summary.total) }}
                    </span>
                </div>
                
                <div class="flex justify-end space-x-3 mt-4">
                    <button class="bg-blue-100 text-blue-600 hover:bg-blue-200 px-3 py-2 rounded-lg text-sm font-medium">
                        <fa :icon="['fas', 'store']" class="mr-1 fa-sm" /> Mua riêng shop
                    </button>
                    <button class="text-gray-600 hover:text-red-600 px-3 py-2 rounded-lg text-sm font-medium border border-gray-300 hover:border-red-300">
                        <fa :icon="['far', 'heart']" class="mr-1 fa-sm" /> Lưu để mua sau
                    </button>
                </div>
              </div>

            </div>
          </div>
          
        </div>
        
        <div class="col-span-12 lg:col-span-4 sticky top-24 space-y-6">
          
          <div class="bg-white rounded-lg shadow-sm p-6">
            <h3 class="text-xl font-bold text-gray-900 mb-4">Tổng đơn hàng</h3>
            
            <div class="space-y-2 text-sm text-gray-600 border-b pb-3">
                <div class="flex justify-between">
                    <span>Tạm tính ({{ totalSelectedItemsCount }} sản phẩm):</span>
                    <span class="font-medium text-gray-900">{{ formatPrice(cartData.overall_summary.subtotal) }}</span>
                </div>
                <div class="flex justify-between">
                    <span>Phí vận chuyển:</span>
                    <span class="font-medium text-gray-900">{{ formatPrice(cartData.overall_summary.shipping_fee) }}</span>
                </div>
                <div class="flex justify-between">
                    <span>Giảm giá:</span>
                    <span class="font-medium text-red-600">- {{ formatPrice(cartData.overall_summary.discount) }}</span>
                </div>
            </div>

            <div class="flex justify-between items-center text-lg font-bold pt-3">
                <span>Tổng cộng:</span>
                <span class="text-red-600">{{ formatPrice(cartData.overall_summary.total) }}</span>
            </div>
            
            <button 
            @click="handleSubmitOrder()"
                :disabled="cartData.overall_summary.subtotal === 0 || loading"
                class="w-full bg-blue-600 text-white py-3 px-4 rounded-lg transition-all font-medium hover:bg-blue-700 mt-4 disabled:opacity-50 disabled:cursor-not-allowed">
                <fa :icon="['fas', 'arrow-right']" class="mr-2 fa-sm" />
                Tiến hành đặt hàng
            </button>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>