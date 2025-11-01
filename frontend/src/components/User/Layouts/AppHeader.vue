<script setup>
import { ref,computed } from 'vue';

// 1. IMPORT COMPONENT
// Giả định component SearchBar nằm trong src/components/
import SearchBar from '@/components/search-bar.vue'; 

// --- PROPS ---
// Giả định thông tin người dùng được truyền vào Header từ Layout
const props = defineProps({
    user: {
        type: Object,
        default: null, // user = null nếu chưa đăng nhập
    }
});

const isLoggedIn = ref(!!props.user); // Trạng thái đăng nhập

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
    { id: 1, message: 'Sản phẩm "iPhone 13" của bạn đã được bán!', isRead: false, time: '1 phút trước' },
    { id: 2, message: 'Bạn có tin nhắn mới từ Nguyễn Văn A.', isRead: true, time: '3 giờ trước' },
]);

const unreadNotificationsCount = computed(() => {
    return userNotifications.value.filter(n => !n.isRead).length;
});

// --- XỬ LÝ ĐỊNH TUYẾN ---
const getRoute = (name) => {
    // Thay thế bằng logic Vue Router (ví dụ: router.push({ name: name }))
    const routes = {
        'home.index': '/',
        'auth.login': '/login',
        'auth.register': '/register',
        // Thêm các routes khác
    };
    return routes[name] || '#';
};

// Xử lý đăng xuất
const handleLogout = () => {
    console.log('Xử lý Đăng xuất...');
    // Gọi API đăng xuất tại đây
};
</script>

<template>
    <header class="bg-white shadow-sm sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <!-- Logo -->
                <div class="flex items-center">
                    <a :href="getRoute('home.index')" class="flex items-center">
                        <div class="w-10 h-10 bg-blue-600 rounded-lg flex items-center justify-center mr-3">
                            <i class="fas fa-graduation-cap text-white"></i>
                        </div>
                        <span class="text-xl font-bold text-blue-600 hidden sm:block">StudentMarket</span>
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
                        <a :href="getRoute('auth.login')" class="text-gray-700 hover:text-blue-600 px-3 py-2 rounded-md text-sm font-medium">
                            Đăng nhập
                        </a>
                        <a :href="getRoute('auth.register')" class="bg-blue-600 text-white hover:bg-blue-700 px-4 py-2 rounded-md text-sm font-medium">
                            Đăng ký
                        </a>
                    </template>
                    
                    <template v-else>
                        <!-- Hiển thị khi ĐÃ ĐĂNG NHẬP -->

                        <!-- Notifications -->
                        <div class="relative" v-click-away="closeAllMenus">
                            <button @click="isNotificationsOpen = !isNotificationsOpen; isUserMenuOpen = false" class="relative p-2 text-gray-600 hover:text-blue-600">
                                <i class="fas fa-bell text-lg"></i>
                                <span v-if="unreadNotificationsCount > 0" class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">
                                    {{ unreadNotificationsCount }}
                                </span>
                            </button>
                            
                            <!-- Menu Thông báo -->
                            <div v-show="isNotificationsOpen" class="absolute right-0 mt-2 w-80 bg-white rounded-md shadow-lg py-1 z-50">
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

                        <!-- Messages -->
                        <a href="#" class="relative p-2 text-gray-600 hover:text-blue-600">
                            <i class="fas fa-comments text-lg"></i>
                            <!-- Giả định count được truyền vào từ user object hoặc Vuex Store -->
                            <!-- <span v-if="props.user?.unreadMessagesCount > 0" class="absolute ...">{{ props.user.unreadMessagesCount }}</span> -->
                        </a>

                        <!-- Favorites -->
                        <a href="#" class="p-2 text-gray-600 hover:text-blue-600">
                            <i class="fas fa-heart text-lg"></i>
                        </a>

                        <!-- User Menu -->
                        <div class="relative" v-click-away="closeAllMenus">
                            <button @click="isUserMenuOpen = !isUserMenuOpen; isNotificationsOpen = false" class="flex items-center space-x-2 text-gray-700 hover:text-blue-600">
                                <!-- Giả định user object có avatar và name -->
                                <img :src="props.user?.avatar || 'https://ui-avatars.com/api/?name=tin'" :alt="props.user?.name || 'User'" class="w-8 h-8 rounded-full">
                                <span class="hidden md:block">{{ props.user?.name || 'tin' }}</span>
                                <i class="fas fa-chevron-down text-xs"></i>
                            </button>
                            
                            <!-- Dropdown Menu -->
                            <div v-show="isUserMenuOpen" class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50">
                                <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-user mr-2"></i>Hồ sơ
                                </a>
                                <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-box mr-2"></i>Sản phẩm của tôi
                                </a>
                                <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-plus mr-2"></i>Đăng sản phẩm
                                </a>
                                <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-cog mr-2"></i>Cài đặt
                                </a>
                                <div class="border-t border-gray-100"></div>
                                <!-- Thay thế form POST bằng hàm Vue handleLogout -->
                                <button @click.prevent="handleLogout" type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-sign-out-alt mr-2"></i>Đăng xuất
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