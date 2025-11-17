import { defineStore } from 'pinia';
import axios from 'axios';
import { useAuthStore } from '@/stores/auth';

const API_URL = 'http://127.0.0.1:8000/api/admin/notifications';

export const useAdminNotificationStore = defineStore('adminNotification', {
    state: () => ({
        notifications: [],
        isLoading: false,
        error: null,
        paginationData: null
    }),

    actions: {
        /**
         * Lấy danh sách Thông báo (Tính năng 8)
         */
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
                const response = await axios.get(API_URL, config);
                
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

        /**
         * Xóa Thông báo (Tính năng 9)
         */
        async deleteNotification(id) {
            this.isLoading = true;
            this.error = null;
            const authStore = useAuthStore();
            const token = authStore.token;

            try {
                const config = { headers: { 'Authorization': `Bearer ${token}` } };
                await axios.delete(`${API_URL}/${id}`, config);
                
                // Xóa khỏi state (Nhanh hơn fetch)
                this.notifications = this.notifications.filter(noti => noti.id !== id);
                return true;
                
            } catch (err) {
                this.error = 'Lỗi khi xóa thông báo.';
                console.error('Lỗi deleteNotification:', err);
                return false;
            } finally {
                this.isLoading = false;
            }
        },

        /**
         * Tạo Thông báo mới (Tính năng 6)
         * SỬA LỖI: Tải lại danh sách sau khi tạo
         */
        async createNotification(data) {
            this.isLoading = true;
            this.error = null;
            const authStore = useAuthStore();
            const token = authStore.token;

            try {
                const config = { headers: { 'Authorization': `Bearer ${token}` } };
                const response = await axios.post(API_URL, data, config);

                if (response.data && response.data.success) {
                    // SỬA LỖI: Tải lại toàn bộ danh sách
                    await this.fetchNotifications(); 
                    return true;
                } else {
                    this.error = 'API tạo mới không trả về dữ liệu.';
                    return false;
                }
            } catch (err) {
                if (err.response && err.response.status === 422) {
                    this.error = "Dữ liệu không hợp lệ. Vui lòng kiểm tra lại (ví dụ: User ID có tồn tại?).";
                } else {
                    this.error = 'Lỗi khi tạo thông báo.';
                }
                console.error('Lỗi createNotification:', err);
                return false;
            } finally {
                this.isLoading = false;
            }
        },

        /**
         * Cập nhật Thông báo (Tính năng 7)
         * SỬA LỖI: Tải lại danh sách sau khi sửa
         */
        async updateNotification(id, data) {
            this.isLoading = true;
            this.error = null;
            const authStore = useAuthStore();
            const token = authStore.token;
            
            try {
                const config = { headers: { 'Authorization': `Bearer ${token}` } };
                const response = await axios.put(`${API_URL}/${id}`, data, config);

                if (response.data && response.data.success) {
                    // SỬA LỖI: Tải lại toàn bộ danh sách
                    await this.fetchNotifications();
                    return true;
                } else {
                     this.error = 'API cập nhật không trả về dữ liệu.';
                    return false;
                }
            } catch (err) {
                 if (err.response && err.response.status === 422) {
                    this.error = "Dữ liệu cập nhật không hợp lệ.";
                } else {
                    this.error = 'Lỗi khi cập nhật thông báo.';
                }
                console.error('Lỗi updateNotification:', err);
                return false;
            } finally {
                this.isLoading = false;
            }
        }
    }
});