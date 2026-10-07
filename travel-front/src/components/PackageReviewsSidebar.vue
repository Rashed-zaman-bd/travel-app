<template>
  <div class="mt-6 border-t border-gray-100 pt-5">
    <!-- Title + write button -->
    <div class="mb-4 flex items-center justify-between gap-2">
      <h4 class="text-lg font-semibold text-gray-800">
        {{ L.title }}
        <span v-if="total" class="text-sm font-normal text-gray-500">({{ total }})</span>
      </h4>

      <button
        type="button"
        class="cursor-pointer border border-amber-600 px-3 py-1.5 text-sm font-semibold text-amber-600 transition hover:bg-amber-50"
        @click="openModal"
      >
        {{ L.write }}
      </button>
    </div>

    <p v-if="loading && !reviews.length" class="py-4 text-center text-sm text-gray-500">{{ L.loading }}</p>
    <p v-else-if="!reviews.length" class="py-4 text-center text-sm text-gray-500">{{ L.none }}</p>

    <!-- Auto slider -->
    <div
      v-else
      @mouseenter="pause"
      @mouseleave="play"
      @focusin="pause"
      @focusout="play"
      @touchstart.passive="onTouchStart"
      @touchend.passive="onTouchEnd"
    >
      <div class="overflow-hidden">
        <ul
          class="flex transition-transform duration-500 ease-in-out"
          :style="{ transform: `translateX(-${current * 100}%)` }"
        >
          <li v-for="r in reviews" :key="r.id" class="w-full shrink-0">
            <div class="h-full border border-gray-200 bg-gray-50 p-4">
              <div class="flex items-center gap-3">
                <img
                  v-if="r.user?.avatar_url"
                  :src="r.user.avatar_url"
                  :alt="r.user?.name || ''"
                  class="h-11 w-11 rounded-full object-cover"
                />
                <div
                  v-else
                  class="flex h-11 w-11 items-center justify-center rounded-full bg-amber-600 text-lg font-bold text-white"
                >
                  {{ (r.user?.name || '?').charAt(0).toUpperCase() }}
                </div>

                <div class="min-w-0">
                  <p class="truncate font-semibold leading-tight text-gray-800">
                    {{ r.user?.name || L.anonymous }}
                  </p>
                  <p v-if="r.traveler_location" class="truncate text-xs text-gray-600">
                    {{ r.traveler_location }}
                  </p>
                </div>
              </div>

              <div class="mt-2 text-lg tracking-wider text-yellow-400" :aria-label="`${r.rating} / 5`">
                {{ '★'.repeat(r.rating) }}<span class="text-gray-300">{{ '★'.repeat(5 - r.rating) }}</span>
              </div>

              <p v-if="r.comment" class="mt-1 break-words text-sm leading-relaxed text-gray-700">
                "{{ isOpen(r.id) || r.comment.length <= LIMIT ? r.comment : r.comment.slice(0, LIMIT).trim() + '...' }}"
                <button
                  v-if="r.comment.length > LIMIT"
                  type="button"
                  class="cursor-pointer text-blue-700 hover:underline"
                  @click="toggle(r.id)"
                >
                  {{ isOpen(r.id) ? L.less : L.more }}
                </button>
              </p>

              <p v-if="travelLine(r)" class="mt-3 text-xs text-gray-500">{{ travelLine(r) }}</p>
            </div>
          </li>
        </ul>
      </div>

      <!-- Controls -->
      <div v-if="reviews.length > 1" class="mt-3 flex items-center justify-between">
        <button
          type="button"
          class="cursor-pointer p-1 text-gray-500 hover:text-gray-800"
          aria-label="Previous"
          @click="prev(); play()"
        >
          <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
            <path d="M15 5l-7 7 7 7" />
          </svg>
        </button>

        <span class="text-xs text-gray-500">{{ current + 1 }} / {{ total || reviews.length }}</span>

        <button
          type="button"
          class="cursor-pointer p-1 text-gray-500 hover:text-gray-800"
          aria-label="Next"
          @click="next(); play()"
        >
          <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
            <path d="M9 5l7 7-7 7" />
          </svg>
        </button>
      </div>
    </div>

    <!-- Review modal -->
    <Teleport to="body">
      <div
        v-if="showModal"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4"
        @click.self="closeModal"
      >
        <div class="max-h-[90vh] w-full max-w-md overflow-y-auto rounded-xl bg-white p-6 shadow-xl">
          <h3 class="mb-4 text-lg font-bold text-gray-800">{{ L.write }}</h3>

          <form class="space-y-4" @submit.prevent="submit">
            <div v-if="serverError" class="rounded-md bg-red-50 p-3 text-xs text-red-600">{{ serverError }}</div>

            <!-- Rating -->
            <div>
              <label class="mb-1 block text-sm font-medium text-gray-700">{{ L.rating }}</label>
              <div class="flex gap-1 text-3xl">
                <button
                  v-for="n in 5"
                  :key="n"
                  type="button"
                  class="cursor-pointer focus:outline-none"
                  :class="n <= form.rating ? 'text-yellow-400' : 'text-gray-300'"
                  @click="form.rating = n"
                >★</button>
              </div>
              <p v-if="errors.rating" class="mt-1 text-xs text-red-500">{{ errors.rating[0] }}</p>
            </div>

            <!-- Location -->
            <div>
              <label class="block text-sm font-medium text-gray-700">{{ L.location }}</label>
              <input
                v-model="form.traveler_location"
                type="text"
                :placeholder="L.locationHint"
                class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 text-sm"
              />
              <p v-if="errors.traveler_location" class="mt-1 text-xs text-red-500">
                {{ errors.traveler_location[0] }}
              </p>
            </div>

            <!-- Type + month -->
            <div class="grid grid-cols-2 gap-3">
              <div>
                <label class="block text-sm font-medium text-gray-700">{{ L.travelType }}</label>
                <select
                  v-model="form.travel_type"
                  class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 text-sm"
                >
                  <option value="">-</option>
                  <option v-for="(label, key) in L.types" :key="key" :value="key">{{ label }}</option>
                </select>
                <p v-if="errors.travel_type" class="mt-1 text-xs text-red-500">{{ errors.travel_type[0] }}</p>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700">{{ L.travelDate }}</label>
                <input
                  v-model="form.travel_date"
                  type="month"
                  :max="maxMonth"
                  class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 text-sm"
                />
                <p v-if="errors.travel_date" class="mt-1 text-xs text-red-500">{{ errors.travel_date[0] }}</p>
              </div>
            </div>

            <!-- Comment -->
            <div>
              <label class="block text-sm font-medium text-gray-700">{{ L.review }}</label>
              <textarea
                v-model="form.comment"
                rows="4"
                :placeholder="L.placeholder"
                class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 text-sm"
              ></textarea>
              <p v-if="errors.comment" class="mt-1 text-xs text-red-500">{{ errors.comment[0] }}</p>
            </div>

            <div class="flex justify-end gap-3 pt-2">
              <button
                type="button"
                class="cursor-pointer px-4 py-2 text-sm text-gray-600 hover:text-gray-800"
                @click="closeModal"
              >
                {{ L.cancel }}
              </button>
              <button
                type="submit"
                :disabled="submitting || !form.rating"
                class="cursor-pointer bg-amber-600 px-5 py-2 text-sm font-semibold text-white transition hover:bg-amber-700 disabled:opacity-50"
              >
                {{ submitting ? '...' : L.submit }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </Teleport>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive, computed, onMounted, onBeforeUnmount, watch } from 'vue'
