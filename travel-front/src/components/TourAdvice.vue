<template>
      <!-- Thailand Travel Advice -->
  <section class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
    <!-- Section Header -->
    <div class="mb-7 text-center">
      <h2 class="text-3xl font-medium text-amber-500 md:text-4xl">
        Thailand Travel Advice
      </h2>

      <p class="mt-2 text-sm text-gray-600 sm:text-base">
        Read our detailed insights to make sure you have the best trip
      </p>
    </div>

    <!-- Advice Cards -->
    <div
      class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4"
    >
      <article
        v-for="(advice, index) in travelAdvice"
        :key="index"
        class="overflow-hidden rounded-md border border-gray-200 bg-white transition duration-300 hover:shadow-lg"
      >
        <!-- Image -->
        <div class="h-48 w-full overflow-hidden">
          <img
            :src="advice.image"
            :alt="advice.title"
            class="h-full w-full object-cover transition duration-500 hover:scale-105"
          />
        </div>

        <!-- Content -->
        <div class="p-5">
          <h3
            class="mb-3 text-lg font-semibold leading-tight text-gray-900"
          >
            {{ advice.title }}
          </h3>

          <p
            class="line-clamp-4 text-sm leading-relaxed text-gray-600"
          >
            {{ advice.description }}
          </p>
        </div>
      </article>
    </div>
     <!-- See More -->
        <div v-for="(advice) in travelBtn" class="mt-9 text-center">
          <a
            href="#"
            class="inline-flex items-center gap-1 text-base font-semibold text-amber-500 transition-all duration-300 hover:text-amber-600 hover:tracking-wider"
          >
            {{ advice.btn }}
            <span class="text-lg">→</span>
          </a>
        </div>
  </section>

  <!-- Thailand Travel Maps -->
  <section class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="mb-6 text-center">
      <h2 class="text-2xl font-medium text-amber-500 md:text-3xl">
        Thailand Travel Maps
      </h2>

      <p class="mt-1 text-xs text-gray-600 sm:text-sm">
        Get the lay of land, and understand distances between the places you want to visit
      </p>
    </div>

    <!-- Maps Grid -->
    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-3">
      <article
        v-for="(map, index) in travelMaps"
        :key="index"
        class="overflow-hidden border border-gray-300 bg-white"
      >
        <!-- Map Image -->
        <div class="h-[165px] w-full overflow-hidden">
          <img
            :src="map.image"
            :alt="map.title"
            class="h-full w-full object-cover transition duration-300 hover:scale-105"
          />
        </div>

        <!-- Map Content -->
        <div class="px-2 py-2">
          <h3 class="text-xs font-medium leading-snug text-gray-900">
            {{ map.title }}
          </h3>

          <a
            :href="map.url"
            class="mt-1 inline-block text-xs text-blue-600 hover:text-blue-800"
          >
            View article
          </a>
        </div>
      </article>
    </div>

    <!-- See All -->
    <div class="mt-5 text-center">
      <a
        href="#"
        class="inline-flex items-center gap-1 text-base font-semibold text-amber-500 transition-all duration-300 hover:text-amber-600 hover:tracking-wider"
      >
        See all Thailand maps →
      </a>
    </div>
  </section>


  <!-- Meet our Local Travel Specialists -->
  <section class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="mb-7 text-center">
      <h2 class="text-2xl font-medium text-amber-500 md:text-3xl">
        Meet our Local Travel Specialists
      </h2>

      <p class="mt-1 text-xs text-gray-600 sm:text-sm">
        Experienced travel agents with a passion for Thailand
      </p>
    </div>

    <!-- Specialists Grid -->
    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-4">
      <article
        v-for="(specialist, index) in travelSpecialists"
        :key="index"
        class="overflow-hidden border border-gray-300 bg-white"
      >
        <!-- Specialist Image -->
        <div class="relative h-[135px] w-full overflow-hidden">
          <img
            :src="specialist.image"
            :alt="specialist.name"
            class="h-full w-full object-cover"
          />

          <!-- Active Time -->
          <span
            v-if="specialist.activeTime"
            class="absolute bottom-1 right-1 bg-black/70 px-1.5 py-0.5 text-[8px] font-medium text-white"
          >
            {{ specialist.activeTime }}
          </span>
        </div>

        <!-- Specialist Content -->
        <div class="p-2.5">
          <h3 class="text-xs font-semibold text-gray-900">
            {{ specialist.name }}
          </h3>

          <p class="mt-1 line-clamp-5 text-[10px] leading-relaxed text-gray-600">
            {{ specialist.description }}
          </p>

          <div class="mt-2 text-right">
            <a
              :href="specialist.url"
              class="text-[10px] font-medium text-blue-600 hover:text-blue-800"
            >
              read more...
            </a>
          </div>
        </div>
      </article>
    </div>

    <!-- See All -->
    <div class="mt-5 text-center">
      <a
        href="#"
        class="inline-flex items-center gap-1 text-base font-semibold text-amber-500 transition-all duration-300 hover:text-amber-600 hover:tracking-wider"
      >
        See all Thailand travel agents →
      </a>
    </div>
  </section>


  <!-- Related Destinations -->
  <section class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="mb-6 text-center">
      <h2 class="text-2xl font-medium text-amber-500 md:text-3xl">
        Related Destinations
      </h2>

      <p class="mt-1 text-xs text-gray-600 sm:text-sm">
        Travelers who visit Thailand also visit these destinations
      </p>
    </div>

    <!-- Related Destinations Grid -->
    <div class="grid grid-cols-1 gap-3 sm:grid-cols-3">
      <a
        v-for="(destinationItem, index) in relatedDestinations"
        :key="index"
        :href="destinationItem.url"
        class="group relative h-[100px] overflow-hidden"
      >
        <!-- Image -->
        <img
          :src="destinationItem.image"
          :alt="destinationItem.name"
          class="h-full w-full object-cover transition duration-500 group-hover:scale-105"
        />

        <!-- Overlay -->
        <div class="absolute inset-0 bg-black/30 transition group-hover:bg-black/40"></div>

        <!-- Name -->
        <div class="absolute inset-0 flex items-center justify-center">
          <span class="text-lg font-medium text-white drop-shadow-md">
            {{ destinationItem.name }}
          </span>
        </div>
      </a>
    </div>
  </section>
