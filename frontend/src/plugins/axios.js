import axios from 'axios';
import { useAuthStore } from '@/stores/auth';

// Tự gắn token cho mọi request
axios.interceptors.request.use(
    (config) => {
        const auth = useAuthStore();

        if (auth.token) {
            config.headers.Authorization = `Bearer ${auth.token}`;
        }

        return config;
    },
    (error) => Promise.reject(error)
);

export default axios;
