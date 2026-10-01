<template>
  <div class="min-h-screen bg-gray-50 px-4 py-8 sm:px-6">
    <div class="mx-auto max-w-2xl">
      <!-- Loading -->
      <div v-if="loading" class="flex justify-center py-24">
        <div class="h-10 w-10 animate-spin rounded-full border-4 border-gray-300 border-t-blue-600"></div>
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
              <input :value="`${tr(pkg.package_price)} Tk.`" readonly class="w-full rounded border bg-gray-100 px-3 py-2" />
            </div>
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
            <input v-model="form.travel_date" type="date" :min="today" class="w-full rounded border px-3 py-2" />
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
import { ref, reactive, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import { useI18n } from 'vue-i18n'
import api from '@/services/api'

type Localized = Record<string, string> | null | undefined

interface TourPackage {
  id: number
  slug: string
  package_name: Localized
  package_price: Localized
  package_duration: Localized
}

const route = useRoute()
const { t, locale } = useI18n({ useScope: 'global' })

const pkg = ref<TourPackage | null>(null)
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

const tr = (value: Localized): string => {
  if (!value) return ''
  return value[locale.value] || value.en || value.bn || ''
}

onMounted(async () => {
  try {
    const { data } = await api.get(`/tour-package/${route.params.slug}`)
    pkg.value = data.data
  } catch {
    loadError.value = 'Package not found.'
  } finally {
    loading.value = false
  }
})

const submit = async () => {
  if (!pkg.value) return
  submitting.value = true
  errors.value = {}
  submitError.value = ''
  try {
    await api.post('/tour-booking', { ...form, tour_package_id: pkg.value.id })
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