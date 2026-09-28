<template>
  <div v-if="tour" class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
    <!-- Header Section -->
    <div class="mb-8">
      <h1 class="text-3xl font-bold text-gray-900 sm:text-4xl">{{ tour.title }}</h1>
      <p class="mt-2 text-sm text-gray-500">By {{ tour.author }} • {{ tour.date }}</p>
    </div>

    <!-- Main Grid Layout (2/3 Main Content, 1/3 Sidebar) -->
    <div class="grid grid-cols-1 gap-10 lg:grid-cols-3">
      <!-- Main Content Area -->
      <div class="lg:col-span-2">
        <!-- Main Hero Image -->
        <div class="mb-8 h-[400px] w-full overflow-hidden rounded-xl">
          <img :src="tour.heroImage" :alt="tour.title" class="h-full w-full object-cover" />
        </div>

        <!-- Overview & Highlights -->
        <div class="mb-10">
          <p class="text-xl leading-relaxed text-gray-700">{{ tour.description }}</p>
        </div>

        <div class="mb-10">
          <h3 class="mt-6 text-2xl font-semibold text-gray-900">Highlights</h3>
          <ul class="mt-3 list-disc space-y-2 pl-5 text-xl text-gray-700">
            <li v-for="(highlight, idx) in tour.highlights" :key="idx">{{ highlight }}</li>
          </ul>
        </div>

        <!-- Brief Schedule Table -->
        <div class="mb-12">
          <h2 class="mb-4 text-2xl font-bold text-gray-900">Brief Schedule</h2>
          <div class="overflow-x-auto rounded-lg border border-gray-200">
            <table class="min-w-full divide-y divide-gray-200 text-left text-sm text-gray-700">
              <thead class="bg-gray-100 font-semibold text-gray-900">
                <tr>
                  <th class="px-4 py-3">Day</th>
                  <th class="px-4 py-3">Highlights</th>
                  <th class="px-4 py-3">Overnight</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-200 bg-white">
                <tr v-for="day in tour.itinerary" :key="day.day">
                  <td class="px-4 py-3 font-medium text-amber-600">Day {{ day.day }}</td>
                  <td class="px-4 py-3">{{ day.title }}</td>
                  <td class="px-4 py-3">{{ day.overnight }}</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Detailed Schedule Breakdown -->
        <div class="space-y-12">
          <h2 class="text-2xl font-bold text-gray-900">Detailed Schedule</h2>

          <article 
            v-for="day in tour.itinerary" 
            :key="day.day" 
            class="border-b border-gray-200 pb-8 last:border-none"
          >
            <h3 class="text-xl font-semibold text-amber-500">
              Day {{ day.day }}: {{ day.title }}
            </h3>
            
            <div v-if="day.image" class="my-1 h-full w-full overflow-hidden rounded-lg">
              <img :src="day.image" :alt="day.title" class="h-full w-full object-cover" />
            </div>

            <p v-if="day.image_title" class="mb-2 text-sm italic text-gray-500">
              {{ day.image_title }}
            </p>

            <p class="leading-relaxed text-gray-700">{{ day.details }}</p>
          </article>
        </div>
      </div>

      <!-- Right Sidebar Section -->
      <aside class="space-y-8 lg:col-span-1">
        <!-- Section 1: Itinerary Ideas -->
        <div>
          <div class="space-y-4">
            <router-link
              v-for="item in itineraryIdeas"
              :key="item.id"
              :to="`/tour/${item.slug}`"
              class="group flex items-start gap-3"
            >
              <img :src="item.image" :alt="item.title" class="h-16 w-16 flex-shrink-0 rounded object-cover" />
              <div>
                <h4 class="text-sm font-semibold text-gray-900 group-hover:text-blue-600 group-hover:underline">
                  {{ item.title }}
                </h4>
                <p class="mt-1 line-clamp-2 text-xs text-gray-500">{{ item.description }}</p>
              </div>
            </router-link>
          </div>
          <a href="#" class="mt-3 block text-xs font-semibold text-blue-600 hover:underline">
            Make the Most of Your Time in Thailand
          </a>
        </div>

        <!-- Section 2: Thailand Travel Advice -->
        <div>
          <h3 class="mb-4 text-base font-bold text-gray-900">Thailand Travel Advice</h3>
          <div class="space-y-4">
            <router-link
              v-for="advice in travelAdvice"
              :key="advice.id"
              :to="`/advice/${advice.slug}`"
              class="group flex items-start gap-3"
            >
              <img :src="advice.image" :alt="advice.title" class="h-16 w-16 flex-shrink-0 rounded object-cover" />
              <div>
                <h4 class="text-sm font-semibold text-gray-900 group-hover:text-blue-600 group-hover:underline">
                  {{ advice.title }}
                </h4>
                <p class="mt-1 line-clamp-2 text-xs text-gray-500">{{ advice.description }}</p>
              </div>
            </router-link>
          </div>
          <a href="#" class="mt-3 block text-xs font-semibold text-blue-600 hover:underline">
            Thailand Travel Advice
          </a>
        </div>

        <!-- Section 3: Trip Planning Essentials -->
        <div>
          <h3 class="mb-4 text-base font-bold text-gray-900">Trip Planning Essentials</h3>
          <div class="space-y-4">
            <router-link
              v-for="item in planningEssentials"
              :key="item.id"
              :to="`/essentials/${item.slug}`"
              class="group flex items-start gap-3"
            >
              <img :src="item.image" :alt="item.title" class="h-16 w-16 flex-shrink-0 rounded object-cover" />
              <div>
                <h4 class="text-sm font-semibold text-gray-900 group-hover:text-blue-600 group-hover:underline">
                  {{ item.title }}
                </h4>
                <p class="mt-1 line-clamp-2 text-xs text-gray-500">{{ item.description }}</p>
              </div>
            </router-link>
          </div>
        </div>

        <!-- Section 4: Most Viewed Thailand Content -->
        <div>
          <h3 class="mb-4 text-base font-bold text-gray-900">Most Viewed Thailand Content</h3>
          <div class="space-y-4">
            <router-link
              v-for="item in mostViewedContent"
              :key="item.id"
              :to="`/tour/${item.slug}`"
              class="group flex items-start gap-3"
            >
              <img :src="item.image" :alt="item.title" class="h-16 w-16 flex-shrink-0 rounded object-cover" />
              <div>
                <h4 class="text-sm font-semibold text-gray-900 group-hover:text-blue-600 group-hover:underline">
                  {{ item.title }}
                </h4>
                <p class="mt-1 line-clamp-2 text-xs text-gray-500">{{ item.description }}</p>
              </div>
            </router-link>
          </div>
        </div>
      </aside>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'