import { useI18n } from 'vue-i18n'
import api from '@/services/api'

interface Review {
  id: number
  rating: number
  comment: string | null
  user: { id: number; name: string | null; avatar_url: string | null }
  traveler_location: string | null
  destination_visited: string | null
  travel_type: string | null
  travel_date: string | null // "2026-09-01"
}

const props = defineProps<{
  tourPackageId: number
  destination?: string // saved as destination_visited, e.g. "Thailand"
}>()

const { locale } = useI18n({ useScope: 'global' })

const LIMIT = 110

const messages = {
  en: {
    title: 'Reviews',
    write: 'Write a review',
    loading: 'Loading...',
    none: 'No reviews yet. Be the first!',
    anonymous: 'Traveler',
    more: 'read more',
    less: 'show less',
    rating: 'Rating',
    location: 'Your location',
    locationHint: 'e.g. Dhaka, Bangladesh',
    travelType: 'Traveled',
    travelDate: 'Month',
    review: 'Your review',
    placeholder: 'Share your experience...',
    cancel: 'Cancel',
    submit: 'Submit',
    login: 'Please log in to write a review.',
    failed: 'Could not submit the review. Please try again.',
    traveled: (to: string, as: string, when: string) =>
      ['Traveled', to && `to ${to}`, as, when && `in ${when}`].filter(Boolean).join(' '),
    types: {
      solo: 'solo',
      couple: 'as a couple',
      family: 'with family',
      group: 'with a group',
      business: 'on business',
    } as Record<string, string>,
  },
  bn: {
    title: 'রিভিউ',
    write: 'রিভিউ লিখুন',
    loading: 'লোড হচ্ছে...',
    none: 'এখনো কোনো রিভিউ নেই। প্রথম রিভিউ আপনিই দিন!',
    anonymous: 'ভ্রমণকারী',
    more: 'আরও পড়ুন',
    less: 'কম দেখুন',
    rating: 'রেটিং',
    location: 'আপনার অবস্থান',
    locationHint: 'যেমন: ঢাকা, বাংলাদেশ',
    travelType: 'ভ্রমণ',
    travelDate: 'মাস',
    review: 'আপনার রিভিউ',
    placeholder: 'আপনার অভিজ্ঞতা শেয়ার করুন...',
    cancel: 'বাতিল',
    submit: 'জমা দিন',
    login: 'রিভিউ লিখতে লগইন করুন।',
    failed: 'রিভিউ জমা দেওয়া যায়নি। আবার চেষ্টা করুন।',
    traveled: (to: string, as: string, when: string) =>
      [to && `${to} ভ্রমণ`, as, when].filter(Boolean).join(' · '),
    types: {
      solo: 'একা',
      couple: 'দম্পতি হিসেবে',
      family: 'পরিবারের সাথে',
      group: 'গ্রুপের সাথে',
      business: 'ব্যবসায়িক সফরে',
    } as Record<string, string>,
  },
} as const

