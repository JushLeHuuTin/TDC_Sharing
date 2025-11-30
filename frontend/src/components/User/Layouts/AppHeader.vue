<script setup>
import { ref, computed } from "vue";
import { useAuthStore } from "@/stores/auth";
// Cần import useRoute và useRouter để xử lý logic Logo phức tạp (nếu cần)
import { useRouter } from "vue-router";
import logo from "@/assets/logo.png";
// Giả định v-click-away đã được đăng ký toàn cục (hoặc import tại đây)
// import { vClickAway } from '@vueuse/components'; // ví dụ

const router = useRouter(); // Khai báo Router
const authStore = useAuthStore();

function handleLogout() {
  authStore.logout(); // ⬅️ GỌI TRỰC TIẾP HÀM TỪ STORE
}

// 1. IMPORT COMPONENT
import SearchBar from "@/components/search-bar.vue";
const user = computed(() => authStore.user);
const isLoggedIn = computed(() => !!authStore.user);

// --- STATE CỤC BỘ ---
const isNotificationsOpen = ref(false);
const isUserMenuOpen = ref(false);

// Hàm đóng tất cả menu khi click ra ngoài
const closeAllMenus = () => {
  isNotificationsOpen.value = false;
  isUserMenuOpen.value = false;
};

// --- DỮ LIỆU GIẢ/MOCK DATA CHO NOTIFICATIONS ---
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

// --- XỬ LÝ ĐỊNH TUYẾN LOGO (Giải quyết vấn đề Ngrok/Dev Host) ---
const navigateToHome = (event) => {
  // Ngăn chặn hành vi mặc định của thẻ <a> (nếu đây là <a>)
  event.preventDefault();

  // Kiểm tra Host hiện tại có phải là Host Dev Server không
  if (
    window.location.host !== "localhost:5173" &&
    window.location.host !== "127.0.0.1:5173"
  ) {
    // Nếu đang ở Ngrok/URL công khai, buộc chuyển về Host Dev Server
    window.location.href = "http://localhost:5173/";
  } else {
    // Nếu đã ở Dev Server, dùng Vue Router để điều hướng về trang chủ
    router.push({ name: "home.index" });
  }
};
</script>

<template>
  <header class="bg-white shadow-sm sticky top-0 z-40">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex justify-between items-center h-16">
        <!-- Logo -->
        <div class="flex items-center">
          <!-- SỬA: Dùng @click để kiểm soát lỗi chuyển Host -->
          <a href="#" @click="navigateToHome" class="flex items-center">
            <div
              class="w-10 h-10 bg-blue-600 rounded-lg flex items-center justify-center mr-3"
            >
              <!-- Giả định logo là một biến string/object đã import -->
              <img :src="logo" alt="Vue Logo" />
            </div>
            <span class="text-xl font-bold text-blue-600 hidden sm:block"
              >TDC_Sharing</span
            >
          </a>
        </div>

        <!-- Search Bar -->
        <div class="flex-1 max-w-2xl mx-8">
          <SearchBar />
        </div>

        <!-- Navigation -->
        <nav class="flex items-center space-x-4">
          <template v-if="!isLoggedIn">
            <!-- Hiển thị khi CHƯA ĐĂNG NHẬP -->
            <router-link
              to="/login"
              class="text-gray-700 hover:text-blue-600 px-3 py-2 rounded-md text-sm font-medium"
            >
              Đăng nhập
            </router-link>
            <router-link
              to="/register"
              class="bg-blue-600 text-white hover:bg-blue-700 px-4 py-2 rounded-md text-sm font-medium"
            >
              Đăng ký
            </router-link>
          </template>

          <template v-else>
            <!-- Hiển thị khi ĐÃ ĐĂNG NHẬP -->

            <!-- Notifications -->
            <!-- Giả định v-click-away đã được đăng ký và hoạt động -->
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
            <router-link 
            :to="{name:'products.wishlist'}"
            class="p-2 text-gray-600 hover:text-blue-600">
              <fa :icon="['fas', 'heart']" class="text-lg" />
            </router-link>
            <!-- cart -->
            <router-link
              :to="{ name: 'cart' }"
              class="relative p-2 text-gray-600 hover:text-blue-600"
            >
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
                  :to="{ name: 'products.my' }"
                  class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                >
                  <fa :icon="['fas', 'box']" class="mr-2" />Sản phẩm của tôi
                </router-link>
                <router-link
                  :to="{ name: 'orders.view' }"
                  class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                >
                  <fa :icon="['fas', 'shopping-cart']" class="mr-2" />Quản lý Đơn hàng
                </router-link>
                  <!-- <a
                    href="#"
                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                  >
                    <fa :icon="['fas', 'plus']" class="mr-2" />Đăng sản phẩm
                  </a> -->
                <a
                  href="#"
                  class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                >
                  <fa :icon="['fas', 'cog']" class="mr-2" />Cài đặt
                </a>
                <div class="border-t border-gray-100"></div>
                <!-- Sử dụng @click.prevent để ngăn chặn hành vi mặc định của form/a -->
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
