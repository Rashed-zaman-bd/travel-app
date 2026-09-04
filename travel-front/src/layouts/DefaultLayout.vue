<!--layouts/DefaultLayout.vue-->

<template>

    <NavbarView/>
    <main>
        <RouterView/>
    </main>

</template>
<script setup lang="ts">
import NavbarView from '@/views/NavbarView.vue';


</script>