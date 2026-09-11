<script setup lang="ts">
import { ref, onMounted, onUnmounted, computed } from 'vue'
import api from '@/services/api'

interface Destination {
  name: string
  meta: string
  img: string
}

// API response shape — adjust field names to match your Laravel resource
interface DestinationApiItem {
  id: number
  name: string
  hotels_count: number
  image_url: string
}

const props = withDefaults(
  defineProps<{
    autoplayMs?: number
    spacing?: number
  }>(),
  {
    autoplayMs: 3000,
    spacing: 235,
  }
)

const destinations = ref<Destination[]>([])
const isLoading = ref(true)
const error = ref<string | null>(null)

const current = ref(0)
let timer: ReturnType<typeof setInterval> | null = null

const total = computed(() => destinations.value.length)

async function fetchDestinations() {
  isLoading.value = true
  error.value = null
  try {
    const { data } = await api.get<{ data: DestinationApiItem[] }>(
      '/destinations/popular'
    )
    destinations.value = data.data.map((item) => ({
      name: item.name,
      meta: `${item.hotels_count} hotels available`,
      img: item.image_url,
    }))
  } catch (e) {
    error.value = 'Could not load destinations. Try again.'
  } finally {
    isLoading.value = false
  }
}

function cardStyle(i: number) {
  let offset = i - current.value
  if (offset > total.value / 2) offset -= total.value
  if (offset < -total.value / 2) offset += total.value

  const abs = Math.abs(offset)
  const x = offset * props.spacing
  const rotateY = offset * -32
  const scale = abs === 0 ? 1 : abs === 1 ? 0.82 : 0.68
  const z = -abs * 160
  const opacity = abs > 2 ? 0 : abs === 0 ? 1 : abs === 1 ? 0.85 : 0.45

  return {
    transform: `translate(-50%, -50%) translateX(${x}px) translateZ(${z}px) rotateY(${rotateY}deg) scale(${scale})`,
    opacity,
    zIndex: 100 - abs,
  }
}

function isCenter(i: number) {
  return i === current.value
}

function goTo(index: number) {
  current.value = (index + total.value) % total.value
  restartAutoplay()
}

function next() {
  goTo(current.value + 1)
}

function startAutoplay() {
  timer = setInterval(next, props.autoplayMs)
}

function stopAutoplay() {
  if (timer) clearInterval(timer)
}

function restartAutoplay() {
  stopAutoplay()
  startAutoplay()
}

onMounted(async () => {
  await fetchDestinations()
  if (destinations.value.length > 0) startAutoplay()
})
onUnmounted(stopAutoplay)
</script>

<template>
  <div class="text-center max-w-xl mx-auto mb-10">
    <h2 class="text-3xl font-extrabold text-slate-900 mb-3">Most popular destinations</h2>
    <p class="text-slate-500 text-sm leading-relaxed">
      Expand your travel horizons with new facets. Explore the world by choosing your ideal travel destination.
    </p>
  </div>

  <div v-if="isLoading" class="text-center text-slate-400 text-sm py-16">
    Loading destinations…
  </div>

  <div v-else-if="error" class="text-center text-sm py-16">
    <p class="text-slate-500 mb-3">{{ error }}</p>
    <button
      class="text-blue-600 text-sm font-medium underline"
      @click="fetchDestinations"
    >
      Retry
    </button>
  </div>

  <div v-else-if="destinations.length === 0" class="text-center text-slate-400 text-sm py-16">
    No destinations to show right now.
  </div>

  <div
    v-else
    class="relative max-w-5xl mx-auto h-[420px] [perspective:1400px]"
    @mouseenter="stopAutoplay"
    @mouseleave="startAutoplay"
  >
    <div class="relative w-full h-full">
      <div
        v-for="(d, i) in destinations"
        :key="d.name"
        class="absolute top-1/2 left-1/2 w-[250px] h-[400px] rounded-2xl overflow-hidden bg-cover bg-center shadow-2xl cursor-pointer transition-[transform,opacity] duration-500 ease-[cubic-bezier(.22,.68,0,1.01)]"
        :style="{ backgroundImage: `url(${d.img})`, ...cardStyle(i) }"
        @click="goTo(i)"
      >
        <div class="absolute inset-0 bg-gradient-to-t from-black/75 via-black/10 to-transparent" />

        <div
          class="absolute top-4 left-4 bg-white/90 text-slate-900 text-xs font-bold px-2.5 py-1 rounded-md transition-opacity duration-300"
          :class="isCenter(i) ? 'opacity-100' : 'opacity-0'"
        >
          {{ d.name }}
        </div>

        <div class="absolute left-5 right-5 bottom-4 text-white">
          <span class="block text-lg font-bold">{{ d.name }}</span>
          <span class="block text-xs opacity-85 mt-0.5">{{ d.meta }}</span>
        </div>
      </div>
    </div>
  </div>

  <div v-if="destinations.length > 0" class="flex justify-center gap-2 mt-8">
    <button
      v-for="(d, i) in destinations"
      :key="d.name"
      class="w-2 h-2 rounded-full transition-all duration-300"
      :class="i === current ? 'bg-blue-600 scale-125' : 'bg-slate-300'"
      @click="goTo(i)"
      :aria-label="`Go to ${d.name}`"
    />
  </div>
</template>