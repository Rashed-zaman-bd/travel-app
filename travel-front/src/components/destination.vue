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

    <!-- Destinations -->
    <div
      class="grid grid-cols-1 gap-5 px-4 sm:grid-cols-2 lg:grid-cols-3"
    >
      <div
        v-for="destination in visibleDestinations"
        :key="destination.name"
        class="group relative h-40 cursor-pointer overflow-hidden rounded-sm"
      >
        <!-- Image -->
        <img
          :src="destination.image"
          :alt="destination.name"
          class="h-full w-full object-cover transition duration-500 ease-in-out group-hover:scale-110"
        />

        <!-- Dark Overlay -->
        <div
          class="absolute inset-0 bg-black/20 transition duration-500 group-hover:bg-black/45"
        ></div>

        <!-- Destination Name -->
        <div class="absolute inset-0 flex items-center justify-center">
          <h3
            class="text-2xl font-medium text-white drop-shadow-lg transition duration-500 group-hover:scale-110"
          >
            {{ destination.name }}
          </h3>
        </div>
      </div>
    </div>

    <!-- More Button -->
    <div
      v-if="visibleCount < destinations.length"
      class="mt-8 flex justify-center"
    >
      <button
        type="button"
        @click="showMore"
        class="rounded-md bg-amber-500 px-8 py-3 font-medium text-white transition duration-300 hover:bg-amber-600"
      >
        {{ t('worldwide_destinations.more') }}
      </button>
    </div>

  </div>
</template>

<script setup lang="ts">
import { computed, ref } from 'vue'
import { useI18n } from 'vue-i18n'

const { t } = useI18n()

// Initially show 9 destinations
const visibleCount = ref(9)

// Show 3 more destinations each time
const showMore = () => {
  visibleCount.value += 3
}

// Get only visible destinations
const visibleDestinations = computed(() => {
  return destinations.slice(0, visibleCount.value)
})

const destinations = [
  // 1 - 9: Initially visible
  {
    name: 'Japan',
    image: '/images/japan.jpg',
  },
  {
    name: 'Italy',
    image: '/images/italy.jpg',
  },
  {
    name: 'Morocco',
    image: '/images/morocco.jpg',
  },
  {
    name: 'Costa Rica',
    image: '/images/costa-rica.jpg',
  },
  {
    name: 'Iceland',
    image: '/images/iceland.jpg',
  },
  {
    name: 'Greece',
    image: '/images/greece.jpg',
  },
  {
    name: 'Thailand',
    image: '/images/thailand.jpg',
  },
  {
    name: 'Portugal',
    image: '/images/portugal.jpg',
  },
  {
    name: 'Spain',
    image: '/images/spain.jpg',
  },

  // 10 - 12: First More click
  {
    name: 'France',
    image: '/images/france.jpg',
  },
  {
    name: 'Switzerland',
    image: '/images/switzerland.jpg',
  },
  {
    name: 'Turkey',
    image: '/images/turkey.jpg',
  },

  // 13 - 15: Second More click
  {
    name: 'Dubai',
    image: '/images/dubai.jpg',
  },
  {
    name: 'Australia',
    image: '/images/australia.jpg',
  },
  {
    name: 'Canada',
    image: '/images/canada.jpg',
  },

  // 16 - 18: Third More click
  {
    name: 'Egypt',
    image: '/images/egypt.jpg',
  },
  {
    name: 'Malaysia',
    image: '/images/malaysia.jpg',
  },
  {
    name: 'Indonesia',
    image: '/images/indonesia.jpg',
  },
]
</script>

<style scoped>
</style>