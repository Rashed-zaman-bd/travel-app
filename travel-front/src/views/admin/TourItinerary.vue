<template>
  <div class="p-6 space-y-6">
    <div class="flex flex-wrap items-center justify-between gap-3">
      <h1 class="text-2xl font-semibold">Tour Package Itinerary</h1>

      <div class="flex items-center gap-3">
        <select v-model="filterPackageId" class="rounded border px-3 py-2" @change="fetchItineraries">
          <option value="">All packages</option>
          <option v-for="p in packages" :key="p.id" :value="p.id">{{ packageLabel(p) }}</option>
        </select>

        <button class="rounded bg-blue-600 px-4 py-2 text-white hover:bg-blue-700" @click="openCreate">
          + Add Day
        </button>
      </div>
    </div>

    <!-- Table -->
    <div class="overflow-x-auto rounded border bg-white">
      <table class="min-w-full text-sm">
        <thead class="bg-gray-50 text-left">
          <tr>
            <th class="px-4 py-3">Day</th>
            <th class="px-4 py-3">Package</th>
            <th class="px-4 py-3">Overnight</th>
            <th class="px-4 py-3">Map</th>
            <th class="px-4 py-3">Order</th>
            <th class="px-4 py-3">Status</th>
            <th class="px-4 py-3 text-right">Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-if="loading">
            <td colspan="7" class="px-4 py-6 text-center text-gray-500">Loading...</td>
          </tr>
          <tr v-else-if="!itineraries.length">
            <td colspan="7" class="px-4 py-6 text-center text-gray-500">No itinerary found.</td>
          </tr>
          <tr v-for="item in itineraries" :key="item.id" class="border-t">
            <td class="px-4 py-3 font-medium">Day {{ item.day_number }}</td>
            <td class="px-4 py-3">{{ item.tour_package ? packageLabel(item.tour_package) : '-' }}</td>
            <td class="px-4 py-3">{{ item.overnight?.en || '-' }}</td>
            <td class="px-4 py-3">
              <img v-if="item.map_image" :src="item.map_image" class="h-10 w-16 rounded object-cover" />
              <span v-else>-</span>
            </td>
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
    <div
      v-if="showModal"
      class="fixed inset-0 z-50 flex items-start justify-center overflow-y-auto bg-black/40 p-4"
    >
      <div class="my-8 w-full max-w-2xl rounded bg-white p-6 shadow-lg">
        <h2 class="mb-4 text-lg font-semibold">{{ editingId ? 'Edit Day' : 'Add Day' }}</h2>

        <div class="space-y-4">
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="mb-1 block text-sm font-medium">Tour Package *</label>
              <select v-model="form.tour_package_id" class="w-full rounded border px-3 py-2">
                <option :value="null" disabled>Select package</option>
                <option v-for="p in packages" :key="p.id" :value="p.id">{{ packageLabel(p) }}</option>
              </select>
              <p v-if="err('tour_package_id')" class="mt-1 text-xs text-red-600">
                {{ err('tour_package_id') }}
              </p>
            </div>
            <div>
              <label class="mb-1 block text-sm font-medium">Day Number *</label>
              <input
                v-model.number="form.day_number"
                type="number"
                min="1"
                class="w-full rounded border px-3 py-2"
              />
              <p v-if="err('day_number')" class="mt-1 text-xs text-red-600">{{ err('day_number') }}</p>
            </div>
          </div>

          <!-- Multilingual Text Fields -->
          <div v-for="f in textFields" :key="f.key" class="rounded border p-3 space-y-2">
            <p class="text-sm font-medium">{{ f.label }}</p>

            <div class="grid gap-3 sm:grid-cols-2">
              <div v-for="lang in langs" :key="lang.code">
                <label class="mb-1 block text-xs text-gray-500">{{ lang.label }}</label>

                <textarea
                  v-if="f.multiline"
                  v-model="form[f.key][lang.code]"
                  rows="3"
                  :placeholder="lang.placeholder"
                  class="w-full rounded border px-3 py-2"
                  :class="{ 'border-red-500': err(`${f.key}.${lang.code}`) }"
                />
                <input
                  v-else
                  v-model="form[f.key][lang.code]"
                  type="text"
                  :placeholder="lang.placeholder"
                  class="w-full rounded border px-3 py-2"
                  :class="{ 'border-red-500': err(`${f.key}.${lang.code}`) }"
                />

                <p v-if="err(`${f.key}.${lang.code}`)" class="mt-1 text-xs text-red-600">
                  {{ err(`${f.key}.${lang.code}`) }}
                </p>
              </div>
            </div>
          </div>

          <!-- Map Image -->
          <div>
            <label class="mb-1 block text-sm font-medium">Map Image</label>
            <input type="file" accept="image/png,image/jpeg,image/webp" @change="onFileChange" />
            <img v-if="previewUrl" :src="previewUrl" class="mt-2 h-24 rounded object-cover" />
            <p v-if="err('map_image')" class="mt-1 text-xs text-red-600">{{ err('map_image') }}</p>
          </div>

          <!-- Order & Active -->
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="mb-1 block text-sm font-medium">Order</label>
              <input
                v-model.number="form.order"
                type="number"
                min="0"
                class="w-full rounded border px-3 py-2"
              />
              <p v-if="err('order')" class="mt-1 text-xs text-red-600">{{ err('order') }}</p>
            </div>
            <label class="flex items-center gap-2 pt-6 text-sm">
              <input v-model="form.is_active" type="checkbox" />
              Active
            </label>
          </div>
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

