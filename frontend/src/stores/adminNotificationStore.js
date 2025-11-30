import { defineStore } from 'pinia';
import axios from 'axios';
import { useAuthStore } from '@/stores/auth';

const API_BASE_URL = 'http://127.0.0.1:8000/api/admin';

export const useAdminNotificationStore = defineStore('adminNotification', {
    state: () => ({
        notifications: [],
        users: [], // Danh sách user để chọn
        isLoading: false,
        error: null,
        paginationData: null
    }),

    actions: {
        async fetchNotifications(filters = {}) {
            this.isLoading = true;
            const authStore = useAuthStore();
            try {
                const response = await axios.get(`${API_BASE_URL}/notifications`, {
                    headers: { 'Authorization': `Bearer ${authStore.token}` },
                    params: filters 
                });
                if (response.data.success) {
                    this.notifications = response.data.data;
                    this.paginationData = response.data.meta;
                }
            } catch (err) {
                console.error(err);
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