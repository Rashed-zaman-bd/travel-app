<template>
  <div
    class="mx-auto max-w-7xl border-b border-gray-200 bg-white p-4 py-6"
  >
    <!-- Loading -->
    <div
      v-if="loading"
      class="flex h-40 items-center justify-center rounded-lg bg-gray-100"
    >
      Loading offers...
    </div>

    <!-- No offers -->
    <div
      v-else-if="offers.length === 0"
      class="flex h-40 items-center justify-center rounded-lg bg-gray-100 text-gray-500"
    >
      No offers available.
    </div>

    <!-- Offers -->
    <Swiper
      v-else
      :slides-per-view="1"
      :space-between="10"
      :loop="offers.length > 2"
      :autoplay="{
        delay: 6000,
        disableOnInteraction: false,
      }"
      :pagination="{
        clickable: true,
      }"
      :navigation="true"
      :breakpoints="{
        640: {
          slidesPerView: 1,
          spaceBetween: 15,
        },
        768: {
          slidesPerView: 2,
          spaceBetween: 20,
        },
        1024: {
          slidesPerView: 2,
          spaceBetween: 20,
        },
      }"
      :modules="modules"
      class="mySwiper"
    >
      <SwiperSlide
        v-for="offer in offers"
        :key="offer.id"
      >
        <!-- Whole card is clickable -->
        <a
          v-if="offer.url"
          :href="offer.url"
          class="relative block h-40 overflow-hidden bg-gray-100"
        >
          <!-- Image -->
          <img
            v-if="offer.image"
            :src="offer.image"
            :alt="getText(offer.title)"
            class="h-full w-full object-cover transition duration-300 hover:scale-105"
          />

          <!-- No Image -->
          <div
            v-else
            class="flex h-full w-full items-center justify-center bg-gray-200 text-gray-500"
          >
            No Image
          </div>

          <!-- Overlay -->
          <div
            class="absolute inset-0 bg-black/50"
          ></div>

          <!-- Content -->
          <div
            class="absolute inset-0 flex flex-col justify-center p-5 text-white"
          >
            <h3
              v-if="getText(offer.title)"
              class="mb-1 text-lg font-bold"
            >
              {{ getText(offer.title) }}
            </h3>

            <p
              v-if="getText(offer.location)"
              class="mb-1 text-sm"
            >
              <i class="bi bi-geo-alt-fill mr-1"></i>
              {{ getText(offer.location) }}
            </p>

            <p
              v-if="getText(offer.tour_duration)"
              class="mb-1 text-sm"
            >
              <i class="bi bi-clock-fill mr-1"></i>
              {{ getText(offer.tour_duration) }}
            </p>

            <div
              class="mt-1 flex items-center justify-between gap-3"
            >
              <span
                v-if="
                  offer.price !== null &&
                  offer.price !== undefined
                "
                class="text-lg font-bold"
              >
               BDT {{ offer.price }}
              </span>

              <span
                v-if="getText(offer.discount)"
                class="rounded bg-orange-500 px-2 py-1 text-xs font-semibold"
              >
                {{ getText(offer.discount) }}
              </span>
            </div>
          </div>
        </a>

        <!-- Card without URL -->
        <div
          v-else
          class="relative h-40 overflow-hidden bg-gray-100"
        >
          <img
            v-if="offer.image"
            :src="offer.image"
            :alt="getText(offer.title)"
            class="h-full w-full object-cover"
          />

          <div
            v-else
            class="flex h-full w-full items-center justify-center bg-gray-200 text-gray-500"
          >
            No Image
          </div>

          <div
            class="absolute inset-0 bg-black/40"
          ></div>

          <div
            class="absolute inset-0 flex flex-col justify-center p-5 text-white"
          >
            <h3
              v-if="getText(offer.title)"
              class="text-xl font-bold"
            >
              {{ getText(offer.title) }}
            </h3>

            <p
              v-if="getText(offer.location)"
              class="text-sm"
            >
              {{ getText(offer.location) }}
            </p>
          </div>
        </div>
      </SwiperSlide>
    </Swiper>
  </div>
</template>



<script setup lang="ts">
import { onMounted, ref } from "vue";
import { Swiper, SwiperSlide } from "swiper/vue";

import "swiper/css";
import "swiper/css/pagination";
import "swiper/css/navigation";

import {
  Pagination,
  Autoplay,
} from "swiper/modules";

import api from "@/services/api";

interface LocalizedText {
  en?: string | null;
  bn?: string | null;
}

interface OfferShow {
  id: number;
  title: LocalizedText | null;
  description: LocalizedText | null;
  location: LocalizedText | null;
  price: string | number | null;
  payment_method: LocalizedText | null;
  discount: LocalizedText | null;
  tour_duration: LocalizedText | null;
  image: string | null;
  url: string | null;
  order: number;
  is_active: boolean;
}

const modules = [
  Pagination,
  Autoplay,
];

const offers = ref<OfferShow[]>([]);
const loading = ref(true);

/**
 * Get localized text
 * Current locale: Bangla first, then English
 */
const getText = (
  value: LocalizedText | null | undefined
): string => {
  if (!value) return "";

  const locale = localStorage.getItem("locale") || "en";

  if (locale === "bn") {
    return value.bn || value.en || "";
  }

  return value.en || value.bn || "";
};

/**
 * Fetch offers from Laravel API
 */
const fetchOffers = async () => {
  loading.value = true;

  try {
    const response = await api.get("/offer-show");

    offers.value = response.data.data || [];
  } catch (error) {
    console.error("Failed to load offers:", error);
    offers.value = [];
  } finally {
    loading.value = false;
  }
};

onMounted(() => {
  fetchOffers();
});
</script>

<style scoped>
.mySwiper {
  padding-bottom: 35px;
}

.mySwiper :deep(.swiper-pagination) {
  bottom: 0;
}

.mySwiper :deep(.swiper-pagination-bullet) {
  width: 8px;
  height: 8px;
  opacity: 0.5;
  background-color: rgb(243, 115, 24);
}

.mySwiper :deep(.swiper-pagination-bullet-active) {
  opacity: 1;
}
</style>
```
