<script setup>
import { onMounted, computed, watch } from "vue";
import { useRoute } from "vue-router";
import { storeToRefs } from "pinia";
import { useDetailProductStore } from "@/stores/detailProductStore";
import AppLayout from "@/Layouts/AppLayout.vue";
import { useReviewStore } from "@/stores/reviewStore";
import { useCartStore } from "@/stores/cartStore";
import CartNotification from "@/components/CartNotification.vue";
const route = useRoute();

const detailProductStore = useDetailProductStore();
const reviewStore = useReviewStore();
const cartStore = useCartStore();

const { product } = storeToRefs(detailProductStore);
const mainImage = computed(() => detailProductStore.mainImage);

onMounted(() => {
  detailProductStore.fetchProduct(route.params.productSlug);
});

watch(
  () => route.params.productSlug,
  (slug) => detailProductStore.fetchProduct(slug)
);
watch(
  () => product.value,
  (newProduct) => {
    if (newProduct?.id) {
      reviewStore.fetchReviews(newProduct.id);
    }
  }
);
</script>

<template>
  <div v-if="product">
    <AppLayout>
      <CartNotification />
      <div class="max-w-7xl mx-auto">
        <!-- Breadcrumb -->
        <nav class="flex mb-6 sm:mb-0 mb-2" aria-label="Breadcrumb">
          <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li class="inline-flex items-center">
              <a href="/" class="text-gray-700 hover:text-blue-600"
                ><svg
                  class="svg-inline--fa fa-house mr-2"
                  data-prefix="fas"
                  data-icon="house"
                  role="img"
                  viewBox="0 0 512 512"
                  aria-hidden="true"
                >
                  <path
                    class=""
                    fill="currentColor"
                    d="M277.8 8.6c-12.3-11.4-31.3-11.4-43.5 0l-224 208c-9.6 9-12.8 22.9-8 35.1S18.8 272 32 272l16 0 0 176c0 35.3 28.7 64 64 64l288 0c35.3 0 64-28.7 64-64l0-176 16 0c13.2 0 25-8.1 29.8-20.3s1.6-26.2-8-35.1l-224-208zM240 320l32 0c26.5 0 48 21.5 48 48l0 96-128 0 0-96c0-26.5 21.5-48 48-48z"
                  ></path></svg
                >Trang chủ
              </a>
            </li>
            <li>
              <div class="flex items-center" style="cursor: pointer">
                <svg
                  class="svg-inline--fa fa-chevron-right text-gray-400 mx-2 fa-xs"
                  data-prefix="fas"
                  data-icon="chevron-right"
                  role="img"
                  viewBox="0 0 320 512"
                  aria-hidden="true"
                >
                  <path
                    class=""
                    fill="currentColor"
                    d="M311.1 233.4c12.5 12.5 12.5 32.8 0 45.3l-192 192c-12.5 12.5-32.8 12.5-45.3 0s-12.5-32.8 0-45.3L243.2 256 73.9 86.6c-12.5-12.5-12.5-32.8 0-45.3s32.8-12.5 45.3 0l192 192z"
                  ></path></svg
                ><span class="text-gray-900 font-medium">Tất cả sản phẩm</span>
              </div>
            </li>
            <li>
              <div class="flex items-center">
                <svg
                  class="svg-inline--fa fa-chevron-right text-gray-400 mx-2 fa-xs"
                  data-prefix="fas"
                  data-icon="chevron-right"
                  role="img"
                  viewBox="0 0 320 512"
                  aria-hidden="true"
                >
                  <path
                    class=""
                    fill="currentColor"
                    d="M311.1 233.4c12.5 12.5 12.5 32.8 0 45.3l-192 192c-12.5 12.5-32.8 12.5-45.3 0s-12.5-32.8 0-45.3L243.2 256 73.9 86.6c-12.5-12.5-12.5-32.8 0-45.3s32.8-12.5 45.3 0l192 192z"
                  ></path></svg
                ><span class="text-gray-900 font-medium">{{ product.title }}</span>
              </div>
            </li>
          </ol>
        </nav>
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
          <!-- Product Images -->
          <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow-sm overflow-hidden">
              <div class="relative">
                <img
                  :src="mainImage"
                  :alt="product.title"
                  class="w-full h-96 object-cover"
                />

                <!-- Status Badge -->
                <div class="absolute top-4 left-4">
                  <span
                    class="bg-red-500 text-white px-3 py-1 rounded-full text-sm font-medium"
                  >
                    <i class="fas fa-fire mr-1"></i>Hot
                  </span>
                </div>

                <!-- Favorite & Share Buttons -->
                <button
                  class="absolute top-4 right-4 w-10 h-10 bg-white rounded-full shadow-md flex items-center justify-center hover:bg-gray-50 transition-colors"
                >
                      <fa :icon="['fas', 'heart']"  />
                </button>
                <button
                  class="absolute top-16 right-4 w-10 h-10 bg-white rounded-full shadow-md flex items-center justify-center hover:bg-gray-50 transition-colors"
                >
                  <fa :icon="['fas', 'share-alt']"/>
                </button>
              </div>

              <!-- Thumbnail Images -->
              <div class="p-4">
                <div class="flex space-x-2 overflow-x-auto">
                  <img
                    v-for="(img, index) in product.images"
                    :key="index"
                    :src="img.path"
                    :alt="'Ảnh ' + (index + 1)"
                    class="w-20 h-16 object-cover rounded-md cursor-pointer border-2 hover:border-blue-500"
                    :class="{
                      'border-blue-500': mainImage === img,
                      'border-gray-200': mainImage !== img,
                    }"
                    @click="detailProductStore.setMainImage(img)"
                  />
                </div>
              </div>
            </div>

            <!-- Product Description -->
            <div class="bg-white rounded-lg shadow-sm p-6 mt-6">
              <h3 class="text-lg font-semibold text-gray-900 mb-4">Mô tả sản phẩm</h3>
              <div
                class="prose max-w-none text-gray-700"
                v-html="product.description"
              ></div>
            </div>

            <!-- Specifications -->
            <div class="bg-white rounded-lg shadow-sm p-6 mt-6">
              <h3 class="text-lg font-semibold text-gray-900 mb-4">Thông số kỹ thuật</h3>

              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Cột trái -->
                <div class="space-y-3">
                  <div
                    v-for="(value, key) in product.specsLeft"
                    :key="key"
                    class="flex items-start justify-between gap-4 border-b border-gray-100 pb-2"
                  >
                    <span class="text-gray-600 flex-shrink-0">{{ key }}:</span>

                    <span class="font-medium text-right break-words flex-1">
                      {{ value }}
                    </span>
                  </div>
                </div>

                <!-- Cột phải -->
                <div class="space-y-3">
                  <div
                    v-for="(value, key) in product.specsRight"
                    :key="key"
                    class="flex items-start justify-between gap-4 border-b border-gray-100 pb-2"
                  >
                    <span class="text-gray-600 flex-shrink-0">{{ key }}:</span>

                    <span
                      class="font-medium text-right break-words flex-1"
                      :class="{ 'text-green-600': key === 'Tình trạng' }"
                    >
                      {{ value }}
                    </span>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Product Info & Actions -->
          <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow-sm p-6 sticky top-24">
              <div class="mb-6">
                <div class="flex items-center space-x-3 mb-2">
                  <span class="text-3xl font-bold text-red-600">{{ product.price }}</span>
                  <span
                    v-if="product.originalPrice"
                    class="text-lg text-gray-500 line-through"
                  >
                    {{ product.originalPrice }}
                  </span>
                </div>
                <div class="flex items-center space-x-2">
                  <span
                    v-if="product.discount"
                    class="bg-red-100 text-red-800 px-2 py-1 rounded text-sm font-medium"
                  >
                    Tiết kiệm {{ product.discount }}
                  </span>
                  <span v-if="product.discount" class="text-sm text-gray-600"
                    >(-{{ product.discountPercent }}%)</span
                  >
                </div>
              </div>

              <!-- Seller Info -->
              <div class="border-t border-b border-gray-200 py-4 mb-6">
                <div class="flex items-center space-x-3 mb-3">
                  <img
                    :src="product.seller.avatar"
                    alt="Seller"
                    class="w-12 h-12 rounded-full"
                  />
                  <div>
                    <h4 class="font-semibold text-gray-900">{{ product.seller.name }}</h4>
                    <div class="flex items-center space-x-2">
                      <div class="flex items-center">
                        <i
                          class="fas fa-star text-yellow-400 text-sm"
                          v-for="i in 5"
                          :key="i"
                        ></i>
                        <!-- <span class="text-sm text-gray-600 ml-1">({{ product.seller.rating }})</span> -->
                      </div>
                      <span class="text-sm text-green-600">
                        <i class="fas fa-check-circle mr-1"></i>Đã xác thực
                      </span>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Location & Time -->
              <div class="space-y-3 mb-6">
                <div class="flex items-center text-gray-600">
                  <fa :icon="['fas', 'map-marker-alt']" class="text-red-500 mx-2 fa-sm" />
                  <span>{{ product.location ?? "Công Nghệ Thủ Đức" }}</span>
                </div>
                <div class="flex items-center text-gray-600">
                  <fa :icon="['fas', 'clock']" class="text-blue-500 mx-2 fa-sm" />
                  <span>{{ product.posted_time_ago }}</span>
                </div>
                <div class="flex items-center text-gray-600">
                  <fa :icon="['fas', 'eye']" class="text-green-500 mx-2 fa-sm" />
                  <span>{{ product.views_count }} lượt xem</span>
                </div>
              </div>

              <!-- Action Buttons -->
              <div class="space-y-3">
                <button
                  @click="cartStore.addToCart(product.id)"
                  :disabled="cartStore.loading"
                  class="w-full bg-blue-600 text-white py-3 px-4 rounded-lg transition-all font-medium hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center"
                >
                  <span v-if="cartStore.loading" class="animate-spin mr-2">
                    <fa :icon="['fas', 'spinner']" />
                  </span>

                  <fa :icon="['fas', 'shopping-basket']" class="text-white fa-sm mr-2" />
                  <span>Thêm vào giỏ hàng</span>
                </button>
                <div
                  v-if="cartStore.successMessage"
                  class="mt-3 p-3 bg-green-100 text-green-700 rounded-lg text-center animate-fade"
                >
                  {{ cartStore.successMessage }}
                </div>

                <button
                  class="w-full bg-green-600 text-white py-3 px-4 rounded-lg hover:bg-green-700 transition-colors font-medium"
                >
                  <fa :icon="['fas', 'phone-alt']" class="text-white fa-sm" />
                  Gọi điện: {{ product.seller.phone }}
                </button>
                <div class="grid grid-cols-2 gap-3">
                  <button
                    class="flex-1 border border-gray-300 text-gray-700 py-2 px-4 rounded-lg hover:bg-gray-50 transition-colors"
                  >
                    <i class="far fa-heart mr-2"></i>Yêu thích
                  </button>
                  <button
                    class="flex-1 border border-gray-300 text-gray-700 py-2 px-4 rounded-lg hover:bg-gray-50 transition-colors"
                  >
                  <fa :icon="['fas', 'share-alt']" class="mr-2" />Chia sẻ
                  </button>
                </div>
              </div>
              <!-- Safety Tips -->
              <div class="mt-6 p-4 bg-yellow-50 rounded-lg">
                <h5 class="font-medium text-yellow-800 mb-2">
                  <fa :icon="['fas', 'shield-alt']" class="mr-2" />Mẹo an toàn
                </h5>
                <ul class="text-sm text-yellow-700 space-y-1">
                  <li>• Gặp mặt tại nơi công cộng</li>
                  <li>• Kiểm tra kỹ sản phẩm trước khi mua</li>
                  <li>• Không chuyển tiền trước</li>
                </ul>
              </div>
            </div>
          </div>
          <!-- Review Summary -->
        </div>
        <div class="bg-white rounded-lg shadow-sm p-6 mt-6">
          <!-- Header -->
          <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-semibold text-gray-900">Đánh giá từ người mua</h3>
            <button class="text-blue-600 hover:text-blue-700 text-sm font-medium">
              Viết đánh giá
            </button>
          </div>

          <!-- Summary Box -->
          <div class="bg-gray-50 rounded-lg p-6 mb-6" v-if="reviewStore.formattedSummary">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <!-- Tổng điểm -->
              <div class="flex flex-col items-center justify-center">
                <div class="text-5xl font-bold text-gray-900 mb-2">
                  {{ reviewStore.formattedSummary.avg }}
                </div>

                <div class="flex items-center mb-2">
                  <i
                    v-for="i in 5"
                    :key="i"
                    class="fas fa-star text-xl"
                    :class="
                      i <= reviewStore.formattedSummary.avg
                        ? 'text-yellow-400'
                        : 'text-gray-300'
                    "
                  ></i>
                </div>

                <div class="text-gray-600 text-sm">
                  Dựa trên {{ reviewStore.formattedSummary.total }} đánh giá
                </div>
              </div>

              <!-- Breakdown -->
              <div class="space-y-2">
                <div
                  v-for="item in reviewStore.formattedSummary.breakdown"
                  :key="item.stars"
                  class="flex items-center space-x-3"
                >
                  <span class="text-sm text-gray-600 w-12"> {{ item.stars }} sao </span>

                  <div class="flex-1 bg-gray-200 rounded-full h-2">
                    <div
                      class="bg-yellow-400 h-2 rounded-full"
                      :style="`width: ${item.percentage}%`"
                    ></div>
                  </div>

                  <span class="text-sm text-gray-600 w-8">
                    {{ item.count }}
                  </span>
                </div>
              </div>
            </div>
          </div>

          <!-- Filter -->
          <div
            class="flex items-center justify-between mb-4 pb-4 border-b border-gray-200"
          >
            <div class="flex items-center space-x-2">
              <button
                class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium"
              >
                Tất cả ({{ reviewStore.summary?.total ?? 0 }})
              </button>
              <button
                class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm hover:bg-gray-200"
              >
                Có hình ảnh
              </button>
              <button
                class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm hover:bg-gray-200"
              >
                5 sao
              </button>
            </div>

            <select class="px-4 py-2 border border-gray-300 rounded-lg text-sm">
              <option>Mới nhất</option>
              <option>Hữu ích nhất</option>
              <option>Xếp hạng cao nhất</option>
              <option>Xếp hạng thấp nhất</option>
            </select>
          </div>

          <div class="space-y-6">
            <div
              v-for="review in reviewStore.filteredReviews"
              :key="review.id"
              class="border-b border-gray-100 pb-6 last:border-b-0 last:pb-0"
            >
              <!-- Reviewer Info -->
              <div class="flex items-start space-x-3 mb-3">
                <!-- Avatar -->
                <img
                  :src="review.user_avatar"
                  class="w-12 h-12 rounded-full object-cover"
                />

                <div class="flex-1">
                  <!-- Name + Verified -->
                  <div class="flex items-center space-x-2 mb-1">
                    <h4 class="font-semibold text-gray-900">
                      {{ review.user_name }}
                    </h4>

                    <span
                      v-if="review.verified"
                      class="text-xs text-green-600 bg-green-50 px-2 py-0.5 rounded flex items-center"
                    >
                      <i class="fas fa-check-circle mr-1"></i>
                      Đã mua hàng
                    </span>
                  </div>

                  <!-- Rating + Date -->
                  <div class="flex items-center space-x-3 mb-2">
                    <div class="flex items-center">
                      <i
                        v-for="i in 5"
                        :key="i"
                        class="fas fa-star text-sm"
                        :class="i <= review.rating ? 'text-yellow-400' : 'text-gray-300'"
                      ></i>
                    </div>

                    <span class="text-sm text-gray-500">{{ review.date }}</span>
                  </div>

                  <!-- Comment -->
                  <p class="text-gray-700 mb-3">{{ review.comment }}</p>

                  <!-- Review Images -->
                  <div v-if="review.images.length" class="flex space-x-2 mb-3">
                    <img
                      v-for="img in review.images"
                      :src="img"
                      class="w-20 h-20 rounded-lg object-cover cursor-pointer hover:opacity-80 transition-opacity"
                    />
                  </div>

                  <!-- Buttons -->
                  <div class="flex items-center space-x-4">
                    <button
                      class="text-sm text-gray-600 hover:text-blue-600 flex items-center space-x-1"
                    >
                      <i class="far fa-thumbs-up"></i>
                      <span>Hữu ích ({{ review.helpful_count }})</span>
                    </button>

                    <button
                      class="text-sm text-gray-600 hover:text-blue-600 flex items-center space-x-1"
                    >
                      <i class="far fa-comment"></i>
                      <span>Phản hồi</span>
                    </button>
                  </div>

                  <!-- Seller Reply -->
                  <div
                    v-if="review.seller_reply"
                    class="mt-3 ml-4 p-3 bg-blue-50 rounded-lg border-l-2 border-blue-500"
                  >
                    <div class="flex items-center space-x-2 mb-1">
                      <i class="fas fa-store text-blue-600 text-sm"></i>
                      <span class="font-medium text-gray-900 text-sm">
                        Phản hồi từ người bán
                      </span>
                    </div>

                    <p class="text-sm text-gray-700">
                      {{ review.seller_reply }}
                    </p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Related Products -->
        <div class="mt-12">
          <h3 class="text-2xl font-bold text-gray-900 mb-6">Sản phẩm tương tự</h3>

          <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <!-- <ProductCard
              v-for="p in productStore.related"
              :key="p.id"
              :product="p"
          /> -->
          </div>
        </div>
      </div>
    </AppLayout>
  </div>
  <div v-else class="text-center py-20">Loading...</div>
</template>
