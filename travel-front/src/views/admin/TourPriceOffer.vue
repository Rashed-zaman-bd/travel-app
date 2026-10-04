<template>
  <div class="min-h-screen bg-gray-50 p-4 md:p-6">
    <!-- Header -->
    <div
      class="mb-6 flex flex-col gap-4 rounded-xl bg-white p-5 shadow-sm sm:flex-row sm:items-center sm:justify-between"
    >
      <div>
        <h1 class="text-xl font-bold text-gray-800">Tour Price Offers</h1>
        <p class="mt-1 text-sm text-gray-500">Manage tour package price offers.</p>
      </div>

      <button
        type="button"
        @click="openCreateModal"
        class="inline-flex items-center justify-center rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-700"
      >
        <i class="bi bi-plus-lg mr-2"></i>
        Add Price Offer
      </button>
    </div>

    <!-- Page messages (only while modal is closed; the modal shows its own) -->
    <div
      v-if="successMessage && !showModal"
      class="mb-5 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700"
    >
      {{ successMessage }}
    </div>

    <div
      v-if="errorMessage && !showModal"
      class="mb-5 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700"
    >
      {{ errorMessage }}
    </div>

    <!-- Filters -->
    <div class="mb-5 rounded-xl bg-white p-4 shadow-sm">
      <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
        <div>
          <label class="mb-1 block text-sm font-medium text-gray-700">Search</label>
          <input
            v-model="search"
            type="text"
            placeholder="Search offer..."
            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
          />
        </div>

        <div>
          <label class="mb-1 block text-sm font-medium text-gray-700">Tour Package</label>
          <select
            v-model="filterPackage"
            class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
          >
            <option value="">All Packages</option>
            <option v-for="pkg in packages" :key="pkg.id" :value="String(pkg.id)">
              {{ getLocalized(pkg.package_name) }}
            </option>
          </select>
        </div>

        <div>
          <label class="mb-1 block text-sm font-medium text-gray-700">Status</label>
          <select
            v-model="filterStatus"
            class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
          >
            <option value="">All Status</option>
            <option value="active">Active</option>
            <option value="inactive">Inactive</option>
          </select>
        </div>
      </div>
    </div>

    <!-- Loading -->
    <div v-if="loading" class="rounded-xl bg-white p-10 text-center shadow-sm">
      <i class="bi bi-arrow-repeat animate-spin text-2xl text-blue-600"></i>
      <p class="mt-2 text-sm text-gray-500">Loading price offers...</p>
    </div>

    <!-- Empty -->
    <div
      v-else-if="filteredOffers.length === 0"
      class="rounded-xl bg-white p-10 text-center shadow-sm"
    >
      <i class="bi bi-tag text-4xl text-gray-300"></i>
      <p class="mt-3 font-medium text-gray-700">No price offers found.</p>
      <p class="mt-1 text-sm text-gray-500">Create a new price offer to get started.</p>
    </div>

    <!-- Table -->
    <div v-else class="overflow-hidden rounded-xl bg-white shadow-sm">
      <div class="overflow-x-auto">
        <table class="min-w-full">
          <thead class="bg-gray-50">
            <tr class="border-b border-gray-200">
              <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-500">#</th>
              <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-500">Package</th>
              <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-500">Offer</th>
              <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-500">Valid From</th>
              <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-500">Valid Till</th>
              <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-500">Departs</th>
              <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-500">Price</th>
              <th class="px-4 py-3 text-center text-xs font-semibold uppercase text-gray-500">Order</th>
              <th class="px-4 py-3 text-center text-xs font-semibold uppercase text-gray-500">Status</th>
              <th class="px-4 py-3 text-right text-xs font-semibold uppercase text-gray-500">Action</th>
            </tr>
          </thead>

          <tbody>
            <tr
              v-for="(offer, index) in filteredOffers"
              :key="offer.id"
              class="border-b border-gray-100 transition hover:bg-gray-50"
            >
              <td class="px-4 py-4 text-sm text-gray-600">{{ index + 1 }}</td>

              <td class="px-4 py-4">
                <p class="max-w-[220px] text-sm font-medium text-gray-800">
                  {{ getPackageName(offer) || '-' }}
                </p>
              </td>

              <td class="px-4 py-4">
                <p class="font-semibold text-blue-600">{{ getLocalized(offer.offer_name) }}</p>
              </td>

              <td class="px-4 py-4 text-sm text-gray-600">
                {{ formatDate(offer.valid_from) }}
              </td>

              <td class="px-4 py-4 text-sm text-gray-600">
                {{ formatDate(offer.valid_till) }}
              </td>

              <td class="px-4 py-4 text-sm text-gray-600">
                {{ getLocalized(offer.departs) || '-' }}
              </td>

              <td class="px-4 py-4">
                <span class="font-semibold text-gray-800">
                  {{ formatPrice(offer.price) }}
                </span>
              </td>

              <td class="px-4 py-4 text-center text-sm text-gray-600">{{ offer.order }}</td>

              <td class="px-4 py-4 text-center">
                <span
                  v-if="offer.is_active"
                  class="inline-flex rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-700"
                >
                  Active
                </span>
                <span
                  v-else
                  class="inline-flex rounded-full bg-red-100 px-3 py-1 text-xs font-semibold text-red-700"
                >
                  Inactive
                </span>
              </td>

              <td class="px-4 py-4">
                <div class="flex justify-end gap-2">
                  <button
                    type="button"
                    @click="openEditModal(offer)"
                    class="rounded-lg border border-blue-200 px-3 py-2 text-blue-600 transition hover:bg-blue-50"
                    title="Edit"
                  >
                    <i class="bi bi-pencil"></i>
                  </button>

                  <button
                    type="button"
                    @click="deleteOffer(offer)"
                    class="rounded-lg border border-red-200 px-3 py-2 text-red-600 transition hover:bg-red-50"
                    title="Delete"
                  >
                    <i class="bi bi-trash"></i>
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Modal -->
    <div
      v-if="showModal"
      class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4"
      @click.self="closeModal"
    >
      <div
        class="flex max-h-[95vh] w-full max-w-4xl flex-col overflow-hidden rounded-2xl bg-white shadow-2xl"
      >
        <!-- Modal Header -->
        <div class="flex items-center justify-between border-b border-gray-200 px-6 py-4">
          <div>
            <h2 class="text-lg font-bold text-gray-800">
              {{ isEditing ? 'Edit Price Offer' : 'Add Price Offer' }}
            </h2>
            <p class="mt-1 text-xs text-gray-500">
              Enter the offer information in English and Bangla.
            </p>
          </div>

          <button
            type="button"
            @click="closeModal"
            class="rounded-lg p-2 text-gray-400 transition hover:bg-gray-100 hover:text-gray-700"
          >
            <i class="bi bi-x-lg"></i>
          </button>
        </div>

        <!-- Modal Body -->
        <div class="overflow-y-auto px-6 py-5">
          <!-- Messages inside the modal so they are visible while it stays open -->
          <div
            v-if="successMessage"
            class="mb-5 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700"
          >
            {{ successMessage }}
          </div>

          <div
            v-if="errorMessage"
            class="mb-5 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700"
          >
            {{ errorMessage }}
          </div>

          <form @submit.prevent="saveOffer" class="space-y-6">
            <!-- Package -->
            <div>
              <label class="mb-1 block text-sm font-semibold text-gray-700">
                Tour Package
                <span class="text-red-500">*</span>
              </label>

              <select v-model="form.tour_package_id" :class="inputClass(errors.tour_package_id)">
                <option value="">Select Tour Package</option>
                <option v-for="pkg in packages" :key="pkg.id" :value="pkg.id">
                  {{ getLocalized(pkg.package_name) }}
                </option>
              </select>

              <p v-if="errors.tour_package_id" class="mt-1 text-xs text-red-500">
                {{ errors.tour_package_id }}
              </p>
            </div>

            <!-- Bilingual fields -->
            <div v-for="section in localeSections" :key="section.key">
              <h3 class="mb-3 text-sm font-bold text-gray-800">
                {{ section.title }}
              </h3>

              <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <div v-for="lang in languages" :key="lang.code">
                  <label class="mb-1 block text-sm font-medium text-gray-700">
                    {{ lang.label }}
                    <span v-if="section.required && lang.code === 'en'" class="text-red-500">*</span>
                  </label>

                  <input
                    v-model="form[section.key][lang.code]"
                    type="text"
                    maxlength="100"
                    :placeholder="section.placeholder?.[lang.code] ?? ''"
                    :class="inputClass(errors[`${section.key}.${lang.code}`])"
                  />

                  <p
                    v-if="errors[`${section.key}.${lang.code}`]"
                    class="mt-1 text-xs text-red-500"
                  >
                    {{ errors[`${section.key}.${lang.code}`] }}
                  </p>
                </div>
              </div>
            </div>

            <!-- Validity & Price (single values, not per-language) -->
            <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
              <div>
                <label class="mb-1 block text-sm font-semibold text-gray-700">Valid From</label>
                <input v-model="form.valid_from" type="date" :class="inputClass(errors.valid_from)" />
                <p v-if="errors.valid_from" class="mt-1 text-xs text-red-500">{{ errors.valid_from }}</p>
              </div>

              <div>
                <label class="mb-1 block text-sm font-semibold text-gray-700">Valid Till</label>
                <input
                  v-model="form.valid_till"
                  type="date"
                  :min="form.valid_from || undefined"
                  :class="inputClass(errors.valid_till)"
                />
                <p v-if="errors.valid_till" class="mt-1 text-xs text-red-500">{{ errors.valid_till }}</p>
              </div>

              <div>
                <label class="mb-1 block text-sm font-semibold text-gray-700">Price</label>
                <input
                  v-model="form.price"
                  type="number"
                  min="0"
                  step="0.01"
                  placeholder="15000"
                  :class="inputClass(errors.price)"
                />
                <p v-if="errors.price" class="mt-1 text-xs text-red-500">{{ errors.price }}</p>
              </div>
            </div>

            <!-- Order / Status -->
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
              <div>
                <label class="mb-1 block text-sm font-semibold text-gray-700">Order</label>
                <input
                  v-model.number="form.order"
                  type="number"
                  min="0"
                  :class="inputClass(errors.order)"
                />
                <p v-if="errors.order" class="mt-1 text-xs text-red-500">{{ errors.order }}</p>
              </div>

              <div>
                <label class="mb-1 block text-sm font-semibold text-gray-700">Status</label>

                <label
                  class="flex h-[42px] cursor-pointer items-center gap-3 rounded-lg border border-gray-300 px-3"
                >
                  <input
                    v-model="form.is_active"
                    type="checkbox"
                    class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                  />
                  <span class="text-sm text-gray-700">Active</span>
                </label>
              </div>
            </div>
          </form>
        </div>

        <!-- Modal Footer -->
        <div
          class="flex items-center justify-between border-t border-gray-200 bg-gray-50 px-6 py-4"
        >
          <p class="text-xs text-gray-500">
            {{ isEditing ? 'Editing existing offer' : 'Creating new offer' }}
          </p>

          <div class="flex gap-3">
            <button
              type="button"
              @click="closeModal"
              class="rounded-lg border border-gray-300 bg-white px-5 py-2.5 text-sm font-semibold text-gray-700 transition hover:bg-gray-100"
            >
              Close
            </button>

            <button
              type="button"
              @click="saveOffer"
              :disabled="saving"
              class="rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-700 disabled:cursor-not-allowed disabled:opacity-60"
            >
              <i v-if="saving" class="bi bi-arrow-repeat mr-2 animate-spin"></i>
              {{ saving ? 'Saving...' : isEditing ? 'Update Offer' : 'Save Offer' }}
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed, onMounted, reactive, ref } from 'vue'
import api from '@/services/api'

