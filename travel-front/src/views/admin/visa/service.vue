<template>
  <div class="p-6">
    <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
      <h1 class="text-2xl font-semibold">Visa Service</h1>
      <button class="rounded bg-blue-600 px-4 py-2 text-white hover:bg-blue-700" @click="openCreate">
        + Add Service
      </button>
    </div>

    <!-- Filters -->
    <div class="mb-4 flex flex-wrap gap-3">
      <input
        v-model="search"
        type="text"
        placeholder="Search by title..."
        class="w-64 rounded border px-3 py-2"
      />

      <select v-model="countryFilter" class="rounded border px-3 py-2">
        <option value="">All countries</option>
        <option v-for="c in countries" :key="c.id" :value="c.id">{{ label(c.name) }}</option>
      </select>

      <select v-model="typeFilter" class="rounded border px-3 py-2">
        <option value="">All visa types</option>
        <option v-for="t in visaTypes" :key="t.id" :value="t.id">{{ label(t.name) }}</option>
      </select>

      <select v-model="activeFilter" class="rounded border px-3 py-2">
        <option value="">All status</option>
        <option value="1">Active</option>
        <option value="0">Inactive</option>
      </select>
    </div>

    <!-- Table -->
    <div class="overflow-x-auto rounded border bg-white">
      <table class="min-w-full text-left text-sm">
        <thead class="bg-gray-50 text-gray-600">
          <tr>
            <th class="px-4 py-3">#</th>
            <th class="px-4 py-3">Title (EN)</th>
            <th class="px-4 py-3">Title (BN)</th>
            <th class="px-4 py-3">Country</th>
            <th class="px-4 py-3">Visa Type</th>
            <th class="px-4 py-3">Fee</th>
            <th class="px-4 py-3">Processing</th>
            <th class="px-4 py-3">Status</th>
            <th class="px-4 py-3 text-right">Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-if="loading">
            <td colspan="9" class="px-4 py-6 text-center text-gray-500">Loading...</td>
          </tr>
          <tr v-else-if="!services.length">
            <td colspan="9" class="px-4 py-6 text-center text-gray-500">No services found.</td>
          </tr>
          <tr v-for="(s, i) in services" :key="s.id" class="border-t">
            <td class="px-4 py-3">{{ (meta.current_page - 1) * meta.per_page + i + 1 }}</td>
            <td class="px-4 py-3">{{ s.title?.en }}</td>
            <td class="px-4 py-3">{{ s.title?.bn || '—' }}</td>
            <td class="px-4 py-3">{{ label(s.country?.name) || '—' }}</td>
            <td class="px-4 py-3">{{ label(s.type?.name) || '—' }}</td>
            <td class="px-4 py-3">
              <template v-if="s.base_fee !== null">
                {{ s.currency?.en || '' }} {{ s.base_fee }}
              </template>
              <template v-else>—</template>
            </td>
            <td class="px-4 py-3">{{ s.processing_time?.en || '—' }}</td>
            <td class="px-4 py-3">
              <span
                class="rounded px-2 py-1 text-xs"
                :class="s.is_active ? 'bg-green-100 text-green-700' : 'bg-gray-200 text-gray-600'"
              >
                {{ s.is_active ? 'Active' : 'Inactive' }}
              </span>
            </td>
            <td class="whitespace-nowrap px-4 py-3 text-right">
              <button class="mr-3 text-blue-600 hover:underline" @click="openEdit(s)">Edit</button>
              <button class="text-red-600 hover:underline" @click="confirmDelete(s)">Delete</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Pagination -->
    <div class="mt-4 flex items-center justify-between text-sm">
      <span class="text-gray-600">Total: {{ meta.total }}</span>
      <div class="flex items-center gap-2">
        <button
          class="rounded border px-3 py-1 disabled:opacity-40"
          :disabled="meta.current_page <= 1"
          @click="page--"
        >
          Prev
        </button>
        <span>Page {{ meta.current_page }} / {{ meta.last_page }}</span>
        <button
          class="rounded border px-3 py-1 disabled:opacity-40"
          :disabled="meta.current_page >= meta.last_page"
          @click="page++"
        >
          Next
        </button>
      </div>
    </div>

    <!-- Create / Edit modal -->
    <div v-if="showForm" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
      <div class="max-h-[90vh] w-full max-w-3xl overflow-y-auto rounded bg-white p-6">
        <h2 class="mb-4 text-xl font-semibold">{{ editingId ? 'Edit Service' : 'Add Service' }}</h2>

        <div class="grid gap-4 md:grid-cols-2">
          <!-- Country -->
          <div>
            <label class="mb-1 block text-sm font-medium">Country *</label>
            <select v-model="form.visa_country_id" class="w-full rounded border px-3 py-2">
              <option value="" disabled>Select country</option>
              <option v-for="c in countries" :key="c.id" :value="c.id">{{ label(c.name) }}</option>
            </select>
            <p v-if="errors.visa_country_id" class="mt-1 text-xs text-red-600">{{ errors.visa_country_id[0] }}</p>
          </div>

          <!-- Visa type -->
          <div>
            <label class="mb-1 block text-sm font-medium">Visa Type *</label>
            <select v-model="form.visa_type_id" class="w-full rounded border px-3 py-2">
              <option value="" disabled>Select visa type</option>
              <option v-for="t in visaTypes" :key="t.id" :value="t.id">{{ label(t.name) }}</option>
            </select>
            <p v-if="errors.visa_type_id" class="mt-1 text-xs text-red-600">{{ errors.visa_type_id[0] }}</p>
          </div>

          <!-- Title -->
          <div>
            <label class="mb-1 block text-sm font-medium">Title (EN) *</label>
            <input v-model="form.title.en" class="w-full rounded border px-3 py-2" />
            <p v-if="errors['title.en']" class="mt-1 text-xs text-red-600">{{ errors['title.en'][0] }}</p>
          </div>
          <div>
            <label class="mb-1 block text-sm font-medium">Title (BN)</label>
            <input v-model="form.title.bn" class="w-full rounded border px-3 py-2" />
            <p v-if="errors['title.bn']" class="mt-1 text-xs text-red-600">{{ errors['title.bn'][0] }}</p>
          </div>

          <!-- Description -->
          <div>
            <label class="mb-1 block text-sm font-medium">Visa Type (EN)</label>
            <textarea v-model="form.description.en" rows="3" class="w-full rounded border px-3 py-2"></textarea>
            <p v-if="errors['description.en']" class="mt-1 text-xs text-red-600">{{ errors['description.en'][0] }}</p>
          </div>
          <div>
            <label class="mb-1 block text-sm font-medium">Visa Type (BN)</label>
            <textarea v-model="form.description.bn" rows="3" class="w-full rounded border px-3 py-2"></textarea>
            <p v-if="errors['description.bn']" class="mt-1 text-xs text-red-600">{{ errors['description.bn'][0] }}</p>
          </div>

          <!-- Important Notes -->
          <div>
            <label class="mb-1 block text-sm font-medium">Important Notes (EN)</label>
            <textarea v-model="form.eligibility.en" rows="3" class="w-full rounded border px-3 py-2"></textarea>
            <p v-if="errors['eligibility.en']" class="mt-1 text-xs text-red-600">{{ errors['eligibility.en'][0] }}</p>
          </div>
          <div>
            <label class="mb-1 block text-sm font-medium">Important Notes (BN)</label>
            <textarea v-model="form.eligibility.bn" rows="3" class="w-full rounded border px-3 py-2"></textarea>
            <p v-if="errors['eligibility.bn']" class="mt-1 text-xs text-red-600">{{ errors['eligibility.bn'][0] }}</p>
          </div>

          <!-- Processing time -->
          <div>
            <label class="mb-1 block text-sm font-medium">Processing Time (EN)</label>
            <input
              v-model="form.processing_time.en"
              placeholder="e.g. 5-7 working days"
              class="w-full rounded border px-3 py-2"
            />
            <p v-if="errors['processing_time.en']" class="mt-1 text-xs text-red-600">{{ errors['processing_time.en'][0] }}</p>
          </div>
          <div>
            <label class="mb-1 block text-sm font-medium">Processing Time (BN)</label>
            <input v-model="form.processing_time.bn" class="w-full rounded border px-3 py-2" />
            <p v-if="errors['processing_time.bn']" class="mt-1 text-xs text-red-600">{{ errors['processing_time.bn'][0] }}</p>
          </div>

          <!-- Currency + fee -->
          <div>
            <label class="mb-1 block text-sm font-medium">Currency (EN)</label>
            <input v-model="form.currency.en" placeholder="e.g. BDT" class="w-full rounded border px-3 py-2" />
            <p v-if="errors['currency.en']" class="mt-1 text-xs text-red-600">{{ errors['currency.en'][0] }}</p>
          </div>
          <div>
            <label class="mb-1 block text-sm font-medium">Currency (BN)</label>
            <input v-model="form.currency.bn" placeholder="e.g. টাকা" class="w-full rounded border px-3 py-2" />
            <p v-if="errors['currency.bn']" class="mt-1 text-xs text-red-600">{{ errors['currency.bn'][0] }}</p>
          </div>

          <div>
            <label class="mb-1 block text-sm font-medium">Base Fee</label>
            <input
              v-model.number="form.base_fee"
              type="number"
              min="0"
              step="0.01"
              class="w-full rounded border px-3 py-2"
            />
            <p v-if="errors.base_fee" class="mt-1 text-xs text-red-600">{{ errors.base_fee[0] }}</p>
          </div>
          <div>
            <label class="mb-1 block text-sm font-medium">Application URL</label>
            <input
              v-model="form.application_url"
              type="url"
              placeholder="https://..."
              class="w-full rounded border px-3 py-2"
            />
            <p v-if="errors.application_url" class="mt-1 text-xs text-red-600">{{ errors.application_url[0] }}</p>
          </div>

          <div class="flex items-center md:col-span-2">
            <label class="flex items-center gap-2">
              <input v-model="form.is_active" type="checkbox" class="h-4 w-4" />
              <span class="text-sm font-medium">Active</span>
            </label>
          </div>
        </div>

        <div class="mt-6 flex justify-end gap-3">
          <button class="rounded border px-4 py-2" :disabled="saving" @click="closeForm">Cancel</button>
          <button
            class="rounded bg-blue-600 px-4 py-2 text-white hover:bg-blue-700 disabled:opacity-60"
            :disabled="saving"
            @click="save"
          >
            {{ saving ? 'Saving...' : 'Save' }}
          </button>
        </div>
      </div>
    </div>

    <!-- Delete confirm -->
    <div v-if="deleteTarget" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
      <div class="w-full max-w-sm rounded bg-white p-6">
        <h3 class="mb-2 text-lg font-semibold">Delete service?</h3>
        <p class="mb-1 text-sm text-gray-600">"{{ deleteTarget.title?.en }}" will be permanently deleted.</p>
        <p v-if="deleteError" class="mb-2 text-sm text-red-600">{{ deleteError }}</p>
        <div class="mt-4 flex justify-end gap-3">
          <button class="rounded border px-4 py-2" @click="deleteTarget = null">Cancel</button>
          <button
            class="rounded bg-red-600 px-4 py-2 text-white disabled:opacity-60"
            :disabled="deleting"
            @click="doDelete"
          >
            {{ deleting ? 'Deleting...' : 'Delete' }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive, watch, onMounted } from 'vue'
