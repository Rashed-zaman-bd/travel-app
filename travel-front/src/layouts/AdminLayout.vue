<!-- layouts/AdminLayout.vue -->
<template>
  <div class="min-h-screen flex bg-gray-100">
    <!-- Sidebar -->
    <aside class="w-64 h-screen bg-gray-900 text-white flex flex-col flex-shrink-0">
      <div class="h-16 flex items-center justify-center border-b border-gray-700 flex-shrink-0">
        <h2 class="text-xl font-bold">Travel Admin</h2>
      </div>

      <nav class="flex-1 p-4 space-y-2 overflow-y-auto">
        <router-link
          to="/admin/dashboard"
          class="block px-4 py-2 rounded transition-colors hover:bg-gray-700"
          active-class="bg-gray-800 text-emerald-400 font-semibold"
        >
          <i class="bi bi-speedometer2 mr-2"></i>Dashboard
        </router-link>

        <!-- Category Menu -->
        <div>
          <button
            type="button"
            @click="toggleMenu('category')"
            class="w-full flex items-center justify-between px-4 py-2 rounded hover:bg-gray-700 transition-colors"
          >
            <span class="flex items-center gap-2">
              <i class="bi bi-folder2-open"></i>
              Menu
            </span>
            <i class="bi bi-chevron-down transition-transform" :class="{ 'rotate-180': openMenus.category }"></i>
          </button>

          <Transition
            enter-active-class="transition-all duration-200 ease-out"
            enter-from-class="opacity-0 max-h-0"
            enter-to-class="opacity-100 max-h-40"
            leave-active-class="transition-all duration-200 ease-in"
            leave-from-class="opacity-100 max-h-40"
            leave-to-class="opacity-0 max-h-0"
          >
            <div v-if="openMenus.category" class="ml-6 mt-1 space-y-1 overflow-hidden">
              <router-link
                to="/admin/category"
                class="block px-4 py-2 rounded text-sm text-gray-300 hover:bg-gray-700 hover:text-white transition-colors"
                active-class="bg-gray-800 text-emerald-400 font-semibold"
              >
                All Categories
              </router-link>
            </div>
          </Transition>
        </div>

        <!-- Article Menu -->
        <div>
          <button
            type="button"
            @click="toggleMenu('articles')"
            class="w-full flex items-center justify-between px-4 py-2 rounded hover:bg-gray-700 transition-colors"
          >
            <span class="flex items-center gap-2">
              <i class="bi bi-newspaper"></i>
              Articles
            </span>
            <i class="bi bi-chevron-down transition-transform" :class="{ 'rotate-180': openMenus.articles }"></i>
          </button>

          <Transition
            enter-active-class="transition-all duration-200 ease-out"
            enter-from-class="opacity-0 max-h-0"
            enter-to-class="opacity-100 max-h-96"
            leave-active-class="transition-all duration-200 ease-in"
            leave-from-class="opacity-100 max-h-96"
            leave-to-class="opacity-0 max-h-0"
          >
            <div v-if="openMenus.articles" class="ml-6 mt-1 space-y-1 overflow-hidden">
              <router-link
                to="/admin/article"
                class="block px-4 py-2 rounded text-sm text-gray-300 hover:bg-gray-700 hover:text-white transition-colors"
                active-class="bg-gray-800 text-emerald-400 font-semibold"
              >
                All Articles
              </router-link>
              <router-link
                to="/admin/article/create"
                class="block px-4 py-2 rounded text-sm text-gray-300 hover:bg-gray-700 hover:text-white transition-colors"
                active-class="bg-gray-800 text-emerald-400 font-semibold"
              >
                Write New
              </router-link>
              <router-link
                to="/admin/article/pending"
                class="block px-4 py-2 rounded text-sm text-gray-300 hover:bg-gray-700 hover:text-white transition-colors"
                active-class="bg-gray-800 text-emerald-400 font-semibold"
              >
                Pending Review
              </router-link>
              <router-link
                to="/admin/tags"
                class="block px-4 py-2 rounded text-sm text-gray-300 hover:bg-gray-700 hover:text-white transition-colors"
                active-class="bg-gray-800 text-emerald-400 font-semibold"
              >
                Tags
              </router-link>
            </div>
          </Transition>
        </div>

        <!-- Advertisement Menu -->
        <div>
          <button
            type="button"
            @click="toggleMenu('ads')"
            class="w-full flex items-center justify-between px-4 py-2 rounded hover:bg-gray-700 transition-colors"
          >
            <span class="flex items-center gap-2">
              <i class="bi bi-newspaper"></i>
              Advertisment
            </span>
            <i class="bi bi-chevron-down transition-transform" :class="{ 'rotate-180': openMenus.ads }"></i>
          </button>

          <Transition
            enter-active-class="transition-all duration-200 ease-out"
            enter-from-class="opacity-0 max-h-0"
            enter-to-class="opacity-100 max-h-96"
            leave-active-class="transition-all duration-200 ease-in"
            leave-from-class="opacity-100 max-h-96"
            leave-to-class="opacity-0 max-h-0"
          >
            <div v-if="openMenus.ads" class="ml-6 mt-1 space-y-1 overflow-hidden">
              <router-link
                to="/admin/advertisement"
                class="block px-4 py-2 rounded transition-colors hover:bg-gray-700"
                active-class="bg-gray-800 text-emerald-400 font-semibold"
              >
                <i class="bi bi-chat-dots mr-2"></i>Details Page Ads
              </router-link>

              <router-link
                :to="{ name: 'admin.ads.categorypageads' }"
                class="block px-4 py-2 rounded transition-colors hover:bg-gray-700"
                active-class="bg-gray-800 text-emerald-400 font-semibold"
              >
                Category Page Ads
              </router-link>

              <router-link
                :to="{ name: 'admin.ads.frontpageads' }"
                class="block px-4 py-2 rounded transition-colors hover:bg-gray-700"
                active-class="bg-gray-800 text-emerald-400 font-semibold"
              >
                Front Page Ads
              </router-link>
            </div>
          </Transition>
        </div>

        <router-link
          to="/admin/comments"
          class="block px-4 py-2 rounded transition-colors hover:bg-gray-700"
          active-class="bg-gray-800 text-emerald-400 font-semibold"
        >
          <i class="bi bi-chat-dots mr-2"></i>Comments
        </router-link>

        <router-link
          to="/admin/opinions"
          class="block px-4 py-2 rounded transition-colors hover:bg-gray-700"
          active-class="bg-gray-800 text-emerald-400 font-semibold"
        >
          <i class="bi bi-pen mr-2"></i>Opinions
        </router-link>

        <router-link
          to="/admin/user"
          class="block px-4 py-2 rounded transition-colors hover:bg-gray-700"
          active-class="bg-gray-800 text-emerald-400 font-semibold"
        >
          <i class="bi bi-people mr-2"></i>Users
        </router-link>

        <router-link
          to="/admin/logo"
          class="block px-4 py-2 rounded transition-colors hover:bg-gray-700"
          active-class="bg-gray-800 text-emerald-400 font-semibold"
        >
          <i class="bi bi-image mr-2"></i>Logos
        </router-link>

        <router-link
          to="/admin/video"
          class="block px-4 py-2 rounded transition-colors hover:bg-gray-700"
          active-class="bg-gray-800 text-emerald-400 font-semibold"
        >
          <i class="bi bi-camera-reels-fill mr-2"></i>Video
        </router-link>
      </nav>
    </aside>

    <!-- Content -->
    <div class="flex-1 flex flex-col h-screen overflow-hidden">
      <!-- Header -->
      <header class="bg-white shadow h-16 px-6 flex items-center justify-between flex-shrink-0 sticky top-0 z-40">
        <h1 class="text-xl font-semibold">Travaisa</h1>

        <!-- Profile -->
        <div class="relative" ref="accountMenuRef">
          <button
            type="button"
            @click="isAccountMenuOpen = !isAccountMenuOpen"
            class="flex items-center gap-2 bg-gray-100 hover:bg-gray-200 px-3 py-2 rounded-lg"
          >
            <img
              v-if="user?.avatar"
              :src="user.avatar"
              alt="Avatar"
              class="w-9 h-9 rounded-full object-cover border"
            >
            <i v-else class="bi bi-person-circle text-3xl"></i>

            <span class="font-medium">{{ user?.name || 'Admin' }}</span>
            <i class="bi bi-chevron-down text-xs transition-transform" :class="{ 'rotate-180': isAccountMenuOpen }"></i>
          </button>

          <!-- Dropdown -->
          <Transition
            enter-active-class="transition duration-150"
            enter-from-class="opacity-0 scale-95"
            enter-to-class="opacity-100 scale-100"
            leave-active-class="transition duration-100"
            leave-from-class="opacity-100 scale-100"
            leave-to-class="opacity-0 scale-95"
          >
            <div
              v-if="isAccountMenuOpen"
              class="absolute right-0 mt-2 w-60 bg-white rounded-lg shadow-lg border overflow-hidden z-50"
            >
              <div class="px-4 py-3 border-b">
                <p class="font-semibold">{{ user?.name || 'Admin' }}</p>
                <p class="text-xs text-gray-500">{{ user?.email }}</p>
              </div>

              <router-link
                to="/admin/profile"
                class="block px-4 py-2 hover:bg-gray-100"
                @click="isAccountMenuOpen = false"
              >
                <i class="bi bi-person mr-2"></i>Profile
              </router-link>

              <button
                type="button"
                :disabled="isLoggingOut"
                @click="handleLogout"
                class="w-full text-left px-4 py-2 hover:bg-red-50 text-red-600 disabled:opacity-50"
              >
                <i class="bi bi-box-arrow-right mr-2"></i>
                {{ isLoggingOut ? 'Logging out...' : 'Logout' }}
              </button>
            </div>
          </Transition>
        </div>
      </header>

      <!-- Page -->
      <main class="flex-1 overflow-y-auto p-6">
        <router-view />
      </main>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive, onMounted, onBeforeUnmount } from 'vue'