interface LocaleValue {
  en: string
  bn: string
}

interface TourPackage {
  id: number
  package_name: LocaleValue | string
}

interface TourPackageOffer {
  id: number
  tour_package_id: number
  tour_package?: TourPackage
  offer_name: LocaleValue
  valid_from: string | null
  valid_till: string | null
  departs: LocaleValue | null
  price: string | number | null
  price_label: LocaleValue | null
  price_note: LocaleValue | null
  order: number
  is_active: boolean | number
}

interface FormData {
  tour_package_id: number | string
  offer_name: LocaleValue
  valid_from: string
  valid_till: string
  departs: LocaleValue
  price: number | string
  price_label: LocaleValue
  price_note: LocaleValue
  order: number
  is_active: boolean
}

type LocaleField =
  | 'offer_name'
  | 'departs'
  | 'price_label'
  | 'price_note'

const localeFields: LocaleField[] = [
  'offer_name',
  'departs',
  'price_label',
  'price_note',
]

const languages: { code: keyof LocaleValue; label: string }[] = [
  { code: 'en', label: 'English' },
  { code: 'bn', label: 'Bangla' },
]

const localeSections: {
  key: LocaleField
  title: string
  required?: boolean
  placeholder?: Partial<LocaleValue>
}[] = [
  { key: 'offer_name', title: 'Offer Name', required: true },
  { key: 'departs', title: 'Departs', placeholder: { en: 'EVERY DAY', bn: 'প্রতিদিন' } },
  {
    key: 'price_label',
    title: 'Price Label',
    placeholder: { en: 'Price Per Person Double:', bn: 'জনপ্রতি ডাবল:' },
  },
  {
    key: 'price_note',
    title: 'Price Note',
    placeholder: { en: 'Price includes VAT & Tax', bn: 'মূল্যের মধ্যে VAT ও Tax অন্তর্ভুক্ত' },
  },
]

