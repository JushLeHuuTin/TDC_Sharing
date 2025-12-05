<script setup>
import { ref, computed, onMounted, onUnmounted } from "vue";
import { useAuthStore } from "@/stores/auth";
import { useNotificationStore } from "@/stores/notificationStore"; // Import Store Thông báo
import { useRouter } from "vue-router";
import logo from "@/assets/logo.png";

const router = useRouter();
const authStore = useAuthStore();
const notificationStore = useNotificationStore(); // Khởi tạo store

// 1. IMPORT COMPONENT
import SearchBar from "@/components/search-bar.vue";
const user = computed(() => authStore.user);
const isLoggedIn = computed(() => !!authStore.user);

// --- STATE CỤC BỘ ---
const isNotificationsOpen = ref(false);
const isUserMenuOpen = ref(false);

// Lấy dữ liệu từ Store thay vì Mock Data
const notifications = computed(() => notificationStore.notifications);
const unreadCount = computed(() => notificationStore.unreadCount);

// --- LIFECYCLE ---
onMounted(() => {
  if (isLoggedIn.value) {
    notificationStore.fetchNotifications(); // Tải thông báo khi load trang
    
    // Tự động tải lại mỗi 60s (Polling)
    // const interval = setInterval(() => notificationStore.fetchNotifications(), 60000);
    // onUnmounted(() => clearInterval(interval));
  }
});

// --- ACTION ---
function handleLogout() {
  authStore.logout();
}

// Xử lý khi click vào thông báo
const handleNotificationClick = async (notification) => {
  // 1. Đánh dấu đã đọc (Store sẽ cập nhật UI ngay lập tức)
  if (!notification.is_read) {
    await notificationStore.markAsRead(notification.id);
  }
  
  // 2. Điều hướng (Nếu cần) - Ví dụ:
  // if (notification.type === 'order') router.push(...)
};

const closeAllMenus = () => {
  isNotificationsOpen.value = false;
  isUserMenuOpen.value = false;
};

const toggleNotifications = () => {
  isNotificationsOpen.value = !isNotificationsOpen.value;
  isUserMenuOpen.value = false;
  if (isNotificationsOpen.value) {
      notificationStore.fetchNotifications(); // Tải lại cho mới khi mở
  }
};

// Helper: Format thời gian
const formatTime = (dateString) => {
    if (!dateString) return '';
    const date = new Date(dateString);
    const now = new Date();
    const diffSeconds = Math.floor((now - date) / 1000);

    if (diffSeconds < 60) return 'Vừa xong';
    const diffMinutes = Math.floor(diffSeconds / 60);
    if (diffMinutes < 60) return `${diffMinutes} phút trước`;
    const diffHours = Math.floor(diffMinutes / 60);
    if (diffHours < 24) return `${diffHours} giờ trước`;
    const diffDays = Math.floor(diffHours / 24);
    if (diffDays < 7) return `${diffDays} ngày trước`;
    
    return date.toLocaleDateString('vi-VN');
};