const L = computed(() => messages[locale.value as keyof typeof messages] ?? messages.en)

/** "2026-09-01" -> "September, 2026" (follows the site language) */
const monthYear = (iso: string | null): string => {
  if (!iso) return ''
  const [y, m] = iso.split('-').map(Number)
  const d = new Date(y, m - 1, 1)
  const loc = locale.value === 'bn' ? 'bn-BD' : 'en-US'
  return `${d.toLocaleDateString(loc, { month: 'long' })}, ${d.toLocaleDateString(loc, { year: 'numeric' })}`
}

const travelLine = (r: Review): string => {
  const to = r.destination_visited ?? ''
  const as = r.travel_type ? L.value.types[r.travel_type] ?? '' : ''
  const when = monthYear(r.travel_date)
  return to || as || when ? L.value.traveled(to, as, when) : ''
}

// ---- list ----
const reviews = ref<Review[]>([])
const page = ref(1)
const lastPage = ref(1)
const total = ref(0)
const loading = ref(false)
const hasMore = computed(() => page.value < lastPage.value)

const load = async (p = 1) => {
  if (!props.tourPackageId) return
  loading.value = true
  try {
    const { data } = await api.get('/reviews', {
      params: { tour_package_id: props.tourPackageId, per_page: 5, page: p },
    })

    const list: Review[] = data.data ?? []
    reviews.value = p === 1 ? list : [...reviews.value, ...list]
    if (p === 1) current.value = 0

    page.value = data.meta?.current_page ?? p
    lastPage.value = data.meta?.last_page ?? p
    total.value = data.meta?.total ?? reviews.value.length

    if (p === 1) play()
  } catch (e) {
    console.error('Failed to load reviews:', e)
  } finally {
    loading.value = false
  }
}

