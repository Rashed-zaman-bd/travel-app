<template>
  <div class="container mx-auto px-4 py-6">
    <!-- Category nav -->
    <nav class="flex gap-2 overflow-x-auto border-b pb-3 mb-6">
      <button
        class="px-4 py-2 rounded-full whitespace-nowrap text-sm cursor-pointer"
        :class="!activeCategory ? 'bg-amber-600 text-white' : 'bg-gray-100 hover:bg-gray-200'"
        @click="selectCategory(null)"
      >
        All
      </button>

      <button
        v-for="cat in categories"
        :key="cat.id"
        class="px-4 py-2 rounded-full whitespace-nowrap text-sm cursor-pointer"
        :class="activeCategory === cat.slug ? 'bg-amber-600 text-white' : 'bg-gray-100 hover:bg-gray-200'"
        @click="selectCategory(cat.slug)"
      >
        {{ t(cat.country_name) }}
      </button>
    </nav>

    <!-- States -->
    <p v-if="loading" class="text-center text-gray-500">Loading...</p>
    <p v-else-if="error" class="text-center text-red-500">{{ error }}</p>
    <p v-else-if="!packages.length" class="text-center text-gray-500">No active packages found.</p>

    <!-- Package grid -->
    <!-- Package grid -->
        <div v-else class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
        <div
            v-for="pkg in packages"
            :key="pkg.id"
            class="flex flex-col bg-white rounded-md overflow-hidden shadow-md hover:shadow-xl transition"
        >
            <!-- Image with overlays -->
            <router-link :to="packageLink(pkg)" class="relative block h-[360px] bg-gray-200">
            <img
                v-if="pkg.package_image"
                :src="pkg.package_image"
                :alt="t(pkg.package_name)"
                class="absolute inset-0 w-full h-full object-cover"
            />

            <!-- dark gradients for readable text -->
            <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-transparent to-black/30"></div>

            <!-- Top left: duration -->
            <div
                v-if="t(pkg.package_duration)"
                class="absolute top-4 left-4 flex items-center gap-2 text-white font-semibold"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <rect x="3" y="4" width="18" height="18" rx="2" />
                <path d="M16 2v4M8 2v4M3 10h18" />
                </svg>
                <span>{{ t(pkg.package_duration) }}</span>
            </div>

            <!-- Bottom left: price + location -->
            <div class="absolute bottom-4 left-4 right-4 text-white">
                <p class="text-sm">Price starts from (per person)</p>
                <p v-if="t(pkg.package_price)" class="text-2xl font-bold leading-tight">
                BDT {{ t(pkg.package_price) }}
                </p>
                <p v-if="t(pkg.location)" class="flex items-center gap-1 text-sm mt-1">
                <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 2a7 7 0 0 0-7 7c0 5 7 13 7 13s7-8 7-13a7 7 0 0 0-7-7Zm0 9.5A2.5 2.5 0 1 1 12 6a2.5 2.5 0 0 1 0 5.5Z" />
                </svg>
                <span class="truncate">{{ t(pkg.location) }}</span>
                </p>
            </div>
            </router-link>

            <!-- Body -->
            <div class="flex flex-col flex-1 p-4">
            <router-link :to="packageLink(pkg)">
                <h3 class="text-lg font-bold text-slate-900 hover:text-[#e07300]">
                {{ t(pkg.package_name) }}
                </h3>
            </router-link>

            <p class="mt-3 text-gray-500 leading-relaxed line-clamp-5">
                {{ t(pkg.sub_header) || t(pkg.header) }}
            </p>

            <!-- Footer row -->
            <div class="mt-auto pt-4 flex items-center justify-between text-sm font-semibold text-slate-800">
                <span class="flex items-center gap-1.5">
                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="9" />
                    <path d="M12 7v5l3 2" />
                </svg>
                {{ t(pkg.package_duration) }}
                </span>

                <router-link :to="packageLink(pkg)" class="hover:underline">
                See details →
                </router-link>
            </div>

            <router-link
                :to="packageLink(pkg)"
                class="mt-4 block text-center rounded-sm bg-[#e07300] hover:bg-[#c96600] text-white font-bold py-3 transition"
            >
                Book now →
            </router-link>
            </div>
        </div>
        </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useI18n } from 'vue-i18n'
import api from '@/services/api'

type Localized = Record<string, string | null> | string | null | undefined

interface Category {
  id: number
  slug: string
  country_name: Localized
  is_active?: boolean
}

interface TourPackage {
  id: number
  slug: string
  package_name: Localized
  package_price: Localized
  package_duration: Localized
  package_image: string | null
  sub_header?: Localized
  header?: Localized
  location?: Localized
  is_active: boolean
  category?: Category
}

const route = useRoute()
const router = useRouter() // was missing
const { locale } = useI18n()

const t = (value: Localized): string => {
  if (!value) return ''
  if (typeof value === 'string') return value
  return value[locale.value] || value.en || value.bn || ''
}

// Absolute path with the slug. Must match your router path.
const packageLink = (pkg: TourPackage) => `/tour-package/${pkg.slug}`

const categories = ref<Category[]>([])
const packages = ref<TourPackage[]>([])
const loading = ref(false)
const error = ref('')

const activeCategory = computed(() => (route.query.category as string) || null)

const fetchCategories = async () => {
  try {
    const { data } = await api.get('/category')
    const list: Category[] = data.data ?? data
    categories.value = list.filter((c) => c.is_active !== false)
  } catch (e) {
    console.error(e)
  }
}

let requestId = 0

const fetchPackages = async () => {
  const current = ++requestId
  loading.value = true
  error.value = ''

  try {
    const url = activeCategory.value
      ? `/category/${activeCategory.value}/tour-package`
      : '/tour-package'

    const { data } = await api.get(url, {
      params: activeCategory.value ? {} : { active: 1 },
    })

    if (current !== requestId) return
    const list: TourPackage[] = data.data ?? []
    packages.value = list.filter((p) => p.is_active)
  } catch (e: any) {
    if (current !== requestId) return
    packages.value = []
    error.value =
      e?.response?.status === 404 ? 'Category not found.' : 'Failed to load packages.'
  } finally {
    if (current === requestId) loading.value = false
  }
}

const selectCategory = (slug: string | null) => {
  router.push({ query: slug ? { category: slug } : {} })
}

watch(activeCategory, fetchPackages)

onMounted(() => {
  fetchCategories()
  fetchPackages()
})
</script>