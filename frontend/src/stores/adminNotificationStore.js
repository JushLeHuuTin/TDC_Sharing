import { defineStore } from 'pinia';
import axios from 'axios';
import { useAuthStore } from '@/stores/auth';

const API_BASE_URL = 'http://127.0.0.1:8000/api/admin';

export const useAdminNotificationStore = defineStore('adminNotification', {
    state: () => ({
        notifications: [],
        users: [], 
        isLoading: false,
        error: null,
        paginationData: null
    }),

    actions: {
        async fetchNotifications(filters = {}) {
            this.isLoading = true;
            this.error = null;
            const authStore = useAuthStore();
            const token = authStore.token;

            if (!token || !authStore.isAdmin) {
                this.error = 'Bạn không có quyền truy cập.';
                this.isLoading = false;
                return;
            }

            try {
                const config = {
                    headers: { 'Authorization': `Bearer ${token}` },
                    params: filters 
                };
                const response = await axios.get(`${API_BASE_URL}/notifications`, config);
                
                if (response.data && response.data.success) {
                    this.notifications = response.data.data;
                    this.paginationData = response.data.meta || null;
                }
            } catch (err) {
                this.error = 'Lỗi khi tải thông báo.';
                console.error('Lỗi fetchNotifications:', err);
            } finally {
                this.isLoading = false;
            }
        },

        // FIX: Gọi đúng API lấy danh sách user
        async fetchUsers() {
            const authStore = useAuthStore();
            try {
                const response = await axios.get(`${API_BASE_URL}/notifications/users`, {
                    headers: { 'Authorization': `Bearer ${authStore.token}` }
                });
                if (response.data.success) {
                    this.users = response.data.data;
                }
            } catch (err) {
                console.error('Lỗi lấy danh sách user:', err);
            }
        },

        async createNotification(data) {
            this.isLoading = true;
            const authStore = useAuthStore();
            try {
                const response = await axios.post(`${API_BASE_URL}/notifications`, data, {
                    headers: { 'Authorization': `Bearer ${authStore.token}` }
                });
                await this.fetchNotifications(); 
                return { success: true };
            } catch (err) {
                return { success: false, message: err.response?.data?.message || 'Lỗi tạo mới' };
            } finally {
                this.isLoading = false;
            }
        },

        async updateNotification(id, data) {
            this.isLoading = true;
            const authStore = useAuthStore();
            try {
                const response = await axios.put(`${API_BASE_URL}/notifications/${id}`, data, {
                    headers: { 'Authorization': `Bearer ${authStore.token}` }
                });
                await this.fetchNotifications();
                return { success: true };
            } catch (err) {
                return { success: false, message: err.response?.data?.message || 'Lỗi cập nhật' };
            } finally {
                this.isLoading = false;
            }
        },

        async deleteNotification(id) {
            const authStore = useAuthStore();
            try {
                await axios.delete(`${API_BASE_URL}/notifications/${id}`, {
                    headers: { 'Authorization': `Bearer ${authStore.token}` }
                });
                this.notifications = this.notifications.filter(n => n.id !== id);
                return { success: true };
            } catch (err) {
                return { success: false, message: 'Lỗi xóa' };
            }
        }
    }
});