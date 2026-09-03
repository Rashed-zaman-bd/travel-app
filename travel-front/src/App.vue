<script setup lang="ts">



</script>

<template>
   <h1 class="text-3xl font-bold underline">
    Hello world!  
    
    <i class="bi bi-airplane-fill"></i>
      <!-- Solid icons (fas prefix) -->
    <font-awesome-icon :icon="['fas', 'user-secret']" />
    <font-awesome-icon :icon="['fas', 'thumbs-up']" />
    <font-awesome-icon :icon="['fas', 'house']" />
    <font-awesome-icon :icon="['fas', 'phone']" />

    <!-- Regular icon (far prefix) -->
    <font-awesome-icon :icon="['far', 'circle-user']" />

    <!-- Brand icon (fab prefix) -->
    <font-awesome-icon :icon="['fab', 'facebook']" />
  </h1>
</template>

<style scoped></style>
