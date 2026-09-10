<template>
  <div>
    <!-- Hero video with overlay text -->
    <div class="relative">
      <video
        class="w-full h-[280px] sm:h-[420px] object-cover pointer-events-none"
        autoplay
        muted
        loop
        playsinline
        disablePictureInPicture
        disableRemotePlayback
      >
        <source src="/images/web video.mp4" type="video/mp4" />
        Your browser does not support the video tag.
      </video>

      <!-- dark gradient so white text stays readable -->
      <div class="absolute inset-0 bg-gradient-to-b from-black/40 via-black/10 to-black/40"></div>

        <div class="absolute inset-0 flex flex-col justify-center max-w-6xl mx-auto px-4 pt-24">
            <h1 class="text-white text-3xl sm:text-5xl font-light">
            Welcome to <span class="font-bold">Travaisa!</span>
            </h1>
            <p class="text-white/90 text-sm sm:text-base font-semibold mt-2">
            Find Flights, Hotels, Visa &amp; Holidays
            </p>
        </div>
    </div>

    <!-- Search card, pulled up over the video -->
    <div class="max-w-6xl mx-auto px-4">
      <div class="bg-white rounded-xl shadow-lg -mt-10 sm:-mt-14 relative z-10">

        <!-- Service tabs -->
        <div class="flex items-center gap-6 overflow-x-auto px-4 sm:px-6 border-b border-gray-100">
          <button
            v-for="tab in tabs"
            :key="tab.name"
            @click="activeTab = tab.name"
            class="flex items-center gap-2 py-4 text-sm font-medium whitespace-nowrap border-b-2 transition-colors"
            :class="activeTab === tab.name
              ? 'text-blue-500 border-blue-500'
              : 'text-gray-500 border-transparent hover:text-gray-700'"
          >
            <component :is="tab.icon" class="w-4 h-4" />
            {{ tab.label }}
            <span
              v-if="tab.badge"
              class="text-[10px] bg-green-500 text-white px-1.5 py-0.5 rounded-full leading-none"
            >{{ tab.badge }}</span>
          </button>
        </div>

        <!-- Trip type + traveller/class -->
        <div class="flex flex-wrap items-center justify-between gap-3 px-4 sm:px-6 pt-4">
          <div class="flex items-center gap-4 text-sm">
            <label
              v-for="type in tripTypes"
              :key="type"
              class="flex items-center gap-1.5 cursor-pointer"
              :class="tripType === type ? 'text-blue-600 font-semibold' : 'text-gray-500'"
            >
              <input type="radio" :value="type" v-model="tripType" class="accent-blue-600" />
              {{ type }}
            </label>
          </div>

          <div class="flex items-center gap-2">
            <button class="text-sm bg-blue-50 text-blue-700 font-medium px-3 py-2 rounded-lg">
              1 Traveller ▾
            </button>
            <button class="text-sm bg-blue-50 text-blue-700 font-medium px-3 py-2 rounded-lg">
              Economy ▾
            </button>
          </div>
        </div>

        <!-- From / To / Dates / Search -->
        <div class="flex flex-col md:flex-row gap-3 px-4 sm:px-6 py-4">
          <div class="flex-1 flex items-center border border-gray-200 rounded-lg px-3 py-2">
            <div>
              <div class="text-xs text-gray-400">From</div>
              <div class="font-bold">DAC <span class="font-normal text-sm">Dhaka</span></div>
            </div>
            <button class="mx-auto text-gray-400">⇄</button>
            <div>
              <div class="text-xs text-gray-400">To</div>
              <div class="font-bold">CXB <span class="font-normal text-sm">Cox's Bazar</span></div>
            </div>
          </div>

          <div class="flex-1 border border-gray-200 rounded-lg px-3 py-2">
            <div class="text-xs text-gray-400">Departure</div>
            <div class="font-bold">11 September</div>
          </div>

          <div class="flex-1 border border-gray-200 rounded-lg px-3 py-2">
            <div class="text-xs text-gray-400">Return</div>
            <div class="font-bold">13 September</div>
          </div>

          <button class="bg-blue-500 hover:bg-blue-600 text-white rounded-lg px-6 flex items-center justify-center">
            <font-awesome-icon :icon="['fas', 'search']" />
          </button>
        </div>

        <!-- Fare type -->
        <div class="flex items-center gap-4 text-sm px-4 sm:px-6 pb-4">
          <label
            v-for="fare in fareTypes"
            :key="fare"
            class="flex items-center gap-1.5 cursor-pointer"
            :class="fareType === fare ? 'text-blue-600 font-semibold' : 'text-gray-500'"
          >
            <input type="radio" :value="fare" v-model="fareType" class="accent-blue-600" />
            {{ fare }}
          </label>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'

const activeTab = ref('flight')
const tripType = ref('Round Trip')
const fareType = ref('Regular Fare')
const tripTypes = ['One Way', 'Round Trip', 'Multi City']
const fareTypes = ['Regular Fare', 'Student Fare', 'Umrah Fare']

const tabs = [
  { name: 'flight', label: 'Flight', icon: 'div', badge: null },
  { name: 'hotel', label: 'Hotel', icon: 'div', badge: null },
  { name: 'holiday', label: 'Holiday', icon: 'div', badge: null },
  { name: 'visa', label: 'Visa', icon: 'div', badge: null },
  { name: 'umrah', label: 'Umrah', icon: 'div', badge: null },
]
</script>

