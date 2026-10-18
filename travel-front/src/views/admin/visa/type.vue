<template>
  <div class="p-6">
    <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
      <h1 class="text-2xl font-semibold">Visa Type</h1>
      <button class="rounded bg-blue-600 px-4 py-2 text-white hover:bg-blue-700" @click="openCreate">
        + Add Type
      </button>
    </div>

    <!-- Filters -->
    <div class="mb-4 flex flex-wrap gap-3">
      <input
        v-model="search"
        type="text"
        placeholder="Search by type..."
        class="w-64 rounded border px-3 py-2"
      />
      <select v-model="activeFilter" class="rounded border px-3 py-2">
        <option value="">All</option>
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
            <th class="px-4 py-3">Name (EN)</th>
            <th class="px-4 py-3">Name (BN)</th>
            <th class="px-4 py-3">Slug</th>
            <th class="px-4 py-3">Order</th>
            <th class="px-4 py-3">Status</th>
            <th class="px-4 py-3 text-right">Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-if="loading">
            <td colspan="8" class="px-4 py-6 text-center text-gray-500">Loading...</td>
          </tr>
          <tr v-else-if="!countries.length">
            <td colspan="8" class="px-4 py-6 text-center text-gray-500">No countries found.</td>
          </tr>
          <tr v-for="(c, i) in countries" :key="c.id" class="border-t">
            <td class="px-4 py-3">{{ (meta.current_page - 1) * meta.per_page + i + 1 }}</td>
            <td class="px-4 py-3">{{ c.name?.en }}</td>
            <td class="px-4 py-3">{{ c.name?.bn || '—' }}</td>
            <td class="px-4 py-3 text-gray-500">{{ c.slug }}</td>
            <td class="px-4 py-3">{{ c.sort_order }}</td>
            <td class="px-4 py-3">
              <span
                class="rounded px-2 py-1 text-xs"
                :class="c.is_active ? 'bg-green-100 text-green-700' : 'bg-gray-200 text-gray-600'"
              >
                {{ c.is_active ? 'Active' : 'Inactive' }}
              </span>
            </td>
            <td class="px-4 py-3 text-right">
              <button class="mr-3 text-blue-600 hover:underline" @click="openEdit(c)">Edit</button>
              <button class="text-red-600 hover:underline" @click="confirmDelete(c)">Delete</button>
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
      <div class="max-h-[90vh] w-full max-w-2xl overflow-y-auto rounded bg-white p-6">
        <h2 class="mb-4 text-xl font-semibold">{{ editingId ? 'Edit Country' : 'Add Country' }}</h2>

        <div class="grid gap-4 md:grid-cols-2">
          <div>
            <label class="mb-1 block text-sm font-medium">Name (EN) *</label>
            <input v-model="form.name.en" class="w-full rounded border px-3 py-2" />
            <p v-if="errors['name.en']" class="mt-1 text-xs text-red-600">{{ errors['name.en'][0] }}</p>
          </div>
          <div>
            <label class="mb-1 block text-sm font-medium">Name (BN)</label>
            <input v-model="form.name.bn" class="w-full rounded border px-3 py-2" />
            <p v-if="errors['name.bn']" class="mt-1 text-xs text-red-600">{{ errors['name.bn'][0] }}</p>
          </div>
          <div>
            <label class="mb-1 block text-sm font-medium">Sort Order</label>
            <input v-model.number="form.sort_order" type="number" min="0" class="w-full rounded border px-3 py-2" />
            <p v-if="errors.sort_order" class="mt-1 text-xs text-red-600">{{ errors.sort_order[0] }}</p>
          </div>
          <div class="flex items-end">
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
        <h3 class="mb-2 text-lg font-semibold">Delete country?</h3>
        <p class="mb-1 text-sm text-gray-600">"{{ deleteTarget.name?.en }}" will be permanently deleted.</p>
        <p v-if="deleteError" class="mb-2 text-sm text-red-600">{{ deleteError }}</p>
        <div class="mt-4 flex justify-end gap-3">
          <button class="rounded border px-4 py-2" @click="deleteTarget = null">Cancel</button>
          <button class="rounded bg-red-600 px-4 py-2 text-white disabled:opacity-60" :disabled="deleting" @click="doDelete">
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
  en: string
  bn?: string | null
}

