<template>
  <div v-if="!loading && firstBanner" class="h-10 w-full border-b border-blue-400">
    <div class="mx-auto flex h-full max-w-7xl items-center justify-center px-4">
      <img
        v-if="firstBanner.image"
        :src="firstBanner.image"
        :alt="firstBanner.title || 'Ad Banner'"
        class="h-full max-h-10 w-auto object-contain"
      />
      <span v-else class="text-sm font-medium text-white">
        {{ firstBanner.title || 'Ad Banner' }}
      </span>
    </div>
  </div>

  <header class="sticky top-0 z-50 w-full bg-white shadow-md">
    <nav class="mx-auto grid max-w-7xl grid-cols-3 items-center px-4 sm:flex sm:justify-between sm:px-6 sm:py-6 lg:px-8">

      <div class="flex items-center sm:hidden">
        <button type="button"
          class="flex h-10 w-10 items-center justify-center rounded-lg text-gray-700 hover:bg-gray-100"
          aria-label="Toggle Navigation" @click="mobileMenuOpen = !mobileMenuOpen">
          <svg v-if="!mobileMenuOpen" class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
          </svg>
          <svg v-else class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>

      <div v-if="menuLogo" class="flex shrink-0 items-center justify-center sm:justify-start">
        <a href="/" class="block">
          <img
            v-if="menuLogo.text_logo"
            :src="menuLogo.text_logo"
            class="h-5 w-auto object-contain sm:h-6"
          />
        </a>
      </div>

      <div class="flex items-center justify-end sm:flex-1 sm:justify-center">
        <ul class="hidden items-center gap-5 sm:flex lg:gap-7 xl:gap-8">
          <li
            v-for="item in navItems"
            :key="item.id"
            class="group relative"
            @mouseenter="item.children.length && (openDropdownId = item.id)"
            @mouseleave="item.children.length && (openDropdownId = null)"
          >
            <a
              v-if="!item.children.length"
              :href="item.url || '#'"
              class="whitespace-nowrap text-base font-medium text-gray-700 transition hover:text-amber-500"
            >
              {{ item.title }}
            </a>

            <button
              v-else
              type="button"
              class="flex items-center gap-1 whitespace-nowrap text-base font-medium text-gray-700 transition hover:text-amber-500"
              aria-haspopup="true"
              :aria-expanded="openDropdownId === item.id"
            >
              {{ item.title }}
              <svg class="h-4 w-4 transition-transform duration-200" :class="{ 'rotate-180': openDropdownId === item.id }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 9-7 7-7-7" />
              </svg>
            </button>

            <div
              v-if="item.children.length"
              v-show="openDropdownId === item.id"
              class="absolute left-1/2 top-full z-[9999] pt-6 -translate-x-1/2 transition-all duration-300 ease-out"
              :class="openDropdownId === item.id ? 'opacity-100 translate-y-0 pointer-events-auto' : 'opacity-0 -translate-y-2 pointer-events-none'"
            >
              <div class="w-54 overflow-hidden rounded-lg bg-white shadow-lg ring-1 ring-black/5">
                <a
                  v-for="child in item.children"
                  :key="child.id"
                  :href="child.url || '#'"
                  class="block px-5 py-3 text-base font-medium text-gray-500 transition hover:bg-amber-50 hover:text-amber-500"
                >
                  {{ child.title }}
                </a>
              </div>
            </div>
          </li>
        </ul>
      </div>

      <!-- Desktop Right Controls -->
      <div class="hidden sm:flex items-center gap-4">
        <!-- Desktop Language Switcher -->
<div class="relative">
  <button
    type="button"
    class="flex items-center gap-1 rounded-md border border-gray-200 px-3 py-1.5 text-xs font-semibold text-gray-700 transition hover:border-gray-300"
    @click="langMenuOpen = !langMenuOpen"
    @keydown.esc="langMenuOpen = false"
  >
    {{ locale === 'en' ? 'EN' : 'BN' }}
    <svg class="h-3.5 w-3.5 transition-transform duration-200" :class="{ 'rotate-180': langMenuOpen }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 9-7 7-7-7" />
    </svg>
  </button>

  <Transition name="expand">
    <div
      v-if="langMenuOpen"
      v-click-outside="() => (langMenuOpen = false)"
      class="absolute right-0 top-full z-[9999] mt-2 w-28 overflow-hidden rounded-lg bg-white shadow-lg ring-1 ring-black/5"
    >
      <button
        type="button"
        class="flex w-full items-center justify-between px-4 py-2 text-sm font-medium transition"
        :class="locale === 'en' ? 'bg-amber-50 text-amber-600' : 'text-gray-600 hover:bg-gray-50'"
        @click="selectLocale('en')"
      >
        English
        <svg v-if="locale === 'en'" class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
        </svg>
      </button>
      <button
        type="button"
        class="flex w-full items-center justify-between px-4 py-2 text-sm font-medium transition"
        :class="locale === 'bn' ? 'bg-amber-50 text-amber-600' : 'text-gray-600 hover:bg-gray-50'"
        @click="selectLocale('bn')"
      >
        বাংলা
        <svg v-if="locale === 'bn'" class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
        </svg>
      </button>
    </div>
  </Transition>
