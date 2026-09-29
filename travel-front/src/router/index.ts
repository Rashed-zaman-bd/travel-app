import { createRouter, createWebHistory } from 'vue-router'
import DefaultLayout from '@/layouts/DefaultLayout.vue'
import AdminLayout from '@/layouts/AdminLayout.vue'
import Heroslider from '@/views/admin/heroslider.vue'

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
        path: 'hero',
        name: 'hero',
        component: () => import ('@/components/hero.vue')
      },
      {
        path: '/destination/:slug',
        name: 'destination',
        component: () => import('@/views/destination/[slug].vue'),
      },
      {
        path: '/tour/:slug',
        name: 'tour',
        component: () => import('@/views/tour/[slug].vue'),
      }
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
      },
      {
        path: 'topbanner',
        name: 'admin.ads.topbanner',
        component: () => import('@/views/admin/ads/topbanner.vue'),
        meta: { requiresAdmin: true },
      },
      {
        path: 'logo',
        name: 'admin.logo',
        component: () => import('@/views/admin/logo.vue'),
        meta: { requiresAdmin: true },
      },
      {
        path: 'menu',
        name: 'admin.menu',
        component: () => import('@/views/admin/menu.vue'),
        meta: { requiresAdmin: true },
      },
      {
        path: 'heroslider',
        name: 'admin.heroslider',
        component: () => import('@/views/admin/heroslider.vue'),
        meta: { requiresAdmin: true },
      },
      {
        path: 'howitwork',
        name: 'admin.howitwork',
        component: () => import('@/views/admin/HowItWork.vue'),
        meta: { requiresAdmin: true },
      },
      {
        path: 'destination',
        name: 'admin.destination',
        component: () => import('@/views/admin/destination.vue'),
        meta: { requiresAdmin: true },
      },
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