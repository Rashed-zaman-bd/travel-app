<template>
  <div>
    <!-- Logged OUT: show Login button -->
    <button
      v-if="!user"
      type="button"
      class="flex items-center gap-2 text-base font-medium text-gray-700 transition hover:text-blue-600 cursor-pointer"
      @click="openModal('login')"
    >
      <font-awesome-icon :icon="['far', 'circle-user']" class="text-lg" />
      <span>Login</span>
    </button>

   <!-- Logged IN: show avatar + name + dropdown -->
<div v-else class="relative" ref="accountMenuRef">
  <button
    type="button"
    ref="accountBtnRef"
    @click="toggleAccountMenu"
    class="flex items-center gap-2 px-3 py-2 rounded-lg"
  >
    <img
      v-if="user?.avatar"
      :src="user.avatar"
      alt="Avatar"
      class="w-7 h-7 rounded-full object-cover border"
    >
    <i v-else class="bi bi-person-circle text-2xl"></i>

    <span class="text-base font-medium">{{ user?.name || 'Admin' }}</span>
    <i class="bi bi-chevron-down text-xs transition-transform" :class="{ 'rotate-180': isAccountMenuOpen }"></i>
  </button>

  <Teleport to="body">
    <!-- MOBILE: centered popup with backdrop -->
    <Transition
      enter-active-class="transition duration-200"
      enter-from-class="opacity-0"
      enter-to-class="opacity-100"
      leave-active-class="transition duration-150"
      leave-from-class="opacity-100"
      leave-to-class="opacity-0"
    >
      <div
        v-if="isAccountMenuOpen && isMobile"
        class="fixed inset-0 z-[99999] flex items-center justify-center bg-black/50 p-4"
        @click.self="closeAccountMenu"
      >
        <Transition
          appear
          enter-active-class="transition duration-200"
          enter-from-class="opacity-0 scale-95"
          enter-to-class="opacity-100 scale-100"
        >
          <div
            ref="accountDropdownRef"
            class="w-full max-w-xs overflow-hidden rounded-2xl bg-white shadow-2xl"
          >
            <div class="flex items-center gap-3 px-5 py-4 border-b">
              <img
                v-if="user?.avatar"
                :src="user.avatar"
                alt="Avatar"
                class="w-10 h-10 rounded-full object-cover border"
              >
              <i v-else class="bi bi-person-circle text-3xl"></i>
              <div class="min-w-0">
                <p class="font-semibold truncate">{{ user?.name || 'Admin' }}</p>
                <p class="text-xs text-gray-500 truncate">{{ user?.email }}</p>
              </div>
            </div>

            <router-link
              to="/admin/profile"
              class="block px-5 py-3 text-sm hover:bg-gray-50"
              @click="closeAccountMenu"
            >
              <i class="bi bi-person mr-2"></i>Profile
            </router-link>

            <button
              type="button"
              :disabled="isLoggingOut"
              @click="handleLogout"
              class="w-full text-left px-5 py-3 text-sm hover:bg-red-50 text-red-600 disabled:opacity-50 border-t"
            >
              <i class="bi bi-box-arrow-right mr-2"></i>
              {{ isLoggingOut ? 'Logging out...' : 'Logout' }}
            </button>

            <button
              type="button"
              class="w-full px-5 py-3 text-sm text-gray-500 hover:bg-gray-50 border-t"
              @click="closeAccountMenu"
            >
              Cancel
            </button>
          </div>
        </Transition>
      </div>
    </Transition>

    <!-- DESKTOP: anchored dropdown -->
    <Transition
      enter-active-class="transition duration-150"
      enter-from-class="opacity-0 scale-95"
      enter-to-class="opacity-100 scale-100"
      leave-active-class="transition duration-100"
      leave-from-class="opacity-100 scale-100"
      leave-to-class="opacity-0 scale-95"
    >
      <div
        v-if="isAccountMenuOpen && !isMobile"
        ref="accountDropdownRef"
        class="fixed w-60 bg-white rounded-lg shadow-lg border overflow-hidden z-[99999]"
        :style="menuStyle"
      >
        <div class="px-4 py-3 border-b">
          <p class="font-semibold">{{ user?.name || 'Admin' }}</p>
          <p class="text-xs text-gray-500">{{ user?.email }}</p>
        </div>

        <router-link
          to="/admin/profile"
          class="block px-4 py-2 hover:bg-gray-100"
          @click="closeAccountMenu"
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
  </Teleport>
