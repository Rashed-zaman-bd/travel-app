<template>
  <div class="min-h-screen bg-slate-50 p-4 sm:p-8">
    <div class="mx-auto max-w-7xl space-y-6">
      <!-- Header -->
      <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
          <h1 class="text-2xl font-bold tracking-tight text-slate-900">Day Activities</h1>
          <p class="mt-1 text-sm text-slate-500">Manage the activities shown inside each itinerary day.</p>
        </div>
        <button
          class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm shadow-indigo-200 transition hover:bg-indigo-700"
          @click="openCreate"
        >
          <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" /></svg>
          Add Activity
        </button>
      </div>

      <!-- Filters -->
      <div class="grid gap-3 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:grid-cols-3">
        <div>
          <label class="mb-1 block text-xs font-medium text-slate-500">Package</label>
          <select v-model="filterPackageId" class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-100">
            <option value="">All packages</option>
            <option v-for="p in packages" :key="p.id" :value="p.id">{{ packageLabel(p) }}</option>
          </select>
        </div>
        <div>
          <label class="mb-1 block text-xs font-medium text-slate-500">Day</label>
          <select
            v-model="filterDayId"
            :disabled="!filterPackageId"
            class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-100 disabled:bg-slate-50 disabled:text-slate-400"
            @change="fetchActivities"
          >
            <option value="">All days</option>
            <option v-for="d in filterDays" :key="d.id" :value="d.id">Day {{ d.day_number }}</option>
          </select>
        </div>
        <div>
          <label class="mb-1 block text-xs font-medium text-slate-500">Search</label>
          <input
            v-model="search"
            type="text"
            placeholder="Search by title..."
            class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-100"
          />
        </div>
      </div>

      <!-- Loading skeleton -->
      <div v-if="loading" class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
        <div v-for="n in 6" :key="n" class="animate-pulse overflow-hidden rounded-2xl border border-slate-200 bg-white">
          <div class="h-40 bg-slate-100" />
          <div class="space-y-3 p-4">
            <div class="h-4 w-2/3 rounded bg-slate-100" />
            <div class="h-3 w-1/2 rounded bg-slate-100" />
            <div class="h-3 w-full rounded bg-slate-100" />
          </div>
        </div>
      </div>

      <!-- Empty state -->
      <div v-else-if="!visibleActivities.length" class="rounded-2xl border border-dashed border-slate-300 bg-white py-16 text-center">
        <div class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-full bg-indigo-50 text-indigo-600">
          <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-3-3v6m9-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
        </div>
        <p class="font-medium text-slate-700">No activities found</p>
        <p class="mt-1 text-sm text-slate-500">Try changing the filters or add a new activity.</p>
      </div>

      <!-- Cards -->
      <div v-else class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
        <div
          v-for="item in visibleActivities"
          :key="item.id"
          class="group overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm transition hover:-translate-y-0.5 hover:shadow-md"
        >
          <div class="relative h-40 bg-gradient-to-br from-indigo-100 via-sky-50 to-slate-100">
            <img v-if="item.image" :src="item.image" :alt="item.title?.en ?? ''" class="h-full w-full object-cover" />
            <div v-else class="flex h-full items-center justify-center text-indigo-300">
              <svg class="h-10 w-10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.16-5.16a2.25 2.25 0 013.18 0l5.16 5.16m-1.5-1.5l1.41-1.41a2.25 2.25 0 013.18 0l2.41 2.41M3.75 19.5h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5z" /></svg>
            </div>
            <span class="absolute left-3 top-3 rounded-full bg-white/90 px-2.5 py-1 text-xs font-semibold text-indigo-700 shadow-sm backdrop-blur">
              Day {{ item.itinerary?.day_number ?? '-' }}
            </span>
            <span
              class="absolute right-3 top-3 rounded-full px-2.5 py-1 text-xs font-medium shadow-sm"
              :class="item.is_active ? 'bg-emerald-500 text-white' : 'bg-slate-700/80 text-white'"
            >
              {{ item.is_active ? 'Active' : 'Inactive' }}
            </span>
          </div>

          <div class="space-y-2 p-4">
            <h3 class="line-clamp-1 font-semibold text-slate-900">{{ item.title?.en }}</h3>
            <p v-if="item.title?.bn" class="line-clamp-1 text-sm text-slate-500">{{ item.title.bn }}</p>
            <p class="line-clamp-2 min-h-[2.5rem] text-sm text-slate-600">{{ item.description?.en || 'No description' }}</p>

            <div class="flex items-center justify-between border-t border-slate-100 pt-3">
              <div class="min-w-0">
                <p class="truncate text-xs text-slate-500">
                  {{ item.tour_package ? packageLabel(item.tour_package) : '-' }}
                </p>
                <p class="text-xs text-slate-400">Order: {{ item.order }}</p>
              </div>
              <div class="flex gap-1">
                <button class="rounded-lg p-2 text-slate-500 transition hover:bg-indigo-50 hover:text-indigo-600" title="Edit" @click="openEdit(item)">
                  <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" /></svg>
                </button>
                <button class="rounded-lg p-2 text-slate-500 transition hover:bg-rose-50 hover:text-rose-600" title="Delete" @click="deleteTarget = item">
                  <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" /></svg>
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Slide-over form -->
    <Transition
      enter-active-class="transition duration-200"
      enter-from-class="opacity-0"
      leave-active-class="transition duration-150"
      leave-to-class="opacity-0"
    >
      <div v-if="drawerOpen" class="fixed inset-0 z-40 bg-slate-900/40 backdrop-blur-sm" @click="closeDrawer" />
    </Transition>

    <Transition
      enter-active-class="transition duration-300 ease-out"
      enter-from-class="translate-x-full"
      leave-active-class="transition duration-200 ease-in"
      leave-to-class="translate-x-full"
    >
      <aside v-if="drawerOpen" class="fixed inset-y-0 right-0 z-50 flex w-full max-w-xl flex-col bg-white shadow-2xl">
        <div class="flex items-center justify-between border-b border-slate-200 px-6 py-4">
          <h2 class="text-lg font-semibold text-slate-900">{{ editingId ? 'Edit Activity' : 'Add Activity' }}</h2>
          <button class="rounded-lg p-2 text-slate-400 hover:bg-slate-100 hover:text-slate-600" @click="closeDrawer">
            <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
          </button>
        </div>

        <div class="flex-1 space-y-5 overflow-y-auto px-6 py-5">
          <!-- Package + Day -->
          <div class="grid gap-4 sm:grid-cols-2">
            <div>
              <label class="mb-1 block text-sm font-medium text-slate-700">Tour Package <span class="text-rose-500">*</span></label>
              <select
                v-model="form.tour_package_id"
                class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-100"
                :class="{ 'border-rose-400': err('tour_package_id') }"
                @change="onFormPackageChange"
              >
                <option :value="null" disabled>Select package</option>
                <option v-for="p in packages" :key="p.id" :value="p.id">{{ packageLabel(p) }}</option>
              </select>
              <p v-if="err('tour_package_id')" class="mt-1 text-xs text-rose-600">{{ err('tour_package_id') }}</p>
            </div>

            <div>
              <label class="mb-1 block text-sm font-medium text-slate-700">Itinerary Day <span class="text-rose-500">*</span></label>
              <select
                v-model="form.tour_package_itinerary_id"
                :disabled="!form.tour_package_id"
                class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-100 disabled:bg-slate-50 disabled:text-slate-400"
                :class="{ 'border-rose-400': err('tour_package_itinerary_id') }"
              >
                <option :value="null" disabled>{{ form.tour_package_id ? 'Select day' : 'Select package first' }}</option>
                <option v-for="d in formDays" :key="d.id" :value="d.id">Day {{ d.day_number }}</option>
              </select>
              <p v-if="err('tour_package_itinerary_id')" class="mt-1 text-xs text-rose-600">{{ err('tour_package_itinerary_id') }}</p>
            </div>
          </div>

          <!-- Language tabs -->
          <div>
            <div class="inline-flex rounded-xl bg-slate-100 p-1">
              <button
                v-for="lang in langs"
                :key="lang.code"
                type="button"
                class="relative rounded-lg px-4 py-1.5 text-sm font-medium transition"
                :class="activeLang === lang.code ? 'bg-white text-indigo-700 shadow-sm' : 'text-slate-500 hover:text-slate-700'"
                @click="activeLang = lang.code"
              >
                {{ lang.label }}
                <span v-if="langHasError(lang.code)" class="absolute -right-0.5 -top-0.5 h-2 w-2 rounded-full bg-rose-500" />
              </button>
            </div>

            <div class="mt-4 space-y-4">
              <div v-for="f in textFields" :key="f.key">
                <label class="mb-1 block text-sm font-medium text-slate-700">
                  {{ f.label }}
                  <span v-if="f.key === 'title' && activeLang === 'en'" class="text-rose-500">*</span>
                </label>

                <textarea
                  v-if="f.multiline"
                  v-model="form[f.key][activeLang]"
                  rows="4"
                  :placeholder="currentLang.placeholder"
                  class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-100"
                  :class="{ 'border-rose-400': err(`${f.key}.${activeLang}`) }"
                />
                <input
                  v-else
                  v-model="form[f.key][activeLang]"
                  type="text"
                  :placeholder="currentLang.placeholder"
                  class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-100"
                  :class="{ 'border-rose-400': err(`${f.key}.${activeLang}`) }"
                />

                <p v-if="err(`${f.key}.${activeLang}`)" class="mt-1 text-xs text-rose-600">{{ err(`${f.key}.${activeLang}`) }}</p>
              </div>
            </div>
          </div>

          <!-- Image dropzone -->
          <div>
            <label class="mb-1 block text-sm font-medium text-slate-700">Image</label>
            <label
              class="relative flex cursor-pointer flex-col items-center justify-center overflow-hidden rounded-xl border-2 border-dashed px-4 py-6 text-center transition"
              :class="dragging ? 'border-indigo-500 bg-indigo-50' : 'border-slate-300 hover:border-indigo-400 hover:bg-slate-50'"
              @dragover.prevent="dragging = true"
              @dragleave.prevent="dragging = false"
              @drop.prevent="onDrop"
            >
              <input type="file" accept="image/png,image/jpeg,image/webp" class="hidden" @change="onFileChange" />
              <img v-if="previewUrl" :src="previewUrl" class="mb-3 h-36 w-full rounded-lg object-cover" />
              <p class="text-sm font-medium text-slate-700">
                {{ previewUrl ? 'Click or drop to replace' : 'Click to upload or drag & drop' }}
              </p>
              <p class="mt-1 text-xs text-slate-400">JPG, PNG or WEBP, up to 2 MB</p>
            </label>
            <p v-if="err('image')" class="mt-1 text-xs text-rose-600">{{ err('image') }}</p>
          </div>

          <!-- Order + Active -->
          <div class="grid items-end gap-4 sm:grid-cols-2">
            <div>
              <label class="mb-1 block text-sm font-medium text-slate-700">Order</label>
              <input
                v-model.number="form.order"
                type="number"
                min="0"
                class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-100"
              />
              <p v-if="err('order')" class="mt-1 text-xs text-rose-600">{{ err('order') }}</p>
            </div>

            <div class="flex items-center justify-between rounded-lg border border-slate-200 px-3 py-2">
              <span class="text-sm font-medium text-slate-700">Active</span>
              <button
                type="button"
                role="switch"
                :aria-checked="form.is_active"
                class="relative inline-flex h-6 w-11 items-center rounded-full transition"
                :class="form.is_active ? 'bg-indigo-600' : 'bg-slate-300'"
                @click="form.is_active = !form.is_active"
              >
                <span
                  class="inline-block h-5 w-5 transform rounded-full bg-white shadow transition"
                  :class="form.is_active ? 'translate-x-5' : 'translate-x-0.5'"
                />
              </button>
            </div>
          </div>
        </div>

        <div class="flex justify-end gap-3 border-t border-slate-200 bg-slate-50 px-6 py-4">
          <button
            class="rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-100 disabled:opacity-60"
            :disabled="saving"
            @click="closeDrawer"
          >
            Cancel
          </button>
          <button
            class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-5 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700 disabled:opacity-60"
            :disabled="saving"
            @click="save"
          >
            <svg v-if="saving" class="h-4 w-4 animate-spin" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" class="opacity-25" /><path fill="currentColor" class="opacity-75" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z" /></svg>
            {{ saving ? 'Saving...' : editingId ? 'Update Activity' : 'Save Activity' }}
          </button>
        </div>
      </aside>
    </Transition>

    <!-- Delete confirm -->
    <div v-if="deleteTarget" class="fixed inset-0 z-[60] flex items-center justify-center bg-slate-900/40 p-4 backdrop-blur-sm">
      <div class="w-full max-w-sm rounded-2xl bg-white p-6 shadow-xl">
        <div class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-rose-50 text-rose-600">
          <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" /></svg>
        </div>
        <h3 class="text-center text-lg font-semibold text-slate-900">Delete activity?</h3>
        <p class="mt-1 text-center text-sm text-slate-500">
          "{{ deleteTarget.title?.en }}" and its image will be permanently removed.
        </p>
        <div class="mt-6 flex gap-3">
          <button class="flex-1 rounded-xl border border-slate-200 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50" :disabled="deleting" @click="deleteTarget = null">Cancel</button>
          <button class="flex-1 rounded-xl bg-rose-600 px-4 py-2 text-sm font-semibold text-white hover:bg-rose-700 disabled:opacity-60" :disabled="deleting" @click="confirmDelete">
            {{ deleting ? 'Deleting...' : 'Delete' }}
          </button>
        </div>
      </div>
    </div>

    <!-- Toast -->
    <Transition
      enter-active-class="transition duration-200"
      enter-from-class="translate-y-2 opacity-0"
      leave-active-class="transition duration-150"
      leave-to-class="opacity-0"
    >
      <div
        v-if="toast"
        class="fixed bottom-6 right-6 z-[70] rounded-xl px-4 py-3 text-sm font-medium text-white shadow-lg"
        :class="toast.type === 'success' ? 'bg-emerald-600' : 'bg-rose-600'"
      >
        {{ toast.message }}
      </div>
    </Transition>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive, computed, watch, onMounted } from 'vue'