</div>

        <UserAccount />
      </div>
    </nav>

    <Transition name="expand">
      <div v-if="mobileMenuOpen" class="overflow-hidden border-t border-gray-100 bg-white px-4 pb-6 pt-4 sm:hidden">
        <ul class="flex flex-col gap-2">
          <li v-for="item in navItems" :key="item.id">
            <a
              v-if="!item.children.length"
              :href="item.url || '#'"
              class="block rounded-lg px-3 py-2 text-base font-medium text-gray-700 hover:bg-gray-50 hover:text-amber-600"
            >
              {{ item.title }}
            </a>

            <template v-else>
              <button
                type="button"
                class="flex w-full items-center justify-between rounded-lg px-3 py-2 text-base font-medium text-gray-700 hover:bg-gray-50 hover:text-amber-600"
                @click="openMobileDropdownId = openMobileDropdownId === item.id ? null : item.id"
              >
                <span>{{ item.title }}</span>
                <svg class="h-4 w-4 transition-transform duration-200" :class="{ 'rotate-180': openMobileDropdownId === item.id }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
              </button>

              <Transition name="expand">
                <div v-if="openMobileDropdownId === item.id" class="ml-4 overflow-hidden space-y-1 pt-1">
                  <a
                    v-for="child in item.children"
                    :key="child.id"
                    :href="child.url || '#'"
                    class="block rounded-md px-3 py-2 text-sm text-gray-600 hover:bg-gray-50 hover:text-amber-600"
                  >
                    {{ child.title }}
                  </a>
                </div>
              </Transition>
            </template>
          </li>

          <!-- Mobile Controls -->
          <li class="pt-3 border-t border-gray-100 flex items-center justify-between">
            <UserAccount />

            <!-- Mobile Language Switcher -->
            <div class="flex items-center overflow-hidden rounded-md border border-gray-200 p-0.5 text-xs font-semibold">
              <button
                type="button"
                class="rounded px-2.5 py-1 transition-colors"
                :class="locale === 'en' ? 'bg-amber-600 text-white' : 'text-gray-600 hover:text-amber-600'"
                @click="setLocale('en')"
              >
                EN
              </button>
              <button
                type="button"
                class="rounded px-2.5 py-1 transition-colors"
                :class="locale === 'bn' ? 'bg-amber-600 text-white' : 'text-gray-600 hover:text-amber-600'"
                @click="setLocale('bn')"
              >
                BN
              </button>
            </div>
          </li>
        </ul>
      </div>
    </Transition>
  </header>
</template>

<script setup lang="ts">
import UserAccount from '@/components/UserAccount.vue'
import { ref, onMounted, watch, computed } from 'vue'
import api from '@/services/api'
import { useI18n } from 'vue-i18n'

interface Banner {
  id: number
  title: string | null
  image: string | null
  order: number
  active: boolean
}

interface BannerResponse {
  status: boolean
  message: string
  data: Banner[]
}

interface Logo {
  id: number
  title: string | null
  text_logo: string | null
  round_logo: string | null
}

interface LogoResponse {
  message?: string
  data: Logo
}

interface NavItem {
  id: number
  title: string
  url: string | null
  order: number
  children: NavItem[]
}

interface NavItemResponse {
  status: boolean
  data: NavItem[]
}

const banners = ref<Banner[]>([])
const loading = ref(true)
const firstBanner = computed(() => banners.value[0] ?? null)

const logo = ref<Logo | null>(null)
const menuLogo = computed(() => logo.value)

const { locale } = useI18n()

function setLocale(lang: string) {
  locale.value = lang
  localStorage.setItem('locale', lang)
  document.documentElement.setAttribute('lang', lang)
}

const navItems = ref<NavItem[]>([])
const openDropdownId = ref<number | null>(null)
const openMobileDropdownId = ref<number | null>(null)

const mobileMenuOpen = ref(false)

const fetchTopBanners = async () => {
  try {
    const response = await api.get<BannerResponse>('/top-banners')
    banners.value = response.data.status ? response.data.data : []
  } catch (error) {
    console.error('Failed to load top banners:', error)
    banners.value = []
  } finally {
    loading.value = false
  }
}

const fetchLogo = async () => {
  try {
    const response = await api.get<LogoResponse>('/logo')
    logo.value = response.data.data ?? null
  } catch (error) {
    console.error('Failed to load logo:', error)
    logo.value = null
  }
}

const fetchNavItems = async () => {
  try {
    const response = await api.get<NavItemResponse>('/nav-items')
    navItems.value = response.data.data ?? []
  } catch (error) {
    console.error('Failed to load nav items:', error)
    navItems.value = []
  }
}

const langMenuOpen = ref(false)

function selectLocale(lang: string) {
  setLocale(lang)
  langMenuOpen.value = false
}

// Re-fetch anything with translated content whenever the locale changes
watch(locale, () => {
  fetchNavItems()
  // uncomment if banners/logo titles are also translated server-side
  // fetchTopBanners()
  // fetchLogo()
})

onMounted(() => {
  fetchTopBanners()
  fetchLogo()
  fetchNavItems()
})
</script>

<style scoped>
/* Smooth Expand/Collapse Animations */
.expand-enter-active,
.expand-leave-active {
  transition: max-height 0.35s cubic-bezier(0.4, 0, 0.2, 1), opacity 0.3s ease;
  max-height: 500px;
}

.expand-enter-from,
.expand-leave-to {
  max-height: 0;
  opacity: 0;
}
</style>