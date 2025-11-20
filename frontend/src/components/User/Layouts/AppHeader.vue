<script setup>
import { ref, computed } from "vue";
import { useAuthStore } from "@/stores/auth";
import logo from "@/assets/logo.png";
const authStore = useAuthStore();

function handleLogout() {
  authStore.logout(); // ⬅️ GỌI TRỰC TIẾP HÀM TỪ STORE
}

// 1. IMPORT COMPONENT
// Giả định component SearchBar nằm trong src/components/
import SearchBar from "@/components/search-bar.vue";
const user = computed(() => authStore.user);
const isLoggedIn = computed(() => !!authStore.user);
// --- PROPS ---

// --- STATE CỤC BỘ THAY THẾ ALPINE.JS ---
const isNotificationsOpen = ref(false);
const isUserMenuOpen = ref(false);

// Hàm đóng tất cả menu khi click ra ngoài (được gọi từ template)
const closeAllMenus = () => {
  isNotificationsOpen.value = false;
  isUserMenuOpen.value = false;
};

// --- DỮ LIỆU GIẢ/MOCK DATA CHO NOTIFICATIONS ---
// Trong ứng dụng thực tế, dữ liệu này sẽ được lấy từ API
const userNotifications = ref([
  {
    id: 1,
    message: 'Sản phẩm "iPhone 13" của bạn đã được bán!',
    isRead: false,
    time: "1 phút trước",
  },
  {
    id: 2,
    message: "Bạn có tin nhắn mới từ Nguyễn Văn A.",
    isRead: true,
    time: "3 giờ trước",
  },
]);

const unreadNotificationsCount = computed(() => {
  return userNotifications.value.filter((n) => !n.isRead).length;
});

// --- XỬ LÝ ĐỊNH TUYẾN ---
const getRoute = (name) => {
  // Thay thế bằng logic Vue Router (ví dụ: router.push({ name: name }))
  const routes = {
    "home.index": "/",
    "auth.login": "/login",
    "auth.register": "/register",
    // Thêm các routes khác
  };
  return routes[name] || "#";
};
</script>

