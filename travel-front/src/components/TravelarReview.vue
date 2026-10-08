<template>
  <div class="max-w-7xl mx-auto py-10 border-b border-gray-200">
    <!-- Header Section -->
    <div class="flex items-center justify-center mb-6">
      <h2 class="mb-3 text-2xl font-semibold text-amber-600 md:text-3xl">{{ t('worldwide_category.whatsay') }}</h2>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="flex justify-center items-center py-12">
      <div class="animate-spin rounded-full h-10 w-10 border-b-2 border-amber-600"></div>
    </div>

    <!-- Endless slider -->
    <div
      v-else-if="reviews.length > 0"
      class="relative flex items-center gap-4"
      @pointerenter="onEnter"
      @pointerleave="onLeave"
      @focusin="pause"
      @focusout="play"
      @touchstart.passive="onTouchStart"
      @touchend.passive="onTouchEnd"
    >
      <button
        v-if="loopable"
        type="button"
        aria-label="Previous"
        class="hidden sm:flex items-center justify-center w-10 h-10 rounded-full border border-gray-300 bg-white shadow-sm hover:bg-gray-50 text-gray-600 flex-shrink-0 z-10 cursor-pointer"
        @click="go(-1); play()"
      >
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
        </svg>
      </button>

      <!-- Viewport: hides everything outside the visible cards -->
      <div ref="viewport" class="min-w-0 flex-1 overflow-hidden" :class="viewW ? '' : 'invisible'">
        <div
          ref="track"
          class="flex"
          :class="[
            animate && !reducedMotion ? 'transition-transform duration-500 ease-in-out' : '',
            loopable ? '' : 'justify-center',
          ]"
          :style="trackStyle"
          @transitionend.self="onTransitionEnd"
        >
          <div
            v-for="slide in slides"
            :key="slide.key"
            :style="{ width: cardW + 'px' }"
            class="shrink-0 bg-gray-50 border border-gray-200 rounded-lg p-5 flex flex-col justify-between shadow-sm"
          >
            <div>
              <!-- User Header -->
              <div class="flex items-center gap-3 mb-3">
                <img
                  :src="slide.review.user?.avatar_url || fallbackAvatar"
                  :alt="slide.review.user?.name || 'Traveler'"
                  class="w-12 h-12 rounded-full object-cover border border-gray-200"
                />
                <div class="min-w-0">
                  <h4 class="font-bold text-gray-900 text-base leading-tight truncate">
                    {{ slide.review.user?.name || 'Anonymous' }}
                  </h4>
                  <p v-if="slide.review.traveler_location" class="text-xs text-gray-500 truncate">
                    {{ slide.review.traveler_location }}
                  </p>
                </div>
              </div>

              <!-- Star Rating -->
              <div class="flex items-center gap-1 mb-3">
                <svg
                  v-for="star in 5"
                  :key="star"
                  :class="star <= slide.review.rating ? 'text-amber-400' : 'text-gray-300'"
                  class="w-5 h-5 fill-current"
                  viewBox="0 0 20 20"
                >
                  <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                </svg>
              </div>

              <!-- Comment -->
              <p v-if="slide.review.comment" class="text-gray-700 text-sm leading-relaxed mb-4 break-words">
                "{{ slide.review.comment }}"
              </p>
            </div>

            <!-- Travel Info -->
            <div
              v-if="slide.review.formatted_travel_info"
              class="text-xs text-gray-500 mt-2 border-t border-gray-100 pt-3"
            >
              {{ slide.review.formatted_travel_info }}
            </div>
          </div>
        </div>
      </div>

      <button
        v-if="loopable"
        type="button"
        aria-label="Next"
        class="hidden sm:flex items-center justify-center w-10 h-10 rounded-full border border-gray-300 bg-white shadow-sm hover:bg-gray-50 text-gray-600 flex-shrink-0 z-10 cursor-pointer"
        @click="go(1); play()"
      >
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
        </svg>
      </button>
    </div>

    <!-- Empty State -->
    <div v-else class="text-center py-10 bg-gray-50 rounded-lg">
      <p class="text-gray-500">No reviews found yet. Be the first to leave a review!</p>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, onBeforeUnmount, watch, nextTick } from 'vue'
import api from '@/services/api'
import { useI18n } from 'vue-i18n'

const { t, te, locale } = useI18n({ useScope: 'global' })

interface User {
  id: number
  name: string
  avatar_url?: string | null
}

interface Review {
  id: number
  rating: number
  comment: string | null
  is_approved: boolean
  user: User
  traveler_location?: string | null
  destination_visited?: string | null
  travel_type?: string | null
  travel_date?: string | null
  formatted_travel_info: string
  created_at: string
}

const props = defineProps<{
  tourPackageId?: number | string
  slug?: string
}>()

const fallbackAvatar =
  'data:image/svg+xml;utf8,' +
  encodeURIComponent(
    `<svg xmlns='http://www.w3.org/2000/svg' width='48' height='48'><rect width='48' height='48' fill='#d1d5db'/><circle cx='24' cy='19' r='8' fill='#fff'/><path d='M8 44c2-10 10-14 16-14s14 4 16 14z' fill='#fff'/></svg>`
  )

const reviews = ref<Review[]>([])
const loading = ref(true)

// ---- resolve the package id (from prop, or from slug) ----
const packageId = ref<number | null>(null)

// true on a package page, false on a global reviews section (home page)
const isPackagePage = computed(() => !!(props.tourPackageId || props.slug))