const route = useRoute()

interface ItineraryDay {
  day: number
  title: string
  overnight: string
  details: string
  image?: string
  image_title?: string
}

interface TourDetail {
  slug: string
  title: string
  author: string
  date: string
  heroImage: string
  description: string
  highlights: string[]
  itinerary: ItineraryDay[]
}

interface SidebarItem {
  id: number
  title: string
  description: string
  image: string
  slug: string
}

const tour = ref<TourDetail | null>(null)

// Dynamic Sidebar Data Arrays
const itineraryIdeas = ref<SidebarItem[]>([
  {
    id: 1,
    title: '1 Week in Thailand - 4 Unique Itinerary Ideas',
    description: 'With one week in Thailand, it\'s best to explore just one of its regions...',
    image: '/images/morocco.jpg',
    slug: '1-week-in-thailand'
  },
  {
    id: 2,
    title: '10 Days in Thailand - 5 Unique Itinerary Ideas',
    description: 'With 10 days in Thailand, you can discover the highlights of Bangkok...',
    image: '/images/morocco.jpg',
    slug: '10-days-in-thailand'
  },
  {
    id: 3,
    title: '2 Weeks in Thailand - 4 Unique Itinerary Ideas',
    description: 'Two weeks allows for plenty of ways to experience the best of Thailand...',
    image: '/images/morocco.jpg',
    slug: '2-weeks-in-thailand'
  }
])

