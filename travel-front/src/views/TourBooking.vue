<template>
  <div class="min-h-screen bg-gray-50 px-4 py-8 sm:px-6">
    <div class="mx-auto max-w-2xl">
      <!-- Loading -->
      <div v-if="loading" class="flex justify-center py-24">
        <div class="h-10 w-10 animate-spin rounded-full border-4 border-gray-300 border-t-amber-500"></div>
      </div>

      <p v-else-if="loadError" class="py-24 text-center text-red-600">{{ loadError }}</p>

      <!-- Success -->
      <div v-else-if="submitted" class="rounded-xl bg-white p-8 text-center shadow">
        <h2 class="mb-2 text-2xl font-semibold text-green-600">{{ t('booking.success_title') }}</h2>
        <p class="mb-6 text-gray-600">{{ t('booking.success_text') }}</p>
        <router-link to="/" class="rounded bg-blue-600 px-6 py-2 text-white">{{ t('booking.back_home') }}</router-link>
      </div>

      <form v-else-if="pkg" class="space-y-5 rounded-xl bg-white p-5 shadow sm:p-8" @submit.prevent="submit">
        <h1 class="text-xl font-semibold text-amber-500 sm:text-2xl">{{ t('booking.title') }}</h1>

        <!-- Auto-filled, read-only -->
        <div class="space-y-3 rounded-lg bg-gray-50 p-4">
          <div>
            <label class="mb-1 block text-sm text-gray-500">{{ t('booking.package') }}</label>
            <input :value="tr(pkg.package_name)" readonly class="w-full rounded border bg-gray-100 px-3 py-2" />
          </div>
          <div class="grid gap-3 sm:grid-cols-2">
            <div>
              <label class="mb-1 block text-sm text-gray-500">{{ t('booking.duration') }}</label>
              <input :value="tr(pkg.package_duration)" readonly class="w-full rounded border bg-gray-100 px-3 py-2" />
            </div>
            <div>
              <label class="mb-1 block text-sm text-gray-500">{{ t('booking.price') }}</label>
              <input :value="priceText" readonly class="w-full rounded border bg-gray-100 px-3 py-2 font-semibold" />
            </div>
          </div>
        </div>

        <!-- Offer selection (only when the package has offers) -->
        <div v-if="activeOffers.length">
          <label class="mb-2 block text-sm font-medium">
            {{ t('booking.offer', 'Select Offer') }} *
          </label>

          <div class="space-y-3">
            <label
              v-for="offer in activeOffers"
              :key="offer.id"
              class="flex cursor-pointer items-start gap-3 rounded-lg border p-4 transition"
              :class="
                selectedOfferId === offer.id
                  ? 'border-amber-500 bg-amber-50'
                  : 'border-gray-200 hover:border-gray-300'
              "
            >
              <input
                v-model="selectedOfferId"
                type="radio"
                name="offer"
                :value="offer.id"
                class="mt-1 h-4 w-4 accent-amber-600"
              />

              <div class="min-w-0 flex-1">
                <div class="flex items-baseline justify-between gap-3">
                  <span class="break-words font-bold uppercase text-amber-600">
                    {{ tr(offer.offer_name) }}
                  </span>

                  <span v-if="hasPrice(offer.price)" class="whitespace-nowrap text-sm font-bold text-gray-900">
                    {{ t('package_details.currency', 'BDT') }} {{ formatPrice(offer.price) }}
                  </span>
                </div>

                <p v-if="validityText(offer)" class="mt-1 text-xs text-gray-500">
                  {{ validityText(offer) }}
                </p>
              </div>
            </label>
          </div>

          <p v-if="errors.tour_package_offer_id" class="mt-2 text-sm text-red-600">
            {{ errors.tour_package_offer_id[0] }}
          </p>

          <!-- Selected offer details -->
          <div v-if="selectedOffer" class="mt-3 space-y-3 rounded-lg bg-gray-50 p-4 text-sm text-gray-700">
            <p v-if="tr(selectedOffer.departs)">
              <span class="text-gray-500">{{ t('package_details.departs', 'Departs') }}:</span>
              <span class="ml-1 font-semibold uppercase">{{ tr(selectedOffer.departs) }}</span>
            </p>

            <p v-if="hasPrice(selectedOffer.price)">
              <span v-if="tr(selectedOffer.price_label)" class="text-gray-500">
                {{ tr(selectedOffer.price_label) }}
              </span>
              <span class="ml-1 font-bold text-gray-900">
                {{ t('package_details.currency', 'BDT') }} {{ formatPrice(selectedOffer.price) }}
              </span>
            </p>

            <ul v-if="activeHotels(selectedOffer).length" class="space-y-2">
              <li
                v-for="hotel in activeHotels(selectedOffer)"
                :key="hotel.id"
                class="flex items-start gap-3"
              >
                <i class="bi bi-buildings-fill mt-0.5 shrink-0 text-gray-600"></i>
                <span class="break-words">{{ hotelLabel(hotel) }}</span>
              </li>
            </ul>

            <p v-if="tr(selectedOffer.price_note)" class="text-gray-500">
              {{ tr(selectedOffer.price_note) }}
            </p>
          </div>
        </div>

        <!-- User fills these -->
        <div>
          <label class="mb-1 block text-sm font-medium">{{ t('booking.name') }} *</label>
          <input v-model="form.name" type="text" class="w-full rounded border px-3 py-2" />
          <p v-if="errors.name" class="mt-1 text-sm text-red-600">{{ errors.name[0] }}</p>
        </div>

        <div class="grid gap-5 sm:grid-cols-2">
          <div>
            <label class="mb-1 block text-sm font-medium">{{ t('booking.phone') }} *</label>
            <input v-model="form.phone" type="tel" class="w-full rounded border px-3 py-2" />
            <p v-if="errors.phone" class="mt-1 text-sm text-red-600">{{ errors.phone[0] }}</p>
          </div>
          <div>
            <label class="mb-1 block text-sm font-medium">{{ t('booking.email') }}</label>
            <input v-model="form.email" type="email" class="w-full rounded border px-3 py-2" />
            <p v-if="errors.email" class="mt-1 text-sm text-red-600">{{ errors.email[0] }}</p>
          </div>
        </div>

        <div class="grid gap-5 sm:grid-cols-3">
          <div>
            <label class="mb-1 block text-sm font-medium">{{ t('booking.travel_date') }} *</label>
            <input
              v-model="form.travel_date"
              type="date"
              :min="minDate"
              :max="maxDate"
              class="w-full rounded border px-3 py-2"
            />
            <p v-if="errors.travel_date" class="mt-1 text-sm text-red-600">{{ errors.travel_date[0] }}</p>
          </div>
          <div>
            <label class="mb-1 block text-sm font-medium">{{ t('booking.adults') }} *</label>
            <input v-model.number="form.adults" type="number" min="1" class="w-full rounded border px-3 py-2" />
            <p v-if="errors.adults" class="mt-1 text-sm text-red-600">{{ errors.adults[0] }}</p>
          </div>
          <div>
            <label class="mb-1 block text-sm font-medium">{{ t('booking.children') }}</label>
            <input v-model.number="form.children" type="number" min="0" class="w-full rounded border px-3 py-2" />
          </div>
        </div>

        <div>
          <label class="mb-1 block text-sm font-medium">{{ t('booking.message') }}</label>
          <textarea v-model="form.message" rows="4" class="w-full rounded border px-3 py-2"></textarea>
        </div>

        <p v-if="submitError" class="text-sm text-red-600">{{ submitError }}</p>

        <button
          type="submit"
          :disabled="submitting"
          class="min-h-[44px] w-full rounded-lg bg-blue-600 px-6 py-2 font-semibold text-white hover:bg-blue-700 disabled:opacity-60"
        >
          {{ submitting ? t('booking.submitting') : t('booking.submit') }}
        </button>
      </form>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive, computed, watch, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useI18n } from 'vue-i18n'
