<template>
  <div class="p-6">
    <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
      <h1 class="text-2xl font-semibold">Visa Assistance Requests</h1>
    </div>

    <!-- Filters -->
    <div class="mb-4 flex flex-wrap gap-3">
      <input
        v-model="search"
        type="text"
        placeholder="Search name, phone, email..."
        class="w-64 rounded border px-3 py-2"
      />

      <select v-model="statusFilter" class="rounded border px-3 py-2">
        <option value="">All status</option>
        <option v-for="s in statuses" :key="s" :value="s">{{ statusLabel(s) }}</option>
      </select>

      <select v-model="serviceFilter" class="rounded border px-3 py-2">
        <option value="">All services</option>
        <option v-for="s in services" :key="s.id" :value="s.id">{{ s.title?.en }}</option>
      </select>
    </div>

    <!-- Table -->
    <div class="overflow-x-auto rounded border bg-white">
      <table class="min-w-full text-left text-sm">
        <thead class="bg-gray-50 text-gray-600">
          <tr>
            <th class="px-4 py-3">#</th>
            <th class="px-4 py-3">Name</th>
            <th class="px-4 py-3">Phone</th>
            <th class="px-4 py-3">Email</th>
            <th class="px-4 py-3">Service</th>
            <th class="px-4 py-3">Status</th>
            <th class="px-4 py-3">Date</th>
            <th class="px-4 py-3 text-right">Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-if="loading">
            <td colspan="8" class="px-4 py-6 text-center text-gray-500">Loading...</td>
          </tr>
          <tr v-else-if="!requests.length">
            <td colspan="8" class="px-4 py-6 text-center text-gray-500">No requests found.</td>
          </tr>
          <tr v-for="(r, i) in requests" :key="r.id" class="border-t">
            <td class="px-4 py-3">{{ (meta.current_page - 1) * meta.per_page + i + 1 }}</td>
            <td class="px-4 py-3 font-medium">{{ r.name }}</td>
            <td class="whitespace-nowrap px-4 py-3">{{ r.phone }}</td>
            <td class="px-4 py-3">{{ r.email || '—' }}</td>
            <td class="px-4 py-3">{{ serviceName(r) }}</td>
            <td class="px-4 py-3">
              <select
                :value="r.status"
                :disabled="updatingId === r.id"
                class="rounded border px-2 py-1 text-xs font-medium disabled:opacity-60"
                :class="statusClass(r.status)"
                @change="changeStatus(r, ($event.target as HTMLSelectElement).value)"
              >
                <option v-for="s in statuses" :key="s" :value="s">{{ statusLabel(s) }}</option>
              </select>
            </td>
            <td class="whitespace-nowrap px-4 py-3 text-gray-500">{{ formatDate(r.created_at) }}</td>
            <td class="whitespace-nowrap px-4 py-3 text-right">
              <button class="mr-3 text-blue-600 hover:underline" @click="openEdit(r)">View / Edit</button>
              <button class="text-red-600 hover:underline" @click="confirmDelete(r)">Delete</button>
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

    <!-- View / Edit modal -->
    <div v-if="showForm" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
      <div class="max-h-[90vh] w-full max-w-2xl overflow-y-auto rounded bg-white p-6">
        <h2 class="mb-1 text-xl font-semibold">Request #{{ editingId }}</h2>
        <p class="mb-4 text-sm text-gray-500">
          Service: {{ editingServiceName }} · Received {{ formatDate(editingCreatedAt) }}
        </p>

        <div class="grid gap-4 md:grid-cols-2">
          <div>
            <label class="mb-1 block text-sm font-medium">Name *</label>
            <input v-model="form.name" class="w-full rounded border px-3 py-2" />
            <p v-if="errors.name" class="mt-1 text-xs text-red-600">{{ errors.name[0] }}</p>
          </div>

          <div>
            <label class="mb-1 block text-sm font-medium">Phone *</label>
            <input v-model="form.phone" class="w-full rounded border px-3 py-2" />
            <p v-if="errors.phone" class="mt-1 text-xs text-red-600">{{ errors.phone[0] }}</p>
          </div>

          <div>
            <label class="mb-1 block text-sm font-medium">Email</label>
            <input v-model="form.email" type="email" class="w-full rounded border px-3 py-2" />
            <p v-if="errors.email" class="mt-1 text-xs text-red-600">{{ errors.email[0] }}</p>
          </div>

          <div>
            <label class="mb-1 block text-sm font-medium">Status</label>
            <select v-model="form.status" class="w-full rounded border px-3 py-2">
              <option v-for="s in statuses" :key="s" :value="s">{{ statusLabel(s) }}</option>
            </select>
            <p v-if="errors.status" class="mt-1 text-xs text-red-600">{{ errors.status[0] }}</p>
          </div>

          <div class="md:col-span-2">
            <label class="mb-1 block text-sm font-medium">Message</label>
            <textarea v-model="form.message" rows="4" class="w-full rounded border px-3 py-2"></textarea>
            <p v-if="errors.message" class="mt-1 text-xs text-red-600">{{ errors.message[0] }}</p>
          </div>
        </div>

        <div class="mt-6 flex items-center justify-between">
          <a
            :href="`tel:${form.phone}`"
            class="text-sm text-blue-600 hover:underline"
          >
            <i class="bi bi-telephone mr-1"></i>Call
          </a>
          <div class="flex gap-3">
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
    </div>

    <!-- Delete confirm -->
    <div v-if="deleteTarget" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
      <div class="w-full max-w-sm rounded bg-white p-6">
        <h3 class="mb-2 text-lg font-semibold">Delete request?</h3>
        <p class="mb-1 text-sm text-gray-600">
          The request from "{{ deleteTarget.name }}" will be permanently deleted.
        </p>
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
import { ref, reactive, computed, watch, onMounted } from 'vue'
import api from '@/services/api'