</div>

    <Teleport to="body">
      <div
        v-if="isModalOpen"
        class="fixed inset-0 z-[99999] flex items-center justify-center bg-black/50 p-4 backdrop-blur-sm"
        @click.self="closeModal"
      >
        <div class="relative w-full max-w-md overflow-hidden rounded-2xl bg-white p-8 shadow-2xl sm:p-10">

          <button
            type="button"
            class="absolute right-4 top-4 text-gray-400 transition hover:text-gray-600 cursor-pointer"
            @click="closeModal"
          >
            ✕
          </button>

          <!-- LOGIN VIEW -->
          <template v-if="authMode === 'login'">
            <div class="text-center">
              <h1 class="text-2xl font-bold text-gray-900 sm:text-3xl">Please Sign in</h1>
              <p class="mt-2 text-sm text-gray-500">You need to Sign in first to continue</p>
            </div>

            <div class="mt-8 grid grid-cols-2 gap-3">
              <button
                type="button"
                class="flex items-center justify-center gap-2 rounded-lg bg-gray-100 py-3 text-sm font-semibold text-gray-700 transition hover:bg-gray-200"
              >
                <svg class="h-5 w-5" viewBox="0 0 24 24">
                  <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                  <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                  <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z"/>
                  <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z"/>
                </svg>
                <span>Google</span>
              </button>

              <button
                type="button"
                class="flex items-center justify-center gap-2 rounded-lg bg-gray-100 py-3 text-sm font-semibold text-gray-700 transition hover:bg-gray-200"
              >
                <svg class="h-5 w-5 text-[#1877F2]" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                </svg>
                <span>Facebook</span>
              </button>
            </div>

            <div class="relative my-6 flex items-center justify-center">
              <div class="w-full border-t border-gray-200"></div>
              <span class="absolute bg-white px-3 text-xs text-gray-400">Or Sign In with</span>
            </div>

            <form class="space-y-4" @submit.prevent="handleLogin">
              <p v-if="errors.general" class="rounded-lg bg-red-50 px-3 py-2 text-sm text-red-600 text-center">
                {{ errors.general }}
              </p>
              <div>
                <label class="block text-xs font-semibold text-gray-700">Email or Phone</label>
                <input
                  v-model="loginForm.login"
                  type="text"
                  required
                  placeholder="example@email.com or 01712345678"
                  class="mt-1.5 w-full rounded-lg bg-gray-50 px-4 py-3 text-sm text-gray-800 placeholder-gray-400 transition focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                />
                <p v-if="errors.login" class="text-red-500 text-sm mt-1">{{ errors.login }}</p>
              </div>

              <div>
                <label class="block text-xs font-semibold text-gray-700">Password</label>
                <input
                  v-model="loginForm.password"
                  type="password"
                  required
                  placeholder="Your password"
                  class="mt-1.5 w-full rounded-lg bg-gray-50 px-4 py-3 text-sm text-gray-800 placeholder-gray-400 transition focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                />
                <p v-if="errors.password" class="text-red-500 text-sm mt-1">{{ errors.password }}</p>
              </div>

              <div class="flex justify-end pt-1">
                <a href="#" class="text-xs font-medium text-blue-500 transition hover:underline">
                  Forgot Password?
                </a>
              </div>

              <button
                type="submit"
                :disabled="isLoading"
                class="w-full rounded-lg bg-blue-500 py-3 text-sm font-semibold text-white shadow-md transition hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-50 cursor-pointer"
              >
                <span v-if="!isLoading">Sign In</span>
                <span v-else>Signing in...</span>
              </button>
            </form>

            <p class="mt-6 text-center text-xs text-gray-600">
              Don't have an account?
              <button
                type="button"
                class="font-semibold text-blue-500 transition hover:underline cursor-pointer"
                @click="authMode = 'register'"
              >
                Sign Up
              </button>
            </p>
          </template>

          <!-- REGISTER VIEW -->
          <template v-else>
            <div class="text-center">
              <h1 class="text-2xl font-bold text-gray-900 sm:text-3xl">Let's Get Started</h1>
              <p class="mt-2 text-sm text-gray-500">Create an account and get the Deals & Promotions news</p>
            </div>

            <div class="mt-8 grid grid-cols-2 gap-3">
              <button
                type="button"
                class="flex items-center justify-center gap-2 rounded-lg bg-gray-100 py-3 text-sm font-semibold text-gray-700 transition hover:bg-gray-200"
              >
                <svg class="h-5 w-5" viewBox="0 0 24 24">
                  <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                  <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                  <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z"/>
                  <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z"/>
                </svg>
                <span>Google</span>
              </button>

              <button
                type="button"
                class="flex items-center justify-center gap-2 rounded-lg bg-gray-100 py-3 text-sm font-semibold text-gray-700 transition hover:bg-gray-200"
              >
                <svg class="h-5 w-5 text-[#1877F2]" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                </svg>
                <span>Facebook</span>
              </button>
            </div>

            <div class="relative my-6 flex items-center justify-center">
              <div class="w-full border-t border-gray-200"></div>
              <span class="absolute bg-white px-3 text-xs text-gray-400">Or Sign Up with</span>
            </div>

            <form class="space-y-4" @submit.prevent="handleRegister">
              <p v-if="errors.general" class="rounded-lg bg-red-50 px-3 py-2 text-sm text-red-600 text-center">
                {{ errors.general }}
              </p>
              <div>
                <label class="block text-xs font-semibold text-gray-700">Name</label>
                <input
                  v-model="registerForm.name"
                  type="text"
                  required
                  placeholder="Name"
                  class="mt-1.5 w-full rounded-lg bg-gray-50 px-4 py-3 text-sm text-gray-800 placeholder-gray-400 transition focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                />
              </div>
              <div>
                <label class="block text-xs font-semibold text-gray-700">Email</label>
                <input
                  v-model="registerForm.email"
                  type="email"
                  required
                  placeholder="example@email.com"
                  class="mt-1.5 w-full rounded-lg bg-gray-50 px-4 py-3 text-sm text-gray-800 placeholder-gray-400 transition focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                />
                <p v-if="errors.email" class="text-red-500 text-sm mt-1">{{ errors.email }}</p>
              </div>

              <div>
                <label class="block text-xs font-semibold text-gray-700">Mobile Number</label>
                <div class="mt-1.5 flex items-center rounded-lg bg-gray-50 focus-within:bg-white focus-within:ring-2 focus-within:ring-blue-500">
                  <div class="flex items-center gap-1.5 border-r border-gray-200 px-3 text-sm text-gray-700">
                    <span class="inline-block h-3.5 w-5 shrink-0 rounded-sm bg-green-600 relative overflow-hidden">
                      <span class="absolute inset-y-0 left-1 my-auto h-2 w-2 rounded-full bg-red-600"></span>
                    </span>
                    <span class="text-xs font-semibold">+880</span>
                  </div>
                  <input
                    v-model="registerForm.phone"
                    type="tel"
                    placeholder="01812-345678"
                    class="w-full rounded-r-lg bg-transparent px-4 py-3 text-sm text-gray-800 placeholder-gray-400 focus:outline-none"
                  />
                </div>
                <p v-if="errors.phone" class="text-red-500 text-sm mt-1">{{ errors.phone }}</p>
              </div>

              <div>
                <label class="block text-xs font-semibold text-gray-700">Password</label>
                <input
                  v-model="registerForm.password"
                  type="password"
                  required
                  placeholder="Your password"
                  class="mt-1.5 w-full rounded-lg bg-gray-50 px-4 py-3 text-sm text-gray-800 placeholder-gray-400 transition focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                />
                <p v-if="errors.password" class="text-red-500 text-sm mt-1">{{ errors.password }}</p>
              </div>

              <div>
                <label class="block text-xs font-semibold text-gray-700">Avatar</label>
                <input
                  type="file"
                  accept="image/jpeg,image/jpg,image/png"
                  @change="handleFileChange"
                  class="mt-1.5 w-full rounded-lg bg-gray-50 px-4 py-3 text-sm text-gray-800 placeholder-gray-400 transition focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                />
                <p v-if="errors.avatar" class="text-red-500 text-sm mt-1">{{ errors.avatar }}</p>

                <div v-if="avatarPreview" class="mt-3 flex justify-center">
                  <img
                    :src="avatarPreview"
                    alt="Profile Preview"
                    class="w-24 h-24 rounded-full object-cover border shadow-sm"
                  >
                </div>
              </div>

              <button
                type="submit"
                :disabled="isLoading"
                class="w-full rounded-lg bg-blue-500 py-3 text-sm font-semibold text-white shadow-md transition hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-50 cursor-pointer"
              >
                <span v-if="!isLoading">Sign Up</span>
                <span v-else>Creating account...</span>
              </button>
            </form>

            <p class="mt-6 text-center text-xs text-gray-600">
              Already have an account?
              <button
                type="button"
                class="font-semibold text-blue-500 transition hover:underline cursor-pointer"
                @click="authMode = 'login'"
              >
                Sign In
              </button>
            </p>

            <p class="mt-4 text-center text-[11px] text-gray-400">
              By Signing up you agree to the
              <a href="/terms" class="text-blue-500 hover:underline">Terms and Conditions</a>
            </p>
          </template>

        </div>
      </div>
    </Teleport>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive, onMounted, onUnmounted } from 'vue'
