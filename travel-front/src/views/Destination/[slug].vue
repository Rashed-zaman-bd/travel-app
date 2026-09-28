<template>
  <section
    class="relative h-[30vh] min-h-[200px] max-h-[300px] w-full overflow-hidden md:max-h-[200px] lg:max-h-[300px]"
  >
    <!-- Loading -->
    <div
      v-if="loading"
      class="flex h-full w-full items-center justify-center bg-slate-900 text-white"
    >
      <div
        class="h-8 w-8 animate-spin rounded-full border-b-2 border-sky-400"
      ></div>
    </div>

    <!-- Error -->
    <div
      v-else-if="error"
      class="flex h-full w-full items-center justify-center bg-slate-900 text-sm text-red-400"
    >
      {{ error }}
    </div>

    <!-- Destination Hero -->
    <div
      v-else-if="destination"
      class="relative h-full w-full "
    >
      <!-- Background Image -->
      <img
        :src="destination.image"
        :alt="destination.image_title"
        class="h-full w-full object-cover"
      />

      <!-- Dark Overlay -->
      <div class="absolute inset-0 bg-black/40"></div>

      <!-- Hero Content -->
      <div
        class="absolute inset-0 z-10 flex items-center justify-center px-4"
      >
        <div class="mx-auto flex max-w-3xl flex-col items-center justify-center text-center">

          <!-- Title -->
          <h1
            class="mb-4 font-semibold leading-tight tracking-wide text-white sm:text-2xl md:text-3xl lg:text-4xl"
          >
            {{ destination.image_title }}
          </h1>

          <!-- CTA -->
          <div v-if="destination.cta_text">
            <a
              :href="destination.cta_url || '#'"
              class="group inline-flex items-center gap-2.5 rounded-xl bg-gradient-to-r from-amber-600 to-amber-500 px-5 py-2.5 text-sm font-semibold text-white shadow-lg shadow-amber-600/35 transition-all duration-300 ease-out hover:-translate-y-0.5 hover:from-amber-600 hover:to-amber-500 hover:shadow-amber-500/50 sm:px-7 sm:py-3.5 sm:text-base"
            >
              <span class="tracking-wider">
                {{ destination.cta_text }}
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
  <div class="mx-auto max-w-7xl bg-white px-4 py-12 sm:px-6 lg:px-8">
    <div v-if="destination">
      <!-- Section Header -->
      <div class="mb-10 text-center">
        <h2 class="mb-3 text-2xl font-semibold text-amber-500 md:text-3xl">
          {{ destination.title }}
        </h2>
        <p class="mt-2 text-sm text-gray-500 sm:text-base">
          {{ destination.sub_title }}
        </p>
      </div>

      <!-- Tours Grid -->
      <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
        <div
          v-for="(tour, index) in visibleTours"
          :key="index"
          class="flex flex-col overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm transition-shadow hover:shadow-lg"
        >
          <!-- Tour Image & Map Overlay -->
          <div class="relative h-52 w-full bg-gray-100">
            <!-- Main Scenic Image -->
            <img
              :src="tour.image"
              :alt="tour.title"
              class="h-full w-full object-cover transition duration-500 ease-in-out hover:scale-110 cursor-pointer"
            />

            <!-- Map Thumbnail Badge -->
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
          </div>

          <!-- Tour Details -->
          <div class="flex flex-1 flex-col p-5">
            <h3 class="mb-3 text-lg font-semibold leading-snug text-gray-900 cursor-pointer">
              {{ tour.title }}
            </h3>
            <p class="text-xs leading-relaxed text-gray-600 sm:text-sm">
              {{ tour.description }}
            </p>
          </div>
        </div>
      </div>

      <!-- Load More Button -->
      <div
        v-if="visibleCount < destination.tours.length"
        class="mt-10 flex justify-center"
      >
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
</template>

<script setup lang="ts">
import { ref, onMounted, computed } from 'vue'
import { useRoute } from 'vue-router'
import { useI18n } from 'vue-i18n'

const route = useRoute()
const { t } = useI18n()

const loading = ref(true)
const error = ref<string | null>(null)

// Initially show 6 tours
const visibleCount = ref(6)

