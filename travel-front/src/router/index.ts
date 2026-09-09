import { createRouter, createWebHistory } from 'vue-router'
import DefaultLayout from '@/layouts/DefaultLayout.vue'
import AdminLayout from '@/layouts/AdminLayout.vue'

const routes = [
  {
    path: '/',
    component: DefaultLayout,
    children: [
      {
        path: '',
        name: 'home',
        component: () => import('@/views/HomeView.vue'),
      },
      {
        path: '/auth/callback',
        name: 'auth.callback',
        component: () => import('@/views/AuthCallbackView.vue'),
      },
    ],
  },

  {
        path: '/auth/callback',
        name: 'auth.callback',
        component: () => import('@/views/AuthCallbackView.vue'),
      },

  {
    path: '/admin',
    component: AdminLayout,
    children: [
      {
        path: 'dashboard',
        name: 'admin.dashboard',
        component: () => import('@/views/admin/dashboard.vue'),
        meta: { requiresAdmin: true },
      },
      {
        path: 'user',
        name: 'admin.user',
        component: () => import('@/views/admin/user.vue'),
        meta: {requiresAdmin: true},
      }
    ],
  },
]

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes,
})

router.beforeEach((to, from, next) => {
  if (to.meta.requiresAdmin) {
    const userRaw = localStorage.getItem('user')
    const user = userRaw ? JSON.parse(userRaw) : null
    const ADMIN_ROLES = ['super_admin', 'admin']

    if (!(user?.role && ADMIN_ROLES.includes(user.role))) {
      next('/')
      return
    }
  }
  next()
})

export default router