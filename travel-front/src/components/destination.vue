//components/destination.vue
<template>
 <section id="destination">
  <div class="mx-auto max-w-7xl bg-white py-10">
    <!-- Section Header -->
    <div class="mx-auto mb-10 max-w-5xl px-4 text-center">
      <h2 class="mb-3 text-2xl font-semibold text-amber-500 md:text-3xl">
        {{ t('worldwide_category.title') }}
      </h2>

      <p class="mx-auto max-w-3xl text-slate-600">
        {{ t('worldwide_category.description') }}
      </p>
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
      v-else-if="categories.length === 0"
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
        :to="{
          name: 'destination',
          params: {
            slug: category.slug
          }
        }"
        class="group relative h-40 cursor-pointer overflow-hidden rounded-sm"
      >
        <!-- Image -->
        <img
          v-if="category.image"
          :src="category.image"
          :alt="getName(category)"
          loading="lazy"
          class="h-full w-full object-cover transition duration-500 ease-in-out group-hover:scale-110"
        />

        <!-- No Image -->
        <div
          v-else
          class="flex h-full w-full items-center justify-center bg-slate-200 text-slate-500"
        >
          No Image
        </div>

        <!-- Dark Overlay -->
        <div
          class="absolute inset-0 bg-black/20 transition duration-500 group-hover:bg-black/45"
        ></div>

        <!-- Destination Name -->
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
      v-if="!loading && !error && visibleCount < categories.length"
      class="mt-8 flex justify-center"
    >
      <button
        type="button"
        class="cursor-pointer rounded-md bg-amber-500 px-8 py-3 font-medium text-white transition duration-300 hover:bg-amber-600"
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

interface Category {
  id: number
  slug: string | null
  country_name: { en: string; bn?: string | null }
  image: string | null
  order: number | null
  is_active: boolean | number | string | null
  worldwide: boolean | number | string | null
}

const PAGE_SIZE = 6

const { t, locale } = useI18n({ useScope: 'global' })

const categories = ref<Category[]>([])
const loading = ref(true)
const error = ref('')
const visibleCount = ref(PAGE_SIZE)

const visibleCategories = computed(() =>
  categories.value.slice(0, visibleCount.value),
)

// Accepts true, 1 and "1"
const isOn = (value: unknown): boolean =>
  value === true || value === 1 || value === '1'

const getName = (category: Category): string =>
  category.country_name?.[locale.value as 'en' | 'bn'] ||
  category.country_name?.en ||
  'Category'

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
      .filter((c) => isOn(c.is_active) && isOn(c.worldwide))
      .sort((a, b) => (a.order ?? 0) - (b.order ?? 0))
  } catch (e: any) {
    error.value = e?.response?.data?.message || 'Failed to load categories.'
  } finally {
    loading.value = false
  }
}

onMounted(fetchCategories)
</script>