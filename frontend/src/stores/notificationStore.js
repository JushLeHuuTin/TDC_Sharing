import { defineStore } from 'pinia';
import axios from 'axios';
import { useAuthStore } from './auth';

export const useNotificationStore = defineStore('notification', {
    state: () => ({
        notifications: [],
        unreadCount: 0,
        isLoading: false
    }),

    actions: {
        async fetchNotifications() {
            const authStore = useAuthStore();
            if (!authStore.token) return;

            this.isLoading = true;
            try {
                const response = await axios.get('http://127.0.0.1:8000/api/my-notifications', {
                    headers: { Authorization: `Bearer ${authStore.token}` }
                });
                if (response.data.success) {
                    this.notifications = response.data.data;
                    this.unreadCount = response.data.unread_count;
                }
            } catch (error) {
                console.error('Lỗi tải thông báo:', error);
            } finally {
                this.isLoading = false;
            }
        },

        async markAsRead(id) {
            const authStore = useAuthStore();
            try {
                // Cập nhật UI trước cho mượt (Optimistic update)
                const noti = this.notifications.find(n => n.id === id);
                if (noti && !noti.is_read) {
                    noti.is_read = true;
                    this.unreadCount = Math.max(0, this.unreadCount - 1);
                }

                await axios.put(`http://127.0.0.1:8000/api/my-notifications/${id}/read`, {}, {
                    headers: { Authorization: `Bearer ${authStore.token}` }
                });
            } catch (error) {
                console.error('Lỗi đánh dấu đã đọc:', error);
            }
        },

        async markAllRead() {
            const authStore = useAuthStore();
            try {
                this.notifications.forEach(n => n.is_read = true);
                this.unreadCount = 0;

                await axios.put(`http://127.0.0.1:8000/api/my-notifications/read-all`, {}, {
                    headers: { Authorization: `Bearer ${authStore.token}` }
                });
            } catch (error) {
                console.error('Lỗi đánh dấu tất cả:', error);
            }
        }
    }
});