import router from '@/router'
import api from '@/services/api'
import Swal from 'sweetalert2'

interface AuthUser {
  name?: string
  email?: string
  phone?: string
  role?: string
  avatar?: string | null
}

const user = ref<AuthUser | null>(null)

const loadUser = () => {
  const raw = localStorage.getItem('user')
  user.value = raw ? JSON.parse(raw) : null
}

onMounted(loadUser)

// Sidebar accordions
const openMenus = reactive({
  category: false,
  articles: false,
  ads: false,
})

const toggleMenu = (key: keyof typeof openMenus) => {
  openMenus[key] = !openMenus[key]
}

// Account dropdown
const isAccountMenuOpen = ref(false)
const accountMenuRef = ref<HTMLElement | null>(null)

const handleClickOutside = (event: MouseEvent) => {
  if (accountMenuRef.value && !accountMenuRef.value.contains(event.target as Node)) {
    isAccountMenuOpen.value = false
  }
}

onMounted(() => {
  document.addEventListener('click', handleClickOutside)
})

onBeforeUnmount(() => {
  document.removeEventListener('click', handleClickOutside)
})

// Logout
const isLoggingOut = ref(false)

const handleLogout = async () => {
  isLoggingOut.value = true
  try {
    await api.post('/logout')
  } catch {
    // even if the API call fails (e.g. token already invalid), proceed with local cleanup
  } finally {
    localStorage.removeItem('apiToken')
    localStorage.removeItem('user')
    isLoggingOut.value = false
    isAccountMenuOpen.value = false

    Swal.fire({
      icon: 'success',
      title: 'Logged out',
      timer: 1500,
      showConfirmButton: false,
    })

    router.push('/')
  }
}
</script>