import api from '@/services/api'

type Localized = Record<string, string> | null | undefined
type LocalizedValue = string | Record<string, string | null> | null | undefined

interface TourPackage {
  id: number
  slug: string
  package_name: Localized
  package_price: Localized
  package_duration: Localized
}

interface OfferHotel {
  id: number
  order?: number
  is_active?: boolean | number
  [key: string]: any
}

interface TourPackageOffer {
  id: number
  tour_package_id: number
  offer_name: Localized
  valid_from: string | null
  valid_till: string | null
  departs: Localized
  price: string | number | null
  price_label: Localized
  price_note: Localized
  order: number
  is_active: boolean
  hotels?: OfferHotel[]
}

const route = useRoute()
const router = useRouter()
const { t, locale } = useI18n({ useScope: 'global' })

const pkg = ref<TourPackage | null>(null)
const offers = ref<TourPackageOffer[]>([])
const selectedOfferId = ref<number | null>(null)

const loading = ref(true)
const loadError = ref('')
const submitting = ref(false)
const submitted = ref(false)
const submitError = ref('')
const errors = ref<Record<string, string[]>>({})

const today = new Date().toISOString().split('T')[0]

const form = reactive({
  name: '',
  phone: '',
  email: '',
  travel_date: '',
  adults: 1,
  children: 0,
  message: '',
})