<template>
  <header class="bg-white shadow-sm sticky top-0 z-40">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex justify-between items-center h-16">
        <!-- Logo -->
        <div class="flex items-center">
          <a :href="getRoute('home.index')" class="flex items-center">
            <div
              class="w-10 h-10 bg-blue-600 rounded-lg flex items-center justify-center mr-3"
            >
              <img :src="logo" alt="Vue Logo" />
            </div>
            <span class="text-xl font-bold text-blue-600 hidden sm:block"
              >TDC_Sharing</span
            >
          </a>
        </div>

        <!-- Search Bar -->
        <!-- Thay thế @include('components.search-bar') bằng component SearchBar -->
        <div class="flex-1 max-w-2xl mx-8">
          <SearchBar />
        </div>

        <!-- Navigation -->
        <nav class="flex items-center space-x-4">
          <!-- Logic @auth / @else được thay bằng v-if / v-else -->
          <template v-if="!isLoggedIn">
            <!-- Hiển thị khi CHƯA ĐĂNG NHẬP -->
            <router-link
              :user="user"
              to="/login"
              class="text-gray-700 hover:text-blue-600 px-3 py-2 rounded-md text-sm font-medium"
            >
              Đăng nhập
            </router-link>
            <router-link
              :user="user"
              to="/register"
              class="bg-blue-600 text-white hover:bg-blue-700 px-4 py-2 rounded-md text-sm font-medium"
            >
              Đăng ký
            </router-link>
          </template>

          <template v-else>
            <!-- Hiển thị khi ĐÃ ĐĂNG NHẬP -->

            <!-- Notifications -->
            <div class="relative" v-click-away="closeAllMenus">
              <button
                @click="
                  isNotificationsOpen = !isNotificationsOpen;
                  isUserMenuOpen = false;
                "
                class="relative p-2 text-gray-600 hover:text-blue-600"
              >
                <fa :icon="['fas', 'bell']" class="text-lg" />
                <span
                  v-if="unreadNotificationsCount > 0"
                  class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center"
                >
                  {{ unreadNotificationsCount }}
                </span>
              </button>

              <!-- Menu Thông báo -->
              <div
                v-show="isNotificationsOpen"
                class="absolute right-0 mt-2 w-80 bg-white rounded-md shadow-lg py-1 z-50"
              >
                <div class="px-4 py-2 border-b">
                  <h3 class="text-sm font-semibold text-gray-900">Thông báo</h3>
                </div>
                <div class="max-h-64 overflow-y-auto">
                  <template v-if="userNotifications.length > 0">
                    <a
                      v-for="notification in userNotifications"
                      :key="notification.id"
                      href="#"
                      class="block px-4 py-3 hover:bg-gray-50"
                      :class="{ 'bg-blue-50': !notification.isRead }"
                    >
                      <p class="text-sm text-gray-900">{{ notification.message }}</p>
                      <p class="text-xs text-gray-500 mt-1">{{ notification.time }}</p>
                    </a>
                  </template>
                  <div v-else class="px-4 py-3 text-sm text-gray-500 text-center">
                    Không có thông báo nào
                  </div>
                </div>
                <div class="border-t px-4 py-2">
                  <a href="#" class="text-sm text-blue-600 hover:text-blue-800">
                    Xem tất cả
                  </a>
                </div>
              </div>
            </div>

            <!-- Favorites -->
            <a href="#" class="p-2 text-gray-600 hover:text-blue-600">
              <fa :icon="['fas', 'heart']" class="text-lg" />
            </a>
            <!-- cart -->
           <router-link :to="{ name: 'cart' }" class="relative p-2 text-gray-600 hover:text-blue-600">
            <fa :icon="['fas', 'shopping-cart']" class="text-lg" />
            </router-link>

            <!-- User Menu -->
            <div class="relative" v-click-away="closeAllMenus">
              <button
                @click="
                  isUserMenuOpen = !isUserMenuOpen;
                  isNotificationsOpen = false;
                "
                class="flex items-center space-x-2 text-gray-700 hover:text-blue-600"
              >
                <!-- Giả định user object có avatar và name -->
                <img
                  :src="user?.avatar || 'https://ui-avatars.com/api/?name=tin'"
                  :alt="user?.name || 'User'"
                  class="w-8 h-8 rounded-full"
                />
                <span class="hidden md:block">{{ user?.name || "tin" }}</span>
                <fa :icon="['fas', 'chevron-down']" class="text-xs" />
              </button>

              <!-- Dropdown Menu -->
              <div
                v-show="isUserMenuOpen"
                class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50"
              >
                <a
                  href="#"
                  class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                >
                  <fa :icon="['fas', 'user']" class="mr-2" />Hồ sơ
                </a>
                <router-link
                  to="/products/my"
                  class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                >
                  <fa :icon="['fas', 'box']" class="mr-2" />Sản phẩm của tôi
                </router-link>
                <a
                  href="#"
                  class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                >
                  <fa :icon="['fas', 'plus']" class="mr-2" />Đăng sản phẩm
                </a>
                <a
                  href="#"
                  class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                >
                  <fa :icon="['fas', 'cog']" class="mr-2" />Cài đặt
                </a>
                <div class="border-t border-gray-100"></div>
                <!-- Thay thế form POST bằng hàm Vue handleLogout -->
                <button
                  @click.prevent="handleLogout"
                  type="submit"
                  class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                >
                  <fa :icon="['fas', 'sign-out-alt']" class="mr-2" />Đăng xuất
                </button>
              </div>
            </div>
          </template>
        </nav>
      </div>
    </div>
  </header>
</template>

<style scoped>
/* Không cần style bổ sung nếu chỉ dùng Tailwind CSS */
</style>
