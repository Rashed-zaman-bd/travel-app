<template>
  <div class="p-6 max-w-7xl mx-auto">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
      <div>
        <h1 class="text-2xl font-bold text-gray-800">Logo Management</h1>
        <p class="text-sm text-gray-500 mt-1">Manage brand text logo and round logo for your news website.</p>
      </div>
      
      <!-- Show Add button only if no logo exists -->
      <button
        v-if="!logo && !loading"
        @click="openModal('create')"
        class="flex items-center gap-2 bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-medium shadow-sm transition-colors"
      >
        <i class="bi bi-plus-lg"></i>
        <span>Add Logo</span>
      </button>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="flex justify-center items-center py-12">
      <div class="animate-spin rounded-full h-10 w-10 border-b-2 border-red-600"></div>
    </div>

    <!-- Logo Display / Table -->
    <div v-else-if="logo" class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
      <div class="p-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 items-center">
          <!-- Title -->
          <div>
            <span class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Title / Brand Name</span>
            <p class="text-lg font-semibold text-gray-800">{{ logo.title }}</p>
          </div>

          <!-- Text Logo Preview -->
          <div>
            <span class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Text Logo</span>
            <div v-if="logo.text_logo" class="bg-gray-50 p-3 rounded-lg border border-gray-100 inline-block">
              <img :src="logo.text_logo" alt="Text Logo" class="h-12 object-contain max-w-full" />
            </div>
            <span v-else class="text-sm text-gray-400 italic">Not set</span>
          </div>

          <!-- Round Logo Preview -->
          <div>
            <span class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Round / Favicon Logo</span>
            <div v-if="logo.round_logo" class="bg-gray-50 p-3 rounded-lg border border-gray-100 inline-block">
              <img :src="logo.round_logo" alt="Round Logo" class="h-12 w-12 object-cover rounded-full" />
            </div>
            <span v-else class="text-sm text-gray-400 italic">Not set</span>
          </div>
        </div>

        <!-- Actions -->
        <div class="mt-6 pt-6 border-t border-gray-100 flex items-center justify-end gap-3">
          <button
            @click="openModal('edit')"
            class="flex items-center gap-2 bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium transition-colors"
          >
            <i class="bi bi-pencil-square"></i>
            <span>Edit Logo</span>
          </button>
          
          <button
            @click="deleteLogo"
            :disabled="submitting"
            class="flex items-center gap-2 bg-red-50 hover:bg-red-100 text-red-600 px-4 py-2 rounded-lg text-sm font-medium transition-colors"
          >
            <i class="bi bi-trash"></i>
            <span>Delete</span>
          </button>
        </div>
      </div>
    </div>

    <!-- Empty State -->
    <div v-else class="bg-white rounded-xl shadow-sm border border-gray-200 p-12 text-center">
      <div class="w-16 h-16 bg-red-50 text-red-600 rounded-full flex items-center justify-center mx-auto mb-4 text-2xl">
        <i class="bi bi-image"></i>
      </div>
      <h3 class="text-lg font-bold text-gray-800">No logo configured</h3>
      <p class="text-gray-500 text-sm mt-1 mb-6">Upload your primary header logo and site icon to brand your news site.</p>
      <button
        @click="openModal('create')"
        class="inline-flex items-center gap-2 bg-red-600 hover:bg-red-700 text-white px-5 py-2.5 rounded-lg font-medium shadow-sm transition-colors"
      >
        <i class="bi bi-plus-lg"></i>
        <span>Create Logo Config</span>
      </button>
    </div>

    <!-- CREATE / EDIT MODAL -->
    <Transition
      enter-active-class="transition duration-200 ease-out"
      enter-from-class="opacity-0 scale-95"
      enter-to-class="opacity-100 scale-100"
      leave-active-class="transition duration-150 ease-in"
      leave-from-class="opacity-100 scale-100"
      leave-to-class="opacity-0 scale-95"
    >
      <div v-if="isModalOpen" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
        <div class="bg-white rounded-xl shadow-xl w-full max-w-lg overflow-hidden">
          <!-- Modal Header -->
          <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="font-bold text-gray-800 text-lg">
              {{ modalMode === 'create' ? 'Add Site Logo' : 'Update Site Logo' }}
            </h3>
            <button @click="closeModal" class="text-gray-400 hover:text-gray-600 text-xl">
              <i class="bi bi-x-lg"></i>
            </button>
          </div>

          <!-- Form -->
          <form @submit.prevent="submitForm" class="p-6 space-y-5">
            <!-- Global Error Banner -->
            <div v-if="errorMessage" class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm">
              {{ errorMessage }}
            </div>

            <!-- Title Input -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Title / Brand Name *</label>
              <input
                v-model="form.title"
                type="text"
                required
                placeholder="e.g. Khobor News"
                class="w-full border border-gray-300 rounded-lg px-3.5 py-2 focus:ring-2 focus:ring-red-500 focus:border-red-500 outline-none text-sm"
              />
              <span v-if="errors.title" class="text-red-500 text-xs mt-1 block">{{ errors.title[0] }}</span>
            </div>

            <!-- Text Logo File Input -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Text Logo (Header)</label>
              <input
                type="file"
                accept="image/*"
                @change="handleFileChange($event, 'text_logo')"
                class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-red-50 file:text-red-700 hover:file:bg-red-100"
              />
              <span v-if="errors.text_logo" class="text-red-500 text-xs mt-1 block">{{ errors.text_logo[0] }}</span>

              <!-- Image Preview -->
              <div v-if="textLogoPreview" class="mt-2 relative inline-block">
                <img :src="textLogoPreview" class="h-14 object-contain rounded border border-gray-200 p-1" />
              </div>
            </div>

            <!-- Round Logo File Input -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Round Logo (Favicon/Mobile)</label>
              <input
                type="file"
                accept="image/*"
                @change="handleFileChange($event, 'round_logo')"
                class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-red-50 file:text-red-700 hover:file:bg-red-100"
              />
              <span v-if="errors.round_logo" class="text-red-500 text-xs mt-1 block">{{ errors.round_logo[0] }}</span>

              <!-- Image Preview -->
              <div v-if="roundLogoPreview" class="mt-2 relative inline-block">
                <img :src="roundLogoPreview" class="h-14 w-14 object-cover rounded-full border border-gray-200 p-1" />
              </div>
            </div>

            <!-- Buttons -->
            <div class="pt-4 border-t border-gray-100 flex items-center justify-end gap-3">
              <button
                type="button"
                @click="closeModal"
                class="px-4 py-2 rounded-lg text-sm font-medium text-gray-600 hover:bg-gray-100 transition-colors"
              >
                Cancel
              </button>
              <button
                type="submit"
                :disabled="submitting"
                class="flex items-center gap-2 bg-red-600 hover:bg-red-700 text-white px-5 py-2 rounded-lg text-sm font-medium transition-colors shadow-sm disabled:opacity-50"
              >
                <i v-if="submitting" class="bi bi-arrow-repeat animate-spin"></i>
                <span>{{ submitting ? 'Saving...' : 'Save Logo' }}</span>
              </button>
            </div>
          </form>
        </div>
      </div>
    </Transition>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import api from '@/services/api'

