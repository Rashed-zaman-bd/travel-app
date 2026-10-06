```vue
<template>
  <div class="mx-auto max-w-7xl border-b border-gray-200 bg-white p-4 py-6">
    <Swiper
      :slides-per-view="1"
      :space-between="10"
      :loop="true"
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
      <SwiperSlide>
        <div class="flex h-40 items-center justify-center bg-gray-100">
          Slide 1
        </div>
      </SwiperSlide>

      <SwiperSlide>
        <div class="flex h-40 items-center justify-center bg-gray-100">
          Slide 2
        </div>
      </SwiperSlide>

      <SwiperSlide>
        <div class="flex h-40 items-center justify-center bg-gray-100">
          Slide 3
        </div>
      </SwiperSlide>

      <SwiperSlide>
        <div class="flex h-40 items-center justify-center bg-gray-100">
          Slide 4
        </div>
      </SwiperSlide>

      <SwiperSlide>
        <div class="flex h-40 items-center justify-center bg-gray-100">
          Slide 5
        </div>
      </SwiperSlide>

      <SwiperSlide>
        <div class="flex h-40 items-center justify-center bg-gray-100">
          Slide 6
        </div>
      </SwiperSlide>

      <SwiperSlide>
        <div class="flex h-40 items-center justify-center bg-gray-100">
          Slide 7
        </div>
      </SwiperSlide>

      <SwiperSlide>
        <div class="flex h-40 items-center justify-center bg-gray-100">
          Slide 8
        </div>
      </SwiperSlide>
    </Swiper>
  </div>
</template>

<script>
import { Swiper, SwiperSlide } from 'swiper/vue'

import 'swiper/css'
import 'swiper/css/pagination'
import 'swiper/css/navigation'

import {
  Pagination,
  Navigation,
  Autoplay,
} from 'swiper/modules'

export default {
  components: {
    Swiper,
    SwiperSlide,
  },

  setup() {
    return {
      modules: [Pagination, Autoplay],
    }
  },
}
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
