
<template>
  <div class="relative min-h-screen">

    <!-- Page Content -->
    <HeroImage />

    <HowItWork />

    <OfferShow />

    <section id="mostpopular">
      <MostPopular />
    </section>

    <!-- Parallax Background Only for Traveler Reviews -->
    <section
      class="relative overflow-hidden bg-cover bg-center bg-no-repeat"
      style="
        background-image: url('/images/background.jpg');
        background-attachment: fixed;
      "
    >
      <!-- Dark Overlay -->
      <div class="absolute inset-0 bg-black/40"></div>

      <!-- Review Content -->
      <div class="relative z-10">
        <TravelarReview />
      </div>
    </section>>

    

    <Bangladesh />

    <section id="destination">
      <Destination />
    </section>
  </div>
</template>

<script setup lang="ts">
import HeroImage from '@/components/hero.vue'
import HowItWork from '@/components/HowItWork.vue'
import Destination from '@/components/destination.vue'
import MostPopular from '@/components/MostPopular.vue'
import OfferShow from '@/components/OfferShow.vue'
import TravelarReview from '@/components/TravelarReview.vue'
import Bangladesh from '@/components/Bangladesh.vue'
</script>
