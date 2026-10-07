<template>
  <section class="py-10">
    <h2 class="text-2xl font-bold text-center mb-6">{{ L.title }}</h2>

    <p v-if="loading && !reviews.length" class="text-center text-gray-500">{{ L.loading }}</p>
    <p v-else-if="!reviews.length" class="text-center text-gray-500">{{ L.none }}</p>

    <!-- Carousel -->
    <div v-else class="relative flex items-center gap-2">
      <button
        type="button"
        class="shrink-0 p-2 text-gray-500 hover:text-gray-800 cursor-pointer"
        :aria-label="L.prev"
        @click="scroll(-1)"
      >
        <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
          <path d="M15 5l-7 7 7 7" />
        </svg>
      </button>

      <div
        ref="scroller"
        class="flex gap-4 overflow-x-auto snap-x snap-mandatory scroll-smooth flex-1
               [scrollbar-width:none] [&::-webkit-scrollbar]:hidden"
      >
        <article
          v-for="r in reviews"
          :key="r.id"
          class="snap-start shrink-0 w-[85%] sm:w-[calc((100%-1rem)/2)] lg:w-[calc((100%-2rem)/3)]
                 bg-gray-50 border border-gray-200 rounded-md p-5"
        >
          <!-- Header: name + photo from users table, location from tour package -->
          <div class="flex items-center gap-3">
            <img
              v-if="r.reviewer_avatar"
              :src="r.reviewer_avatar"
              :alt="r.reviewer_name"
              class="w-12 h-12 rounded-full object-cover"
            />
            <div
              v-else
              class="w-12 h-12 rounded-full bg-[#e07300] text-white flex items-center justify-center font-bold text-lg"
            >
              {{ r.reviewer_name?.charAt(0) }}
            </div>

            <div>
              <p class="font-semibold text-slate-800 leading-tight">{{ r.reviewer_name }}</p>
              <p v-if="tr(r.package_location)" class="text-xs text-gray-600">
                {{ tr(r.package_location) }}
              </p>
            </div>
          </div>

          <!-- Stars -->
          <div class="mt-1 text-xl tracking-wider text-yellow-400" :aria-label="`${r.rating} / 5`">
            {{ '★'.repeat(r.rating) }}<span class="text-gray-300">{{ '★'.repeat(5 - r.rating) }}</span>
          </div>

          <!-- Comment -->
          <p v-if="r.comment" class="mt-2 text-sm text-slate-700 leading-relaxed">
            "{{ isOpen(r.id) || r.comment.length <= LIMIT ? r.comment : r.comment.slice(0, LIMIT).trim() + '...' }}"
            <button
              v-if="r.comment.length > LIMIT"
              type="button"
              class="text-blue-700 hover:underline cursor-pointer"
              @click="toggle(r.id)"
            >
              {{ isOpen(r.id) ? L.less : L.more }}
            </button>
          </p>

          <!-- Traveled line -->
          <p v-if="travelLine(r)" class="mt-3 text-xs text-gray-600">{{ travelLine(r) }}</p>
        </article>
      </div>

      <button
        type="button"
        class="shrink-0 p-2 text-gray-500 hover:text-gray-800 cursor-pointer"
        :aria-label="L.next"
        @click="scroll(1)"
      >
        <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
          <path d="M9 5l7 7-7 7" />
        </svg>
      </button>
    </div>

    <!-- Read more reviews -->
    <div v-if="hasMore" class="text-center mt-6">
      <button
        type="button"
        class="font-semibold text-blue-700 hover:underline cursor-pointer disabled:opacity-50"
        :disabled="loading"
        @click="load(page + 1)"
      >
        {{ L.readMore }} →
      </button>
    </div>

    <!-- Write a review -->
    <div class="mt-10 max-w-xl mx-auto">
      <button
        type="button"
        class="block mx-auto border border-[#e07300] text-[#e07300] font-semibold px-5 py-2 rounded hover:bg-orange-50 cursor-pointer"
        @click="showForm = !showForm"
      >
        {{ L.write }}
      </button>

      <form v-if="showForm" class="mt-4 space-y-3" @submit.prevent="submit">
        <div class="flex gap-1 text-3xl">
          <button
            v-for="n in 5"
            :key="n"
            type="button"
            :class="n <= form.rating ? 'text-yellow-400' : 'text-gray-300'"
            @click="form.rating = n"
          >★</button>
        </div>

        <textarea
          v-model="form.comment"
          rows="4"
          :placeholder="L.placeholder"
          class="w-full border rounded p-3"
        />

        <div class="grid grid-cols-2 gap-3">
          <select v-model="form.travel_type" class="border rounded p-3">
            <option value="">{{ L.travelType }}</option>
            <option v-for="(label, key) in L.types" :key="key" :value="key">{{ label }}</option>
          </select>
          <input v-model="form.travel_date" type="month" class="border rounded p-3" />
        </div>

        <p v-if="message" class="text-sm" :class="isError ? 'text-red-500' : 'text-green-600'">
          {{ message }}
        </p>

        <button
          :disabled="!form.rating || submitting"
          class="bg-[#e07300] text-white font-bold px-6 py-2 rounded disabled:opacity-50 cursor-pointer"
        >
          {{ submitting ? '...' : L.submit }}
        </button>
      </form>
    </div>
  </section>
</template>

<script setup lang="ts">
import { ref, reactive, computed, onMounted, watch } from 'vue'
import { useI18n } from 'vue-i18n'
import api from '@/services/api'

type Localized = Record<string, string | null> | string | null | undefined

