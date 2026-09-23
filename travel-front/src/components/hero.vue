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

