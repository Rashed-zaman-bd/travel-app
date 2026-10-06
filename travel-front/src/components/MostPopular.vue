<template>
  <section id="mostpopular">
    <div class="mx-auto max-w-7xl bg-white py-10 border-b border-gray-200">
      <!-- Section Header -->
      <div class="mx-auto mb-6 max-w-5xl px-4 text-center">
        <h2 class="mb-3 text-2xl font-semibold text-amber-600 md:text-3xl">
          {{ t('worldwide_category.holiday') }}
        </h2>
      </div>

      <!-- Tabs -->
      <div class="mb-8 flex flex-wrap justify-center gap-2 px-4">
        <button
          v-for="tab in tabs"
          :key="tab.key"
          type="button"
          class="cursor-pointer rounded-full border px-5 py-2 text-sm font-medium transition duration-300"
          :class="
            activeTab === tab.key
              ? 'border-amber-600 bg-amber-600 text-white'
              : 'border-slate-300 bg-white text-slate-600 hover:border-amber-600 hover:text-amber-700'
          "
          @click="setTab(tab.key)"
        >
          {{ tab.label }}
        </button>
      </div>

      <!-- Loading -->
      <div v-if="loading" class="py-10 text-center text-sm text-slate-400">
        Loading...
      </div>

      <!-- Error -->
      <div v-else-if="error" class="py-10 text-center text-sm text-red-500">
        {{ error }}
      </div>

      <!-- Empty -->
      <div
        v-else-if="filteredCategories.length === 0"
        class="py-10 text-center text-sm text-slate-500"
      >
        No categories found.
      </div>

      <!-- Categories -->
      <div
        v-else
        class="grid grid-cols-1 gap-5 px-4 sm:grid-cols-2 lg:grid-cols-3"
      >
        <router-link
          v-for="category in visibleCategories"
          :key="category.id"
          :to="{ name: 'destination', params: { slug: category.slug } }"
          class="group relative h-40 cursor-pointer overflow-hidden rounded-sm"
        >
          <img
            v-if="category.image"
            :src="category.image"
            :alt="getName(category)"
            loading="lazy"
            class="h-full w-full object-cover transition duration-500 ease-in-out group-hover:scale-110"
          />

          <div
            v-else
            class="flex h-full w-full items-center justify-center bg-slate-200 text-slate-500"
          >
            No Image
          </div>

          <div
            class="absolute inset-0 bg-black/20 transition duration-500 group-hover:bg-black/45"
          ></div>

          <div class="absolute inset-0 flex items-center justify-center px-4">
            <h3
              class="text-center text-2xl font-medium text-white drop-shadow-lg transition duration-500 group-hover:scale-110"
            >
              {{ getName(category) }}
            </h3>
          </div>
        </router-link>
      </div>

      <!-- More Button -->
      <div
        v-if="!loading && !error && visibleCount < filteredCategories.length"
        class="mt-8 flex justify-center"
      >
        <button
          type="button"
          class="cursor-pointer bg-amber-600 px-8 py-3 font-medium text-white transition duration-300 hover:bg-amber-700"
          @click="showMore"
        >
          {{ t('worldwide_category.more') }}
        </button>
      </div>
    </div>
  </section>
</template>

<script setup lang="ts">
import { computed, onMounted, ref } from 'vue'
import { useI18n } from 'vue-i18n'
import api from '@/services/api'

type Flag = boolean | number | string | null
type FlagKey = 'easy_visa_destination' | 'popular_destination' | 'honeymoon' | 'featured'
type TabKey = 'all' | FlagKey

interface Category {
  id: number
  slug: string | null
  country_name: { en: string; bn?: string | null }
  image: string | null
  order: number | null
  is_active: Flag
  easy_visa_destination: Flag
  popular_destination: Flag
  honeymoon: Flag
  featured: Flag
}

const PAGE_SIZE = 6

const { t, te, locale } = useI18n({ useScope: 'global' })

const categories = ref<Category[]>([])
const loading = ref(true)
const error = ref('')
const visibleCount = ref(PAGE_SIZE)
const activeTab = ref<TabKey>('all') // all items show first

const label = (key: string, fallback: string) => (te(key) ? t(key) : fallback)

const tabs = computed<{ key: TabKey; label: string }[]>(() => [
  { key: 'all', label: label('worldwide_category.tab_all', 'All') },
  {
    key: 'easy_visa_destination',
    label: label('worldwide_category.tab_easy_visa', 'Easy Visa Destination'),
  },
  {
    key: 'popular_destination',
    label: label('worldwide_category.tab_popular', 'Most Popular'),
  },
  {
    key: 'honeymoon',
    label: label('worldwide_category.tab_honeymoon', 'Honeymoon'),
  },
  {
    key: 'featured',
    label: label('worldwide_category.tab_umrah', 'Umrah'),
  },
])

const isOn = (value: unknown): boolean =>
  value === true || value === 1 || value === '1'

const filteredCategories = computed(() => {
  const tab = activeTab.value

  if (tab === 'all') {
    return categories.value.filter(
      (c) =>
        isOn(c.easy_visa_destination) ||
        isOn(c.popular_destination) ||
        isOn(c.honeymoon),
    )
  }

  // here `tab` is narrowed to FlagKey, so c[tab] is valid
  return categories.value.filter((c) => isOn(c[tab]))
})



const visibleCategories = computed(() =>
  filteredCategories.value.slice(0, visibleCount.value),
)

const getName = (category: Category): string =>
  category.country_name?.[locale.value as 'en' | 'bn'] ||
  category.country_name?.en ||
  'Category'

const setTab = (key: TabKey) => {
  activeTab.value = key
  visibleCount.value = PAGE_SIZE
}

const showMore = () => {
  visibleCount.value += 3
}

const fetchCategories = async () => {
  loading.value = true
  error.value = ''

  try {
    const res = await api.get('/category')
    const list: Category[] = res.data?.data ?? res.data ?? []

    categories.value = list
      .filter((c) => isOn(c.is_active))
      .sort((a, b) => (a.order ?? 0) - (b.order ?? 0))
  } catch (e: any) {
    error.value = e?.response?.data?.message || 'Failed to load categories.'
  } finally {
    loading.value = false
  }
}

onMounted(fetchCategories)
</script>