interface Review {
  id: number
  rating: number
  comment: string | null
  reviewer_name: string
  reviewer_avatar: string | null
  package_name: Localized
  package_location: Localized
  package_slug: string | null
  travel_type: string | null
  travel_date: string | null // "2026-09-01"
}

const props = defineProps<{ slug: string }>()
const { locale } = useI18n()

const LIMIT = 110 // characters shown before "read more"

const messages = {
  en: {
    title: 'What travelers say',
    loading: 'Loading...',
    none: 'No reviews yet.',
    more: 'read more',
    less: 'show less',
    readMore: 'Read more reviews',
    write: 'Write a review',
    placeholder: 'Share your experience...',
    travelType: 'Traveled as...',
    submit: 'Submit review',
    prev: 'Previous',
    next: 'Next',
    traveled: (to: string, as: string, when: string) =>
      ['Traveled', to && `to ${to}`, as, when && `in ${when}`].filter(Boolean).join(' '),
    types: {
      solo: 'solo',
      couple: 'as a couple',
      family: 'with family',
      friends: 'with friends',
      business: 'on business',
    } as Record<string, string>,
    login: 'Please log in to write a review.',
    failed: 'Could not submit review.',
  },
  bn: {
    title: 'ভ্রমণকারীরা যা বলছেন',
    loading: 'লোড হচ্ছে...',
    none: 'এখনো কোনো রিভিউ নেই।',
    more: 'আরও পড়ুন',
    less: 'কম দেখুন',
    readMore: 'আরও রিভিউ দেখুন',
    write: 'রিভিউ লিখুন',
    placeholder: 'আপনার অভিজ্ঞতা শেয়ার করুন...',
    travelType: 'কীভাবে ভ্রমণ করেছেন...',
    submit: 'রিভিউ জমা দিন',
    prev: 'আগে',
    next: 'পরে',
    traveled: (to: string, as: string, when: string) =>
      [to && `${to} ভ্রমণ`, as, when].filter(Boolean).join(' · '),
    types: {
      solo: 'একা',
      couple: 'দম্পতি হিসেবে',
      family: 'পরিবারের সাথে',
      friends: 'বন্ধুদের সাথে',
      business: 'ব্যবসায়িক সফরে',
    } as Record<string, string>,
    login: 'রিভিউ লিখতে লগইন করুন।',
    failed: 'রিভিউ জমা দেওয়া যায়নি।',
  },
} as const

const L = computed(() => messages[locale.value as keyof typeof messages] ?? messages.en)

/** Pick the current language from a {en, bn} object. */
const tr = (v: Localized): string => {
  if (!v) return ''
  if (typeof v === 'string') return v
  return v[locale.value] || v.en || v.bn || ''
}

/** "2026-09-01" -> "September, 2026" (follows the site language) */
const monthYear = (iso: string | null): string => {
  if (!iso) return ''
  const [y, m] = iso.split('-').map(Number)
  const d = new Date(y, m - 1, 1) // local date, avoids timezone shifting the month
  const month = d.toLocaleDateString(locale.value, { month: 'long' })
  const year = d.toLocaleDateString(locale.value, { year: 'numeric' })
  return `${month}, ${year}`
}

const travelLine = (r: Review): string => {
  const to = tr(r.package_name)
  const as = r.travel_type ? L.value.types[r.travel_type] ?? '' : ''
  const when = monthYear(r.travel_date)
  return to || as || when ? L.value.traveled(to, as, when) : ''
}

// ---- data ----
const reviews = ref<Review[]>([])
const page = ref(1)
const lastPage = ref(1)
const loading = ref(false)
const hasMore = computed(() => page.value < lastPage.value)

const load = async (p = 1) => {
  loading.value = true
  try {
    const { data } = await api.get(`/tour-package/${props.slug}/reviews`, { params: { page: p } })
    const list: Review[] = data.data.data
    reviews.value = p === 1 ? list : [...reviews.value, ...list]
    page.value = data.data.meta.current_page
    lastPage.value = data.data.meta.last_page
  } catch (e) {
    console.error(e)
  } finally {
    loading.value = false
  }
}

// ---- carousel ----
const scroller = ref<HTMLElement | null>(null)
const scroll = (dir: 1 | -1) =>
  scroller.value?.scrollBy({ left: dir * scroller.value.clientWidth, behavior: 'smooth' })

// ---- read more ----
const open = ref<Record<number, boolean>>({})
const isOpen = (id: number) => !!open.value[id]
const toggle = (id: number) => (open.value[id] = !open.value[id])

// ---- form ----
const showForm = ref(false)
const form = reactive({
  rating: 0,
  comment: '',
  travel_type: '',
  travel_date: '', // "2026-09" from <input type="month">
})
const submitting = ref(false)
const message = ref('')
const isError = ref(false)

const submit = async () => {
  submitting.value = true
  message.value = ''
  try {
    const { data } = await api.post(`/tour-package/${props.slug}/reviews`, form)
    message.value = data.message
    isError.value = false
    Object.assign(form, { rating: 0, comment: '', travel_type: '', travel_date: '' })
    await load(1) // show the new review if it is already approved
  } catch (e: any) {
    isError.value = true
    message.value =
      e?.response?.status === 401
        ? L.value.login
        : e?.response?.data?.message || L.value.failed
  } finally {
    submitting.value = false
  }
}

// reload when the user opens another package
watch(
  () => props.slug,
  () => {
    reviews.value = []
    open.value = {}
    load(1)
  }
)

onMounted(() => load(1))
</script>