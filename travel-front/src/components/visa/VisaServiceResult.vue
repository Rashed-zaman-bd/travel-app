<template>
  <section class="mx-auto mt-10 max-w-7xl px-4 pb-16 md:px-6">
    <!-- Loading -->
    <div v-if="loading" class="py-16 text-center text-gray-500">Loading...</div>

    <!-- Error -->
    <div v-else-if="error" class="py-16 text-center text-red-600">{{ error }}</div>

    <!-- Not found -->
    <div v-else-if="!service" class="py-16 text-center text-gray-500">
      No visa information is available for this country and visa type yet.
    </div>

    <div v-else class="grid gap-8 lg:grid-cols-3">
      <!-- Left: summary + notes -->
      <div class="lg:col-span-2">
        <h2 class="mb-3 text-xl font-bold text-black">Summary</h2>

        <div class="space-y-1 text-base text-gray-600">
          <p>
            <span class="font-semibold text-gray-800">Visa Type:</span>
            {{ localized(service.description) }}
          </p>
          <p v-if="localized(service.processing_time)">
            <span class="font-semibold text-gray-800">Processing Time:</span>
            {{ localized(service.processing_time) }}
          </p>
          <template v-if="service.base_fee !== null">
            <p><span class="font-semibold text-gray-800"> Processing Fee:</span>
            {{ localized(service.currency) }} {{ formatFee(service.base_fee) }} <span class="text-base">(Without Airport Transfer)</span></p>
          </template>
          <p><span class="font-semibold text-gray-800">Visa Add-on Service: </span>
            <button
              type="button"
              class="font-semibold text-violet-600 hover:underline cursor-pointer"
              @click="showAddons = true"
            >
              Details
            </button>
          </p>
        </div>

        <p
          v-if="localized(service.description)"
          class="mt-6 whitespace-pre-line text-sm text-gray-600"
        >
          
        </p>

        <template v-if="notes.length">
          <h2 class="mb-3 mt-8 text-xl font-bold text-black">Important Notes</h2>
          <ul class="list-disc space-y-1.5 pl-7 text-base text-gray-600">
            <li v-for="(note, i) in notes" :key="i">{{ note }}</li>
          </ul>
        </template>
      </div>

      <!-- Right: assistance form -->
      <aside class="h-fit rounded-lg border bg-white p-5 shadow-sm">
        <h3 class="text-lg font-bold text-black">Request Visa Assistance</h3>
        <p class="mb-4 mt-1 text-xs text-gray-500">
          Please share your contact information. Our team will get in touch with you shortly.
        </p>

        <p
          v-if="success"
          class="mb-3 rounded bg-green-50 p-3 text-sm text-green-700"
        >
          {{ success }}
        </p>

        <div class="space-y-4">
          <div>
            <label class="mb-1 block text-xs text-gray-500">Name</label>
            <input
              v-model="form.name"
              type="text"
              placeholder="Enter your name"
              class="w-full rounded border px-3 py-2 text-sm outline-none focus:border-amber-500"
            />
            <p v-if="errors.name" class="mt-1 text-xs text-red-600">{{ errors.name[0] }}</p>
          </div>

          <div>
            <label class="mb-1 block text-xs text-gray-500">Phone Number</label>
            <div class="flex">
              <select
                v-model="form.phone_code"
                class="rounded-l border border-r-0 bg-gray-100 px-2 text-sm"
              >
                <option v-for="c in phoneCodes" :key="c" :value="c">{{ c }}</option>
              </select>
              <input
                v-model="form.phone"
                type="tel"
                placeholder="1XXX XXXXXX"
                class="w-full rounded-r border px-3 py-2 text-sm outline-none focus:border-amber-500"
              />
            </div>
            <p v-if="errors.phone" class="mt-1 text-xs text-red-600">{{ errors.phone[0] }}</p>
          </div>

          <div>
            <label class="mb-1 block text-xs text-gray-500">Email Address</label>
            <input
              v-model="form.email"
              type="email"
              placeholder="someone@example.com"
              class="w-full rounded border px-3 py-2 text-sm outline-none focus:border-amber-500"
            />
            <p v-if="errors.email" class="mt-1 text-xs text-red-600">{{ errors.email[0] }}</p>
          </div>

          <button
            type="button"
            :disabled="submitting"
            class="w-full rounded bg-yellow-400 py-2.5 text-sm font-semibold text-indigo-950 hover:bg-yellow-300 disabled:opacity-60 cursor-pointer"
            @click="submit"
          >
            {{ submitting ? 'Submitting...' : 'Submit' }}
          </button>
        </div>
      </aside>
    </div>
    <VisaFee
      v-model="showAddons"
      :country-id="countryId"
      :type-id="typeId"
      :currency="localized(service?.currency)"
    />
  </section>
</template>

<script setup lang="ts">
import { ref, reactive, computed, watch } from 'vue'
import { useI18n } from 'vue-i18n'
import api from '@/services/api'
import VisaFee from '@/components/visa/VisaFee.vue'

const showAddons = ref(false)

type Localized = Record<string, string | null | undefined>

interface Service {
  id: number
  title: Localized
  description: Localized
  processing_time: Localized
  currency: Localized
  base_fee: number | null
  eligibility: Localized
  application_url: string | null
  type?: { name: Localized }
}

const props = defineProps<{
  countryId: number
  typeId: number
}>()

const { locale } = useI18n()

function localized(value?: Localized | null): string {
  return value?.[locale.value] || value?.en || ''
}

const formatFee = (n: number) => Number(n).toLocaleString()

// ----- service data -----
const service = ref<Service | null>(null)
const loading = ref(false)
const error = ref('')

// "Important Notes" = eligibility text, one bullet per line
const notes = computed(() =>
  localized(service.value?.eligibility)
    .split(/\r?\n/)
    .map((l) => l.trim())
    .filter(Boolean),
)


watch(() => [props.countryId, props.typeId], fetchService, { immediate: true })

// ----- assistance form -----
// ----- assistance form state (must be declared BEFORE the immediate watch) -----
const phoneCodes = ['+880', '+91', '+1', '+44', '+971', '+966', '+65']
const errors = ref<Record<string, string[]>>({})
const submitting = ref(false)
const success = ref('')
const form = reactive({ name: '', phone_code: '+880', phone: '', email: '' })



async function fetchService() {
  loading.value = true
  error.value = ''
  success.value = ''
  try {
    const { data } = await api.get('/visa-services', {
      params: {
        visa_country_id: props.countryId,
        visa_type_id: props.typeId,
        is_active: 1,
        paginate: 0,
        all_locales: 1,
      },
    })
    service.value = data.data[0] ?? null
  } catch {
    error.value = 'Could not load visa information. Please try again.'
  } finally {
    loading.value = false
  }
}

// Keep the watch AFTER everything fetchService touches
watch(() => [props.countryId, props.typeId], fetchService, { immediate: true })

async function submit() {
  if (!service.value) return
  submitting.value = true
  errors.value = {}
  success.value = ''

  try {
    await api.post('/visa-assistance-requests', {
      visa_service_id: service.value.id,
      name: form.name,
      phone: `${form.phone_code}${form.phone.replace(/\s+/g, '')}`,
      email: form.email || null,
    })
    success.value = 'Thank you! Our team will contact you shortly.'
    form.name = ''
    form.phone = ''
    form.email = ''
  } catch (e: any) {
    if (e.response?.status === 422) {
      errors.value = e.response.data.errors ?? {}
    } else {
      alert(e.response?.data?.message ?? 'Something went wrong.')
    }
  } finally {
    submitting.value = false
  }
}
</script>