import api from '@/services/api'

interface Localized {
  en?: string | null
  bn?: string | null
}

interface Option {
  id: number
  name: Localized
}

interface VisaService {
  id: number
  visa_country_id: number
  visa_type_id: number
  title: Localized
  description: Localized
  processing_time: Localized
  currency: Localized
  base_fee: number | null
  eligibility: Localized
  application_url: string | null
  is_active: boolean
  country?: Option
  type?: Option
}

interface Meta {
  current_page: number
  last_page: number
  per_page: number
  total: number
}

// ----- list state -----
const services = ref<VisaService[]>([])
const meta = reactive<Meta>({ current_page: 1, last_page: 1, per_page: 15, total: 0 })
const loading = ref(false)

const search = ref('')
const countryFilter = ref<number | ''>('')
const typeFilter = ref<number | ''>('')
const activeFilter = ref('')
const page = ref(1)

// dropdown options
const countries = ref<Option[]>([])
const visaTypes = ref<Option[]>([])

const label = (v?: Localized | null) => v?.en || v?.bn || ''

// ----- form state -----
const showForm = ref(false)
const editingId = ref<number | null>(null)
const saving = ref(false)
const errors = ref<Record<string, string[]>>({})

const emptyForm = () => ({
  visa_country_id: '' as number | '',
  visa_type_id: '' as number | '',
  title: { en: '', bn: '' },
  description: { en: '', bn: '' },
  processing_time: { en: '', bn: '' },
  currency: { en: '', bn: '' },
  base_fee: '' as number | '',
  eligibility: { en: '', bn: '' },
  application_url: '',
  is_active: true,
})
const form = reactive(emptyForm())

