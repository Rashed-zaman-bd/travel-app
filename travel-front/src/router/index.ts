import { createRouter, createWebHistory } from 'vue-router'

import DefaultLayout from '@/layouts/DefaultLayout.vue'
import AdminLayout from '@/layouts/AdminLayout.vue'

const routes = [
  // =========================================================
  // Public routes
  // =========================================================
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
        component: () => import('@/components/hero.vue'),
      },

      {
        path: 'destination',
        name: 'destination.show',
        component: () => import('@/components/destination.vue'),
      },

      {
        path: 'destination/:slug',
        name: 'destination',
        component: () => import('@/views/package/[slug].vue'),
      },

      {
        path: 'tour/:slug',
        name: 'tour',
        component: () => import('@/views/tour/[slug].vue'),
      },

      {
        path: 'tour-package/:slug',
        name: 'tour-package-details',
        component: () => import('@/views/TourPackageDetails.vue'),
      },

      {
        path: 'tour-package/:slug/book',
        name: 'tour-booking',
        component: () => import('@/views/TourBooking.vue'),
      },
    ],
  },

  // =========================================================
  // Auth
  // =========================================================
  {
    path: '/auth/callback',
    name: 'auth.callback',
    component: () => import('@/views/AuthCallbackView.vue'),
  },

  // =========================================================
  // Admin routes
  // =========================================================
  {
    path: '/admin',
    component: AdminLayout,

    children: [
      {
        path: 'dashboard',
        name: 'admin.dashboard',
        component: () => import('@/views/admin/dashboard.vue'),
        meta: {
          requiresAdmin: true,
        },
      },

      {
        path: 'user',
        name: 'admin.user',
        component: () => import('@/views/admin/user.vue'),
        meta: {
          requiresAdmin: true,
        },
      },

      {
        path: 'topbanner',
        name: 'admin.ads.topbanner',
        component: () => import('@/views/admin/ads/topbanner.vue'),
        meta: {
          requiresAdmin: true,
        },
      },

      {
        path: 'logo',
        name: 'admin.logo',
        component: () => import('@/views/admin/logo.vue'),
        meta: {
          requiresAdmin: true,
        },
      },

      {
        path: 'menu',
        name: 'admin.menu',
        component: () => import('@/views/admin/menu.vue'),
        meta: {
          requiresAdmin: true,
        },
      },

      {
        path: 'heroslider',
        name: 'admin.heroslider',
        component: () => import('@/views/admin/heroslider.vue'),
        meta: {
          requiresAdmin: true,
        },
      },

      {
        path: 'howitwork',
        name: 'admin.howitwork',
        component: () => import('@/views/admin/HowItWork.vue'),
        meta: {
          requiresAdmin: true,
        },
      },

      {
        path: 'tourpackage',
        name: 'admin.tourpackage',
        component: () => import('@/views/admin/tourpackage.vue'),
        meta: {
          requiresAdmin: true,
        },
      },

      {
        path: 'destination',
        name: 'admin.destination',
        component: () => import('@/views/admin/destination.vue'),
        meta: {
          requiresAdmin: true,
        },
      },

      {
        path: 'category',
        name: 'admin.category',
        component: () => import('@/views/admin/category.vue'),
        meta: {
          requiresAdmin: true,
        },
      },

      {
        path: 'tourhighlight',
        name: 'admin.tourhighlight',
        component: () => import('@/views/admin/TourHighlight.vue'),
        meta: {
          requiresAdmin: true,
        },
      },

      {
        path: 'touritinerary',
        name: 'admin.touritinerary',
        component: () => import('@/views/admin/TourItinerary.vue'),
        meta: {
          requiresAdmin: true,
        },
      },

      {
        path: 'tourdayactivity',
        name: 'admin.tourdayactivity',
        component: () => import('@/views/admin/TourDayActivity.vue'),
        meta: {
          requiresAdmin: true,
        },
      },

      {
        path: 'tourinformation',
        name: 'admin.tourinformatin',
        component: () => import('@/views/admin/TourInformation.vue'),
        meta: {
          requiresAdmin: true,
        },
      },

      {
        path: 'tourpriceoffer',
        name: 'admin.tourpriceoffer',
        component: () => import('@/views/admin/TourPriceOffer.vue'),
        meta: {
          requiresAdmin: true,
        },
      },

      {
        path: 'tourhotel',
        name: 'admin.tourhotel',
        component: () => import('@/views/admin/TourHotel.vue'),
        meta: {
          requiresAdmin: true,
        },
      },

      {
        path: 'offershow',
        name: 'admin.offershow',
        component: () => import('@/views/admin/OfferShow.vue'),
        meta: {
          requiresAdmin: true,
        },
      },
    ],
  },
]

// =========================================================
// Create Router
// =========================================================

const router = createRouter({
    history: createWebHistory(import.meta.env.BASE_URL),

    routes,

    // =========================================================
    // Scroll Behavior
    // =========================================================
    scrollBehavior(to, from, savedPosition) {
    // Browser Back / Forward
    if (savedPosition) {
      return {
        ...savedPosition,
        behavior: 'auto',
      }
    }

    // Hash navigation
    if (to.hash) {
      return new Promise((resolve) => {
        setTimeout(() => {
          const element = document.querySelector(to.hash)

          if (!element) {
            resolve({ top: 0 })
            return
          }

          // Height of your sticky header + some spacing
          const headerOffset = 80

          const elementPosition =
            element.getBoundingClientRect().top + window.scrollY

          resolve({
            top: elementPosition - headerOffset,
            behavior: 'smooth',
          })
        }, 300)
      })
    }

    // Normal route navigation
    return {
      top: 0,
      left: 0,
      behavior: 'auto',
    }
  },
})

// =========================================================
// Admin Authentication
// =========================================================

router.beforeEach((to, from, next) => {
  if (to.meta.requiresAdmin) {
    const userRaw = localStorage.getItem('user')

    let user: any = null

    try {
      user = userRaw ? JSON.parse(userRaw) : null
    } catch {
      user = null
    }

    const ADMIN_ROLES = ['super_admin', 'admin']

    if (!user?.role || !ADMIN_ROLES.includes(user.role)) {
      next('/')
      return
    }
  }

  next()
})

export default router