import api from '@/services/api'

type Locales = { en: string; bn: string }
type LangCode = 'en' | 'bn'
type TextKey = 'title' | 'description' | 'image_caption'
type LocaleField = { en?: string | null; bn?: string | null } | null

interface Activity {
  id: number
  tour_package_itinerary_id: number
  itinerary?: { id: number; day_number: number }
  tour_package?: { id: number; package_name: any }
  title: LocaleField
  description: LocaleField
  image: string | null
  image_caption: LocaleField
  order: number
  is_active: boolean
}

interface TourPackageLite {
  id: number
  package_name: any
}

interface DayLite {
  id: number
  day_number: number
}

const langs = [
  { code: 'en' as LangCode, label: 'English', placeholder: 'Write in English' },
  { code: 'bn' as LangCode, label: 'বাংলা', placeholder: 'বাংলায় লিখুন' },
]

const textFields: { key: TextKey; label: string; multiline: boolean }[] = [
  { key: 'title', label: 'Title', multiline: false },
  { key: 'description', label: 'Description', multiline: true },
  { key: 'image_caption', label: 'Image Caption', multiline: false },
]

// ---------- state ----------
const activities = ref<Activity[]>([])
const packages = ref<TourPackageLite[]>([])
const filterDays = ref<DayLite[]>([])
const formDays = ref<DayLite[]>([])

