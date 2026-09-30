<template>
  <section
    class="relative h-[30vh] min-h-[200px] max-h-[300px] w-full overflow-hidden md:max-h-[200px] lg:max-h-[300px]"
  >
    <!-- Loading -->
    <div
      v-if="loading"
      class="flex h-full w-full items-center justify-center bg-slate-900 text-white"
    >
      <div class="h-8 w-8 animate-spin rounded-full border-b-2 border-sky-400"></div>
    </div>

    <!-- Error -->
    <div
      v-else-if="error"
      class="flex h-full w-full items-center justify-center bg-slate-900 text-sm text-red-400"
    >
      {{ error }}
    </div>

    <!-- Destination Hero -->
    <div v-else-if="destination" class="relative h-full w-full">
      <img
        v-if="destination.destination.hero.image"
        :src="destination.destination.hero.image"
        :alt="heroTitle"
        class="h-full w-full object-cover"
      />
      <div v-else class="h-full w-full bg-slate-800"></div>

      <div class="absolute inset-0 bg-black/40"></div>

      <div class="absolute inset-0 z-10 flex items-center justify-center px-4">
        <div class="mx-auto flex max-w-3xl flex-col items-center justify-center text-center">
          <h1
            class="mb-4 font-semibold leading-tight tracking-wide text-white sm:text-2xl md:text-3xl lg:text-4xl"
          >
            {{ heroTitle }}
          </h1>

          <div v-if="destination.destination.hero.btn">
            <a
              href="#tours"
              class="group inline-flex items-center gap-2.5 rounded-xl bg-gradient-to-r from-amber-600 to-amber-500 px-5 py-2.5 text-sm font-semibold text-white shadow-lg shadow-amber-600/35 transition-all duration-300 ease-out hover:-translate-y-0.5 hover:from-amber-600 hover:to-amber-500 hover:shadow-amber-500/50 sm:px-7 sm:py-3.5 sm:text-base"
            >
              <span class="tracking-wider">
                {{ destination.destination.hero.btn }}
              </span>

              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-4 w-4 transition-transform duration-200 group-hover:translate-x-1 sm:h-5 sm:w-5"
                viewBox="0 0 20 20"
                fill="currentColor"
              >
                <path
                  fill-rule="evenodd"
                  d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z"
                  clip-rule="evenodd"
                />
              </svg>
            </a>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Tours Section -->
  <div id="tours" class="mx-auto max-w-7xl bg-white px-4 py-12 sm:px-6 lg:px-8">
    <div v-if="destination">
      <!-- Section Header -->
      <div class="mb-10 text-center">
        <h2 class="mb-3 text-2xl font-semibold text-amber-500 md:text-3xl">
          {{ destination.destination.title }}
        </h2>
        <p class="mt-2 text-sm text-gray-500 sm:text-base">
          {{ destination.destination.sub_title }}
        </p>
      </div>

      <!-- Tours Grid -->
      <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
        <div
          v-for="tour in visibleTours"
          :key="tour.to"
          class="flex flex-col overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm transition-shadow hover:shadow-lg"
        >
          <!-- Tour Image & Map Overlay -->
          <div class="relative h-52 w-full bg-gray-100">
            <router-link :to="tour.to">
              <img
                v-if="tour.image"
                :src="tour.image"
                :alt="tour.title ?? ''"
                class="h-full w-full cursor-pointer object-cover transition duration-500 ease-in-out hover:scale-110"
              />

              <div
                v-if="tour.map_image"
                class="absolute bottom-1 right-1 overflow-hidden rounded border-2 border-white shadow-md transition-transform hover:scale-105"
              >
                <img
                  :src="tour.map_image"
                  :alt="`${tour.title} Map`"
                  class="h-12 w-18 object-cover sm:h-14 sm:w-18"
                />
              </div>
            </router-link>
          </div>

          <!-- Tour Details -->
          <div class="flex flex-1 flex-col p-5">
            <router-link :to="tour.to">
              <h3 class="mb-3 cursor-pointer text-lg font-semibold leading-snug text-gray-900">
                {{ tour.title }}
              </h3>
            </router-link>
            <p class="text-xs leading-relaxed text-gray-600 sm:text-sm">
              {{ tour.description }}
            </p>
          </div>
        </div>
      </div>

      <!-- Load More Button -->
      <div v-if="visibleCount < tours.length" class="mt-10 flex justify-center">
        <button
          type="button"
          @click="showMore"
          class="cursor-pointer rounded-md bg-amber-500 px-8 py-3 font-medium text-white transition duration-300 hover:bg-amber-600 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:ring-offset-2"
        >
          {{ t('worldwide_destinations.more') }}
        </button>
      </div>
    </div>
  </div>

  <TourAdvice />
</template>

<script setup lang="ts">
import { ref, computed, watch } from 'vue'
import { useRoute } from 'vue-router'
import { useI18n } from 'vue-i18n'
import axios from 'axios'
import TourAdvice from '@/components/TourAdvice.vue'
import api from '@/services/api'

/* ---------- types (match DestinationResource, single locale) ---------- */
interface Destination {
  id: number
  slug: string
  title: string | null
  sub_title: string | null
  image: string | null
  image_title: string | null
  destination: {
    name: string | null
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
}

interface TourCard {
  to: string
  title: string | null
  description: string | null
  image: string | null
  map_image: string | null
}

const route = useRoute()
const { t, locale } = useI18n({ useScope: 'global' })

const loading = ref(true)
const error = ref<string | null>(null)
const destination = ref<Destination | null>(null)

/* ---------- load more ---------- */
const visibleCount = ref(6)
const showMore = () => {
  visibleCount.value += 3
}

/* ---------- helpers ---------- */
const slugify = (text: string) =>
  text
    .toLowerCase()
    .replace(/[^\w ]+/g, '')
    .replace(/ +/g, '-')

const heroTitle = computed(
  () => destination.value?.destination.hero.title || destination.value?.image_title || ''
)

// The API has one tour per destination, so expose it as a list for the grid.
// The link uses tour.slug (same in every language), never the translated title.
const tours = computed<TourCard[]>(() => {
  const d = destination.value
  if (!d || !d.tour.title) return []

  return [
    {
      to: `/tour/${slugify(d.tour.slug || d.tour.title)}`,
      title: d.tour.title,
      description: d.tour.description,
      image: d.image,
      map_image: d.tour.map_image,
    },
  ]
})

const visibleTours = computed(() => tours.value.slice(0, visibleCount.value))

/* ---------- fetch ---------- */
async function loadDestination() {
  const slug = route.params.slug as string
  if (!slug) return

  loading.value = true
  error.value = null

  try {
    // the axios interceptor adds ?lang= and Accept-Language from vue-i18n
    const res = await api.get(`/destinations/${encodeURIComponent(slug)}`)
    destination.value = res.data.data
  } catch (e) {
    destination.value = null
    error.value =
      axios.isAxiosError(e) && e.response?.status === 404
        ? 'Destination not found.'
        : 'Failed to load destination. Please try again.'
  } finally {
    loading.value = false
  }
}

// runs on mount, and again when the slug or the language changes
watch(() => [route.params.slug, locale.value], loadDestination, { immediate: true })
</script>