import { useRoute } from 'vue-router'
import router from '@/router'
import api from '@/services/api'
import Swal from 'sweetalert2'

const route = useRoute()

const isModalOpen = ref(false)
const authMode = ref<'login' | 'register'>('login')
const isLoading = ref(false)
const isMobile = ref(false)
let mql: MediaQueryList | null = null

const handleMqlChange = (e: MediaQueryListEvent | MediaQueryList) => {
  isMobile.value = e.matches
}

// --- Auth / logged-in state ---

const isAccountMenuOpen = ref(false)
const isLoggingOut = ref(false)
const accountMenuRef = ref<HTMLElement | null>(null)
const accountBtnRef = ref<HTMLElement | null>(null)
const accountDropdownRef = ref<HTMLElement | null>(null)
const menuStyle = ref<Record<string, string>>({})

const MENU_WIDTH = 240 // matches w-60

const updateMenuPosition = () => {
  const btn = accountBtnRef.value
  if (!btn) return
  const rect = btn.getBoundingClientRect()

  let left = rect.right - MENU_WIDTH
  if (left < 8) left = 8
  if (left + MENU_WIDTH > window.innerWidth - 8) {
    left = window.innerWidth - MENU_WIDTH - 8
  }

  menuStyle.value = {
    top: `${rect.bottom + 0}px`,
    left: `${left}px`,
  }
}

