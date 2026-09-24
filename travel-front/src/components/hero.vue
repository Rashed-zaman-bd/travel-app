<template>
  <section class="relative w-full h-[50vh] min-h-[300px] max-h-[500px] md:max-h-[300px] lg:max-h-[500px] overflow-hidden">
    <!-- Loading State -->
    <div v-if="loading" class="w-full h-full flex items-center justify-center bg-slate-900 text-white">
      <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-sky-400"></div>
    </div>

    <!-- Error State -->
    <div v-else-if="error" class="w-full h-full flex items-center justify-center bg-slate-900 text-red-400 text-sm">
      {{ error }}
    </div>

    <!-- Swiper Carousel -->
    <swiper
      v-else-if="slides.length > 0"
      :modules="modules"
      :slides-per-view="1"
      :space-between="0"
      :loop="slides.length > 1"
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
            class="slide-image absolute inset-0 w-full h-full object-cover scale-100 transition-transform duration-[7000ms] ease-out"
          />

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
                <span v-if="slide.com_name" class="font-bold text-2xl sm:text-3xl md:text-4xl text-amber-400">
                  '{{ slide.com_name }}'
                </span>
              </p>

              <!-- CTA Button -->
              <div v-if="slide.cta_text" class="slide-anim-cta opacity-0">
                <a
                  :href="slide.cta_url || '#'"
                  class="group inline-flex items-center gap-2.5 px-5 py-2.5 sm:px-7 sm:py-3.5 text-sm sm:text-base font-semibold text-white bg-gradient-to-r from-amber-600 to-amber-600 hover:from-amber-700 hover:to-amber-700 rounded-xl shadow-lg shadow-amber-600/35 hover:shadow-amber-600/50 hover:-translate-y-0.5 transition-all duration-300 ease-out cursor-pointer"
                >
                  <span class="botton-text tracking-wider">{{ slide.cta_text }}</span>
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
                </a>
              </div>
            </div>
          </div>

          <!-- Bottom Right Image Credit Overlay -->
          <div
            v-if="slide.author_name"
            class="absolute bottom-4 right-4 sm:bottom-6 sm:right-2 lg:bottom-8 lg:right-8 z-[2] flex flex-wrap items-center justify-end gap-1 sm:gap-1.5 text-xs sm:text-sm lg:text-base text-right pointer-events-none"
          >
            <p v-if="slide.photo_text" class="text-slate-100 m-0">{{ slide.photo_text }},</p>
            <div class="font-semibold text-white">
              {{ slide.author_name }}<span v-if="slide.location || slide.photo_date">,</span>
            </div>
            <div v-if="slide.location" class="text-amber-400 font-medium">
              {{ slide.location }}<span v-if="slide.photo_date">,</span>
            </div>
            <div v-if="slide.photo_date" class="text-slate-300/80">{{ slide.photo_date }}</div>
          </div>
        </div>
      </swiper-slide>
    </swiper>
  </section>
</template>

<script setup lang="ts">
import { ref, onMounted, onUnmounted, watch } from 'vue'
import { Swiper, SwiperSlide } from 'swiper/vue'
import { Autoplay, Pagination, Navigation, EffectFade } from 'swiper/modules'
import { useI18n } from 'vue-i18n'

import api from '@/services/api'

// Import Swiper styles
import 'swiper/css'
import 'swiper/css/pagination'
import 'swiper/css/navigation'
import 'swiper/css/effect-fade'

interface PublicHeroSlide {
  id: number
  title: string
  description: string
  com_name: string | null
  image: string
  cta_text: string | null
  cta_url: string | null
  photo_text: string | null
  author_name: string | null
  location: string | null
  photo_date: string | null
  order: number
  is_active: boolean
  created_at: string
}

const modules = [Autoplay, Pagination, Navigation, EffectFade]

const { locale } = useI18n({ useScope: 'global' })

const slides = ref<PublicHeroSlide[]>([])
const loading = ref(false)
const error = ref<string | null>(null)

async function fetchSlides() {
  loading.value = true
  error.value = null
  
  // Resolve current active language
  const currentLang =
    (typeof locale.value === 'string' ? locale.value : (locale as any).value) ||
    localStorage.getItem('locale') ||
    'en'

  try {
    // Explicitly passing lang query parameter and header guarantees backend receives it
    const { data } = await api.get('/hero-slides', {
      params: { lang: currentLang },
      headers: {
        'X-Locale': currentLang,
        'Accept-Language': currentLang,
      },
    })
    slides.value = data.data
  } catch (e: any) {
    error.value = e?.response?.data?.message || 'Failed to load hero slides.'
  } finally {
    loading.value = false
  }
}

// Watch i18n locale change directly
watch(
  () => (typeof locale.value === 'string' ? locale.value : (locale as any).value),
  () => {
    fetchSlides()
  },
  { immediate: false }
)

// Global custom event fallback to catch language switches from Header
const handleLocaleChange = () => {
  fetchSlides()
}

onMounted(() => {
  fetchSlides()
  window.addEventListener('locale-changed', handleLocaleChange)
})

onUnmounted(() => {
  window.removeEventListener('locale-changed', handleLocaleChange)
})
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