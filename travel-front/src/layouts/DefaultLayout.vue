<!-- layouts/DefaultLayout.vue -->

<template>
  <NavbarView />

  <main class="min-h-screen">
    <RouterView />
  </main>
</template>

<script setup lang="ts">
import NavbarView from '@/views/NavbarView.vue'
</script>