const filterPackageId = ref<number | ''>('')
const filterDayId = ref<number | ''>('')
const search = ref('')

const loading = ref(false)
const saving = ref(false)
const deleting = ref(false)
const drawerOpen = ref(false)
const editingId = ref<number | null>(null)
const deleteTarget = ref<Activity | null>(null)
const errors = ref<Record<string, string[]>>({})
const activeLang = ref<LangCode>('en')
const dragging = ref(false)

const imageFile = ref<File | null>(null)
const previewUrl = ref<string | null>(null)

const toast = ref<{ type: 'success' | 'error'; message: string } | null>(null)
let toastTimer: ReturnType<typeof setTimeout> | undefined

const emptyLocales = (): Locales => ({ en: '', bn: '' })

const emptyForm = () => ({
  tour_package_id: null as number | null,
  tour_package_itinerary_id: null as number | null,
  title: emptyLocales(),
  description: emptyLocales(),
  image_caption: emptyLocales(),
  order: 0,
  is_active: true,
})

const form = reactive(emptyForm())

// ---------- helpers ----------
const currentLang = computed(() => langs.find((l) => l.code === activeLang.value)!)

const err = (key: string) => errors.value[key]?.[0]

const langHasError = (code: LangCode) =>
  Object.keys(errors.value).some((k) => k.endsWith(`.${code}`))