const loading = ref(false)
const saving = ref(false)

const showModal = ref(false)
const isEditing = ref(false)

const offers = ref<TourPackageOffer[]>([])
const packages = ref<TourPackage[]>([])

const editingId = ref<number | null>(null)

const search = ref('')
const filterPackage = ref('')
const filterStatus = ref('')

const successMessage = ref('')
const errorMessage = ref('')

const errors = ref<Record<string, string>>({})

const emptyLocale = (): LocaleValue => ({ en: '', bn: '' })

const createForm = (): FormData => ({
  tour_package_id: '',
  offer_name: emptyLocale(),
  valid_from: '',
  valid_till: '',
  departs: emptyLocale(),
  price: '',
  price_label: emptyLocale(),
  price_note: emptyLocale(),
  order: 0,
  is_active: true,
})

const form = reactive<FormData>(createForm())

/* ------------------------------------------------------------------ */
/* Helpers                                                            */
/* ------------------------------------------------------------------ */

const getLocalized = (value?: LocaleValue | string | null): string => {
  if (!value) return ''
  if (typeof value === 'string') return value
  return value.en || value.bn || ''
}

const getPackageName = (offer: TourPackageOffer): string => {
  return getLocalized(offer.tour_package?.package_name)
}

const inputClass = (error?: string) => [
  'w-full rounded-lg border px-3 py-2 text-sm outline-none transition',
  error
    ? 'border-red-400 focus:border-red-500 focus:ring-2 focus:ring-red-100'
    : 'border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-100',
]