const travelAdvice = ref<SidebarItem[]>([
  {
    id: 1,
    title: 'Thailand in January: Travel Tips, Weather, and More',
    description: 'January is one of the best times of the year to visit Thailand. High...',
    image: '/images/morocco.jpg',
    slug: 'thailand-in-january'
  },
  {
    id: 2,
    title: 'Thailand in February: Travel Tips, Weather, and More',
    description: 'February is one of the best times to visit Thailand. In addition to...',
    image: '/images/morocco.jpg',
    slug: 'thailand-in-february'
  },
  {
    id: 3,
    title: 'Thailand in March: Travel Tips, Weather, and More',
    description: 'March is a fabulous month to visit Thailand. The weather is still...',
    image: '/images/morocco.jpg',
    slug: 'thailand-in-march'
  },
  {
    id: 4,
    title: 'Thailand in April: Travel Tips, Weather, and More',
    description: 'Depending on the type of vacation you\'re looking for, April can be a...',
    image: '/images/morocco.jpg',
    slug: 'thailand-in-april'
  },
  {
    id: 5,
    title: 'Thailand in May: Travel Tips, Weather, and More',
    description: 'May is the beginning of the rainy season in Thailand and, in most parts...',
    image: '/images/morocco.jpg',
    slug: 'thailand-in-may'
  }
])

const planningEssentials = ref<SidebarItem[]>([
  {
    id: 1,
    title: 'How kimkim Trips Work',
    description: 'Planning your dream trip? Here\'s a quick introduction to kimkim, how our...',
    image: '/images/morocco.jpg',
    slug: 'how-trips-work'
  },
  {
    id: 2,
    title: 'Thailand Travel Agents',
    description: 'Kimkim has selected the best Thailand travel agents so you\'ll have a...',
    image: '/images/morocco.jpg',
    slug: 'thailand-travel-agents'
  }
])

const mostViewedContent = ref<SidebarItem[]>([
  {
    id: 1,
    title: '10 Days in Thailand - 5 Unique Itinerary Ideas',
    description: 'With 10 days in Thailand, you can discover the highlights of Bangkok...',
    image: '/images/morocco.jpg',
    slug: '10-days-in-thailand'
  },
  {
    id: 2,
    title: 'Thailand in August: Travel Tips, Weather, and More',
    description: 'Expect heavy rain in Thailand during the month of August—but that...',
    image: '/images/morocco.jpg',
    slug: 'thailand-in-august'
  },
  {
    id: 3,
    title: '1 Week in Thailand - 4 Unique Itinerary Ideas',
    description: 'With one week in Thailand, it\'s best to explore just one of its regions...',
    image: '/images/morocco.jpg',
    slug: '1-week-in-thailand'
  }
])