/* ------------------------------------------------------------------ */
/* Helpers                                                            */
/* ------------------------------------------------------------------ */

const tr = (value: Localized): string => {
  if (!value) return ''
  return value[locale.value] || value.en || value.bn || ''
}

const tri = (value: LocalizedValue): string => {
  if (!value) return ''
  if (typeof value === 'string') return value
  return value[locale.value] || value.en || value.bn || ''
}

const hasPrice = (value: string | number | null | undefined): boolean =>
  value !== null && value !== undefined && value !== ''

const formatPrice = (value: string | number | null): string => {
  const n = Number(value)
  if (Number.isNaN(n)) return ''
  return n.toLocaleString(locale.value === 'bn' ? 'bn-BD' : 'en-US', {
    maximumFractionDigits: 2,
  })
}

// "2026-08-13" -> "13 Aug'26"
const shortDate = (value?: string | null): string => {
  if (!value) return ''

  const [y, m, d] = value.slice(0, 10).split('-').map(Number)
  const loc = locale.value === 'bn' ? 'bn-BD' : 'en-GB'
  const month = new Intl.DateTimeFormat(loc, { month: 'short' }).format(new Date(y, m - 1, d))
  const year = new Intl.NumberFormat(loc, { minimumIntegerDigits: 2, useGrouping: false }).format(y % 100)

  return `${new Intl.NumberFormat(loc, { useGrouping: false }).format(d)} ${month}'${year}`
}

const validityText = (offer: TourPackageOffer): string => {
  const from = shortDate(offer.valid_from)
  const till = shortDate(offer.valid_till)

  if (from && till) return `${t('package_details.valid_from', 'Valid From')} ${from} - ${t('package_details.valid_till', 'Valid Till')} ${till}`
  if (till) return `${t('package_details.valid_till', 'Valid Till')} ${till}`
  if (from) return `${t('package_details.valid_from', 'Valid From')} ${from}`
  return ''
}

const pickText = (hotel: OfferHotel, keys: string[]): string => {
  for (const key of keys) {
    const text = tri(hotel[key])
    if (text) return text
  }
  return ''
}

// "Hotel Arts Kathmandu (Kathmandu)"
const hotelLabel = (hotel: OfferHotel): string => {
  const name = pickText(hotel, ['hotel_name', 'name', 'title', 'hotel'])
  const place = pickText(hotel, ['location', 'city', 'area', 'address'])
  return place ? `${name} (${place})` : name
}