// ----- delete state -----
const deleteTarget = ref<VisaService | null>(null)
const deleting = ref(false)
const deleteError = ref('')

// ----- data loading -----
async function fetchOptions() {
  try {
    const [c, t] = await Promise.all([
      api.get('/visa-countries', { params: { all: 1, paginate: 0, all_locales: 1 } }),
      api.get('/visa-types', { params: { paginate: 0, all_locales: 1 } }),
    ])
    countries.value = c.data.data
    visaTypes.value = t.data.data
  } catch {
    alert('Could not load countries or visa types.')
  }
}

async function fetchServices() {
  loading.value = true
  try {
    const params: Record<string, any> = {
      page: page.value,
      per_page: 15,
      all_locales: 1, // return { en, bn } for table + edit form
    }
    if (search.value) params.search = search.value
    if (countryFilter.value !== '') params.visa_country_id = countryFilter.value
    if (typeFilter.value !== '') params.visa_type_id = typeFilter.value
    if (activeFilter.value !== '') params.is_active = activeFilter.value

    const { data } = await api.get('/admin/visa-services', { params })
    services.value = data.data
    Object.assign(meta, data.meta)
  } finally {
    loading.value = false
  }
}

let searchTimer: ReturnType<typeof setTimeout>
watch(search, () => {
  clearTimeout(searchTimer)
  searchTimer = setTimeout(() => {
    page.value = 1
    fetchServices()
  }, 400)
})
watch([countryFilter, typeFilter, activeFilter], () => {
  page.value = 1
  fetchServices()
})
watch(page, fetchServices)

