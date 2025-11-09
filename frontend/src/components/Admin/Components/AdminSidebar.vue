<script setup>
import { RouterLink, useRoute } from 'vue-router'; // ⬅️ Import RouterLink và useRoute

// const emit = defineEmits(['navigate']);

// Dữ liệu menuItems (Đã chuyển thành route paths)
const menuItems = [
    { name: 'Dashboard', route: '/admin', page: 'dashboard', icon: ['fas', 'tachometer-alt'] },
    { name: 'Quản lý người dùng', route: '/admin/users', page: 'users', icon: ['fas', 'users'] },
    { name: 'Quản lý sản phẩm', route: '/admin/products', page: 'products', icon: ['fas', 'box'] },
    { name: 'Quản lý danh mục', route: '/admin/categories', page: 'categories', icon: ['fas', 'tags'] },
    { name: 'Quản lý đơn hàng', route: '/admin/orders', page: 'orders', icon: ['fas', 'shopping-cart'] },
    { name: 'Quản lý blog', route: '/admin/blog', page: 'blog', icon: ['fas', 'blog'] },
    { name: 'Báo cáo thống kê', route: '/admin/reports', page: 'reports', icon: ['fas', 'chart-bar'] },
    { name: 'Quản lý đánh giá', route: '/admin/reviews', page: 'reviews', icon: ['fas', 'star'] },
    { name: 'Khuyến mãi & Voucher', route: '/admin/promotions', page: 'promotions', icon: ['fas', 'percentage'] },
    { name: 'Thông báo', route: '/admin/notifications', page: 'notifications', icon: ['fas', 'bell'] },
    { name: 'Cài đặt hệ thống', route: '/admin/settings', page: 'settings', icon: ['fas', 'cog'] },
    { name: 'Hỗ trợ khách hàng', route: '/admin/support', page: 'support', icon: ['fas', 'headset'] },
    { name: 'Hồ sơ admin', route: '/admin/profile', page: 'profile', icon: ['fas', 'user-circle'], class: 'text-warning' },
    { name: 'Đăng xuất', route: '/logout', page: 'logout', icon: ['fas', 'sign-out-alt'], class: 'text-danger' },
];

// const handleClick = (pageName) => {
//     emit('navigate', pageName); 
// };

</script>

<template>
    <nav class="sidebar me-2"> 
        <div class="p-4 d-flex flex-column h-100">
            <div class="d-flex align-items-center mb-4">
                <div class="bg-white rounded p-2 me-3">
                    <fa :icon="['fas', 'graduation-cap']" class="text-primary" />
                </div>
                <div>
                    <h5 class="text-white mb-0">StudentMarket</h5>
                    <small class="text-white-50">Admin Panel</small>
                </div>
            </div>
            
            <ul class="nav flex-column flex-grow-1">
                <li v-for="item in menuItems" :key="item.page" class="nav-item">
                    <RouterLink 
                        :to="item.route" 
                        :class="['nav-link', item.class]" 
                        @click="handleClick(item.page)"
                    >
                        <fa :icon="item.icon" />{{ item.name }}
                    </RouterLink>
                </li>
            </ul>
        </div>
    </nav>
</template>

<style scoped>
/* ... (CSS Sidebar từ AdminLayout gốc) ... */

/* Thêm lại các CSS đã chuyển từ AdminLayout */
.sidebar {
    position: fixed;
    top: 0;
    left: 0;
    width: 280px;
    min-height: 100vh;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
    z-index: 1000;
    transition: left 0.3s ease;
}

.sidebar .nav-link {
    color: rgba(255, 255, 255, 0.8);
    padding: 12px 20px;
    border-radius: 8px;
    margin: 2px 10px;
    transition: all 0.3s ease;
}

.sidebar .nav-link:hover {
    background-color: rgba(255, 255, 255, 0.1);
    color: white;
    transform: translateX(5px);
}

/* Kích hoạt class active bằng Vue Router's 'router-link-active' */
.sidebar .nav-link.router-link-active {
    background-color: rgba(255, 255, 255, 0.2);
    color: white;
    font-weight: 600;
}

.sidebar .nav-link > svg {
    width: 20px;
    text-align: center;
    margin-right: 10px;
}

@media (max-width: 768px) {
    .sidebar {
        left: -280px; 
    }
    .sidebar.show {
        left: 0;
    }
}
</style>