type Locales = { en: string; bn: string }
type TextKey = 'highlights' | 'overnight' | 'description' | 'image_caption'
type LocaleField = { en?: string | null; bn?: string | null } | null

interface Itinerary {
  id: number
  tour_package_id: number
  tour_package?: { id: number; package_name: any }
  day_number: number
  highlights: LocaleField
  overnight: LocaleField
  description: LocaleField
  map_image: string | null
  image_caption: LocaleField
  order: number
  is_active: boolean
}

interface TourPackageLite {
  id: number
  package_name: any
}

const langs = [
  { code: 'en' as const, label: 'English', placeholder: 'English' },
  { code: 'bn' as const, label: 'বাংলা', placeholder: 'বাংলায় লিখুন' },
]

const textFields: { key: TextKey; label: string; multiline: boolean }[] = [
  { key: 'highlights', label: 'Highlights', multiline: true },
  { key: 'overnight', label: 'Overnight', multiline: false },
  { key: 'description', label: 'Description', multiline: true },
  { key: 'image_caption', label: 'Image Caption', multiline: false },
]

const itineraries = ref<Itinerary[]>([])
const packages = ref<TourPackageLite[]>([])
const filterPackageId = ref<number | ''>('')
const loading = ref(false)
const saving = ref(false)
const showModal = ref(false)
const editingId = ref<number | null>(null)
const errors = ref<Record<string, string[]>>({})

const mapFile = ref<File | null>(null)
const previewUrl = ref<string | null>(null)

const emptyLocales = (): Locales => ({ en: '', bn: '' })

const emptyForm = () => ({
  tour_package_id: null as number | null,
  day_number: 1,
  highlights: emptyLocales(),
  overnight: emptyLocales(),
  description: emptyLocales(),
  image_caption: emptyLocales(),
  order: 0,
  is_active: true,
})

const form = reactive(emptyForm())

const err = (key: string) => errors.value[key]?.[0]

const packageLabel = (p: { package_name: any }) =>
  p.package_name && typeof p.package_name === 'object' ? p.package_name.en : p.package_name

const fetchPackages = async () => {
  try {
    const { data } = await api.get('/tour-package')
    packages.value = data.data ?? data
  } catch (e) {
    console.error('Failed to fetch packages:', e)
  }
}

const fetchItineraries = async () => {
  loading.value = true
  try {
    const { data } = await api.get('/admin/tour-itinerary', {
      params: { tour_package_id: filterPackageId.value || undefined },
    })
    itineraries.value = data.data
  } finally {
    loading.value = false
  }
}

const resetFile = () => {
  mapFile.value = null
  previewUrl.value = null
}

const onFileChange = (e: Event) => {
  const file = (e.target as HTMLInputElement).files?.[0] ?? null
  mapFile.value = file
  if (file) previewUrl.value = URL.createObjectURL(file)
}

const openCreate = () => {
  editingId.value = null
  errors.value = {}
  resetFile()
  Object.assign(form, emptyForm())
  if (filterPackageId.value) form.tour_package_id = Number(filterPackageId.value)
  showModal.value = true
}

const openEdit = (item: Itinerary) => {
  editingId.value = item.id
  errors.value = {}
  resetFile()
  previewUrl.value = item.map_image

  const pick = (v: LocaleField): Locales => ({ en: v?.en ?? '', bn: v?.bn ?? '' })

  Object.assign(form, {
    tour_package_id: item.tour_package_id,
    day_number: item.day_number,
    highlights: pick(item.highlights),
    overnight: pick(item.overnight),
    description: pick(item.description),
    image_caption: pick(item.image_caption),
    order: item.order,
    is_active: item.is_active,
  })
  showModal.value = true
}

const closeModal = () => {
  showModal.value = false
}

const buildFormData = () => {
  const fd = new FormData()

  if (editingId.value) fd.append('_method', 'PUT')

  fd.append('tour_package_id', String(form.tour_package_id ?? ''))
  fd.append('day_number', String(form.day_number))
  fd.append('order', String(form.order ?? 0))
  fd.append('is_active', form.is_active ? '1' : '0')

  textFields.forEach(({ key }) => {
    fd.append(`${key}[en]`, form[key].en)
    fd.append(`${key}[bn]`, form[key].bn)
  })

  if (mapFile.value) fd.append('map_image', mapFile.value)

  return fd
}

const save = async () => {
  errors.value = {}

  if (!form.tour_package_id) {
    errors.value = { tour_package_id: ['Tour package is required.'] }
    return
  }

  saving.value = true
  try {
    const fd = buildFormData()

    if (editingId.value) {
      await api.post(`/admin/tour-itinerary/${editingId.value}`, fd)
    } else {
      await api.post('/admin/tour-itinerary', fd)
    }

    closeModal()
    await fetchItineraries()
  } catch (e: any) {
    if (e.response?.status === 422) {
      errors.value = e.response.data.errors ?? {}
    } else {
      alert(e.response?.data?.message || 'Something went wrong.')
    }
  } finally {
    saving.value = false
  }
}

const remove = async (item: Itinerary) => {
  if (!confirm(`Delete Day ${item.day_number}?`)) return
  try {
    await api.delete(`/admin/tour-itinerary/${item.id}`)
    itineraries.value = itineraries.value.filter((i) => i.id !== item.id)
  } catch (e: any) {
    alert(e.response?.data?.message || 'Delete failed.')
  }
}

onMounted(async () => {
  await Promise.all([fetchPackages(), fetchItineraries()])
})
</script>