const packageLabel = (p: { package_name: any }) =>
  p.package_name && typeof p.package_name === 'object' ? p.package_name.en : p.package_name

const showToast = (type: 'success' | 'error', message: string) => {
  toast.value = { type, message }
  clearTimeout(toastTimer)
  toastTimer = setTimeout(() => (toast.value = null), 3000)
}

const visibleActivities = computed(() => {
  const q = search.value.trim().toLowerCase()
  if (!q) return activities.value
  return activities.value.filter(
    (a) =>
      a.title?.en?.toLowerCase().includes(q) ||
      a.title?.bn?.toLowerCase().includes(q)
  )
})

// ---------- data loading ----------
const fetchPackages = async () => {
  // adjust to your real tour package list endpoint
  const { data } = await api.get('/tour-package')
  packages.value = data.data ?? data
}

const fetchDays = async (packageId: number | null | ''): Promise<DayLite[]> => {
  if (!packageId) return []
  const { data } = await api.get('/admin/tour-itinerary', {
    params: { tour_package_id: packageId },
  })
  return (data.data ?? []).map((d: any) => ({ id: d.id, day_number: d.day_number }))
}

const fetchActivities = async () => {
  loading.value = true
  try {
    const { data } = await api.get('/admin/tour-activity', {
      params: {
        tour_package_id: filterPackageId.value || undefined,
        tour_package_itinerary_id: filterDayId.value || undefined,
      },
    })
    activities.value = data.data
  } finally {
    loading.value = false
  }
}