interface VisaCountry {
  id: number
  name: Localized
  slug: string
  is_active: boolean
  sort_order: number
}

interface Meta {
  current_page: number
  last_page: number
  per_page: number
  total: number
}

const countries = ref<VisaCountry[]>([])
const meta = reactive<Meta>({ current_page: 1, last_page: 1, per_page: 15, total: 0 })
const loading = ref(false)

const search = ref('')
const activeFilter = ref('')
const page = ref(1)

// form state
const showForm = ref(false)
const editingId = ref<number | null>(null)
const saving = ref(false)
const errors = ref<Record<string, string[]>>({})
const imageFile = ref<File | null>(null)
const imagePreview = ref<string | null>(null)

const emptyForm = () => ({
  name: { en: '', bn: '' },
  sort_order: 0,
  is_active: true,
})
const form = reactive(emptyForm())

// delete state
const deleteTarget = ref<VisaCountry | null>(null)
const deleting = ref(false)
const deleteError = ref('')

async function fetchCountries() {
  loading.value = true
  try {
    const params: Record<string, any> = {
      page: page.value,
      per_page: 15,
      all_locales: 1, // get { en, bn } for the table and edit form
    }
    if (search.value) params.search = search.value
    if (activeFilter.value !== '') params.is_active = activeFilter.value

    const { data } = await api.get('/admin/visa-types', { params })
    countries.value = data.data
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
    fetchCountries()
  }, 400)
})
watch(activeFilter, () => {
  page.value = 1
  fetchCountries()
})
watch(page, fetchCountries)

function resetForm() {
  Object.assign(form, emptyForm())
  errors.value = {}
  imageFile.value = null
  imagePreview.value = null
}

function openCreate() {
  resetForm()
  editingId.value = null
  showForm.value = true
}

function openEdit(c: VisaCountry) {
  resetForm()
  editingId.value = c.id
  form.name.en = c.name?.en ?? ''
  form.name.bn = c.name?.bn ?? ''
  form.sort_order = c.sort_order
  form.is_active = c.is_active
  showForm.value = true
}

function closeForm() {
  showForm.value = false
}

function onFileChange(e: Event) {
  const file = (e.target as HTMLInputElement).files?.[0]
  if (!file) return
  imageFile.value = file
  imagePreview.value = URL.createObjectURL(file)
}

async function save() {
  saving.value = true
  errors.value = {}

  const fd = new FormData()
  fd.append('name[en]', form.name.en)
  fd.append('name[bn]', form.name.bn)
  fd.append('sort_order', String(form.sort_order ?? 0))
  fd.append('is_active', form.is_active ? '1' : '0')
  if (imageFile.value) fd.append('image', imageFile.value)

  try {
    if (editingId.value) {
      fd.append('_method', 'PUT') // PHP doesn't parse multipart on a real PUT
      await api.post(`/admin/visa-types/${editingId.value}`, fd)
    } else {
      await api.post('/admin/visa-types', fd)
    }
    showForm.value = false
    await fetchCountries()
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

function confirmDelete(c: VisaCountry) {
  deleteError.value = ''
  deleteTarget.value = c
}

async function doDelete() {
  if (!deleteTarget.value) return
  deleting.value = true
  deleteError.value = ''
  try {
    await api.delete(`/admin/visa-types/${deleteTarget.value.id}`)
    deleteTarget.value = null
    // step back a page if we just deleted the last row on this page
    if (countries.value.length === 1 && page.value > 1) page.value--
    else await fetchCountries()
  } catch (e: any) {
    deleteError.value = e.response?.data?.message ?? 'Delete failed.'
  } finally {
    deleting.value = false
  }
}

onMounted(fetchCountries)
</script>