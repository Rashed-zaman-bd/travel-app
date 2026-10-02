<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Loading -->
    <div v-if="loading" class="flex justify-center py-24">
      <div class="h-10 w-10 animate-spin rounded-full border-4 border-gray-300 border-t-blue-600"></div>
    </div>

    <!-- Error -->
    <div v-else-if="error" class="mx-auto max-w-xl px-4 py-24 text-center">
      <p class="mb-4 text-red-600">{{ error }}</p>
      <button
        class="min-h-[44px] rounded bg-blue-600 px-6 py-2 text-white"
        @click="fetchData"
      >
        Retry
      </button>
    </div>

    <template v-else-if="category">
      <!-- Country hero -->
      <section class="relative h-44 w-full overflow-hidden bg-gray-800 sm:h-60 md:h-72 lg:h-80">
        <img
          v-if="category.image"
          :src="category.image"
          :alt="tr(category.country_name)"
          class="absolute inset-0 h-full w-full object-cover"
        />
        <!-- dark overlay keeps the title readable on any image -->
        <div class="absolute inset-0 bg-black/40"></div>

        <div class="absolute inset-0 flex items-center justify-center px-4">
          <h1
            class="break-words text-center text-2xl font-bold text-white drop-shadow sm:text-4xl md:text-5xl"
          >
            {{ tr(category.country_name) }}
          </h1>
        </div>
      </section>

      <!-- Packages -->
      <section class="mx-auto max-w-7xl px-4 py-8 sm:px-6 sm:py-10 lg:px-8">
        <div class="mb-6 text-center sm:mb-10">
          <h2 class="text-xl font-semibold text-amber-500 sm:text-2xl md:text-3xl">
            {{ tr(category.country_name) }} {{ t('worldwide_category.tours_itineraries') }}
          </h2>
        </div>

        <p v-if="!packages.length" class="py-16 text-center text-gray-500">
          No packages available for this country yet.
        </p>

        <div v-else class="grid grid-cols-1 gap-2 sm:grid-cols-2 sm:gap-2 lg:grid-cols-3">
          <div
            v-for="pkg in packages"
            :key="pkg.id"
            class="group flex h-full flex-col overflow-hidden bg-white shadow transition hover:shadow-lg"
          >
            <!-- Image -> details page -->
            <router-link
              :to="`/tour-package/${pkg.slug}`"
              class="block aspect-[16/10] w-full overflow-hidden bg-gray-200"
            >
              <img
                v-if="pkg.package_image"
                :src="pkg.package_image"
                :alt="tr(pkg.package_image_title) || tr(pkg.package_name)"
                class="h-full w-full object-cover transition duration-300 md:group-hover:scale-105"
                loading="lazy"
              />
            </router-link>

            <div class="flex flex-1 flex-col p-4">
              <!-- Name + destination -> details page -->
              <router-link :to="`/tour-package/${pkg.slug}`" class="block">
                <h3 class="mb-2 line-clamp-2 break-words text-base font-semibold text-gray-800 sm:text-lg">
                  {{ tr(pkg.package_name) }}
                </h3>

                <p
                  v-if="tr(pkg.package_destination)"
                  class="mb-1 line-clamp-7 break-words text-lg text-gray-500"
                >
                  {{ tr(pkg.package_destination) }}
                </p>
              

              <p
                v-if="tr(pkg.package_duration)"
                class="mt-2 pb-2 text-sm font-semibold text-gray-700 sm:text-base"
              >
                🕒 {{ tr(pkg.package_duration) }}
              </p>

              <!-- mt-auto pins the footer to the card bottom so cards align in a row -->
              <div class="mt-auto mt-2 flex flex-wrap items-center justify-between gap-x-3  border-gray-100">
                <span class="text-sm font-semibold text-red-600 sm:text-base">
                  <span class="text-gray-700">{{ t('worldwide_category.cost') }}-</span>
                  {{ tr(pkg.package_price) }}<template v-if="tr(pkg.package_price)"> Tk.</template>
                </span>
                <span class="text-sm font-semibold text-amber-600 md:group-hover:underline">
                  {{ t('worldwide_category.show_details') }} →
                </span>
              </div>
              </router-link>
                <!-- Book now -> booking form page -->
                <div class="flex items-center justify-center">
                  <router-link
                    :to="`/tour-package/${pkg.slug}/book`"
                    class="mt-5 flex w-2/3 items-center justify-center rounded-lg bg-amber-500 px-4 py-2 text-lg font-semibold text-white hover:bg-amber-600"
                  >
                    {{ t('worldwide_category.book_now') }} →
                  </router-link>
                </div>
            </div>
            
          </div>
        </div>
      </section>
    </template>
  </div>
</template>



<script setup lang="ts">
import { ref, onMounted, watch } from 'vue'
import { useRoute } from 'vue-router'
import { useI18n } from 'vue-i18n'
import api from '@/services/api'

type Localized = Record<string, string> | null | undefined

interface Category {
  id: number
  slug: string
  image: string | null
  country_name: Localized
}

interface TourPackage {
  id: number
  slug: string
  header: Localized
  package_name: Localized
  package_image: string | null
  package_image_title: Localized
  package_price: Localized
  package_duration: Localized
  package_destination: Localized
  order: number
  is_active: boolean
}

const route = useRoute()
const { t, locale } = useI18n({ useScope: 'global' })

const category = ref<Category | null>(null)
const packages = ref<TourPackage[]>([])
const loading = ref(true)
const error = ref('')

// Pick current locale, fall back to en, then bn
const tr = (value: Localized): string => {
  if (!value) return ''
  return value[locale.value] || value.en || value.bn || ''
}

const fetchData = async () => {
  loading.value = true
  error.value = ''
  try {
    const slug = route.params.slug as string
    const { data } = await api.get(`/category/${slug}/tour-package`)
    category.value = data.category
    packages.value = data.data
  } catch (e: any) {
    error.value =
      e?.response?.status === 404
        ? 'Country not found.'
        : 'Failed to load packages. Please try again.'
  } finally {
    loading.value = false
  }
}

onMounted(fetchData)

// Reload when navigating from one country to another
watch(() => route.params.slug, (n, o) => n && n !== o && fetchData())
</script>