type Status = 'pending' | 'contacted' | 'completed' | 'cancelled'

interface Localized {
  en?: string | null
  bn?: string | null
}

interface AssistanceRequest {
  id: number
  visa_service_id: number | null
  service_name: string | null
  name: string
  phone: string
  email: string | null
  message: string | null
  status: Status
  service?: { id: number; title: Localized } | null
  created_at: string
}

interface ServiceOption {
  id: number
  title: Localized
}

interface Meta {
  current_page: number
  last_page: number
  per_page: number
  total: number
}

const statuses: Status[] = ['pending', 'contacted', 'completed', 'cancelled']

const statusLabel = (s: string) => s.charAt(0).toUpperCase() + s.slice(1)

function statusClass(s: Status) {
  return {
    pending: 'bg-yellow-100 text-yellow-800',
    contacted: 'bg-blue-100 text-blue-800',
    completed: 'bg-green-100 text-green-800',
    cancelled: 'bg-gray-200 text-gray-600',
  }[s]
}

function formatDate(iso?: string | null) {
  return iso ? new Date(iso).toLocaleString() : ''
}

// Live service title, or the saved name if the service was deleted
function serviceName(r: AssistanceRequest) {
  return r.service?.title?.en || r.service_name || '—'
}

// ----- list state -----
const requests = ref<AssistanceRequest[]>([])
const meta = reactive<Meta>({ current_page: 1, last_page: 1, per_page: 15, total: 0 })
const loading = ref(false)

const search = ref('')
const statusFilter = ref('')
const serviceFilter = ref<number | ''>('')
const page = ref(1)

const services = ref<ServiceOption[]>([])

// ----- form state -----
const showForm = ref(false)
const editingId = ref<number | null>(null)
const editingCreatedAt = ref('')
const editingRow = ref<AssistanceRequest | null>(null)
const saving = ref(false)
const errors = ref<Record<string, string[]>>({})

const emptyForm = () => ({
  name: '',
  phone: '',
  email: '',
  message: '',
  status: 'pending' as Status,
})
const form = reactive(emptyForm())

const editingServiceName = computed(() => (editingRow.value ? serviceName(editingRow.value) : '—'))

// ----- status quick-change -----
const updatingId = ref<number | null>(null)

// ----- delete state -----
const deleteTarget = ref<AssistanceRequest | null>(null)
const deleting = ref(false)
const deleteError = ref('')

// ----- data loading -----
async function fetchServices() {
  try {
    const { data } = await api.get('/admin/visa-services', {
      params: { paginate: 0, all_locales: 1 },
    })
    services.value = data.data
  } catch {
    // the service filter is optional, so ignore errors
  }
}

async function fetchRequests() {
  loading.value = true
  try {
    const params: Record<string, any> = { page: page.value, per_page: 15 }
    if (search.value) params.search = search.value
    if (statusFilter.value) params.status = statusFilter.value
    if (serviceFilter.value !== '') params.visa_service_id = serviceFilter.value

    const { data } = await api.get('/admin/visa-assistance-requests', { params })
    requests.value = data.data
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
    fetchRequests()
  }, 400)
})
watch([statusFilter, serviceFilter], () => {
  page.value = 1
  fetchRequests()
})
watch(page, fetchRequests)

// ----- quick status change from the table -----
async function changeStatus(r: AssistanceRequest, status: string) {
  const previous = r.status
  updatingId.value = r.id
  r.status = status as Status
  try {
    await api.put(`/admin/visa-assistance-requests/${r.id}`, { status })
  } catch (e: any) {
    r.status = previous // roll back
    alert(e.response?.data?.message ?? 'Could not update status.')
  } finally {
    updatingId.value = null
  }
}

// ----- view / edit -----
function openEdit(r: AssistanceRequest) {
  errors.value = {}
  editingId.value = r.id
  editingRow.value = r
  editingCreatedAt.value = r.created_at
  form.name = r.name
  form.phone = r.phone
  form.email = r.email ?? ''
  form.message = r.message ?? ''
  form.status = r.status
  showForm.value = true
}

function closeForm() {
  showForm.value = false
}

async function save() {
  if (!editingId.value) return
  saving.value = true
  errors.value = {}

  try {
    await api.put(`/admin/visa-assistance-requests/${editingId.value}`, {
      name: form.name,
      phone: form.phone,
      email: form.email || null,
      message: form.message || null,
      status: form.status,
    })
    showForm.value = false
    await fetchRequests()
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

// ----- delete -----
function confirmDelete(r: AssistanceRequest) {
  deleteError.value = ''
  deleteTarget.value = r
}

async function doDelete() {
  if (!deleteTarget.value) return
  deleting.value = true
  deleteError.value = ''
  try {
    await api.delete(`/admin/visa-assistance-requests/${deleteTarget.value.id}`)
    deleteTarget.value = null
    // step back a page if we just deleted the last row on this page
    if (requests.value.length === 1 && page.value > 1) page.value--
    else await fetchRequests()
  } catch (e: any) {
    deleteError.value = e.response?.data?.message ?? 'Delete failed.'
  } finally {
    deleting.value = false
  }
}

onMounted(() => {
  fetchServices()
  fetchRequests()
})
</script>