// Works for { data: [...] } and for paginated { data: { data: [...] } }
const extractList = <T,>(response: any): T[] => {
  const body = response?.data
  if (Array.isArray(body)) return body
  if (Array.isArray(body?.data)) return body.data
  if (Array.isArray(body?.data?.data)) return body.data.data
  return []
}

const toLocale = (value?: Partial<LocaleValue> | null): LocaleValue => ({
  en: value?.en ?? '',
  bn: value?.bn ?? '',
})

const emptyToNull = (value: string): string | null => {
  const trimmed = (value ?? '').trim()
  return trimmed === '' ? null : trimmed
}

// API may return "2026-10-04" or "2026-10-04T00:00:00.000000Z"
const toDateInput = (value?: string | null): string =>
  value ? String(value).slice(0, 10) : ''

const formatDate = (value?: string | null): string => {
  const date = toDateInput(value)
  if (!date) return '-'
  const [y, m, d] = date.split('-')
  return `${d}/${m}/${y}`
}

const formatPrice = (value?: string | number | null): string => {
  if (value === null || value === undefined || value === '') return '-'
  const n = Number(value)
  return Number.isNaN(n)
    ? '-'
    : n.toLocaleString('en-US', { maximumFractionDigits: 2 })
}

const clearMessages = () => {
  successMessage.value = ''
  errorMessage.value = ''
}

/* ------------------------------------------------------------------ */
/* Filter                                                             */
/* ------------------------------------------------------------------ */

const filteredOffers = computed(() => {
  const term = search.value.trim().toLowerCase()

  return offers.value.filter((offer) => {
    const offerNames = [offer.offer_name?.en, offer.offer_name?.bn]
      .filter(Boolean)
      .join(' ')
      .toLowerCase()

    const packageName = getPackageName(offer).toLowerCase()

    const matchesSearch =
      !term || offerNames.includes(term) || packageName.includes(term)

    const matchesPackage =
      !filterPackage.value ||
      String(offer.tour_package_id) === filterPackage.value

    const isActive = Boolean(offer.is_active)

    const matchesStatus =
      !filterStatus.value ||
      (filterStatus.value === 'active' ? isActive : !isActive)

    return matchesSearch && matchesPackage && matchesStatus
  })
})

/* ------------------------------------------------------------------ */
/* Load                                                               */
/* ------------------------------------------------------------------ */

