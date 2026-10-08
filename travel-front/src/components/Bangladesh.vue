<template>
  <section v-if="loading || error || packages.length" class="w-full py-10 overflow-hidden">
    <div class="mx-auto max-w-7xl px-4 border-b border-gray-200">
      <div class="flex items-center justify-center mb-6">
        <h2 class="mb-3 text-2xl font-semibold text-amber-600 md:text-3xl">
          {{ t('worldwide_category.bangladesh') }}
        </h2>
      </div>

      <!-- Loading -->
      <div v-if="loading" class="flex justify-center py-12">
        <div class="h-10 w-10 animate-spin rounded-full border-4 border-gray-300 border-t-amber-600"></div>
      </div>

      <!-- Error -->
      <div v-else-if="error" class="py-10 text-center">
        <p class="mb-3 text-red-600">{{ error }}</p>
        <button
          type="button"
          class="cursor-pointer rounded bg-amber-600 px-5 py-2 text-white hover:bg-amber-700"
          @click="fetchData"
        >
          Retry
        </button>
      </div>

      <!-- Slider -->
      <Swiper
        v-else
        :modules="modules"
        effect="coverflow"
        :grab-cursor="true"
        :centered-slides="true"
        :slides-per-view="3"
        :loop="canLoop"
        :speed="800"
        :autoplay="{
            delay: 2500,
            disableOnInteraction: false,
            pauseOnMouseEnter: true
        }"
        :coverflow-effect="{
            rotate: 8,
            stretch: 0,
            depth: 120,
            modifier: 1,
            slideShadows: false
        }"
        :pagination="{ clickable: true }"
        :breakpoints="{
            0: {
            slidesPerView: 1.5
            },
            640: {
            slidesPerView: 2.5
            },
            1024: {
            slidesPerView: 3
            },
            1280: {
            slidesPerView: 4
            }
        }"
        class="bangladesh-swiper"
        >
        <SwiperSlide
            v-for="pkg in packages"
            :key="pkg.id"
            class="destination-slide"
        >
            <router-link
            :to="`/tour-package/${pkg.slug}`"
            class="destination-card block"
            >
            <img
                v-if="pkg.package_image"
                :src="pkg.package_image"
                :alt="tr(pkg.package_image_title) || tr(pkg.package_name)"
                class="h-full w-full object-cover"
                loading="lazy"
            />

            <div
                class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent"
            ></div>

            <div class="absolute bottom-0 left-0 w-full p-5 text-white">
                <p
                v-if="tr(pkg.category?.country_name)"
                class="mb-1 text-xs uppercase tracking-wide text-amber-300"
                >
                {{ tr(pkg.category?.country_name) }}
                </p>

                <h3 class="line-clamp-2 break-words text-xl font-bold">
                {{ tr(pkg.package_name) }}
                </h3>

                <div class="mt-2 flex items-center justify-between gap-2 text-sm">
                <span v-if="tr(pkg.package_duration)">
                    🕒 {{ tr(pkg.package_duration) }}
                </span>

                <span
                    v-if="tr(pkg.package_price)"
                    class="font-semibold"
                >
                    BDT {{ tr(pkg.package_price) }}
                </span>
                </div>
            </div>
            </router-link>
        </SwiperSlide>
        </Swiper>
    </div>
  </section>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { useI18n } from 'vue-i18n'
import { Swiper, SwiperSlide } from 'swiper/vue'
import { EffectCoverflow, Autoplay, Pagination } from 'swiper/modules'
import api from '@/services/api'

import 'swiper/css'
import 'swiper/css/effect-coverflow'
import 'swiper/css/pagination'

const { t, locale } = useI18n({ useScope: 'global' })

const modules = [EffectCoverflow, Autoplay, Pagination]

type Flag = boolean | number | string | null | undefined
type Localized = string | number | Record<string, string | null> | null | undefined

interface Category {
  id: number
  slug: string | null
  country_name: Localized
  order?: number
  is_active?: Flag
  domestic?: Flag
}

interface TourPackage {
  id: number
  slug: string
  package_name: Localized
  package_price: Localized
  package_duration: Localized
  package_image: string | null
  package_image_title?: Localized
  is_active?: boolean
  category?: { slug: string | null; country_name: Localized } | null
}

const isOn = (v: Flag): boolean => v === true || v === 1 || v === '1' || v === 'true'

const tr = (value: Localized): string => {
  if (value === null || value === undefined || value === '') return ''
  if (typeof value === 'string') return value
  if (typeof value === 'number') return String(value)
  return value[locale.value] || value.en || value.bn || ''
}

const packages = ref<TourPackage[]>([])
const loading = ref(true)
const error = ref('')

// Swiper's loop needs enough slides, otherwise it breaks. Below that it rewinds instead.
const canLoop = computed(() => packages.value.length >= 5)

const fetchData = async () => {
  loading.value = true
  error.value = ''

  try {
    // 1) only active + domestic categories
    const res = await api.get('/category')
    const list: Category[] = res.data?.data ?? res.data ?? []

    const domestic = list
      .filter((c) => c.slug && isOn(c.is_active) && isOn(c.domestic))
      .sort((a, b) => (a.order ?? 0) - (b.order ?? 0))

    // 2) active packages of each of those categories (already ordered by the API)
    const results = await Promise.allSettled(
      domestic.map((c) => api.get(`/category/${c.slug}/tour-package`))
    )

    const items: TourPackage[] = []
    results.forEach((r, i) => {
      if (r.status !== 'fulfilled') return
      const rows: TourPackage[] = r.value.data?.data ?? []
      const cat = domestic[i]
      rows.forEach((p) =>
        items.push({
          ...p,
          category: p.category ?? { slug: cat.slug, country_name: cat.country_name },
        })
      )
    })

    packages.value = items.filter((p) => p.is_active !== false)

    if (domestic.length && results.every((r) => r.status === 'rejected')) {
      error.value = 'Failed to load packages.'
    }
  } catch (e: any) {
    packages.value = []
    error.value = e?.response?.data?.message || 'Failed to load packages.'
  } finally {
    loading.value = false
  }
}

onMounted(fetchData)
</script>

<style scoped>
.bangladesh-swiper {
  width: 100%;
  padding-top: 10px;
  padding-bottom: 55px;
}

.destination-slide {
  height: 400px;
  transition: transform 0.5s ease;
}

.destination-card {
  position: relative;
  width: 100%;
  height: 100%;
  overflow: hidden;
  border-radius: 0px;
  background: #ddd;
}

.destination-card img {
  transition: transform 0.6s ease;
}

.destination-card:hover img {
  transform: scale(1.05);
}

/* Pagination */
:deep(.swiper-pagination-bullet) {
  width: 8px;
  height: 8px;
  opacity: 1;
  background: #ffd1b9;
}

:deep(.swiper-pagination-bullet-active) {
  width: 14px;
  border-radius: 999px;
  background: #f58216;
}

/* Mobile */
@media (max-width: 640px) {
  .destination-slide {
    height: 350px;
  }
}
</style>