const activeHotels = (offer: TourPackageOffer): OfferHotel[] =>
  (offer.hotels ?? [])
    .filter((h) => h.is_active === undefined || Boolean(h.is_active))
    .sort((a, b) => (a.order ?? 0) - (b.order ?? 0))

/* ------------------------------------------------------------------ */
/* Offers                                                             */
/* ------------------------------------------------------------------ */

const activeOffers = computed(() =>
  offers.value.filter((o) => o.is_active).sort((a, b) => a.order - b.order),
)

const selectedOffer = computed(
  () => activeOffers.value.find((o) => o.id === selectedOfferId.value) ?? null,
)

// Price shown in the read-only box: selected offer price, else package price
const priceText = computed(() => {
  if (selectedOffer.value && hasPrice(selectedOffer.value.price)) {
    return `${t('package_details.currency', 'BDT')} ${formatPrice(selectedOffer.value.price)}`
  }
  return `${tr(pkg.value?.package_price)} Tk.`
})

// Travel date must fall inside the selected offer's validity window
const minDate = computed(() => {
  const from = selectedOffer.value?.valid_from?.slice(0, 10)
  return from && from > today ? from : today
})

const maxDate = computed(() => selectedOffer.value?.valid_till?.slice(0, 10) || undefined)

// A failure here must never break the booking page
const fetchOffers = async (tourPackageId: number) => {
  try {
    const { data } = await api.get('/tour-price-offer', {
      params: { tour_package_id: tourPackageId },
    })

    offers.value = (data.data ?? []).filter(
      (o: TourPackageOffer) => o.tour_package_id === tourPackageId,
    )
  } catch (e) {
    console.error('Failed to load price offers:', e)
    offers.value = []
  }
}

// Pre-select the offer coming from ?offer=ID
const preselectOffer = () => {
  const fromQuery = Number(route.query.offer)

  selectedOfferId.value = activeOffers.value.some((o) => o.id === fromQuery)
    ? fromQuery
    : null
}

watch(selectedOfferId, (id) => {
  // Keep the URL in sync so refresh / sharing keeps the same offer
  router.replace({ query: { ...route.query, offer: id ?? undefined } })

  // Drop a travel date that is outside the new offer's validity window
  if (form.travel_date) {
    const outside =
      form.travel_date < minDate.value ||
      (maxDate.value && form.travel_date > maxDate.value)

    if (outside) form.travel_date = ''
  }

  delete errors.value.tour_package_offer_id
})

/* ------------------------------------------------------------------ */
/* Load                                                               */
/* ------------------------------------------------------------------ */

onMounted(async () => {
  try {
    const { data } = await api.get(`/tour-package/${route.params.slug}`)
    pkg.value = data.data

    if (pkg.value?.id) {
      await fetchOffers(pkg.value.id)
      preselectOffer()
    }
  } catch {
    loadError.value = 'Package not found.'
  } finally {
    loading.value = false
  }
})

/* ------------------------------------------------------------------ */
/* Submit                                                             */
/* ------------------------------------------------------------------ */

const submit = async () => {
  if (!pkg.value) return

  errors.value = {}
  submitError.value = ''

  // Package has offers, so one must be chosen
  if (activeOffers.value.length && !selectedOfferId.value) {
    errors.value = {
      tour_package_offer_id: [t('booking.offer_required', 'Please select an offer.')],
    }
    return
  }

  submitting.value = true

  try {
    await api.post('/tour-booking', {
      ...form,
      tour_package_id: pkg.value.id,
      tour_package_offer_id: selectedOfferId.value,
    })
    submitted.value = true
  } catch (e: any) {
    if (e?.response?.status === 422) {
      errors.value = e.response.data.errors ?? {}
    } else {
      submitError.value = 'Something went wrong. Please try again.'
    }
  } finally {
    submitting.value = false
  }
}
</script>