// ---- read more ----
const open = ref<Record<number, boolean>>({})
const isOpen = (id: number) => !!open.value[id]
const toggle = (id: number) => {
  open.value[id] = !open.value[id]
  if (open.value[id]) pause()
  else play()
}

// ---- form ----
const showModal = ref(false)
const submitting = ref(false)
const serverError = ref('')
const errors = ref<Record<string, string[]>>({})

const emptyForm = () => ({
  rating: 5,
  comment: '',
  traveler_location: '',
  travel_type: '',
  travel_date: '', // "2026-09" from <input type="month">
})
const form = reactive(emptyForm())

const maxMonth = (() => {
  const now = new Date()
  return `${now.getFullYear()}-${String(now.getMonth() + 1).padStart(2, '0')}`
})()

const openModal = () => (showModal.value = true)
const closeModal = () => {
  showModal.value = false
  errors.value = {}
  serverError.value = ''
}

const submit = async () => {
  errors.value = {}
  serverError.value = ''
  submitting.value = true

  try {
    await api.post('/reviews', {
      tour_package_id: props.tourPackageId,
      rating: form.rating,
      comment: form.comment || null,
      traveler_location: form.traveler_location || null,
      destination_visited: props.destination || null,
      travel_type: form.travel_type || null,
      travel_date: form.travel_date ? `${form.travel_date}-01` : null,
    })

    Object.assign(form, emptyForm())
    closeModal()
    await load(1)
  } catch (e: any) {
    const res = e?.response
    if (!res) {
      serverError.value = L.value.failed
    } else if (res.status === 401) {
      serverError.value = L.value.login
    } else if (res.status === 422) {
      errors.value = res.data.errors ?? {}
      serverError.value = errors.value.tour_package_id?.[0] ?? ''
    } else {
      serverError.value = res.data?.message || L.value.failed
    }
  } finally {
    submitting.value = false
  }
}

// ---- auto slider ----
const INTERVAL = 5000
const current = ref(0)
let timer: ReturnType<typeof setInterval> | null = null

const reducedMotion =
  typeof window !== 'undefined' &&
  window.matchMedia?.('(prefers-reduced-motion: reduce)').matches

const next = async () => {
  if (current.value < reviews.value.length - 1) {
    current.value++
    return
  }

  // last loaded review: fetch the next page if there is one, else go back to the start
  if (hasMore.value && !loading.value) {
    await load(page.value + 1)
    if (current.value < reviews.value.length - 1) current.value++
    return
  }

  current.value = 0
}

const prev = () => {
  current.value = current.value > 0 ? current.value - 1 : reviews.value.length - 1
}

const pause = () => {
  if (timer) {
    clearInterval(timer)
    timer = null
  }
}

const play = () => {
  pause()
  if (reducedMotion || reviews.value.length < 2 || showModal.value) return
  timer = setInterval(next, INTERVAL)
}

// swipe on mobile
let touchX = 0
const onTouchStart = (e: TouchEvent) => {
  touchX = e.changedTouches[0].clientX
  pause()
}
const onTouchEnd = (e: TouchEvent) => {
  const dx = e.changedTouches[0].clientX - touchX
  if (Math.abs(dx) > 40) {
    if (dx < 0) next()
    else prev()
  }
  play()
}

// don't slide behind the review modal
watch(showModal, (v) => (v ? pause() : play()))

// another package opened
watch(
  () => props.tourPackageId,
  () => {
    pause()
    reviews.value = []
    open.value = {}
    current.value = 0
    load(1)
  }
)

onMounted(() => load(1))
onBeforeUnmount(pause)
</script>