// ----- form actions -----
function resetForm() {
  Object.assign(form, emptyForm())
  errors.value = {}
}

function openCreate() {
  resetForm()
  editingId.value = null
  showForm.value = true
}

function openEdit(s: VisaService) {
  resetForm()
  editingId.value = s.id

  form.visa_country_id = s.visa_country_id
  form.visa_type_id = s.visa_type_id
  form.title = { en: s.title?.en ?? '', bn: s.title?.bn ?? '' }
  form.description = { en: s.description?.en ?? '', bn: s.description?.bn ?? '' }
  form.processing_time = { en: s.processing_time?.en ?? '', bn: s.processing_time?.bn ?? '' }
  form.currency = { en: s.currency?.en ?? '', bn: s.currency?.bn ?? '' }
  form.base_fee = s.base_fee ?? ''
  form.eligibility = { en: s.eligibility?.en ?? '', bn: s.eligibility?.bn ?? '' }
  form.application_url = s.application_url ?? ''
  form.is_active = s.is_active

  showForm.value = true
}

function closeForm() {
  showForm.value = false
}

async function save() {
  saving.value = true
  errors.value = {}

  const payload = {
    visa_country_id: form.visa_country_id,
    visa_type_id: form.visa_type_id,
    title: form.title,
    description: form.description,
    processing_time: form.processing_time,
    currency: form.currency,
    base_fee: form.base_fee === '' ? null : form.base_fee,
    eligibility: form.eligibility,
    application_url: form.application_url || null,
    is_active: form.is_active,
  }

  try {
    if (editingId.value) {
      await api.put(`/admin/visa-services/${editingId.value}`, payload)
    } else {
      await api.post('/admin/visa-services', payload)
    }
    showForm.value = false
    await fetchServices()
  } catch (e: any) {
    if (e.response?.status === 422) {
      errors.value = e.response.data.errors ?? {}
    } else {
      alert(e.response?.data?.message ?? 'Something went wrong.')
    }
  } finally {
    saving.value = false
  }
}

// ----- delete actions -----
function confirmDelete(s: VisaService) {
  deleteError.value = ''
  deleteTarget.value = s
}

async function doDelete() {
  if (!deleteTarget.value) return
  deleting.value = true
  deleteError.value = ''
  try {
    await api.delete(`/admin/visa-services/${deleteTarget.value.id}`)
    deleteTarget.value = null
    // step back a page if we just deleted the last row on this page
    if (services.value.length === 1 && page.value > 1) page.value--
    else await fetchServices()
  } catch (e: any) {
    deleteError.value = e.response?.data?.message ?? 'Delete failed.'
  } finally {
    deleting.value = false
  }
}

onMounted(() => {
  fetchOptions()
  fetchServices()
})
</script>