</template>
<script setup lang="ts">
import { ref, onMounted, computed } from 'vue'
import { useRoute } from 'vue-router'
import { useI18n } from 'vue-i18n'

const route = useRoute()
const { t } = useI18n()

interface Tour {
  title: string
  description: string
  image: string
  map_image?: string
}

interface TravelAdvice {
  title: string
  description: string
  image: string
 
}

interface TravelBtn {
  btn: string
 
}

const travelBtn: TravelBtn[] = [
  {
    btn: 'See more Thailand travel advice',
  }
]

const travelAdvice: TravelAdvice[] = [
  {
    title: 'Thailand in January: Travel Tips, Weather, and More',
    description:
      "January is one of the best times of the year to visit Thailand. High season is in full effect with perfect weather in just about every part of the country. It's cooler than...",
    image: '/images/thailand-january.jpg',
    
  },
  {
    title: 'Thailand in February: Travel Tips, Weather, and More',
    description:
      "February is one of the best times to visit Thailand. In addition to all-but-guaranteed spectacular weather, it's the month of the famous Chiang Mai Flower Festival, a vibrant...",
    image: '/images/thailand-february.jpg',
  
  },
  {
    title: 'Thailand in March: Travel Tips, Weather, and More',
    description:
      'March is a fabulous month to visit Thailand. The weather is still pleasant, especially in the first half of the month, although the southern beaches will start getting some rain...',
    image: '/images/thailand-march.jpg',

  },
  {
    title: 'Thailand in April: Travel Tips, Weather, and More',
    description:
      "Depending on the type of vacation you're looking for, April can be a great month to visit Thailand. Yes, the hot weather deters many tourists —but it's also the month of the...",
    image: '/images/thailand-april.jpg',

  },
]

interface TravelMap {
  title: string
  image: string
  url: string
}

interface TravelSpecialist {
  name: string
  description: string
  image: string
  activeTime?: string
  url: string
}

interface RelatedDestination {
  name: string
  image: string
  url: string
}

const travelMaps: TravelMap[] = [
  {
    title: '10 Days in Thailand - 5 Unique Itinerary Ideas',
    image: '/images/thailand-map-10-days.jpg',
    url: '#',
  },
  {
    title: '1 Week in Thailand - 4 Unique Itinerary Ideas',
    image: '/images/thailand-map-1-week.jpg',
    url: '#',
  },
  {
    title: '5 Days in Thailand - 4 Unique Itinerary Ideas',
    image: '/images/thailand-map-5-days.jpg',
    url: '#',
  },
]

const travelSpecialists: TravelSpecialist[] = [
  {
    name: 'Hien Do',
    description:
      'I was born and grown in Vietnam and deeply love my country. I traveled a lot around my country’s full of beautiful places and friendly people. And this inspired me to start my travel company.',
    image: '/images/hien-do.jpg',
    activeTime: 'active 2 hours ago',
    url: '#',
  },
  {
    name: 'Ashley Nguyen',
    description:
      'I was born and grown in Hanoi - the capital of Vietnam. I have always been interested in travel and discovering other cultures and places. When I was a little girl, I dreamed of being a tour guide...',
    image: '/images/ashley-nguyen.jpg',
    activeTime: 'active 4 hours ago',
    url: '#',
  },
  {
    name: 'Long Dao',
    description:
      "Hi, my name is Long. I am a young and passionate travel specialist from Vietnam. I was in love with travel since I was a child and always wanted it to be a part of my life. My idea about...",
    image: '/images/long-dao.jpg',
    activeTime: 'active 22 mins ago',
    url: '#',
  },
  {
    name: 'Leny Vergara',
    description:
      "Hello! I'm Leny. I travel is my passion, and if you're anything like me, you believe the best adventures happen off the beaten path. Japan is the first country I lived in, and I fell in love with...",
    image: '/images/leny-vergara.jpg',
    activeTime: 'active 50 hours ago',
    url: '#',
  },
]

const relatedDestinations: RelatedDestination[] = [
  {
    name: 'Vietnam',
    image: '/images/vietnam.jpg',
    url: '/destination/vietnam',
  },
  {
    name: 'Cambodia',
    image: '/images/cambodia.jpg',
    url: '/destination/cambodia',
  },
  {
    name: 'Laos',
    image: '/images/laos.jpg',
    url: '/destination/laos',
  },
]
</script>