const toursData: TourDetail[] = [
  {
    slug: 'best-of-thailand---8-days',
    title: 'Best of Thailand - 8 Days',
    author: 'Ha Thu',
    date: 'March 8, 2019',
    heroImage: '/images/costa-rica.jpg',
    description: "If you've ever dreamed of Thailand, this 8-day adventure covers the highlights with a perfect mix of guided tours and free time. Start off in vibrant Bangkok for iconic Buddhist sites and a gourmet dinner cruise on the Chao Phraya River. Then fly north to Chiang Mai to explore the Old Town on foot and spend a day with elephants. Finish off in Phuket, where you'll kayak, island-hop, and relax on gorgeous white sand beaches.",
    highlights: [
      "Let a private guide show you Bangkok's Grand Palace and Emerald Buddha",
      'Spend a day feeding and bathing elephants at a respected sanctuary',
      'Kayak between limestone cliffs and pristine islands off Phang Nga Bay',
      "Explore Thailand's restaurant and street food scene in all three stops"
    ],
    itinerary: [
      {
        day: 1,
        title: 'Arrive in Bangkok, Explore',
        overnight: 'Bangkok',
        image: '/images/morocco.jpg',
        image_title: "Bangkok's Wat Arun Temple sits along the Chao Phraya River",
        details: "Welcome to Thailand! Get ready for a smooth and easy arrival at Bangkok's international airport with 'VIP Meet and Greet and Fast Track Service.' This special process starts at the airport's air bridge, where you'll be met by a representative who will personally escort you to the immigration counter without a queue. You'll then be introduced to your private driver for transportation to the hotel. The rest of the day is free time to explore at your own pace."
      },
      {
        day: 2,
        title: 'Bangkok City Tour & Dinner Cruise',
        overnight: 'Bangkok',
        image: '/images/morocco.jpg',
        image_title: 'Spend a few hours at the Grand Palace, one of the most important historical attractions in Bangkok',
        details: 'After breakfast at your hotel, your private guide will pick you up for a fun day of sightseeing. The tour starts at The Grand Palace, one of the most important and impressive royal palaces in Thailand. Your guide will show you the site\'s many different palaces, including the mysterious Emerald Buddha. From here, you\'ll continue to Wat Pho to see the famous 149-foot reclining Buddha. End your day with a six-course dinner cruise on the Chao Phraya River.'
      },
      {
        day: 3,
        title: 'Fly to Chiang Mai, Explore',
        overnight: 'Chiang Mai',
        image: '/images/morocco.jpg',
        image_title: "Walk between key sites in Chiang Mai's Old Town like Wat Phan Tao",
        details: 'After breakfast, take a memorable transfer by long-tail boat to Resotel Pier before taking a short flight to Chiang Mai. Upon arrival, check into your hotel and explore historic Old Town on foot, including visits to Wat Phra Singh, Wat Chedi Luang, and Wat Phan Tao.'
      },
      {
        day: 4,
        title: 'Full-Day Elephant Jungle Sanctuary Excursion',
        overnight: 'Chiang Mai',
        image: '/images/morocco.jpg',
        image_title: 'Spend some quality time with gentle giants at this elephant sanctuary',
        details: 'Head 1.5 hours outside the city to a Karen Village and walk to the Elephant Jungle Sanctuary. Change into traditional hill tribe clothing, learn about elephant behavior, feed them, and take part in a mud spa and river swim with the gentle giants.'
      },
      {
        day: 5,
        title: 'Fly to Phuket, Explore',
        overnight: 'Phuket',
        image: '/images/morocco.jpg',
        image_title: 'Phuket is the largest island in Thailand along the Andaman Sea',
        details: 'Enjoy a free morning in Chiang Mai before taking your flight to Phuket. Spend the afternoon and evening exploring the turquoise waters, pristine beaches, vibrant restaurants, and nightlife of Thailand’s largest island.'
      },
      {
        day: 6,
        title: 'Kayaking Tour in Phang Nga Bay',
        overnight: 'Phuket',
        image: '/images/morocco.jpg',
        image_title: 'Steep limestone cliffs provide a stunning backdrop to the colorful sea',
        details: 'Spend a relaxed morning in Phuket before joining an afternoon kayaking and island-hopping tour around the dramatic limestone cliffs of Phang Nga Bay. Enjoy a prepared Thai seafood buffet dinner while watching the sunset over the bay.'
      },
      {
        day: 7,
        title: 'Free Day in Phuket',
        overnight: 'Phuket',
        image: '/images/morocco.jpg',
        image_title: 'Freedom Beach is off the beaten track and requires a hike or boat ride to arrive',
        details: 'Spend your final full day relaxing on Kata Beach, visiting Nai Harn Beach, or trekking out to the isolated shores of Freedom Beach for quiet turquoise waters and soft white sand.'
      },
      {
        day: 8,
        title: 'Depart Phuket',
        overnight: 'N/A',
        image: '/images/morocco.jpg',
        image_title: 'Until next time, Phuket!',
        details: 'Enjoy your remaining free time in Phuket after breakfast before your private driver escorts you to the airport for your departure flight home.'
      }
    ]
  }
]

onMounted(() => {
  const currentSlug = route.params.slug as string
  tour.value = toursData.find((t) => t.slug === currentSlug) || toursData[0]
})
</script>