<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import { storeToRefs } from 'pinia';
import { useNotificationStore } from '@/stores/notificationStore';
import { useRouter } from 'vue-router';

const router = useRouter();
const notificationStore = useNotificationStore();
const { notifications, unreadCount } = storeToRefs(notificationStore);

const isOpen = ref(false);
const bellRef = ref(null);

// Toggle dropdown
const toggleDropdown = () => {
    isOpen.value = !isOpen.value;
    if (isOpen.value) {
        // Khi mở ra thì tải lại cho mới
        notificationStore.fetchNotifications();
    }
};

// Đóng khi click ra ngoài
const closeDropdown = (e) => {
    if (bellRef.value && !bellRef.value.contains(e.target)) {
        isOpen.value = false;
    }
};

onMounted(() => {
    notificationStore.fetchNotifications();
    document.addEventListener('click', closeDropdown);
    // Có thể set interval để polling thông báo mới mỗi 30s
    // setInterval(() => notificationStore.fetchNotifications(), 30000);
});

onUnmounted(() => {
    document.removeEventListener('click', closeDropdown);
});

// Xử lý khi click vào 1 thông báo
const handleNotificationClick = async (notification) => {
    // 1. Đánh dấu đã đọc
    if (!notification.is_read) {
        await notificationStore.markAsRead(notification.id);
    }
    
    // 2. Chuyển trang tùy theo loại thông báo (Ví dụ)
    if (notification.type === 'order') {
        // Giả sử nội dung có chứa mã đơn hoặc bạn lưu order_id trong data json
        // router.push('/orders'); 
    } else if (notification.type === 'promotion') {
        // router.push('/promotions');
    }
    
    // Đóng dropdown
    // isOpen.value = false; 
};

const handleMarkAllRead = () => {
    notificationStore.markAllRead();
};

// Helper format ngày
const formatTime = (dateString) => {
    const date = new Date(dateString);
    const now = new Date();
    const diffMs = now - date;
    const diffMins = Math.floor(diffMs / 60000);
    const diffHours = Math.floor(diffMins / 60);
    const diffDays = Math.floor(diffHours / 24);

    if (diffMins < 1) return 'Vừa xong';
    if (diffMins < 60) return `${diffMins} phút trước`;
    if (diffHours < 24) return `${diffHours} giờ trước`;
    if (diffDays < 7) return `${diffDays} ngày trước`;
    return date.toLocaleDateString('vi-VN');
};
</script>

<template>
    <div class="relative" ref="bellRef">
        <!-- Button Chuông -->
        <button @click="toggleDropdown" class="relative p-2 text-gray-600 hover:text-blue-600 transition-colors focus:outline-none">
            <fa :icon="['fas', 'bell']" class="text-xl" />
            
            <!-- Badge số lượng -->
            <span v-if="unreadCount > 0" class="absolute top-0 right-0 inline-flex items-center justify-center px-1.5 py-0.5 text-xs font-bold leading-none text-red-100 bg-red-600 rounded-full transform translate-x-1/4 -translate-y-1/4 border-2 border-white">
                {{ unreadCount > 99 ? '99+' : unreadCount }}
            </span>
        </button>

        <!-- Dropdown -->
        <div v-if="isOpen" class="absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-xl border border-gray-100 z-50 overflow-hidden animate__animated animate__fadeInDown animate__faster">
            <!-- Header Dropdown -->
            <div class="px-4 py-3 border-b border-gray-100 flex justify-between items-center bg-gray-50">
                <h3 class="text-sm font-bold text-gray-800">Thông báo</h3>
                <button v-if="unreadCount > 0" @click="handleMarkAllRead" class="text-xs text-blue-600 hover:underline">
                    Đánh dấu đã đọc hết
                </button>
            </div>

            <!-- List Thông báo -->
            <div class="max-h-80 overflow-y-auto custom-scrollbar">
                <div v-if="notifications.length === 0" class="p-6 text-center text-gray-500">
                    <fa :icon="['far', 'bell-slash']" class="text-2xl mb-2 text-gray-300" />
                    <p class="text-sm">Bạn không có thông báo nào.</p>
                </div>

                <div v-else>
                    <div v-for="item in notifications" :key="item.id" 
                        @click="handleNotificationClick(item)"
                        class="px-4 py-3 border-b border-gray-50 hover:bg-gray-50 cursor-pointer transition-colors relative group"
                        :class="{'bg-blue-50': !item.is_read}">
                        
                        <div class="flex items-start">
                            <!-- Icon tùy loại -->
                            <div class="flex-shrink-0 mr-3 mt-1">
                                <div v-if="item.type === 'order'" class="w-8 h-8 rounded-full bg-green-100 flex items-center justify-center text-green-600">
                                    <fa :icon="['fas', 'box']" class="text-xs" />
                                </div>
                                <div v-else-if="item.type === 'promotion'" class="w-8 h-8 rounded-full bg-purple-100 flex items-center justify-center text-purple-600">
                                    <fa :icon="['fas', 'gift']" class="text-xs" />
                                </div>
                                <div v-else-if="item.type === 'warning'" class="w-8 h-8 rounded-full bg-red-100 flex items-center justify-center text-red-600">
                                    <fa :icon="['fas', 'exclamation']" class="text-xs" />
                                </div>
                                <div v-else class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center text-blue-600">
                                    <fa :icon="['fas', 'info']" class="text-xs" />
                                </div>
                            </div>

                            <div class="flex-1">
                                <p class="text-sm text-gray-800 leading-snug" :class="{'font-semibold': !item.is_read}">
                                    {{ item.content }}
                                </p>
                                <p class="text-xs text-gray-400 mt-1 flex items-center">
                                    <fa :icon="['far', 'clock']" class="mr-1 text-[10px]" />
                                    {{ formatTime(item.created_at) }}
                                </p>
                            </div>

                            <!-- Chấm xanh chưa đọc -->
                            <div v-if="!item.is_read" class="w-2 h-2 bg-blue-600 rounded-full mt-2 ml-2"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer Dropdown -->
            <div class="px-4 py-2 border-t border-gray-100 bg-gray-50 text-center">
                <router-link to="/notifications" class="text-xs text-blue-600 hover:text-blue-800 font-medium block py-1">
                    Xem tất cả
                </router-link>
            </div>
        </div>
    </div>
</template>

<style scoped>
.custom-scrollbar::-webkit-scrollbar { width: 4px; }
.custom-scrollbar::-webkit-scrollbar-track { background: #f1f1f1; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
</style>