const fetchPackages = async () => {
  try {
    const response = await api.get('/tour-package')
    packages.value = extractList<TourPackage>(response)
  } catch (error) {
    console.error('Failed to load packages:', error)
  }
}

const fetchOffers = async () => {
  loading.value = true

  try {
    const response = await api.get('/admin/tour-price-offer?all_locales=true')
    offers.value = extractList<TourPackageOffer>(response)
  } catch (error: any) {
    console.error(error)
    errorMessage.value =
      error?.response?.data?.message || 'Failed to load price offers.'
  } finally {
    loading.value = false
  }
}

/* ------------------------------------------------------------------ */
/* Modal                                                              */
/* ------------------------------------------------------------------ */

const resetForm = () => {
  Object.assign(form, createForm())
  editingId.value = null
  errors.value = {}
}

const openCreateModal = () => {
  resetForm()
  clearMessages()

  isEditing.value = false
  showModal.value = true
}

const fillForm = (offer: TourPackageOffer) => {
  form.tour_package_id = offer.tour_package_id
  form.valid_from = toDateInput(offer.valid_from)
  form.valid_till = toDateInput(offer.valid_till)
  form.price = offer.price ?? ''

  localeFields.forEach((field) => {
    form[field] = toLocale(offer[field])
  })

  form.order = offer.order ?? 0
  form.is_active = Boolean(offer.is_active ?? true)
}

const openEditModal = (offer: TourPackageOffer) => {
  resetForm()
  clearMessages()

  isEditing.value = true
  editingId.value = offer.id

  fillForm(offer)

  showModal.value = true
}

const closeModal = () => {
  showModal.value = false
  errors.value = {}
}

/* ------------------------------------------------------------------ */
/* Save                                                               */
/* ------------------------------------------------------------------ */

const buildPayload = () => ({
  tour_package_id: Number(form.tour_package_id),

  offer_name: {
    en: form.offer_name.en.trim(),
    bn: emptyToNull(form.offer_name.bn),
  },

  valid_from: form.valid_from || null,
  valid_till: form.valid_till || null,
  departs: { en: emptyToNull(form.departs.en), bn: emptyToNull(form.departs.bn) },
  price: form.price === '' || form.price === null ? null : Number(form.price),
  price_label: { en: emptyToNull(form.price_label.en), bn: emptyToNull(form.price_label.bn) },
  price_note: { en: emptyToNull(form.price_note.en), bn: emptyToNull(form.price_note.bn) },

  order: Number(form.order) || 0,
  is_active: form.is_active,
})

const saveOffer = async () => {
  if (saving.value) return

  saving.value = true
  errors.value = {}
  clearMessages()

  try {
    const payload = buildPayload()

    if (isEditing.value && editingId.value) {
      await api.put(`/admin/tour-price-offer/${editingId.value}`, payload)
      successMessage.value = 'Price offer updated successfully.'
    } else {
      await api.post('/admin/tour-price-offer', payload)
      successMessage.value = 'Price offer created successfully.'
    }

    // Close the modal on success; the message shows on the page.
    closeModal()

    await fetchOffers()
  } catch (error: any) {
    console.error(error)

    if (error?.response?.status === 422) {
      const validationErrors = error.response.data?.errors || {}
      const converted: Record<string, string> = {}

      Object.keys(validationErrors).forEach((key) => {
        converted[key] = validationErrors[key]?.[0] || ''
      })

      errors.value = converted
      errorMessage.value = 'Please check the form errors.'
    } else {
      errorMessage.value =
        error?.response?.data?.message || 'Failed to save price offer.'
    }
  } finally {
    saving.value = false
  }
}

/* ------------------------------------------------------------------ */
/* Delete                                                             */
/* ------------------------------------------------------------------ */

const deleteOffer = async (offer: TourPackageOffer) => {
  const name = getLocalized(offer.offer_name)

  if (!window.confirm(`Are you sure you want to delete "${name}"?`)) {
    return
  }

  clearMessages()

  try {
    await api.delete(`/admin/tour-price-offer/${offer.id}`)
    successMessage.value = 'Price offer deleted successfully.'
    await fetchOffers()
  } catch (error: any) {
    console.error(error)
    errorMessage.value =
      error?.response?.data?.message || 'Failed to delete price offer.'
  }
}

/* ------------------------------------------------------------------ */
/* Init                                                               */
/* ------------------------------------------------------------------ */

onMounted(async () => {
  await Promise.all([fetchPackages(), fetchOffers()])
})
</script>