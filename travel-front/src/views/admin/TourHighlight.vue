<template>
  <div class="p-6 space-y-6">
    <div class="flex flex-wrap items-center justify-between gap-3">
      <h1 class="text-2xl font-semibold">Tour Package Highlights</h1>

      <div class="flex items-center gap-3">
        <select v-model="filterPackageId" class="rounded border px-3 py-2" @change="fetchHighlights">
          <option value="">All packages</option>
          <option v-for="p in packages" :key="p.id" :value="p.id">{{ packageLabel(p) }}</option>
        </select>

        <button class="rounded bg-blue-600 px-4 py-2 text-white hover:bg-blue-700" @click="openCreate">
          + Add Highlight
        </button>
      </div>
    </div>

    <!-- Table -->
    <div class="overflow-x-auto rounded border bg-white">
      <table class="min-w-full text-sm">
        <thead class="bg-gray-50 text-left">
          <tr>
            <th class="px-4 py-3">#</th>
            <th class="px-4 py-3">Package</th>
            <th class="px-4 py-3">Highlight (EN)</th>
            <th class="px-4 py-3">Highlight (BN)</th>
            <th class="px-4 py-3">Icon</th>
            <th class="px-4 py-3">Order</th>
            <th class="px-4 py-3">Status</th>
            <th class="px-4 py-3 text-right">Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-if="loading">
            <td colspan="8" class="px-4 py-6 text-center text-gray-500">Loading...</td>
          </tr>
          <tr v-else-if="!highlights.length">
            <td colspan="8" class="px-4 py-6 text-center text-gray-500">No highlights found.</td>
          </tr>
          <tr v-for="(item, i) in highlights" :key="item.id" class="border-t">
            <td class="px-4 py-3">{{ i + 1 }}</td>
            <td class="px-4 py-3">{{ item.tour_package ? packageLabel(item.tour_package) : '-' }}</td>
            <td class="px-4 py-3">{{ item.highlight?.en }}</td>
            <td class="px-4 py-3">{{ item.highlight?.bn || '-' }}</td>
            <td class="px-4 py-3">{{ item.icon || '-' }}</td>
            <td class="px-4 py-3">{{ item.order }}</td>
            <td class="px-4 py-3">
              <span
                class="rounded px-2 py-1 text-xs"
                :class="item.is_active ? 'bg-green-100 text-green-700' : 'bg-gray-200 text-gray-600'"
              >
                {{ item.is_active ? 'Active' : 'Inactive' }}
              </span>
            </td>
            <td class="px-4 py-3 text-right space-x-3">
              <button class="text-blue-600 hover:underline" @click="openEdit(item)">Edit</button>
              <button class="text-red-600 hover:underline" @click="remove(item)">Delete</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Modal -->
    <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4">
      <div class="w-full max-w-lg rounded bg-white p-6 shadow-lg">
        <h2 class="mb-4 text-lg font-semibold">
          {{ editingId ? 'Edit Highlight' : 'Add Highlight' }}
        </h2>

        <div class="space-y-4">
          <div>
            <label class="mb-1 block text-sm font-medium">Tour Package *</label>
            <select v-model="form.tour_package_id" class="w-full rounded border px-3 py-2">
              <option :value="null" disabled>Select package</option>
              <option v-for="p in packages" :key="p.id" :value="p.id">{{ packageLabel(p) }}</option>
            </select>
            <p v-if="errors.tour_package_id" class="mt-1 text-xs text-red-600">{{ errors.tour_package_id[0] }}</p>
          </div>

          <div>
            <label class="mb-1 block text-sm font-medium">Highlight (English) *</label>
            <textarea v-model="form.highlight.en" rows="2" class="w-full rounded border px-3 py-2" />
            <p v-if="errors['highlight.en']" class="mt-1 text-xs text-red-600">{{ errors['highlight.en'][0] }}</p>
          </div>

          <div>
            <label class="mb-1 block text-sm font-medium">Highlight (Bangla)</label>
            <textarea v-model="form.highlight.bn" rows="2" class="w-full rounded border px-3 py-2" />
            <p v-if="errors['highlight.bn']" class="mt-1 text-xs text-red-600">{{ errors['highlight.bn'][0] }}</p>
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="mb-1 block text-sm font-medium">Icon</label>
              <input v-model="form.icon" type="text" class="w-full rounded border px-3 py-2" />
              <p v-if="errors.icon" class="mt-1 text-xs text-red-600">{{ errors.icon[0] }}</p>
            </div>
            <div>
              <label class="mb-1 block text-sm font-medium">Order</label>
              <input v-model.number="form.order" type="number" min="0" class="w-full rounded border px-3 py-2" />
              <p v-if="errors.order" class="mt-1 text-xs text-red-600">{{ errors.order[0] }}</p>
            </div>
          </div>

          <label class="flex items-center gap-2 text-sm">
            <input v-model="form.is_active" type="checkbox" />
            Active
          </label>
        </div>

        <div class="mt-6 flex justify-end gap-3">
          <button class="rounded border px-4 py-2" :disabled="saving" @click="closeModal">Cancel</button>
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
</template>

