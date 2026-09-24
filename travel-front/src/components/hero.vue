<template>
  <section class="relative w-full h-[50vh] min-h-[300px] max-h-[400px] md:max-h-[300px] lg:max-h-[400px] overflow-hidden">
    <swiper
      :modules="modules"
      :slides-per-view="1"
      :space-between="0"
      :loop="true"
      :effect="'fade'"
      :fade-effect="{ crossFade: true }"
      :autoplay="{
        delay: 6000,
        disableOnInteraction: false,
      }"
      :pagination="{ clickable: true, dynamicBullets: true }"
      :navigation="true"
      class="hero-swiper w-full h-full"
    >
      <swiper-slide v-for="slide in slides" :key="slide.id">
        <div class="relative w-full h-full flex items-center">
          <!-- Background Image -->
          <img
            :src="slide.image"
            :alt="slide.title"
            class="slide-image absolute inset-0 w-full h-full object-cover scale-105 transition-transform duration-[7000ms] ease-out"
          />

          <!-- Gradient Overlay -->
          <div
            class="absolute inset-0 bg-gradient-to-r from-slate-900/85 via-slate-900/40 to-slate-900/10 z-[1]"
          ></div>

          <!-- Hero Content Center -->
          <div class="relative z-[2] w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex justify-center">
            <div class="flex flex-col items-center justify-center text-center max-w-2xl mx-auto">
              <!-- Title -->
              <h1
                class="slide-anim-title text-2xl sm:text-3xl md:text-4xl lg:text-5xl font-semibold leading-tight text-white tracking-wide mb-3 sm:mb-4 opacity-0"
              >
                {{ slide.title }}
              </h1>

              <!-- Description -->
              <p
                class="slide-anim-desc text-base sm:text-xl md:text-2xl font-medium leading-relaxed text-white mb-6 opacity-0"
              >
                {{ slide.description }}
                <span class="font-bold text-2xl sm:text-3xl md:text-4xl text-sky-400">'{{ slide.comName }}'</span>
              </p>

              <!-- CTA Button -->
              <div v-if="slide.ctaText" class="slide-anim-cta opacity-0">
                <button
                  class="group inline-flex items-center gap-2.5 px-5 py-2.5 sm:px-7 sm:py-3.5 text-sm sm:text-base font-semibold text-white bg-gradient-to-r from-sky-600 to-blue-600 hover:from-sky-700 hover:to-blue-700 rounded-xl shadow-lg shadow-blue-600/35 hover:shadow-blue-600/50 hover:-translate-y-0.5 transition-all duration-300 ease-out cursor-pointer"
                >
                  <span>{{ slide.ctaText }}</span>
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="w-4 h-4 sm:w-5 sm:h-5 transition-transform duration-200 group-hover:translate-x-1"
                    viewBox="0 0 20 20"
                    fill="currentColor"
                  >
                    <path
                      fill-rule="evenodd"
                      d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z"
                      clip-rule="evenodd"
                    />
                  </svg>
                </button>
              </div>
            </div>
          </div>

          <!-- Bottom Right Image Credit Overlay -->
          <div
            v-if="slide.authorName"
            class="absolute bottom-4 right-4 sm:bottom-6 sm:right-2 lg:bottom-8 lg:right-8 z-[2] flex flex-wrap items-center justify-end gap-1 sm:gap-1.5 text-xs sm:text-sm lg:text-base text-right pointer-events-none"
          >
            <p class="text-slate-100 m-0">{{ slide.photoText }},</p>
            <div class="font-semibold text-white">{{ slide.authorName }}<span v-if="slide.location || slide.photoDate">,</span></div>
            <div v-if="slide.location" class="text-sky-400 font-medium">{{ slide.location }}<span v-if="slide.photoDate">,</span></div>
            <div v-if="slide.photoDate" class="text-slate-300/80">{{ slide.photoDate }}</div>
          </div>
        </div>
      </swiper-slide>
    </swiper>
  </section>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { Swiper, SwiperSlide } from 'swiper/vue'
import { Autoplay, Pagination, Navigation, EffectFade } from 'swiper/modules'

// Import Swiper styles
import 'swiper/css'
import 'swiper/css/pagination'
import 'swiper/css/navigation'
import 'swiper/css/effect-fade'

interface Slide {
  id: number
  tagline?: string
  title: string
  description: string
  comName: string
  image: string
  ctaText?: string
  photoText?: string
  authorName?: string
  location?: string
  photoDate?: string
}

const modules = [Autoplay, Pagination, Navigation, EffectFade]

const slides = ref<Slide[]>([
  {
    id: 1,
    title: 'Where do you want to go?',
    description: 'Do it easy with',
    comName: 'Travaisa',
    image: 'https://images.unsplash.com/photo-1618005182384-a83a8bd57fbe?q=80&w=1920&auto=format&fit=crop',
    ctaText: 'Get Started Free',
    photoText: 'Photo by Travaisa traveler',
    authorName: 'Alex Morgan',
    location: 'Kyoto, Japan',
    photoDate: 'October 2025'
  },
  {
    id: 2,
    title: 'Where do you want to go?',
    description: 'Do it easy with',
    comName: 'Travaisa',
    image: 'https://images.unsplash.com/photo-1550745165-9bc0b252726f?q=80&w=1920&auto=format&fit=crop',
    ctaText: 'Explore Features',
    photoText: 'Photo by Travaisa traveler',
    authorName: 'Sarah Chen',
    location: 'Santorini, Greece',
    photoDate: 'August 2025'
  },
  {
    id: 3,
    title: 'Where do you want to go?',
    description: 'Do it easy with',
    comName: 'Travaisa',
    image: 'https://images.unsplash.com/photo-1526374965328-7f61d4dc18c5?q=80&w=1920&auto=format&fit=crop',
    ctaText: 'Join the Community',
    photoText: 'Photo by Travaisa traveler',
    authorName: 'David Miller',
    location: 'Banff, Canada',
    photoDate: 'January 2026'
  }
])
</script>

<style scoped>
/* Image Zoom on active slide */
:deep(.swiper-slide-active .slide-image) {
  transform: scale(1);
}

/* Entry Animations for Active Slide Text */
:deep(.swiper-slide-active .slide-anim-title) {
  animation: fadeInUp 0.8s cubic-bezier(0.16, 1, 0.3, 1) 0.1s forwards;
}

:deep(.swiper-slide-active .slide-anim-desc) {
  animation: fadeInUp 0.8s cubic-bezier(0.16, 1, 0.3, 1) 0.25s forwards;
}

:deep(.swiper-slide-active .slide-anim-cta) {
  animation: fadeInUp 0.8s cubic-bezier(0.16, 1, 0.3, 1) 0.4s forwards;
}

@keyframes fadeInUp {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

/* Customizing Swiper Navigation & Pagination */
:deep(.swiper-button-next),
:deep(.swiper-button-prev) {
  width: 44px;
  height: 44px;
  color: #ffffff;
  transition: all 0.3s ease;
}

:deep(.swiper-button-next::after),
:deep(.swiper-button-prev::after) {
  font-size: 1.1rem;
  font-weight: bold;
}

/* Hide Swiper Navigation arrows on mobile screens */
@media (max-width: 639px) {
  :deep(.swiper-button-next),
  :deep(.swiper-button-prev) {
    display: none;
  }
}

:deep(.swiper-pagination-bullet) {
  background: #ffffff;
  opacity: 0.4;
  transition: all 0.3s ease;
}

:deep(.swiper-pagination-bullet-active) {
  opacity: 1;
  background: #f1f2f3;
  width: 24px;
  border-radius: 6px;
}
</style>