// Xử lý Logo
const navigateToHome = (event) => {
  event.preventDefault();
  if (
    window.location.host !== "localhost:5173" &&
    window.location.host !== "127.0.0.1:5173"
  ) {
    window.location.href = "http://localhost:5173/";
  } else {
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
          <a href="#" @click="navigateToHome" class="flex items-center">
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
            <div class="relative" v-click-away="closeAllMenus">
              <button
                @click="toggleNotifications"
                class="relative p-2 text-gray-600 hover:text-blue-600 transition-colors"
              >
                <fa :icon="['fas', 'bell']" class="text-lg" />
                
                <!-- Badge số lượng -->
                <span
                  v-if="unreadCount > 0"
                  class="absolute -top-1 -right-1 bg-red-500 text-white text-xs font-bold rounded-full h-5 w-5 flex items-center justify-center border-2 border-white"
                >
                  {{ unreadCount > 99 ? '99+' : unreadCount }}
                </span>
              </button>

              <!-- Menu Thông báo -->
              <div
                v-show="isNotificationsOpen"
                class="absolute right-0 mt-2 w-80 sm:w-96 bg-white rounded-xl shadow-xl border border-gray-100 py-1 z-50 overflow-hidden"
              >
                <div class="px-4 py-3 border-b flex justify-between items-center bg-gray-50">
                  <h3 class="text-sm font-bold text-gray-900">Thông báo</h3>
                  <button 
                    v-if="unreadCount > 0" 
                    @click="notificationStore.markAllRead()" 
                    class="text-xs text-blue-600 hover:text-blue-800 font-medium"
                  >
                    Đánh dấu đã đọc hết
                  </button>
                </div>

                <div class="max-h-[400px] overflow-y-auto custom-scrollbar">
                  <!-- Loading -->
                  <div v-if="notificationStore.isLoading && notifications.length === 0" class="p-4 text-center text-gray-500">
                      Đang tải...
                  </div>

                  <!-- Empty -->
                  <div v-else-if="notifications.length === 0" class="p-8 text-center text-gray-500 flex flex-col items-center">
                      <fa :icon="['far', 'bell-slash']" class="text-2xl mb-2 text-gray-300" />
                      <p class="text-sm">Bạn không có thông báo nào</p>
                  </div>

                  <!-- List -->
                  <template v-else>
                    <div
                      v-for="notification in notifications"
                      :key="notification.id"
                      @click="handleNotificationClick(notification)"
                      class="block px-4 py-3 cursor-pointer border-b border-gray-50 last:border-0 transition-colors duration-200"
                      :class="notification.is_read ? 'bg-white hover:bg-gray-50' : 'bg-blue-50 hover:bg-blue-100'"
                    >
                      <div class="flex items-start">
                          <!-- Icon Status -->
                          <div class="flex-shrink-0 mt-1 mr-3">
                             <div v-if="!notification.is_read" class="w-2 h-2 bg-blue-600 rounded-full"></div>
                             <div v-else class="w-2 h-2 bg-gray-300 rounded-full"></div>
                          </div>
                          
                          <div class="flex-1">
                              <p 
                                class="text-sm text-gray-900 leading-snug"
                                :class="{ 'font-semibold': !notification.is_read }"
                              >
                                {{ notification.content }}
                              </p>
                              <p class="text-xs text-gray-500 mt-1.5 flex items-center">
                                <fa :icon="['far', 'clock']" class="mr-1 text-[10px]" />
                                {{ formatTime(notification.created_at) }}
                              </p>
                          </div>
                      </div>
                    </div>
                  </template>
                </div>
                
                <div class="border-t px-4 py-2 bg-gray-50 text-center">
                  <a href="#" class="text-xs font-bold text-blue-600 hover:text-blue-800 uppercase tracking-wide">
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
                <!-- Avatar -->
                <img
                  :src="user?.avatar || `https://ui-avatars.com/api/?name=${user?.name || 'User'}&background=random`"
                  :alt="user?.name || 'User'"
                  class="w-8 h-8 rounded-full border border-gray-200"
                />
                <span class="hidden md:block font-medium">{{ user?.name || "Thành viên" }}</span>
                <fa :icon="['fas', 'chevron-down']" class="text-xs" />
              </button>

              <!-- Dropdown Menu -->
              <div
                v-show="isUserMenuOpen"
                class="absolute right-0 mt-2 w-56 bg-white rounded-lg shadow-xl py-2 z-50 border border-gray-100 animate__animated animate__fadeIn"
              >
                <div class="px-4 py-2 border-b border-gray-100 mb-2">
                    <p class="text-sm font-bold text-gray-900">{{ user?.name }}</p>
                    <p class="text-xs text-gray-500 truncate">{{ user?.email }}</p>
                </div>

                <a
                  href="#"
                  class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors"
                >
                  <fa :icon="['fas', 'user']" class="mr-2 w-4 text-center" />Hồ sơ cá nhân
                </a>
                <router-link
                  :to="{ name: 'products.my' }"
                  class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors"
                >
                  <fa :icon="['fas', 'box']" class="mr-2 w-4 text-center" />Sản phẩm của tôi
                </router-link>
                <router-link
                  :to="{ name: 'orders.view' }"
                  class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors"
                >
                  <fa :icon="['fas', 'shopping-cart']" class="mr-2 w-4 text-center" />Quản lý Đơn hàng
                </router-link>
                
                <div class="border-t border-gray-100 my-1"></div>
                
                <button
                  @click.prevent="handleLogout"
                  class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors"
                >
                  <fa :icon="['fas', 'sign-out-alt']" class="mr-2 w-4 text-center" />Đăng xuất
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
.custom-scrollbar::-webkit-scrollbar { width: 5px; }
.custom-scrollbar::-webkit-scrollbar-track { background: #f1f1f1; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
.custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
</style>