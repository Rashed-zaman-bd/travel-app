<template>
  <section class="mx-auto grid max-w-7xl py-14 bg-white overflow-hidden">
    <div class="max-w-5xl mx-auto text-center px-4 mb-10">
      <h2 class="text-3xl md:text-4xl font-bold text-slate-900 mb-3">
        Most Popular Destinations
      </h2>
      <p class="text-slate-500 max-w-2xl mx-auto">
        Expand your travel horizons with new facets! Explore the world by
        choosing your ideal travel destinations in Asia, Europe, America,
        Australia and more with ShareTrip.
      </p>
    </div>

    <Swiper
      :modules="[EffectCoverflow, Pagination, Autoplay]"
      effect="coverflow"
      :grab-cursor="true"
      :centered-slides="true"
      :slides-per-view="3"
      :breakpoints="{
        768: {
          slidesPerView: 5,
        },
      }"
      :loop="true"
      :looped-slides="5"
      :coverflow-effect="{
        rotate: 15,
        stretch: 0,
        depth: 120,
        modifier: 1,
        slideShadows: false,
      }"
      :autoplay="{ delay: 3000, disableOnInteraction: false }"
      :pagination="{ clickable: true, el: '.destinations-pagination' }"
      class="destinations-swiper"
    >
      <SwiperSlide
        v-for="dest in destinations"
        :key="dest.name"
        class="destination-slide"
      >
        <div
          class="relative w-full h-full rounded-2xl overflow-hidden shadow-lg group"
        >
          <img
            :src="dest.image"
            :alt="dest.name"
            class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105"
          />
          <div
            class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent"
          />
          <div class="absolute bottom-4 left-4 text-white text-left">
            <p class="font-semibold text-lg leading-tight">{{ dest.name }}</p>
            <p class="text-sm text-white/85">
              
            </p>
          </div>
        </div>
      </SwiperSlide>
    </Swiper>

    <div class="destinations-pagination flex justify-center gap-2 mt-8" />
  </section>
</template>

<script setup lang="ts">
import { Swiper, SwiperSlide } from "swiper/vue";
import { EffectCoverflow, Pagination, Autoplay } from "swiper/modules";

import "swiper/css";
import "swiper/css/effect-coverflow";
import "swiper/css/pagination";

interface Destination {
  name: string;

  image: string;
}

const destinations: Destination[] = [
  {
    name: "Cox's Bazar",
    
    image: "https://images.unsplash.com/photo-1590523278191-995cbcda646b?w=600&q=80",
  },
  {
    name: "Chittagong",
   
    image: "https://images.unsplash.com/photo-1507525428034-b723cf961d3e?w=600&q=80",
  },
  {
    name: "Dhaka",
    
    image: "https://images.unsplash.com/photo-1583417319070-4a69db38a482?w=600&q=80",
  },
  {
    name: "Sreemangal",
    
    image: "https://images.unsplash.com/photo-1500835556837-99ac94a94552?w=600&q=80",
  },
  {
    name: "Gazipur",
    
    image: "https://images.unsplash.com/photo-1523217582562-09d0def993a6?w=600&q=80",
  },
];
</script>

<style scoped>
.destinations-swiper {
  width: 100%;
  padding: 40px 0 20px;
}

.destination-slide {
  width: 100%;
  height: 380px;
  transition: transform 0.3s ease;
}

:deep(.destinations-pagination .swiper-pagination-bullet) {
  width: 10px;
  height: 10px;
  background: #cbd5e1;
  opacity: 0.8;
  border-radius: 50%;
  transition: all 0.3s ease;
  margin: 0 4px;
}

:deep(.destinations-pagination .swiper-pagination-bullet-active) {
  background: #3b82f6;
  opacity: 1;
  outline: 3px solid #bfdbfe;
  outline-offset: 1px;
}

@media (max-width: 640px) {
  .destination-slide {
    height: 300px;
  }
}
</style>