// Show 3 more tours each time button is clicked
const showMore = () => {
  visibleCount.value += 3
}

const destination = ref<Destination | null>(null)

// Computed property to slice the tour items based on visibleCount
const visibleTours = computed(() => {
  if (!destination.value) return []
  return destination.value.tours.slice(0, visibleCount.value)
})

interface Tour {
  title: string
  description: string
  image: string
  map_image?: string
}

interface Destination {
  image_title: string
  slug: string
  image: string
  cta_text?: string
  cta_url?: string
  title: string
  sub_title: string
  tours: Tour[]
}

const destinations: Destination[] = [
  {
    image_title: 'Personalized trips to Thailand',
    slug: 'thailand',
    image: '/images/thailand.jpg',
    cta_text: 'Explore Thailand',
    cta_url: '/destination/thailand',
    title: 'Thailand Tours & Schedules',
    sub_title: 'Get inspired by trips other travelers have loved',
    tours: [
      {
        title: 'Best of Thailand - 8 Days',
        description:
          'If you\'ve ever dreamed of Thailand, this 8-day adventure covers the highlights with a perfect mix of guided tours and free time. Start off in vibrant Bangkok for iconic Buddhist sites and a gourmet dinner cruise on the Chao Phraya River.',
        image: '/images/costa-rica.jpg',
        map_image: '/images/map.jpg',
      },
      {
        title: 'A Taste of Southern Thailand - 5 Days',
        description:
          'Go beyond the stunning islands and emerald waters of Phang Nga Bay, and get a taste of the local lifestyle on this five-day trip to southern Thailand. You\'ll kayak through the lush jungle, unwind on pristine beaches, and eat authentic Thai food.',
        image: '/images/greece.jpg',
        map_image: '/images/map.jpg',
      },
      {
        title: 'Ultimate Highlights of Thailand: Bangkok, Chiang Mai & Ko Samui - 10 Days',
        description:
          'Experience Thailand\'s amazing highlights from north to south on this exciting 10-day itinerary. Kick off the trip in Bangkok, equipped with a local guide, where you\'ll hit the city\'s best monuments and foodie options.',
        image: '/images/iceland.jpg',
        map_image: '/images/map.jpg',
      },
      {
        title: 'Thailand Authentic Grand Tour - 12 Days',
        description:
          'Get to the heart of Thailand on this 12-day tour, from urban neighborhoods to age-old traditions tucked away in the jungles and mountains. The adventure starts in the exotic capital, where your private guide will show you the best sites.',
        image: '/images/italy.jpg',
        map_image: '/images/map.jpg',
      },
      {
        title: 'Highlights of Thailand, Cambodia & Vietnam - 14 Days',
        description:
          'Hit the ultimate trifecta of beautiful Southeast Asian countries on this two-week journey to Thailand, Cambodia, and Vietnam. In Thailand, you\'ll browse the floating markets, visit elephant sanctuaries, and go island hopping.',
        image: '/images/japan.jpg',
        map_image: '/images/map.jpg',
      },
      {
        title: 'Culture & Nature Family Adventure in Thailand: Chiang Mai - 7 Days',
        description:
          'This week-long family tour of Chiang Mai offers active families seeking an authentic Thailand experience the perfect blend of culture, nature, and kid-friendly activities. Hike to stunning temples in the mountains before cooling off.',
        image: '/images/morocco.jpg',
        map_image: '/images/map.jpg',
      },
      {
        title: 'Best of Thailand - 8 Days',
        description:
          'If you\'ve ever dreamed of Thailand, this 8-day adventure covers the highlights with a perfect mix of guided tours and free time. Start off in vibrant Bangkok for iconic Buddhist sites and a gourmet dinner cruise on the Chao Phraya River.',
        image: '/images/costa-rica.jpg',
        map_image: '/images/map.jpg',
      },
    ],
  },
]

onMounted(() => {
  const slug = (route.params.slug as string) || 'thailand'

  const foundDestination = destinations.find((item) => item.slug === slug)

  if (!foundDestination) {
    error.value = 'Destination not found.'
    loading.value = false
    return
  }

  destination.value = foundDestination
  loading.value = false
})
</script>