watch(filterPackageId, async (id) => {
  filterDayId.value = ''
  filterDays.value = await fetchDays(id)
  await fetchActivities()
})

const onFormPackageChange = async () => {
  form.tour_package_itinerary_id = null
  formDays.value = await fetchDays(form.tour_package_id)
}

// ---------- image ----------
const setFile = (file: File | null) => {
  if (!file) return
  if (file.size > 2 * 1024 * 1024) {
    errors.value = { ...errors.value, image: ['Image may not be larger than 2 MB.'] }
    return
  }
  delete errors.value.image
  imageFile.value = file
  previewUrl.value = URL.createObjectURL(file)
}

const onFileChange = (e: Event) => {
  setFile((e.target as HTMLInputElement).files?.[0] ?? null)
}

const onDrop = (e: DragEvent) => {
  dragging.value = false
  setFile(e.dataTransfer?.files?.[0] ?? null)
}

// ---------- drawer ----------
const openCreate = async () => {
  editingId.value = null
  errors.value = {}
  activeLang.value = 'en'
  imageFile.value = null
  previewUrl.value = null
  Object.assign(form, emptyForm())
  formDays.value = []

  if (filterPackageId.value) {
    form.tour_package_id = Number(filterPackageId.value)
    formDays.value = filterDays.value
    if (filterDayId.value) form.tour_package_itinerary_id = Number(filterDayId.value)
  }
  drawerOpen.value = true
}