const toggleAccountMenu = () => {
  if (isAccountMenuOpen.value) {
    isAccountMenuOpen.value = false
    return
  }
  if (!isMobile.value) {
    updateMenuPosition()
  }
  isAccountMenuOpen.value = true
}

const closeAccountMenu = () => {
  isAccountMenuOpen.value = false
}

// click-outside now has to check BOTH the button wrapper and the teleported dropdown
const handleClickOutside = (event: MouseEvent) => {
  const target = event.target as Node
  const clickedButton = accountMenuRef.value?.contains(target)
  const clickedDropdown = accountDropdownRef.value?.contains(target)
  if (!clickedButton && !clickedDropdown) {
    isAccountMenuOpen.value = false
  }
}

const loadUserFromStorage = () => {
  const stored = localStorage.getItem('user')
  user.value = stored ? JSON.parse(stored) : null
}


onMounted(() => {
  loadUserFromStorage()
  document.addEventListener('click', handleClickOutside)
  window.addEventListener('resize', closeAccountMenu)
  window.addEventListener('scroll', closeAccountMenu, true)

  mql = window.matchMedia('(max-width: 639px)')
  handleMqlChange(mql)
  mql.addEventListener('change', handleMqlChange)
})

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside)
  window.removeEventListener('resize', closeAccountMenu)
  window.removeEventListener('scroll', closeAccountMenu, true)
  mql?.removeEventListener('change', handleMqlChange)
})

const loginForm = reactive({
  login: '',
  password: '',
})

const registerForm = reactive({
  name: '',
  email: '',
  phone: '',
  password: '',
})

interface AuthUser {
  id: number
  name: string
  email: string
  phone: string | null
  role: string
  avatar: string | null
  email_verified: boolean
  created_at: string
}

const user = ref<AuthUser | null>(null)

const errors = reactive<{
  general?: string
  login?: string
  password?: string
  name?: string
  email?: string
  phone?: string
  avatar?: string
}>({})

const clearErrors = () => {
  errors.general = undefined
  errors.login = undefined
  errors.password = undefined
  errors.name = undefined
  errors.email = undefined
  errors.phone = undefined
  errors.avatar = undefined
}

const avatarFile = ref<File | null>(null)
const avatarPreview = ref<string | null>(null)

const openModal = (mode: 'login' | 'register') => {
  authMode.value = mode
  isModalOpen.value = true
}

const closeModal = () => {
  isModalOpen.value = false
  resetForms()
}