interface LogoData {
  id: number
  title: string
  text_logo: string | null
  round_logo: string | null
}

/* =========================
   STATE
========================= */
const logo = ref<LogoData | null>(null)
const loading = ref(true)
const submitting = ref(false)

const isModalOpen = ref(false)
const modalMode = ref<'create' | 'edit'>('create')
const errorMessage = ref('')
const errors = ref<Record<string, string[]>>({})

const form = ref({
  title: '',
  text_logo: null as File | null,
  round_logo: null as File | null,
})

const textLogoPreview = ref<string | null>(null)
const roundLogoPreview = ref<string | null>(null)

/* =========================
   FETCH LOGO
========================= */
const fetchLogo = async () => {
  loading.value = true
  try {
    const { data } = await api.get('/logo')
    // API returns Resource object directly or inside data wrapper
    logo.value = data.data || data
  } catch (e: any) {
    if (e?.response?.status === 404) {
      logo.value = null
    } else {
      console.error('Failed to load logo:', e)
    }
  } finally {
    loading.value = false
  }
}

/* =========================
   MODAL CONTROLS
========================= */
const openModal = (mode: 'create' | 'edit') => {
  modalMode.value = mode
  errorMessage.value = ''
  errors.value = {}

  if (mode === 'edit' && logo.value) {
    form.value.title = logo.value.title
    textLogoPreview.value = logo.value.text_logo
    roundLogoPreview.value = logo.value.round_logo
  } else {
    form.value.title = ''
    textLogoPreview.value = null
    roundLogoPreview.value = null
  }

  form.value.text_logo = null
  form.value.round_logo = null
  isModalOpen.value = true
}

const closeModal = () => {
  isModalOpen.value = false
}

/* =========================
   FILE HANDLING
========================= */
const handleFileChange = (event: Event, field: 'text_logo' | 'round_logo') => {
  const target = event.target as HTMLInputElement
  if (!target.files || !target.files[0]) return

  const file = target.files[0]
  form.value[field] = file

  // Preview local file selection
  const reader = new FileReader()
  reader.onload = (e) => {
    if (field === 'text_logo') {
      textLogoPreview.value = e.target?.result as string
    } else {
      roundLogoPreview.value = e.target?.result as string
    }
  }
  reader.readAsDataURL(file)
}

/* =========================
   SUBMIT (CREATE / UPDATE)
========================= */
const submitForm = async () => {
  submitting.value = true
  errorMessage.value = ''
  errors.value = {}

  // Construct Multipart Form Data for File Uploads
  const formData = new FormData()
  formData.append('title', form.value.title)

  if (form.value.text_logo) {
    formData.append('text_logo', form.value.text_logo)
  }

  if (form.value.round_logo) {
    formData.append('round_logo', form.value.round_logo)
  }

  try {
    if (modalMode.value === 'create') {
      await api.post('/admin/logo', formData, {
        headers: { 'Content-Type': 'multipart/form-data' },
      })
    } else {
      // PHP issue with PUT & Multipart FormData -> Use POST to the logo ID endpoint
      await api.post(`/admin/logo/${logo.value?.id}`, formData, {
        headers: { 'Content-Type': 'multipart/form-data' },
      })
    }

    closeModal()
    await fetchLogo()
  } catch (e: any) {
    if (e?.response?.status === 422) {
      errors.value = e.response.data.errors || {}
      errorMessage.value = e.response.data.message || 'Validation error occurred.'
    } else {
      errorMessage.value = 'Failed to submit logo. Please try again.'
    }
  } finally {
    submitting.value = false
  }
}

/* =========================
   DELETE LOGO
========================= */
const deleteLogo = async () => {
  if (!logo.value) return
  if (!confirm('Are you sure you want to delete the site logo?')) return

  submitting.value = true
  try {
    await api.delete(`/admin/logo/${logo.value.id}`)
    logo.value = null
  } catch (e) {
    console.error('Failed to delete logo:', e)
  } finally {
    submitting.value = false
  }
}

/* =========================
   LIFECYCLE
========================= */
onMounted(() => {
  fetchLogo()
})
</script>