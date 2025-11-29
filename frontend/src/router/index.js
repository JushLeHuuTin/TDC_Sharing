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
import ProductManagePage from '@/pages/Client/products/ProductManagePage.vue';
import CategoriesFilterPage from '@/pages/Client/products/CategoriesFilterPage.vue';
import InformationView from '@/pages/Client/cart/InformationView.vue';
import PaymentView from '@/pages/Client/cart/PaymentPage.vue';
import CartView from '@/pages/Client/cart/CartView.vue';
import ProductWishlist from '@/pages/Client/products/ProductWishlist.vue';
// Cần import các Page khác ở đây khi chuyển đổi chúng (ví dụ: ProductView, AdminDashboard,...)


const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    // === 1. PUBLIC ROUTES (Không cần đăng nhập) ===
    {
      path: '/',
      name: 'home.index',
      component: HomePage,
      meta: { title: 'TDC_Sharing - Chợ Sinh Viên' }
    },
    {
      path: '/products/:productSlug',
      name: 'products.show',
      component: () => import('@/pages/Client/products/ProductDetail.vue'),
      meta: { title: 'Chi tiết sản phẩm' }
    },
    {
      path: '/danhmuc/:categorySlug',
      name: 'category.products',
      component: CategoriesFilterPage,
      meta: { title: 'Sản phẩm theo Danh mục' }
    },
    {
      path: '/sanpham',
      name: 'products.index',
      component: CategoriesFilterPage,
      meta: { title: 'Khám phá Sản phẩm' }
    },
    {
      path: '/login',
      name: 'login',
      component: () => import('@/pages/Client/LoginPage.vue'),
      meta: { title: 'Đăng nhập' }
    },
    {
      path: '/checkout/success',
      name: 'checkout-success',
      component: () => import('@/pages/Client/checkout/CheckoutSuccess.vue'),
    },
    {
      path: '/checkout/fail',
      name: 'checkout-fail',
      component: () => import('@/pages/Client/checkout/CheckoutFail.vue'),
    },

    // === 2. AUTHENTICATED ROUTES (Cần đăng nhập - Customer/Admin) ===
    {
      path: '/', // Dùng path gốc để nhóm các routes
      meta: { requiresAuth: true, roles: ['customer', 'admin'] },
      children: [
        {
          path: 'products/create',
          name: 'products.create',
          component: ProductCreatePage,
          meta: { title: 'Đăng bán sản phẩm' }
        },
        {
          path: 'sanpham/yeu-thich',
          name: 'products.wishlist',
          component: ProductWishlist,
          meta: { title: 'Sản phẩm yêu thích' }
        },
        {
          path: 'products/my',
          name: 'products.my',
          component: ProductManagePage,
          meta: { title: 'Sản phẩm của tôi' }
        },
        {
          path: 'cart',
          name: 'cart',
          component: CartView,
          meta: { title: 'Giỏ hàng' }
        },
        {
          path: 'checkout/information',
          name: 'checkout-information',
          component: InformationView,
          meta: { progress: 2, title: 'Thông tin thanh toán' }
        },
        {
          path: 'checkout/payment',
          name: 'checkout-payment',
          component: PaymentView,
          meta: { progress: 3, title: 'Thanh toán' }
        },
        {
          path: 'orders',
          name: 'orders.view', 
          component: () => import('@/pages/Client/orders/MySellerOrders.vue'),
          meta: { title: 'Quản lý đơn hàng (Người bán)' }
        },
      ]
    },

    // === 3. ADMIN ROUTES (Cần đăng nhập - Admin/Super Admin) ===
    {
      path: '/admin',
      component: AdminLayout,
      name: 'admin',
      redirect: '/admin/dashboard',
      meta: { requiresAuth: true, roles: ['admin', 'super_admin'], title: 'Admin' },
      children: [
        // Dashboard (Route gốc của AdminLayout)
        {
          path: 'dashboard',
          name: 'admin.dashboard',
          component: DashBoard,
          meta: { title: 'Tổng quan' }
        },

        // Quản lý danh mục
        {
          path: 'categories',
          name: 'admin.categories',
          component: CategoriesPage,
          meta: { title: 'Quản lý Danh mục' }
        },

        // Quản lý người dùng
        {
          path: 'users',
          name: 'admin.users',
          component: UsersPage,
          meta: { title: 'Quản lý Người dùng' }
        },

        // Quản lý sản phẩm
        {
          path: 'products',
          name: 'admin.products',
          component: DevelopingPage,
          meta: { title: 'Quản lý Sản phẩm' }
        },

        // Quản lý đơn hàng
        {
          path: 'orders',
          name: 'admin.orders',
          component: OrdersPage,
          meta: { title: 'Quản lý Đơn hàng' }
        },

        // Quản lý blog
        {
          path: 'blog',
          name: 'admin.blog',
          component: DevelopingPage,
          meta: { title: 'Quản lý Blog' }
        },

        // Báo cáo thống kê
        {
          path: 'reports',
          name: 'admin.reports',
          component: DevelopingPage,
          meta: { title: 'Báo cáo Thống kê' }
        },

        // Quản lý đánh giá
        {
          path: 'reviews',
          name: 'admin.reviews',
          component: DevelopingPage,
          meta: { title: 'Quản lý Đánh giá' }
        },

        // Khuyến mãi & Voucher
        {
          path: 'promotions',
          name: 'admin.promotions',
          component: PromotionsPage,
          meta: { title: 'Khuyến mãi' }
        },
        // Thông báo
        {
          path: 'notifications',
          name: 'admin.notifications',
          component: NotificationsPage,
          meta: { title: 'Thông báo Hệ thống' }
        },
        // Cài đặt hệ thống
        {
          path: 'settings',
          name: 'admin.settings',
          component: DevelopingPage,
          meta: { title: 'Cài đặt' }
        },
        // Hỗ trợ khách hàng
        {
          path: 'support',
          name: 'admin.support',
          component: DevelopingPage,
          meta: { title: 'Hỗ trợ Khách hàng' }
        },
        // Hồ sơ Admin
        {
          path: 'profile',
          name: 'admin.profile',
          component: DevelopingPage,
          meta: { title: 'Hồ sơ' }
        },
      ]
    },
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