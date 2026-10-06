<template>
  
    <div class="">
      <HeroImage />

      <HowItWork />

      <OfferShow/>

      <section id="mostpopular">
        <MostPopular />
      </section>

      <section id="destination">
        <Destination />
      </section>
    </div>

</template>

<script setup lang="ts">
import HeroImage from '@/components/hero.vue'
import HowItWork from '@/components/HowItWork.vue'
import Destination from '@/components/destination.vue'
import MostPopular from '@/components/MostPopular.vue';
import OfferShow from '@/components/OfferShow.vue';
</script>