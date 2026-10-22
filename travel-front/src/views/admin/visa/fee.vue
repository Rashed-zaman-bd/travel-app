<template>
  <div class="p-6 space-y-6">
    <!-- Header -->
    <div class="flex flex-wrap items-center justify-between gap-3">
      <h1 class="text-2xl font-semibold">Visa Fee Options</h1>
      <button class="rounded bg-blue-600 px-4 py-2 text-white hover:bg-blue-700" @click="openCreate">
        + Add Fee Option
      </button>
    </div>

    <!-- Flash message -->
    <div v-if="flash" class="rounded border border-green-300 bg-green-50 px-4 py-2 text-green-800">
      {{ flash }}
    </div>

    <!-- Filters -->
    <div class="flex flex-wrap gap-3">
      <select v-model="filters.visa_country_id" class="rounded border px-3 py-2" @change="applyFilters">
        <option value="">All countries</option>
        <option v-for="c in countries" :key="c.id" :value="c.id">{{ label(c) }}</option>
      </select>

      <select v-model="filters.visa_type_id" class="rounded border px-3 py-2" @change="applyFilters">
        <option value="">All visa types</option>
        <option v-for="t in visaTypes" :key="t.id" :value="t.id">{{ label(t) }}</option>
      </select>

      <select v-model="filters.is_active" class="rounded border px-3 py-2" @change="applyFilters">
        <option value="">Any status</option>
        <option value="1">Active</option>
        <option value="0">Inactive</option>
      </select>
    </div>

    <!-- Table -->
    <div class="overflow-x-auto rounded border">
      <table class="min-w-full text-left text-sm">
        <thead class="bg-gray-50 text-gray-600">
          <tr>
            <th class="px-4 py-3">#</th>
            <th class="px-4 py-3">Name (EN / BN)</th>
            <th class="px-4 py-3">Country</th>
            <th class="px-4 py-3">Visa Type</th>
            <th class="px-4 py-3">Fee</th>
            <th class="px-4 py-3">Status</th>
            <th class="px-4 py-3 text-right">Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-if="loading">
            <td colspan="7" class="px-4 py-8 text-center text-gray-500">Loading...</td>
          </tr>
          <tr v-else-if="!items.length">
            <td colspan="7" class="px-4 py-8 text-center text-gray-500">No fee options found.</td>
          </tr>
          <tr v-for="item in items" v-else :key="item.id" class="border-t">
            <td class="px-4 py-3">{{ item.id }}</td>
            <td class="px-4 py-3">
              <div class="font-medium">{{ item.name?.en }}</div>
              <div class="text-gray-500">{{ item.name?.bn }}</div>
            </td>
            <td class="px-4 py-3">{{ label(item.visa_country) }}</td>
            <td class="px-4 py-3">{{ label(item.visa_type) }}</td>
            <td class="px-4 py-3">{{ item.fee ?? '-' }}</td>
            <td class="px-4 py-3">
              <button
                class="rounded-full px-3 py-1 text-xs font-medium"
                :class="item.is_active ? 'bg-green-100 text-green-700' : 'bg-gray-200 text-gray-600'"
                @click="toggleActive(item)"
              >
                {{ item.is_active ? 'Active' : 'Inactive' }}
              </button>
            </td>
            <td class="px-4 py-3 text-right space-x-2">
              <button class="text-blue-600 hover:underline" @click="openEdit(item)">Edit</button>
              <button class="text-red-600 hover:underline" @click="remove(item)">Delete</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Pagination -->
    <div v-if="meta.last_page > 1" class="flex items-center justify-between">
      <span class="text-sm text-gray-600">Page {{ meta.current_page }} of {{ meta.last_page }} ({{ meta.total }} total)</span>
      <div class="space-x-2">
        <button class="rounded border px-3 py-1 disabled:opacity-40" :disabled="meta.current_page <= 1" @click="fetchItems(meta.current_page - 1)">Prev</button>
        <button class="rounded border px-3 py-1 disabled:opacity-40" :disabled="meta.current_page >= meta.last_page" @click="fetchItems(meta.current_page + 1)">Next</button>
      </div>
    </div>

    <!-- Modal -->
    <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
      <div class="max-h-[90vh] w-full max-w-2xl overflow-y-auto rounded bg-white p-6 shadow-lg">
        <h2 class="mb-4 text-xl font-semibold">{{ editingId ? 'Edit' : 'Add' }} Fee Option</h2>

        <form class="space-y-4" @submit.prevent="save">
          <div class="grid gap-4 sm:grid-cols-2">
            <div>
              <label class="mb-1 block text-sm font-medium">Country *</label>
              <select v-model="form.visa_country_id" class="w-full rounded border px-3 py-2">
                <option value="">Select country</option>
                <option v-for="c in countries" :key="c.id" :value="c.id">{{ label(c) }}</option>
              </select>
              <p v-if="err('visa_country_id')" class="mt-1 text-xs text-red-600">{{ err('visa_country_id') }}</p>
            </div>

            <div>
              <label class="mb-1 block text-sm font-medium">Visa Type *</label>
              <select v-model="form.visa_type_id" class="w-full rounded border px-3 py-2">
                <option value="">Select visa type</option>
                <option v-for="t in visaTypes" :key="t.id" :value="t.id">{{ label(t) }}</option>
              </select>
              <p v-if="err('visa_type_id')" class="mt-1 text-xs text-red-600">{{ err('visa_type_id') }}</p>
            </div>

            <div>
              <label class="mb-1 block text-sm font-medium">Name (English) *</label>
              <input v-model="form.name_en" type="text" class="w-full rounded border px-3 py-2" />
              <p v-if="err('name.en')" class="mt-1 text-xs text-red-600">{{ err('name.en') }}</p>
            </div>

            <div>
              <label class="mb-1 block text-sm font-medium">Name (বাংলা)</label>
              <input v-model="form.name_bn" type="text" class="w-full rounded border px-3 py-2" />
              <p v-if="err('name.bn')" class="mt-1 text-xs text-red-600">{{ err('name.bn') }}</p>
            </div>

            <div>
              <label class="mb-1 block text-sm font-medium">Description (English)</label>
              <textarea v-model="form.description_en" rows="3" class="w-full rounded border px-3 py-2" />
            </div>

            <div>
              <label class="mb-1 block text-sm font-medium">Description (বাংলা)</label>
              <textarea v-model="form.description_bn" rows="3" class="w-full rounded border px-3 py-2" />
            </div>

            <div>
              <label class="mb-1 block text-sm font-medium">Fee</label>
              <input v-model="form.fee" type="number" step="0.01" min="0" class="w-full rounded border px-3 py-2" />
              <p v-if="err('fee')" class="mt-1 text-xs text-red-600">{{ err('fee') }}</p>
            </div>

            <label class="flex items-center gap-2 self-end pb-2">
              <input v-model="form.is_active" type="checkbox" class="h-4 w-4" />
              <span class="text-sm font-medium">Active</span>
            </label>
          </div>

          <div class="flex justify-end gap-2 pt-2">
            <button type="button" class="rounded border px-4 py-2" @click="closeModal">Cancel</button>
            <button type="submit" :disabled="saving" class="rounded bg-blue-600 px-4 py-2 text-white disabled:opacity-60">
              {{ saving ? 'Saving...' : 'Save' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { onMounted, reactive, ref } from 'vue'
import api from '@/services/api'

type Localized = { en?: string | null; bn?: string | null }

interface Lookup {
  id: number
  name?: Localized | string
  [key: string]: any
}

interface FeeOption {
  id: number
  visa_country_id: number
  visa_type_id: number
  name: Localized | null
  description: Localized | null
  fee: string | null
  is_active: boolean
  visa_country?: Lookup
  visa_type?: Lookup
}

const items = ref<FeeOption[]>([])
const countries = ref<Lookup[]>([])
const visaTypes = ref<Lookup[]>([])
const loading = ref(false)
const saving = ref(false)
const flash = ref('')
const showModal = ref(false)
const editingId = ref<number | null>(null)
const errors = ref<Record<string, string[]>>({})

const meta = reactive({ current_page: 1, last_page: 1, total: 0 })
const filters = reactive({ visa_country_id: '', visa_type_id: '', is_active: '' })

const emptyForm = () => ({
  visa_country_id: '' as number | '',
  visa_type_id: '' as number | '',
  name_en: '',
  name_bn: '',
  description_en: '',
  description_bn: '',
  fee: '' as string | number,
  is_active: true,
})
const form = reactive(emptyForm())

/** Works whether the related model's name is a string or a {en, bn} object */
function label(item?: Lookup | null): string {
  if (!item) return '-'
  const n = item.name
  if (typeof n === 'string') return n
  return n?.en || n?.bn || `#${item.id}`
}

const err = (key: string) => errors.value[key]?.[0]

function showFlash(msg: string) {
  flash.value = msg
  setTimeout(() => (flash.value = ''), 3000)
}

async function fetchItems(page = 1) {
  loading.value = true
  try {
    const params: Record<string, any> = { page, all_locales: 1 }
    if (filters.visa_country_id) params.visa_country_id = filters.visa_country_id
    if (filters.visa_type_id) params.visa_type_id = filters.visa_type_id
    if (filters.is_active !== '') params.is_active = filters.is_active

    const { data } = await api.get('/admin/visa-fee', { params })
    items.value = data.data
    meta.current_page = data.meta.current_page
    meta.last_page = data.meta.last_page
    meta.total = data.meta.total
  } finally {
    loading.value = false
  }
}

async function fetchLookups() {
  // Adjust these two endpoints to match your existing routes
  const [c, t] = await Promise.all([
    api.get('/admin/visa-countries', { params: { per_page: 200 } }),
    api.get('/admin/visa-types', { params: { per_page: 200 } }),
  ])
  countries.value = c.data.data ?? c.data
  visaTypes.value = t.data.data ?? t.data
}

function applyFilters() {
  fetchItems(1)
}

function openCreate() {
  editingId.value = null
  errors.value = {}
  Object.assign(form, emptyForm())
  showModal.value = true
}

function openEdit(item: FeeOption) {
  editingId.value = item.id
  errors.value = {}
  Object.assign(form, {
    visa_country_id: item.visa_country_id,
    visa_type_id: item.visa_type_id,
    name_en: item.name?.en ?? '',
    name_bn: item.name?.bn ?? '',
    description_en: item.description?.en ?? '',
    description_bn: item.description?.bn ?? '',
    fee: item.fee ?? '',
    is_active: item.is_active,
  })
  showModal.value = true
}

function closeModal() {
  showModal.value = false
}

function buildPayload() {
  return {
    visa_country_id: form.visa_country_id,
    visa_type_id: form.visa_type_id,
    name: { en: form.name_en, bn: form.name_bn || null },
    description: { en: form.description_en || null, bn: form.description_bn || null },
    fee: form.fee === '' ? null : Number(form.fee),
    is_active: form.is_active,
  }
}

async function save() {
  saving.value = true
  errors.value = {}
  try {
    if (editingId.value) {
      await api.put(`/admin/visa-fee/${editingId.value}`, buildPayload())
      showFlash('Fee option updated.')
    } else {
      await api.post('/admin/visa-fee', buildPayload())
      showFlash('Fee option created.')
    }
    closeModal()
    await fetchItems(editingId.value ? meta.current_page : 1)
  } catch (e: any) {
    if (e.response?.status === 422) {
      errors.value = e.response.data.errors ?? {}
    } else {
      showFlash('Something went wrong. Please try again.')
    }
  } finally {
    saving.value = false
  }
}

async function toggleActive(item: FeeOption) {
  try {
    await api.patch(`/admin/visa-fee/${item.id}`, {
      visa_country_id: item.visa_country_id,
      visa_type_id: item.visa_type_id,
      name: item.name,
      description: item.description,
      fee: item.fee === null ? null : Number(item.fee),
      is_active: !item.is_active,
    })
    item.is_active = !item.is_active
  } catch {
    showFlash('Could not update status.')
  }
}

async function remove(item: FeeOption) {
  if (!window.confirm(`Delete "${item.name?.en ?? 'this option'}"?`)) return
  try {
    await api.delete(`/admin/visa-fee/${item.id}`)
    showFlash('Fee option deleted.')
    // Go back a page if we just removed the last row on this one
    const page = items.value.length === 1 && meta.current_page > 1 ? meta.current_page - 1 : meta.current_page
    await fetchItems(page)
  } catch {
    showFlash('Could not delete.')
  }
}

onMounted(async () => {
  await Promise.all([fetchLookups(), fetchItems()])
})
</script>