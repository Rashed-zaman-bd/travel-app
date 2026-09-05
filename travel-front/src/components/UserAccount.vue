<template>
  <div>
    <button
      type="button"
      class="flex items-center gap-2 text-base font-medium text-gray-700 transition hover:text-blue-600"
      @click="openModal('login')"
    >
      <font-awesome-icon :icon="['far', 'circle-user']" class="text-lg" />
      <span>Login</span>
    </button>

    <Teleport to="body">
      <div 
        v-if="isModalOpen" 
        class="fixed inset-0 z-[99999] flex items-center justify-center bg-black/50 p-4 backdrop-blur-sm"
        @click.self="closeModal"
      >
        <div class="relative w-full max-w-md overflow-hidden rounded-2xl bg-white p-8 shadow-2xl sm:p-10">
          
          <button 
            type="button" 
            class="absolute right-4 top-4 text-gray-400 transition hover:text-gray-600"
            @click="closeModal"
          >
            ✕
          </button>

          <template v-if="authMode === 'login'">
            <div class="text-center">
              <h1 class="text-2xl font-bold text-gray-900 sm:text-3xl">Please Sign in</h1>
              <p class="mt-2 text-sm text-gray-500">You need to Sign in first to continue</p>
            </div>

            <div class="mt-8 grid grid-cols-2 gap-3">
              <button
                type="button"
                class="flex items-center justify-center gap-2 rounded-lg bg-gray-100 py-3 text-sm font-semibold text-gray-700 transition hover:bg-gray-200"
                @click="handleGoogleSignIn"
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
                @click="handleFacebookSignIn"
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

            <form @submit.prevent="handleLogin" class="space-y-4">
              <div>
                <label for="login-email" class="block text-xs font-semibold text-gray-700">Email</label>
                <input
                  id="login-email"
                  v-model="loginEmail"
                  type="email"
                  required
                  placeholder="example@email.com"
                  class="mt-1.5 w-full rounded-lg bg-gray-50 px-4 py-3 text-sm text-gray-800 placeholder-gray-400 transition focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                />
              </div>

              <div>
                <label for="login-password" class="block text-xs font-semibold text-gray-700">Password</label>
                <input
                  id="login-password"
                  v-model="loginPassword"
                  type="password"
                  required
                  placeholder="Your password"
                  class="mt-1.5 w-full rounded-lg bg-gray-50 px-4 py-3 text-sm text-gray-800 placeholder-gray-400 transition focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                />
              </div>

              <div class="flex justify-end pt-1">
                <a href="/forgot-password" class="text-xs font-medium text-blue-500 transition hover:underline">
                  Forgot Password?
                </a>
              </div>

              <button
                type="submit"
                :disabled="isLoading"
                class="w-full rounded-lg bg-blue-500 py-3 text-sm font-semibold text-white shadow-md transition hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-50"
              >
                <span v-if="!isLoading">Sign In</span>
                <span v-else>Signing in...</span>
              </button>
            </form>

            <p class="mt-6 text-center text-xs text-gray-600">
              Don't have an account ?
              <button 
                type="button" 
                class="font-semibold text-blue-500 transition hover:underline"
                @click="authMode = 'register'"
              >
                Sign Up
              </button>
            </p>
          </template>

          <template v-else>
            <div class="text-center">
              <h1 class="text-2xl font-bold text-gray-900 sm:text-3xl">Let's Get Started</h1>
              <p class="mt-2 text-sm text-gray-500">Create an account and get the Deals & Promotions news</p>
            </div>

            <div class="mt-8 grid grid-cols-2 gap-3">
              <button
                type="button"
                class="flex items-center justify-center gap-2 rounded-lg bg-gray-100 py-3 text-sm font-semibold text-gray-700 transition hover:bg-gray-200"
                @click="handleGoogleSignIn"
              >
                <svg class="h-5 w-5" viewBox="0 0 24 24">
                  <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                  <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                  <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z"/>
                  <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z"/>
                </svg>
                <span>Google</span>
              </button>

              <button
                type="button"
                class="flex items-center justify-center gap-2 rounded-lg bg-gray-100 py-3 text-sm font-semibold text-gray-700 transition hover:bg-gray-200"
                @click="handleFacebookSignIn"
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

            <form @submit.prevent="handleRegister" class="space-y-4">
              <div>
                <label for="reg-email" class="block text-xs font-semibold text-gray-700">Email</label>
                <input
                  id="reg-email"
                  v-model="registerEmail"
                  type="email"
                  required
                  placeholder="example@email.com"
                  class="mt-1.5 w-full rounded-lg bg-gray-50 px-4 py-3 text-sm text-gray-800 placeholder-gray-400 transition focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                />
              </div>

              <div>
                <label for="reg-mobile" class="block text-xs font-semibold text-gray-700">Mobile Number</label>
                <div class="mt-1.5 flex rounded-lg bg-gray-50 focus-within:bg-white focus-within:ring-2 focus-within:ring-blue-500">
                  <div class="flex items-center gap-1.5 border-r border-gray-200 px-3 text-sm text-gray-700">
                    <span class="inline-block h-3.5 w-5 shrink-0 rounded-sm bg-red-600 relative overflow-hidden">
                      <span class="absolute inset-y-0 left-1 my-auto h-2 w-2 rounded-full bg-emerald-600"></span>
                    </span>
                    <select v-model="countryCode" class="bg-transparent text-xs font-semibold focus:outline-none">
                      <option value="+880">+880</option>
                      <option value="+1">+1</option>
                      <option value="+44">+44</option>
                    </select>
                  </div>
                  <input
                    id="reg-mobile"
                    v-model="registerMobile"
                    type="tel"
                    required
                    placeholder="01812-345678"
                    class="w-full rounded-r-lg bg-transparent px-4 py-3 text-sm text-gray-800 placeholder-gray-400 focus:outline-none"
                  />
                </div>
              </div>

              <div>
                <label for="reg-password" class="block text-xs font-semibold text-gray-700">Password</label>
                <input
                  id="reg-password"
                  v-model="registerPassword"
                  type="password"
                  required
                  placeholder="Your password"
                  class="mt-1.5 w-full rounded-lg bg-gray-50 px-4 py-3 text-sm text-gray-800 placeholder-gray-400 transition focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                />
              </div>

              <button
                type="submit"
                :disabled="isLoading"
                class="w-full rounded-lg bg-blue-500 py-3 text-sm font-semibold text-white shadow-md transition hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-50"
              >
                <span v-if="!isLoading">Sign Up</span>
                <span v-else>Creating account...</span>
              </button>
            </form>

            <p class="mt-6 text-center text-xs text-gray-600">
              Already have an account?
              <button 
                type="button" 
                class="font-semibold text-blue-500 transition hover:underline"
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
import { ref } from 'vue'

const isModalOpen = ref(false)
const authMode = ref<'login' | 'register'>('login')
const isLoading = ref(false)

// Login State
const loginEmail = ref('')
const loginPassword = ref('')

// Register State
const registerEmail = ref('')
const countryCode = ref('+880')
const registerMobile = ref('')
const registerPassword = ref('')

const openModal = (mode: 'login' | 'register') => {
  authMode.value = mode
  isModalOpen.value = true
}

const closeModal = () => {
  isModalOpen.value = false
}

const handleLogin = async () => {
  isLoading.value = true
  try {
    console.log('Logging in:', {
      email: loginEmail.value,
      password: loginPassword.value,
    })
  } finally {
    isLoading.value = false
  }
}

const handleRegister = async () => {
  isLoading.value = true
  try {
    console.log('Registering:', {
      email: registerEmail.value,
      phone: `${countryCode.value}${registerMobile.value}`,
      password: registerPassword.value,
    })
  } finally {
    isLoading.value = false
  }
}

const handleGoogleSignIn = () => console.log('Google Auth initiated')
const handleFacebookSignIn = () => console.log('Facebook Auth initiated')
</script>