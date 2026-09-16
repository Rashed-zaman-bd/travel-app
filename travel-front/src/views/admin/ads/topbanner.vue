<template>
  <div class="p-6">
    <div class="mb-4 flex items-center justify-between">
      <h1 class="text-xl font-semibold text-gray-800">Top Banners</h1>
      <button
        type="button"
        class="rounded-lg bg-emerald-600 px-4 py-2 text-sm font-medium text-white hover:bg-emerald-700"
        @click="openCreateModal"
      >
        + Add Banner
      </button>
    </div>

    <div v-if="loading" class="py-10 text-center text-gray-500">Loading...</div>

    <div v-else-if="banners.length === 0" class="py-10 text-center text-gray-500">
      No banners yet.
    </div>

    <table v-else class="w-full overflow-hidden rounded-lg bg-white shadow">
      <thead class="bg-gray-100 text-left text-sm text-gray-600">
        <tr>
          <th class="px-4 py-3">Image</th>
          <th class="px-4 py-3">Title</th>
          <th class="px-4 py-3">Order</th>
          <th class="px-4 py-3">Active</th>
          <th class="px-4 py-3 text-right">Actions</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-100 text-sm">
        <tr v-for="banner in banners" :key="banner.id">
          <td class="px-4 py-3">
            <img
              v-if="banner.image"
              :src="banner.image"
              :alt="banner.title || 'Banner'"
              class="h-10 w-24 rounded object-cover"
            />
            <span v-else class="text-gray-400">—</span>
          </td>
          <td class="px-4 py-3">{{ banner.title || '—' }}</td>
          <td class="px-4 py-3">{{ banner.order ?? '—' }}</td>
          <td class="px-4 py-3">
            <span
              class="rounded-full px-2 py-1 text-xs font-medium"
              :class="banner.active ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-500'"
            >
              {{ banner.active ? 'Active' : 'Inactive' }}
            </span>
          </td>
          <td class="px-4 py-3 text-right">
            <button
              type="button"
              class="mr-3 text-blue-600 hover:underline"
              @click="openEditModal(banner)"
            >
              Edit
            </button>
            <button
              type="button"
              class="text-red-600 hover:underline"
              @click="confirmDelete(banner)"
            >
              Delete
            </button>
          </td>
        </tr>
      </tbody>
    </table>

    <!-- Create/Edit Modal -->
    <div
      v-if="showModal"
      class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 px-4"
      @click.self="closeModal"
    >
      <div class="w-full max-w-md rounded-lg bg-white p-6 shadow-lg">
        <h2 class="mb-4 text-lg font-semibold text-gray-800">
          {{ isEditing ? 'Edit Banner' : 'Add Banner' }}
        </h2>

        <form @submit.prevent="submitForm">
          <div class="mb-3">
            <label class="mb-1 block text-sm font-medium text-gray-700">Title</label>
            <input
              v-model="form.title"
              type="text"
              class="w-full rounded border border-gray-300 px-3 py-2 text-sm focus:border-emerald-500 focus:outline-none"
            />
            <p v-if="errors.title" class="mt-1 text-xs text-red-600">{{ errors.title[0] }}</p>
          </div>

          <div class="mb-3">
            <label class="mb-1 block text-sm font-medium text-gray-700">
              Image {{ isEditing ? '(leave blank to keep current)' : '' }}
            </label>
            <input
              type="file"
              accept="image/jpeg,image/png,image/gif"
              class="w-full text-sm"
              @change="handleFileChange"
            />
            <img
              v-if="isEditing && editingBanner?.image"
              :src="editingBanner.image"
              class="mt-2 h-10 w-24 rounded object-cover"
            />
            <p v-if="errors.image" class="mt-1 text-xs text-red-600">{{ errors.image[0] }}</p>
          </div>

          <div class="mb-3">
            <label class="mb-1 block text-sm font-medium text-gray-700">Order</label>
            <input
              v-model.number="form.order"
              type="number"
              min="0"
              class="w-full rounded border border-gray-300 px-3 py-2 text-sm focus:border-emerald-500 focus:outline-none"
            />
            <p v-if="errors.order" class="mt-1 text-xs text-red-600">{{ errors.order[0] }}</p>
          </div>

          <div class="mb-4 flex items-center gap-2">
            <input id="active" v-model="form.active" type="checkbox" class="h-4 w-4" />
            <label for="active" class="text-sm font-medium text-gray-700">Active</label>
          </div>

          <div class="flex justify-end gap-2">
            <button
              type="button"
              class="rounded px-4 py-2 text-sm text-gray-600 hover:bg-gray-100"
              @click="closeModal"
            >
              Cancel
            </button>
            <button
              type="submit"
              :disabled="submitting"
              class="rounded bg-emerald-600 px-4 py-2 text-sm font-medium text-white hover:bg-emerald-700 disabled:opacity-50"
            >
              {{ submitting ? 'Saving...' : 'Save' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive, onMounted } from 'vue'
import api from '@/services/api'

interface Banner {
  id: number
  title: string | null
  image: string | null
  order: number | null
  active: boolean
}

interface BannerResponse {
  status: boolean
  message: string
  data: Banner[]
}

const banners = ref<Banner[]>([])
const loading = ref(true)
const submitting = ref(false)

const showModal = ref(false)
const isEditing = ref(false)
const editingBanner = ref<Banner | null>(null)
const selectedFile = ref<File | null>(null)
const errors = reactive<Record<string, string[]>>({})

const form = reactive({
  title: '',
  order: null as number | null,
  active: true,
})

const fetchBanners = async () => {
  loading.value = true
  try {
    const response = await api.get<BannerResponse>('/admin/top-banners')
    banners.value = response.data.status ? response.data.data : []
  } catch (error) {
    console.error('Failed to load banners:', error)
    banners.value = []
  } finally {
    loading.value = false
  }
}

const resetForm = () => {
  form.title = ''
  form.order = null
  form.active = true
  selectedFile.value = null
  Object.keys(errors).forEach((key) => delete errors[key])
}

const openCreateModal = () => {
  isEditing.value = false
  editingBanner.value = null
  resetForm()
  showModal.value = true
}

const openEditModal = (banner: Banner) => {
  isEditing.value = true
  editingBanner.value = banner
  resetForm()
  form.title = banner.title || ''
  form.order = banner.order
  form.active = banner.active
  showModal.value = true
}

const closeModal = () => {
  showModal.value = false
}

const handleFileChange = (event: Event) => {
  const target = event.target as HTMLInputElement
  selectedFile.value = target.files?.[0] ?? null
}

const submitForm = async () => {
  submitting.value = true
  Object.keys(errors).forEach((key) => delete errors[key])

  const formData = new FormData()
  formData.append('title', form.title)
  if (form.order !== null) formData.append('order', String(form.order))
  formData.append('active', form.active ? '1' : '0')
  if (selectedFile.value) formData.append('image', selectedFile.value)

  try {
    if (isEditing.value && editingBanner.value) {
      formData.append('_method', 'PUT')
      await api.post(`/admin/top-banners/${editingBanner.value.id}`, formData, {
        headers: { 'Content-Type': 'multipart/form-data' },
      })
    } else {
      await api.post('/admin/top-banners', formData, {
        headers: { 'Content-Type': 'multipart/form-data' },
      })
    }

    closeModal()
    await fetchBanners()
  } catch (error: any) {
    if (error.response?.status === 422) {
      Object.assign(errors, error.response.data.errors)
    } else {
      console.error('Failed to save banner:', error)
    }
  } finally {
    submitting.value = false
  }
}

const confirmDelete = async (banner: Banner) => {
  if (!confirm(`Delete banner "${banner.title || 'Untitled'}"?`)) return

  try {
    await api.delete(`/admin/top-banners/${banner.id}`)
    await fetchBanners()
  } catch (error) {
    console.error('Failed to delete banner:', error)
  }
}

onMounted(() => {
  fetchBanners()
})
</script>