const openEdit = async (item: Activity) => {
  editingId.value = item.id
  errors.value = {}
  activeLang.value = 'en'
  imageFile.value = null
  previewUrl.value = item.image

  const pick = (v: LocaleField): Locales => ({ en: v?.en ?? '', bn: v?.bn ?? '' })
  const packageId = item.tour_package?.id ?? null

  Object.assign(form, {
    tour_package_id: packageId,
    tour_package_itinerary_id: item.tour_package_itinerary_id,
    title: pick(item.title),
    description: pick(item.description),
    image_caption: pick(item.image_caption),
    order: item.order,
    is_active: item.is_active,
  })

  formDays.value = await fetchDays(packageId)
  drawerOpen.value = true
}

const closeDrawer = () => {
  drawerOpen.value = false
}

// ---------- save ----------
const buildFormData = () => {
  const fd = new FormData()

  if (editingId.value) fd.append('_method', 'PUT')

  fd.append('tour_package_id', String(form.tour_package_id ?? ''))
  fd.append('tour_package_itinerary_id', String(form.tour_package_itinerary_id ?? ''))
  fd.append('order', String(form.order ?? 0))
  fd.append('is_active', form.is_active ? '1' : '0')

  textFields.forEach(({ key }) => {
    fd.append(`${key}[en]`, form[key].en)
    fd.append(`${key}[bn]`, form[key].bn)
  })

  // only send the file when a new one was picked
  if (imageFile.value) fd.append('image', imageFile.value)

  return fd
}

const save = async () => {
  errors.value = {}

  const local: Record<string, string[]> = {}
  if (!form.tour_package_id) local.tour_package_id = ['Tour package is required.']
  if (!form.tour_package_itinerary_id) local.tour_package_itinerary_id = ['Itinerary day is required.']
  if (!form.title.en.trim()) local['title.en'] = ['English activity title is required.']

  if (Object.keys(local).length) {
    errors.value = local
    activeLang.value = 'en'
    return
  }

  saving.value = true
  try {
    const fd = buildFormData()

    if (editingId.value) {
      await api.post(`/admin/tour-activity/${editingId.value}`, fd)
    } else {
      await api.post('/admin/tour-activity', fd)
    }

    showToast('success', editingId.value ? 'Activity updated.' : 'Activity created.')
    closeDrawer()
    await fetchActivities()
  } catch (e: any) {
    if (e.response?.status === 422) {
      errors.value = e.response.data.errors ?? {}
      const firstLangKey = Object.keys(errors.value).find((k) => /\.(en|bn)$/.test(k))
      if (firstLangKey && !langHasError(activeLang.value)) {
        activeLang.value = firstLangKey.endsWith('.bn') ? 'bn' : 'en'
      }
    } else {
      showToast('error', e.response?.data?.message || 'Something went wrong.')
    }
  } finally {
    saving.value = false
  }
}

// ---------- delete ----------
const confirmDelete = async () => {
  if (!deleteTarget.value) return
  deleting.value = true
  try {
    await api.delete(`/admin/tour-activity/${deleteTarget.value.id}`)
    activities.value = activities.value.filter((a) => a.id !== deleteTarget.value!.id)
    showToast('success', 'Activity deleted.')
    deleteTarget.value = null
  } catch (e: any) {
    showToast('error', e.response?.data?.message || 'Delete failed.')
  } finally {
    deleting.value = false
  }
}

onMounted(async () => {
  await Promise.all([fetchPackages(), fetchActivities()])
})
</script>