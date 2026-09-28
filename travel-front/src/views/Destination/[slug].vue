//Destination/[slug].vue

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
            <!-- Main Scenic Image -->
            <router-link :to="`/tour/${slugify(tour.title)}`">
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
            </router-link> 
          </div>

          <!-- Tour Details -->
          <div class="flex flex-1 flex-col p-5">
            <router-link :to="`/tour/${slugify(tour.title)}`">
              <h3 class="mb-3 text-lg font-semibold leading-snug text-gray-900 cursor-pointer">
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

const slugify = (text: string) => {
  return text
    .toLowerCase()
    .replace(/[^\w ]+/g, '')
    .replace(/ +/g, '-')
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