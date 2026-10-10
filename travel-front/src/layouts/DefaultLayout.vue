<!-- layouts/DefaultLayout.vue -->

<template>
  <NavbarView />

  <main class="min-h-screen">
    <RouterView />
  </main>

  <FooterView/>
</template>

<script setup lang="ts">
import FooterView from '@/views/FooterView.vue';
import NavbarView from '@/views/NavbarView.vue'
</script>