const resetForms = () => {
  loginForm.login = ''
  loginForm.password = ''

  registerForm.name = ''
  registerForm.email = ''
  registerForm.phone = ''
  registerForm.password = ''

  avatarFile.value = null
  avatarPreview.value = null
  clearErrors()
}

const handleFileChange = (event: Event) => {
  const target = event.target as HTMLInputElement
  const file = target.files?.[0]
  errors.avatar = undefined

  if (!file) {
    avatarFile.value = null
    avatarPreview.value = null
    return
  }

  const validTypes = ['image/jpeg', 'image/jpg', 'image/png']
  if (!validTypes.includes(file.type)) {
    errors.avatar = 'Only JPG, JPEG, or PNG images are allowed.'
    target.value = ''
    return
  }

  if (file.size > 2 * 1024 * 1024) {
    errors.avatar = 'Image must be smaller than 2MB.'
    target.value = ''
    return
  }

  avatarFile.value = file
  avatarPreview.value = URL.createObjectURL(file)
}

const Toast = Swal.mixin({
  toast: true,
  position: 'top-end',
  showConfirmButton: false,
  timer: 2500,
  timerProgressBar: true,
})

const ADMIN_ROLES = ['super_admin', 'admin']

const redirectAfterAuth = (u: any) => {
  const redirect = route.query.redirect as string | undefined

  setTimeout(() => {
    if (redirect) {
      router.push(redirect)
    } else if (u?.role && ADMIN_ROLES.includes(u.role)) {
      router.push({ name: 'admin.dashboard' })
    } else {
      router.push('/')
    }
  }, 800)
}

const handleLogin = async () => {
  clearErrors()
  isLoading.value = true
  try {
    const response = await api.post('/login', loginForm)

    localStorage.setItem('apiToken', response.data.token)
    localStorage.setItem('user', JSON.stringify(response.data.user))
    user.value = response.data.user

    Toast.fire({
      icon: 'success',
      title: response.data.message || 'Login successful',
    })

    resetForms()
    closeModal()
    redirectAfterAuth(response.data.user)
  } catch (error: any) {
    if (error?.response?.status === 422) {
      const fieldErrors = error.response.data.errors || {}
      errors.login = fieldErrors.login?.[0]
      errors.password = fieldErrors.password?.[0]
    } else {
      errors.general =
        error?.response?.data?.message ||
        'কিছু একটা ভুল হয়েছে। আবার চেষ্টা করুন।'
    }
  } finally {
    isLoading.value = false
  }
}

const handleRegister = async () => {
  clearErrors()
  isLoading.value = true
  try {
    const cleanedPhone = registerForm.phone.replace(/[^0-9]/g, '')

    const formData = new FormData()
    formData.append('name', registerForm.name)
    formData.append('email', registerForm.email)
    if (cleanedPhone) formData.append('phone', cleanedPhone)
    formData.append('password', registerForm.password)
    if (avatarFile.value) formData.append('avatar', avatarFile.value)

    const response = await api.post('/register', formData, {
      headers: { 'Content-Type': 'multipart/form-data' },
    })

    localStorage.setItem('apiToken', response.data.token)
    localStorage.setItem('user', JSON.stringify(response.data.user))
    user.value = response.data.user

    Toast.fire({
      icon: 'success',
      title: response.data.message || 'Registration successful',
    })

    resetForms()
    closeModal()
    redirectAfterAuth(response.data.user)
  } catch (error: any) {
    if (error?.response?.status === 422) {
      const fieldErrors = error.response.data.errors || {}
      errors.name = fieldErrors.name?.[0]
      errors.email = fieldErrors.email?.[0]
      errors.phone = fieldErrors.phone?.[0]
      errors.password = fieldErrors.password?.[0]
      errors.avatar = fieldErrors.avatar?.[0]
    } else {
      errors.general =
        error?.response?.data?.message ||
        'কিছু একটা ভুল হয়েছে। আবার চেষ্টা করুন।'
    }
  } finally {
    isLoading.value = false
  }
}

const handleLogout = async () => {
  isLoggingOut.value = true
  try {
    await api.post('/logout')
  } catch {
    // even if the API call fails, clear local session so UI doesn't get stuck
  } finally {
    localStorage.removeItem('apiToken')
    localStorage.removeItem('user')
    user.value = null
    isAccountMenuOpen.value = false
    isLoggingOut.value = false

    Toast.fire({ icon: 'success', title: 'Logged out' })
    router.push('/')
  }
}
</script>