<script setup lang="ts">
import { ref, reactive, onMounted } from 'vue'
import api from '@/services/api'

interface Highlight {
  id: number
  tour_package_id: number
  tour_package?: { id: number; package_name: any }
  highlight: { en: string; bn?: string | null }
  icon: string | null
  order: number
  is_active: boolean
}

interface TourPackageLite {
  id: number
  package_name: any
}

const highlights = ref<Highlight[]>([])
const packages = ref<TourPackageLite[]>([])
const filterPackageId = ref<number | ''>('')
const loading = ref(false)
const saving = ref(false)
const showModal = ref(false)
const editingId = ref<number | null>(null)
const errors = ref<Record<string, string[]>>({})

const emptyForm = () => ({
  tour_package_id: null as number | null,
  highlight: { en: '', bn: '' },
  icon: '',
  order: 0,
  is_active: true,
})

const form = reactive(emptyForm())

// package_name may be a string or a {en, bn} object
const packageLabel = (p: { package_name: any }) =>
  typeof p.package_name === 'object' && p.package_name !== null
    ? p.package_name.en
    : p.package_name

const fetchPackages = async () => {
  // adjust to your real tour package list endpoint
  const { data } = await api.get('/tour-package')
  packages.value = data.data ?? data
}

const fetchHighlights = async () => {
  loading.value = true
  try {
    const { data } = await api.get('/tour-highlight', {
      params: {
        all_locales: 1,
        tour_package_id: filterPackageId.value || undefined,
      },
    })
    highlights.value = data.data
  } finally {
    loading.value = false
  }
}

const openCreate = () => {
  editingId.value = null
  errors.value = {}
  Object.assign(form, emptyForm())
  if (filterPackageId.value) form.tour_package_id = Number(filterPackageId.value)
  showModal.value = true
}

const openEdit = (item: Highlight) => {
  editingId.value = item.id
  errors.value = {}
  Object.assign(form, {
    tour_package_id: item.tour_package_id,
    highlight: { en: item.highlight?.en ?? '', bn: item.highlight?.bn ?? '' },
    icon: item.icon ?? '',
    order: item.order,
    is_active: item.is_active,
  })
  showModal.value = true
}

const closeModal = () => {
  showModal.value = false
}

const save = async () => {
  saving.value = true
  errors.value = {}

  const payload = {
    tour_package_id: form.tour_package_id,
    highlight: {
      en: form.highlight.en,
      bn: form.highlight.bn || null,
    },
    icon: form.icon || null,
    order: form.order ?? 0,
    is_active: form.is_active,
  }

  try {
    if (editingId.value) {
      await api.put(`/admin/tour-highlight/${editingId.value}`, payload)
    } else {
      await api.post('/admin/tour-highlight', payload)
    }
    closeModal()
    await fetchHighlights()
  } catch (err: any) {
    if (err.response?.status === 422) {
      errors.value = err.response.data.errors ?? {}
    } else {
      alert(err.response?.data?.message || 'Something went wrong.')
    }
  } finally {
    saving.value = false
  }
}

const remove = async (item: Highlight) => {
  if (!confirm('Delete this highlight?')) return
  try {
    await api.delete(`/admin/tour-highlight/${item.id}`)
    highlights.value = highlights.value.filter((h) => h.id !== item.id)
  } catch (err: any) {
    alert(err.response?.data?.message || 'Delete failed.')
  }
}

onMounted(async () => {
  await Promise.all([fetchPackages(), fetchHighlights()])
})
</script>