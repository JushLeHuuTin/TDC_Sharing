import { createRouter, createWebHistory } from 'vue-router'

// 1. Import các Page Component đã được chuyển đổi
import HomePage from '@/pages/Client/HomePage.vue'
// Cần import các Page khác ở đây khi chuyển đổi chúng (ví dụ: ProductView, AdminDashboard,...)

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'home',
      component: HomePage,
      meta: { title: 'StudentMarket - Chợ Sinh Viên' }
    },
    // Thêm các Route khác ở đây
    // Ví dụ cho trang chi tiết sản phẩm:
    // {
    //   path: '/products/:id',
    //   name: 'products.show',
    //   component: () => import('@/pages/Client/ProductView.vue'),
    //   meta: { title: 'Chi tiết sản phẩm' }
    // }
  ]
})

// Tùy chỉnh tiêu đề trang dựa trên meta (Tùy chọn)
router.beforeEach((to, from, next) => {
    document.title = to.meta.title || 'StudentMarket';
    next();
});

export default router