```vue
<template>
  <div class="mx-auto max-w-7xl bg-white py-10">
    <!-- Section Header -->
    <div class="mx-auto mb-10 max-w-5xl px-4 text-center">
      <h2 class="mb-3 text-2xl font-semibold text-amber-500 md:text-3xl">
        {{ t('worldwide_destinations.title') }}
      </h2>

      <p class="mx-auto max-w-3xl text-slate-600">
        {{ t('worldwide_destinations.description') }}
      </p>
    </div>

    <!-- Loading -->
    <div
      v-if="loading"
      class="py-10 text-center text-sm text-slate-400"
    >
      Loading...
    </div>

    <!-- Error -->
    <div
      v-else-if="error"
      class="py-10 text-center text-sm text-red-500"
    >
      {{ error }}
    </div>

    <!-- Empty -->
    <div
      v-else-if="destinations.length === 0"
      class="py-10 text-center text-sm text-slate-500"
    >
      No destinations found.
    </div>

    <!-- Destinations -->
    <div
      v-else
      class="grid grid-cols-1 gap-5 px-4 sm:grid-cols-2 lg:grid-cols-3"
    >
      <router-link
        v-for="destination in visibleDestinations"
        :key="destination.id"
        :to="{
          name: 'destination',
          params: {
            slug: destination.slug
          }
        }"
        class="group relative h-40 cursor-pointer overflow-hidden rounded-sm"
      >
        <!-- Image -->
        <img
          v-if="destination.image"
          :src="destination.image"
          :alt="destination.destination.name || destination.title?.en || 'Destination'"
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
        <div
          class="absolute inset-0 flex items-center justify-center px-4"
        >
          <h3
            class="text-center text-2xl font-medium text-white drop-shadow-lg transition duration-500 group-hover:scale-110"
          >
            {{ destination.destination.name }}
          </h3>
        </div>
      </router-link>
    </div>

    <!-- More Button -->
    <div
      v-if="visibleCount < destinations.length"
      class="mt-8 flex justify-center"
    >
      <button
        type="button"
        @click="showMore"
        class="cursor-pointer rounded-md bg-amber-500 px-8 py-3 font-medium text-white transition duration-300 hover:bg-amber-600"
      >
        {{ t('worldwide_destinations.more') }}
      </button>
    </div>
  </div>
</template>

<script setup lang="ts">
import {
  computed,
  onMounted,
  onUnmounted,
  ref,
  watch,
} from 'vue'

import { useI18n } from 'vue-i18n'
import api from '@/services/api'

const { t, locale } = useI18n({
  useScope: 'global',
})

/*
|--------------------------------------------------------------------------
| Types
|--------------------------------------------------------------------------
*/

interface Translation {
  en?: string
  bn?: string
}

interface DestinationData {
  id: number
  slug: string

  title: Translation
  sub_title: Translation

  image: string | null
  image_title: Translation | null

  destination: {
    name: string
    title: string | null
    sub_title: string | null

    hero: {
      image: string | null
      title: string | null
      btn: string | null
    }
  }

  tour: {
    slug: string | null
    title: string | null
    description: string | null
    map_image: string | null
  }

  created_at: string | null
  updated_at: string | null

  // Optional admin fields
  order?: number
  is_active?: boolean
}

/*
|--------------------------------------------------------------------------
| State
|--------------------------------------------------------------------------
*/

const destinations = ref<DestinationData[]>([])

const loading = ref(false)

const error = ref<string | null>(null)

const visibleCount = ref(9)

/*
|--------------------------------------------------------------------------
| Visible destinations
|--------------------------------------------------------------------------
*/

const visibleDestinations = computed(() => {
  return destinations.value
    .filter((destination) => {
      // If backend sends is_active, only show active items.
      // If it does not send it, keep the destination visible.
      return destination.is_active !== false
    })
    .sort((a, b) => {
      return (a.order ?? 0) - (b.order ?? 0)
    })
    .slice(0, visibleCount.value)
})

/*
|--------------------------------------------------------------------------
| Show More
|--------------------------------------------------------------------------
*/

const showMore = () => {
  visibleCount.value += 3
}

/*
|--------------------------------------------------------------------------
| Current Language
|--------------------------------------------------------------------------
*/

const getCurrentLocale = (): string => {
  return (
    locale.value ||
    localStorage.getItem('locale') ||
    'en'
  )
}

/*
|--------------------------------------------------------------------------
| Fetch Destinations
|--------------------------------------------------------------------------
*/

const fetchDestinations = async () => {
  loading.value = true
  error.value = null

  const currentLang = getCurrentLocale()

  try {
    const response = await api.get('/destinations', {
      params: {
        lang: currentLang,
      },

      headers: {
        'X-Locale': currentLang,
        'Accept-Language': currentLang,
      },
    })

    destinations.value = response.data?.data ?? []

    // Reset visible count after language change
    visibleCount.value = 9
  } catch (e: any) {
    console.error('Destination fetch error:', e)

    error.value =
      e?.response?.data?.message ||
      'Failed to load destinations.'

    destinations.value = []
  } finally {
    loading.value = false
  }
}

/*
|--------------------------------------------------------------------------
| Watch Language
|--------------------------------------------------------------------------
*/

watch(
  locale,
  () => {
    fetchDestinations()
  }
)

/*
|--------------------------------------------------------------------------
| Custom Language Event
|--------------------------------------------------------------------------
*/

const handleLocaleChange = () => {
  fetchDestinations()
}

/*
|--------------------------------------------------------------------------
| Mounted
|--------------------------------------------------------------------------
*/

onMounted(() => {
  fetchDestinations()

  window.addEventListener(
    'locale-changed',
    handleLocaleChange
  )
})

/*
|--------------------------------------------------------------------------
| Unmounted
|--------------------------------------------------------------------------
*/

onUnmounted(() => {
  window.removeEventListener(
    'locale-changed',
    handleLocaleChange
  )
})
</script>

<style scoped>
</style>
```