const resolvePackageId = async () => {
  packageId.value = null

  if (props.tourPackageId) {
    packageId.value = Number(props.tourPackageId)
    return
  }

  if (props.slug) {
    try {
      const { data } = await api.get(`/tour-package/${props.slug}`)
      packageId.value = (data.data ?? data).id ?? null
    } catch (e) {
      console.error('Could not load package:', e)
    }
  }
}

// ---- reviews ----
const fetchReviews = async () => {
  pause()

  if (isPackagePage.value && !packageId.value) {
    reviews.value = []
    loading.value = false
    return
  }

  loading.value = true
  try {
    const params: Record<string, any> = { per_page: 12 }
    if (packageId.value) params.tour_package_id = packageId.value

    const response = await api.get('/reviews', { params })
    reviews.value = response.data.data ?? []
  } catch (error) {
    console.error('Failed to load reviews:', error)
    reviews.value = []
  } finally {
    loading.value = false
  }

  await nextTick() // wait until the slider is in the DOM
  play()
}

const init = async () => {
  await resolvePackageId()
  await fetchReviews()
}

// ---- endless slider ----
const GAP = 16
const INTERVAL = 5000

const reducedMotion =
  typeof window !== 'undefined' &&
  window.matchMedia?.('(prefers-reduced-motion: reduce)').matches

const viewport = ref<HTMLElement | null>(null)
const track = ref<HTMLElement | null>(null)
const viewW = ref(0)

// how many cards are visible at once
const perView = computed(() => (viewW.value < 560 ? 1 : viewW.value < 900 ? 2 : 3))

const cardW = computed(() =>
  Math.max(0, (viewW.value - GAP * (perView.value - 1)) / perView.value)
)

// the circle only makes sense when there are more reviews than visible cards
const loopable = computed(() => reviews.value.length > perView.value)

// [copy of the last cards] + [real cards] + [copy of the first cards]
const slides = computed(() => {
  const real = reviews.value.map((review) => ({ key: String(review.id), review }))
  if (!loopable.value) return real

  const n = perView.value
  const head = reviews.value.slice(-n).map((review, i) => ({ key: `pre-${i}-${review.id}`, review }))
  const tail = reviews.value.slice(0, n).map((review, i) => ({ key: `post-${i}-${review.id}`, review }))
  return [...head, ...real, ...tail]
})

const index = ref(0)
const animate = ref(true)
let busy = false
let fallbackTimer: ReturnType<typeof setTimeout> | null = null

const trackStyle = computed(() => ({
  gap: GAP + 'px',
  transform: loopable.value ? `translateX(-${index.value * (cardW.value + GAP)}px)` : 'none',
}))

/** move without animation (used to jump from a copy back to the real card) */
const jump = async (to: number) => {
  animate.value = false
  index.value = to
  await nextTick()
  void track.value?.offsetWidth // force the browser to apply the position first
  animate.value = true
}

/** after a slide ends on a copy, silently jump to the matching real card */
const normalize = async () => {
  if (fallbackTimer) {
    clearTimeout(fallbackTimer)
    fallbackTimer = null
  }

  const n = reviews.value.length
  const pv = perView.value

  if (index.value >= pv + n) await jump(index.value - n)
  else if (index.value < pv) await jump(index.value + n)

  busy = false
}

const onTransitionEnd = (e: TransitionEvent) => {
  if (e.propertyName === 'transform') normalize()
}

/** one card forward (1) or back (-1), forever in a circle */
const go = (dir: 1 | -1) => {
  if (!loopable.value || busy) return
  busy = true
  index.value += dir

  if (reducedMotion) {
    normalize() // no animation, so no transitionend event
  } else {
    fallbackTimer = setTimeout(normalize, 800) // safety net, e.g. hidden browser tab
  }
}

// start on the first real card whenever the layout or the data changes
watch([() => reviews.value.length, perView], async () => {
  busy = false
  await jump(loopable.value ? perView.value : 0)
})

// ---- autoplay ----
let timer: ReturnType<typeof setInterval> | null = null

const pause = () => {
  if (timer) {
    clearInterval(timer)
    timer = null
  }
}

const play = () => {
  pause()
  if (!loopable.value) return
  timer = setInterval(() => go(1), INTERVAL)
}

// only a real mouse pauses on hover (touch screens fire a fake hover after a tap)
const onEnter = (e: PointerEvent) => {
  if (e.pointerType === 'mouse') pause()
}
const onLeave = (e: PointerEvent) => {
  if (e.pointerType === 'mouse') play()
}

// swipe on phones
let touchX = 0
const onTouchStart = (e: TouchEvent) => {
  touchX = e.changedTouches[0]?.clientX ?? 0
  pause()
}
const onTouchEnd = (e: TouchEvent) => {
  const endX = e.changedTouches[0]?.clientX ?? touchX
  const dx = endX - touchX
  if (Math.abs(dx) > 40) go(dx < 0 ? 1 : -1)
  play()
}

// ---- measure the viewport (also on resize) ----
let ro: ResizeObserver | null = null

watch(viewport, (el) => {
  ro?.disconnect()
  if (!el) return
  viewW.value = el.clientWidth
  if (typeof ResizeObserver !== 'undefined') {
    ro = new ResizeObserver(() => {
      viewW.value = el.clientWidth
    })
    ro.observe(el)
  }
})

// re-run when the parent passes a different package, or the id arrives late
watch(() => [props.tourPackageId, props.slug], init)

onMounted(init)

onBeforeUnmount(() => {
  pause()
  ro?.disconnect()
  if (fallbackTimer) clearTimeout(fallbackTimer)
})
</script>