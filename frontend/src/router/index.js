import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '@/stores/auth'; // ⬅️ Thêm Pinia Store
// 1. Import các Page Component đã được chuyển đổi
import HomePage from '@/pages/Client/HomePage.vue'
// import AdminLayout from '@/components/test/AdminLayout.vue';
import ProductPanel from '@/components/test/ProductPanel.vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import DashBoard from '@/components/Admin/Components/DashBoard.vue';
import CategoriesPage from '@/components/Admin/Components/CategoriesPage.vue';
import DevelopingPage from '@/components/Admin/Components/DevelopingPage.vue';
import UsersPage from '@/components/Admin/Components/UsersPage.vue';
import ProductsPage from '@/components/Admin/Components/ProductsPage.vue';
import OrdersPage from '@/components/Admin/Components/OrdersPage.vue'; // (Import này đã có sẵn, rất tốt)
// import BlogPage from '@/components/Admin/Components/BlogPage.vue';
// import ReportsPage from '@/components/Admin/Components/ReportsPage.vue';
import ReviewsPage from '@/components/Admin/Components/ReviewsPage.vue';
import PromotionsPage from '@/components/Admin/Components/PromotionsPage.vue';
import NotificationsPage from '@/components/Admin/Components/NotificationsPage.vue';
// import SettingsPage from '@/components/Admin/Components/SettingsPage.vue';
// import SupportPage from '@/components/Admin/Components/SupportPage.vue';
// import ProfilePage from '@/components/Admin/Components/ProfilePage.vue';
// import CategoriesPage from '@/components/Admin/Components/CategoriesPage.vue';
import ProductCreatePage from '@/pages/Client/products/ProductCreatePage.vue';
// Cần import các Page khác ở đây khi chuyển đổi chúng (ví dụ: ProductView, AdminDashboard,...)

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'home',
      component: HomePage,
      meta: { title: 'TDC_Sharing - Chợ Sinh Viên' }
    },
    // Thêm các Route khác ở đây
    // Ví dụ cho trang chi tiết sản phẩm:
    {
      path: '/products/:id',
      name: 'products.show',
      component: () => import('@/pages/Client/ProductView.vue'),
      meta: { title: 'Chi tiết sản phẩm' }
    },
    // Truy cập trang admin
    {
      path: '/login',
      name: 'login',
      component: () => import('@/pages/Client/LoginPage.vue'),
      meta: { title: 'admin' }
    },
    {
      path: '/products/create',
      name: 'products.create', // ⬅️ Tên route bạn sử dụng trong code
      component: ProductCreatePage,
      meta: { 
          title: 'Đăng bán sản phẩm',
          requiresAuth: true, 
          roles: ['customer', 'admin'] 
      }
    },
    {
      path: '/orders',
      name: 'orders.view', // ⬅️ Tên route bạn sử dụng trong code
      component: () => import('@/pages/Client/orders/MySellerOrders.vue'),
      meta: { 
          title: 'Đăng bán sản phẩm',
          requiresAuth: true, 
          roles: ['customer', 'admin'] 
      }
    },
    {
      path: '/admin',
      component: AdminLayout,
      // Đặt tên cho route chính (sử dụng trong watch của AdminLayout)
      name: 'admin',
      redirect: '/admin/dashboard', 
      meta: { requiresAuth: true, roles: ['admin', 'super_admin']},
      children: [
        // Dashboard (Route gốc của AdminLayout)
        {
          path: 'dashboard',
          name: 'admin.dashboard',
          component: DashBoard
        },

        // Quản lý danh mục
        {
          path: 'categories',
          name: 'admin.categories',
          component: CategoriesPage
        },

        // // Quản lý người dùng
        {
          path: 'users',
          name: 'admin.users',
          component: UsersPage
        },

        // // Quản lý sản phẩm
        {
          path: 'products',
          name: 'admin.products',
          // Sử dụng DevelopingPage nếu chưa có component chính thức
          component: DevelopingPage
        },

        // Quản lý đơn hàng
        {
          path: 'orders',
          name: 'admin.orders',
          component: OrdersPage // <--- TÔI ĐÃ SỬA DÒNG NÀY CHO BẠN
        },

        // Quản lý blog
        {
          path: 'blog',
          name: 'admin.blog',
          component: DevelopingPage
        },

        // Báo cáo thống kê
        {
          path: 'reports',
          name: 'admin.reports',
          component: DevelopingPage
        },

        // Quản lý đánh giá
        {
          path: 'reviews',
          name: 'admin.reviews',
          component: DevelopingPage
        },

        // Khuyến mãi & Voucher
        {
          path: 'promotions',
          name: 'admin.promotions',
          component: PromotionsPage
        },
        // Thông báo
        {
          path: 'notifications',
          name: 'admin.notifications',
          component: NotificationsPage
        },
        // Cài đặt hệ thống
        {
          path: 'settings',
          name: 'admin.settings',
          component: DevelopingPage
        },
        // Hỗ trợ khách hàng
        {
          path: 'support',
          name: 'admin.support',
          component: DevelopingPage
        },
        // Hồ sơ Admin
        {
          path: 'profile',
          name: 'admin.profile',
          component: DevelopingPage
        },
      ]
    }
  ]
})


router.beforeEach((to, from, next) => {
    document.title = to.meta.title || 'StudentMarket';

    const authStore = useAuthStore();
    const userRole = authStore.user?.role; // Lấy role hiện tại
    const userIsLoggedIn = authStore.isLoggedIn;
    
    // --- BƯỚC 1: Xử lý Route Bắt buộc đăng nhập ---
     if (to.meta.requiresAuth && !userIsLoggedIn) {
         // Nếu chưa đăng nhập, chuyển hướng đến trang Login
        next({ name: 'login', query: { redirect: to.fullPath } });
         return;
     }
    const requiredRoles = to.meta.roles;

    if (requiredRoles && userIsLoggedIn) {
        if (!requiredRoles.includes(userRole)) {
          
            next({ name: 'home' }); 
            return;
        }
    }
    if ((to.name === 'login' || to.name === 'register') && userIsLoggedIn) {
        if (userRole === 'admin' || userRole === 'super_admin') {
            next({ name: 'admin.dashboard' }); 
        } else {
            next({ name: 'home' }); 
        }
        return;
    }
    next();
});

export default router;