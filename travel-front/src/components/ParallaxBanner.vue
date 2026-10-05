<template>
    <div 
        class="relative w-full bg-cover bg-center bg-no-repeat bg-fixed flex items-center justify-center text-center"
        :class="height"
        :style="{ backgroundImage: `url('${image}')` }"
    >
        <!-- Overlay for better text readability -->
        <div class="absolute inset-0 bg-black/40"></div>

        <!-- Content Box -->
        <div class="relative z-10 max-w-3xl px-4 text-white">
            <slot />
        </div>
    </div>
</template>

<script setup lang="ts">
withDefaults(
    defineProps<{
        image: string
        title?: string
        subtitle?: string
        height?: string
    }>(),
    {
        height: 'h-[6vh] sm:h-[8vh]', // Default height if not full-screen
    }
)
</script>

<style scoped>
/* Fallback for iOS Safari where bg-fixed can act buggy */
@supports (-webkit-touch-callout: none) {
    .bg-fixed {
        background-attachment: scroll;
    }
}
</style>