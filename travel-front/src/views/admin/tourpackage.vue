```vue
<template>
  <div class="min-h-screen bg-stone-100 p-4 md:p-8">
    <div class="mx-auto max-w-7xl">

      <!-- ================= HEADER ================= -->
      <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
          <h1 class="text-3xl font-bold text-slate-900">
            Tour Packages
          </h1>

          <p class="mt-1 text-sm text-slate-500">
            Create, edit and manage your tour packages.
          </p>
        </div>

        <button
          type="button"
          @click="openCreateModal"
          class="rounded-xl bg-slate-900 px-5 py-3 text-sm font-semibold text-white hover:bg-slate-700"
        >
          + Add Package
        </button>
      </div>


      <!-- ================= MESSAGES ================= -->
      <div
        v-if="successMessage"
        class="mb-4 rounded-xl bg-emerald-50 px-4 py-3 text-sm text-emerald-700"
      >
        {{ successMessage }}
      </div>

      <div
        v-if="errorMessage"
        class="mb-4 rounded-xl bg-red-50 px-4 py-3 text-sm text-red-700"
      >
        {{ errorMessage }}
      </div>


      <!-- ================= STATS ================= -->
      <div class="mb-6 grid grid-cols-3 gap-4">

        <div class="rounded-2xl bg-white p-5 shadow-sm">
          <p class="text-3xl font-bold text-slate-900">
            {{ tourPackages.length }}
          </p>

          <p class="mt-1 text-sm text-slate-500">
            Total Packages
          </p>
        </div>

        <div class="rounded-2xl bg-white p-5 shadow-sm">
          <p class="text-3xl font-bold text-emerald-600">
            {{ activePackages }}
          </p>

          <p class="mt-1 text-sm text-slate-500">
            Active
          </p>
        </div>

        <div class="rounded-2xl bg-white p-5 shadow-sm">
          <p class="text-3xl font-bold text-red-600">
            {{ inactivePackages }}
          </p>

          <p class="mt-1 text-sm text-slate-500">
            Inactive
          </p>
        </div>

      </div>


      <!-- ================= FILTERS ================= -->
      <div class="mb-5 grid gap-3 rounded-2xl bg-white p-4 shadow-sm md:grid-cols-3">

        <input
          v-model="search"
          type="text"
          placeholder="Search package..."
          :class="inputClass"
        />

        <select
          v-model="filterCategory"
          :class="inputClass"
        >
          <option value="">
            All Countries
          </option>

          <option
            v-for="category in categories"
            :key="category.id"
            :value="String(category.id)"
          >
            {{ localized(category.country_name) }}
          </option>
        </select>

        <select
          v-model="filterStatus"
          :class="inputClass"
        >
          <option value="">
            All Status
          </option>

          <option value="active">
            Active
          </option>

          <option value="inactive">
            Inactive
          </option>
        </select>

      </div>


      <!-- ================= LOADING ================= -->
      <div
        v-if="loading"
        class="rounded-2xl bg-white p-10 text-center shadow-sm"
      >
        <div
          class="mx-auto h-8 w-8 animate-spin rounded-full
                 border-4 border-slate-200 border-t-slate-800"
        ></div>

        <p class="mt-3 text-sm text-slate-500">
          Loading packages...
        </p>
      </div>


      <!-- ================= EMPTY ================= -->
      <div
        v-else-if="filteredPackages.length === 0"
        class="rounded-2xl bg-white p-10 text-center shadow-sm"
      >
        <h3 class="font-semibold text-slate-800">
          No packages found
        </h3>

        <p class="mt-1 text-sm text-slate-500">
          Try another search or add a package.
        </p>
      </div>


      <!-- ================= DESKTOP TABLE ================= -->
      <div
        v-else
        class="hidden overflow-hidden rounded-2xl bg-white shadow-sm md:block"
      >
        <div class="overflow-x-auto">

          <table class="w-full">

            <thead class="border-b bg-slate-50">
              <tr class="text-left text-xs text-slate-500">

                <th class="px-5 py-4">
                  Package
                </th>

                <th class="px-5 py-4">
                  Country
                </th>

                <th class="px-5 py-4">
                  Price
                </th>

                <th class="px-5 py-4">
                  Duration
                </th>

                <th class="px-5 py-4">
                  Order
                </th>

                <th class="px-5 py-4">
                  Status
                </th>

                <th class="px-5 py-4 text-right">
                  Actions
                </th>

              </tr>
            </thead>


            <tbody class="divide-y">

              <tr
                v-for="pkg in filteredPackages"
                :key="pkg.id"
                class="hover:bg-slate-50"
              >

                <!-- Package -->
                <td class="px-5 py-4">

                  <div class="flex items-center gap-3">

                    <img
                      v-if="pkg.package_image"
                      :src="pkg.package_image"
                      :alt="localized(pkg.package_name)"
                      class="h-14 w-20 rounded-lg object-cover"
                    />

                    <div
                      v-else
                      class="flex h-14 w-20 items-center justify-center
                             rounded-lg bg-slate-100 text-xs text-slate-400"
                    >
                      No Image
                    </div>

                    <div>
                      <p class="font-semibold text-slate-900">
                        {{ localized(pkg.package_name) }}
                      </p>

                      <p class="text-xs text-slate-400">
                        /{{ pkg.slug }}
                      </p>
                    </div>

                  </div>

                </td>


                <!-- Country -->
                <td class="px-5 py-4 text-sm text-slate-600">
                  {{ categoryName(pkg.category_id) }}
                </td>


                <!-- Price -->
                <td class="px-5 py-4 text-sm font-semibold">
                  {{ localized(pkg.package_price) || '-' }}
                </td>


                <!-- Duration -->
                <td class="px-5 py-4 text-sm text-slate-600">
                  {{ localized(pkg.package_duration) || '-' }}
                </td>


                <!-- Order -->
                <td class="px-5 py-4">
                  <span
                    class="rounded-lg bg-slate-100 px-3 py-2 text-sm font-semibold"
                  >
                    {{ pkg.order ?? 0 }}
                  </span>
                </td>


                <!-- Status -->
                <td class="px-5 py-4">

                  <button
                    type="button"
                    :disabled="statusUpdating === pkg.id"
                    @click="toggleStatus(pkg)"
                    class="flex items-center gap-2 disabled:opacity-50"
                  >

                    <span
                      class="relative h-6 w-11 rounded-full"
                      :class="
                        pkg.is_active
                          ? 'bg-emerald-500'
                          : 'bg-slate-300'
                      "
                    >
                      <span
                        class="absolute top-1 h-4 w-4 rounded-full
                               bg-white shadow transition-all"
                        :class="
                          pkg.is_active
                            ? 'left-6'
                            : 'left-1'
                        "
                      ></span>
                    </span>

                    <span
                      class="text-xs font-semibold"
                      :class="
                        pkg.is_active
                          ? 'text-emerald-600'
                          : 'text-slate-500'
                      "
                    >
                      {{ pkg.is_active ? 'Active' : 'Inactive' }}
                    </span>

                  </button>

                </td>


                <!-- Actions -->
                <td class="px-5 py-4">

                  <div class="flex justify-end gap-2">

                    <button
                      type="button"
                      @click="editPackage(pkg)"
                      class="rounded-lg border px-3 py-2 text-xs font-semibold
                             hover:bg-slate-900 hover:text-white"
                    >
                      Edit
                    </button>

                    <button
                      type="button"
                      @click="deletePackage(pkg)"
                      class="rounded-lg border border-red-200 px-3 py-2
                             text-xs font-semibold text-red-600
                             hover:bg-red-600 hover:text-white"
                    >
                      Delete
                    </button>

                  </div>

                </td>

              </tr>

            </tbody>

          </table>

        </div>
      </div>


      <!-- ================= MOBILE ================= -->
      <div
        v-if="!loading && filteredPackages.length"
        class="space-y-4 md:hidden"
      >

        <div
          v-for="pkg in filteredPackages"
          :key="pkg.id"
          class="overflow-hidden rounded-2xl bg-white shadow-sm"
        >

          <img
            v-if="pkg.package_image"
            :src="pkg.package_image"
            :alt="localized(pkg.package_name)"
            class="h-40 w-full object-cover"
          />

          <div class="p-4">

            <div class="flex justify-between gap-3">

              <div>
                <h3 class="font-semibold text-slate-900">
                  {{ localized(pkg.package_name) }}
                </h3>

                <p class="text-sm text-slate-500">
                  {{ categoryName(pkg.category_id) }}
                </p>
              </div>

              <span
                class="rounded-full px-3 py-1 text-xs font-medium"
                :class="
                  pkg.is_active
                    ? 'bg-emerald-50 text-emerald-600'
                    : 'bg-slate-100 text-slate-500'
                "
              >
                {{ pkg.is_active ? 'Active' : 'Inactive' }}
              </span>

            </div>

            <p class="mt-3 text-sm font-semibold">
              {{ localized(pkg.package_price) || '-' }}

              <span class="font-normal text-slate-400">
                · {{ localized(pkg.package_duration) || '-' }}
              </span>
            </p>

            <div class="mt-4 flex gap-2">

              <button
                type="button"
                @click="editPackage(pkg)"
                class="flex-1 rounded-lg border py-2 text-sm font-semibold"
              >
                Edit
              </button>

              <button
                type="button"
                @click="deletePackage(pkg)"
                class="flex-1 rounded-lg border border-red-200
                       py-2 text-sm font-semibold text-red-600"
              >
                Delete
              </button>

            </div>

          </div>

        </div>

      </div>

    </div>


    <!-- ================= MODAL ================= -->
    <div
      v-if="showModal"
      class="fixed inset-0 z-50 flex items-center justify-center
             bg-black/50 p-4"
    >

      <div
        class="max-h-[95vh] w-full max-w-4xl overflow-y-auto
               rounded-2xl bg-white shadow-2xl"
      >

        <!-- Modal Header -->
        <div
          class="flex items-center justify-between border-b
                 px-6 py-4"
        >
          <div>
            <h2 class="text-xl font-bold">
              {{ editingId ? 'Edit Package' : 'Add Package' }}
            </h2>

            <p class="text-sm text-slate-500">
              {{ editingId
                ? 'Update package information.'
                : 'Create a new tour package.'
              }}
            </p>
          </div>

          <button
            type="button"
            @click="closeModal"
            class="text-2xl text-slate-400 hover:text-slate-700"
          >
            ×
          </button>
        </div>


        <!-- Form -->
        <form
          @submit.prevent="submitForm"
          class="space-y-6 p-6"
        >

          <!-- Country -->
          <div>
            <label :class="labelClass">
              Country
            </label>

            <select
              v-model="form.category_id"
              :class="inputClass"
            >
              <option value="">
                Select Country
              </option>

              <option
                v-for="category in categories"
                :key="category.id"
                :value="String(category.id)"
              >
                {{ localized(category.country_name) }}
              </option>
            </select>

            <p
              v-if="errors.category_id"
              :class="errorClass"
            >
              {{ errors.category_id }}
            </p>
          </div>


          <!-- Name -->
          <div class="grid gap-4 md:grid-cols-2">

            <div>
              <label :class="labelClass">
                Package Name (English)
              </label>

              <input
                v-model="form.package_name.en"
                type="text"
                :class="inputClass"
              />
            </div>

            <div>
              <label :class="labelClass">
                Package Name (Bangla)
              </label>

              <input
                v-model="form.package_name.bn"
                type="text"
                :class="inputClass"
              />
            </div>

          </div>


          <!-- Price -->
          <div class="grid gap-4 md:grid-cols-2">

            <div>
              <label :class="labelClass">
                Price (English)
              </label>

              <input
                v-model="form.package_price.en"
                type="text"
                placeholder="$500"
                :class="inputClass"
              />
            </div>

            <div>
              <label :class="labelClass">
                Price (Bangla)
              </label>

              <input
                v-model="form.package_price.bn"
                type="text"
                placeholder="৫০,০০০ টাকা"
                :class="inputClass"
              />
            </div>

          </div>


          <!-- Duration -->
          <div class="grid gap-4 md:grid-cols-2">

            <div>
              <label :class="labelClass">
                Duration (English)
              </label>

              <input
                v-model="form.package_duration.en"
                type="text"
                placeholder="7 Days / 6 Nights"
                :class="inputClass"
              />
            </div>

            <div>
              <label :class="labelClass">
                Duration (Bangla)
              </label>

              <input
                v-model="form.package_duration.bn"
                type="text"
                placeholder="৭ দিন / ৬ রাত"
                :class="inputClass"
              />
            </div>

          </div>

          <!-- Description -->
          <div class="grid gap-4 md:grid-cols-2">

            <div>
              <label :class="labelClass">
                Description (English)
              </label>

              <textarea
                v-model="form.package_destination.en"
                type="text"
                placeholder="english"
                :class="inputClass"
              ></textarea>
            </div>

            <div>
              <label :class="labelClass">
                Description (Bangla)
              </label>

              <textarea
                v-model="form.package_destination.bn"
                type="text"
                placeholder="bangla"
                :class="inputClass"
              ></textarea>
            </div>

          </div>


          <!-- Header -->
          <div class="grid gap-4 md:grid-cols-2">

            <div>
              <label :class="labelClass">
                Header (English)
              </label>

              <input
                v-model="form.header.en"
                type="text"
                :class="inputClass"
              />
            </div>

            <div>
              <label :class="labelClass">
                Header (Bangla)
              </label>

              <input
                v-model="form.header.bn"
                type="text"
                :class="inputClass"
              />
            </div>

          </div>


          <!-- Sub Header -->
          <div class="grid gap-4 md:grid-cols-2">

            <div>
              <label :class="labelClass">
                Sub Header (English)
              </label>

              <textarea
                v-model="form.sub_header.en"
                rows="3"
                :class="inputClass"
              ></textarea>
            </div>

            <div>
              <label :class="labelClass">
                Sub Header (Bangla)
              </label>

              <textarea
                v-model="form.sub_header.bn"
                rows="3"
                :class="inputClass"
              ></textarea>
            </div>

          </div>


          <!-- Hero title -->
          <div class="grid gap-4 md:grid-cols-2">

            <div>
              <label :class="labelClass">
                Hero Title (English)
              </label>

              <input
                v-model="form.hero_image_title.en"
                type="text"
                :class="inputClass"
              />
            </div>

            <div>
              <label :class="labelClass">
                Hero Title (Bangla)
              </label>

              <input
                v-model="form.hero_image_title.bn"
                type="text"
                :class="inputClass"
              />
            </div>

          </div>


          <!-- Hero Button -->
          <div class="grid gap-4 md:grid-cols-2">

            <div>
              <label :class="labelClass">
                Hero Button (English)
              </label>

              <input
                v-model="form.hero_image_btn.en"
                type="text"
                placeholder="Book Now"
                :class="inputClass"
              />
            </div>

            <div>
              <label :class="labelClass">
                Hero Button (Bangla)
              </label>

              <input
                v-model="form.hero_image_btn.bn"
                type="text"
                placeholder="বুক করুন"
                :class="inputClass"
              />
            </div>

          </div>


          <!-- Package Image Title -->
          <div class="grid gap-4 md:grid-cols-2">

            <div>
              <label :class="labelClass">
                Image Title (English)
              </label>

              <input
                v-model="form.package_image_title.en"
                type="text"
                :class="inputClass"
              />
            </div>

            <div>
              <label :class="labelClass">
                Image Title (Bangla)
              </label>

              <input
                v-model="form.package_image_title.bn"
                type="text"
                :class="inputClass"
              />
            </div>

          </div>


          <!-- Images -->
          <div class="grid gap-4 md:grid-cols-3">

            <div
              v-for="image in imageFields"
              :key="image.type"
            >

              <label :class="labelClass">
                {{ image.label }}
              </label>

              <label
                class="flex h-36 cursor-pointer items-center
                       justify-center overflow-hidden rounded-xl
                       border-2 border-dashed border-slate-300
                       bg-slate-50 hover:border-amber-400"
              >

                <img
                  v-if="previews[image.type]"
                  :src="previews[image.type]!"
                  class="h-full w-full object-cover"
                  alt=""
                />

                <span v-else class="text-center text-xs text-slate-400" >
                  Click to upload
                  <br />
                  JPG, PNG or WebP
                </span>

                <input
                  type="file"
                  accept="image/jpeg,image/png,image/webp"
                  class="hidden"
                  @change="handleImage($event, image.type)"
                />

              </label>

              <p
                v-if="errors[image.type]"
                :class="errorClass"
              >
                {{ errors[image.type] }}
              </p>

            </div>

          </div>


          <!-- Slug / Order / Status -->
          <div class="grid gap-4 md:grid-cols-3">

            <div>
              <label :class="labelClass">
                Slug
              </label>

              <input v-model="form.slug" type="text" placeholder="Auto generate"
                :class="inputClass"
              />
            </div>

            <div>
              <label :class="labelClass">
                Display Order
              </label>

              <input
                v-model.number="form.order"
                type="number"
                min="0"
                :class="inputClass"
              />
            </div>

            <div>
              <label :class="labelClass">
                Status
              </label>

              <select
                v-model="form.is_active"
                :class="inputClass"
              >
                <option :value="true">
                  Active
                </option>

                <option :value="false">
                  Inactive
                </option>
              </select>
            </div>

          </div>


          <!-- Buttons -->
          <div
            class="flex justify-end gap-3 border-t pt-5"
          >

            <button
              type="button"
              @click="closeModal"
              class="rounded-xl border px-5 py-2.5 text-sm font-semibold"
            >
              Cancel
            </button>

            <button
              type="submit"
              :disabled="saving"
              class="rounded-xl bg-slate-900 px-6 py-2.5
                     text-sm font-semibold text-white
                     disabled:opacity-50"
            >
              {{ saving
                ? 'Saving...'
                : editingId
                  ? 'Update Package'
                  : 'Create Package'
              }}
            </button>

          </div>

        </form>

      </div>

    </div>

  </div>
</template>


<script setup lang="ts">

import { computed, onMounted, reactive, ref } from 'vue'
import api from '@/services/api'


// ======================================================
// TYPES
// ======================================================

interface Localized {
  en: string
  bn: string
}

interface Category {
  id: number
  country_name: Localized
}

interface TourPackage {
  id: number
  category_id: number | null

  package_name: Localized
  package_price: Localized | null
  package_duration: Localized | null
  package_destination: Localized | null

  header: Localized | null
  sub_header: Localized | null
  hero_image_title: Localized | null
  hero_image_btn: Localized | null
  package_image_title: Localized | null

  slug: string

  hero_image: string | null
  package_image: string | null
  package_map_image: string | null

  order: number
  is_active: boolean
}


type ImageType =
  | 'hero_image'
  | 'package_image'
  | 'package_map_image'


// ======================================================
// BASIC STATE
// ======================================================

const loading = ref(false)
const saving = ref(false)

const showModal = ref(false)
const editingId = ref<number | null>(null)

const search = ref('')
const filterCategory = ref('')
const filterStatus = ref('')

const statusUpdating = ref<number | null>(null)

const successMessage = ref('')
const errorMessage = ref('')

const errors = ref<Record<string, string>>({})


// ======================================================
// DATA
// ======================================================

const tourPackages = ref<TourPackage[]>([])
const categories = ref<Category[]>([])


// ======================================================
// FORM
// ======================================================

const emptyForm = () => ({
  category_id: '',

  package_name: {
    en: '',
    bn: '',
  },

  package_price: {
    en: '',
    bn: '',
  },

  package_duration: {
    en: '',
    bn: '',
  },

  package_destination: {
    en: '',
    bn: '',
  },


  header: {
    en: '',
    bn: '',
  },

  sub_header: {
    en: '',
    bn: '',
  },

  hero_image_title: {
    en: '',
    bn: '',
  },

  hero_image_btn: {
    en: '',
    bn: '',
  },

  package_image_title: {
    en: '',
    bn: '',
  },

  slug: '',

  order: 0,

  is_active: true,
})


const form = reactive(emptyForm())


// ======================================================
// IMAGES
// ======================================================

const files = reactive<Record<ImageType, File | null>>({
  hero_image: null,
  package_image: null,
  package_map_image: null,
})


const previews = reactive<Record<ImageType, string | null>>({
  hero_image: null,
  package_image: null,
  package_map_image: null,
})


const imageFields = [
  {
    type: 'hero_image' as ImageType,
    label: 'Hero Image',
  },

  {
    type: 'package_image' as ImageType,
    label: 'Package Image',
  },

  {
    type: 'package_map_image' as ImageType,
    label: 'Map Image',
  },
]


// ======================================================
// STYLES
// ======================================================

const inputClass =
  'w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none focus:border-amber-400 focus:ring-4 focus:ring-amber-100'

const labelClass =
  'mb-1.5 block text-sm font-medium text-slate-700'

const errorClass =
  'mt-1 text-xs text-red-600'


// ======================================================
// HELPERS
// ======================================================

function localized(
  value: Localized | null | undefined
): string {
  return value?.en || value?.bn || ''
}


function categoryName(
  categoryId: number | null
): string {
  const category = categories.value.find(
    item => item.id === categoryId
  )

  return category
    ? localized(category.country_name)
    : '-'
}


// ======================================================
// COMPUTED
// ======================================================

const activePackages = computed(() =>
  tourPackages.value.filter(
    item => item.is_active
  ).length
)


const inactivePackages = computed(() =>
  tourPackages.value.filter(
    item => !item.is_active
  ).length
)


const filteredPackages = computed(() => {

  const keyword = search.value
    .trim()
    .toLowerCase()

  return tourPackages.value.filter(pkg => {

    const name = localized(
      pkg.package_name
    ).toLowerCase()

    const matchesSearch =
      !keyword ||
      name.includes(keyword) ||
      pkg.slug.toLowerCase().includes(keyword)

    const matchesCategory =
      !filterCategory.value ||
      String(pkg.category_id) ===
        filterCategory.value

    const matchesStatus =
      !filterStatus.value ||
      (
        filterStatus.value === 'active'
          ? pkg.is_active
          : !pkg.is_active
      )

    return (
      matchesSearch &&
      matchesCategory &&
      matchesStatus
    )
  })
})


// ======================================================
// LOAD CATEGORIES
// ======================================================

async function loadCategories() {

  try {

    const response = await api.get(
      '/admin/category'
    )

    categories.value =
      response.data?.data ||
      response.data ||
      []

  } catch (error) {

    console.error(
      'Category loading failed:',
      error
    )

  }
}


// ======================================================
// LOAD PACKAGES
// ======================================================

async function loadPackages() {

  loading.value = true
  errorMessage.value = ''

  try {

    const response = await api.get(
      '/admin/tour-package'
    )

    tourPackages.value =
      response.data?.data || []

    tourPackages.value.sort(
      (a, b) =>
        (a.order ?? 0) -
        (b.order ?? 0)
    )

  } catch (error: any) {

    console.error(error)

    errorMessage.value =
      error?.response?.data?.message ||
      'Failed to load packages.'

  } finally {

    loading.value = false

  }
}


// ======================================================
// RESET FORM
// ======================================================

function resetForm() {

  Object.assign(
    form,
    emptyForm()
  )

  editingId.value = null
  errors.value = {}

  files.hero_image = null
  files.package_image = null
  files.package_map_image = null


  previews.hero_image = null
  previews.package_image = null
  previews.package_map_image = null
}


// ======================================================
// CREATE
// ======================================================

function openCreateModal() {

  resetForm()

  showModal.value = true
}


// ======================================================
// EDIT
// ======================================================

function editPackage(pkg: TourPackage) {

  resetForm()

  editingId.value = pkg.id

  form.category_id =
    pkg.category_id
      ? String(pkg.category_id)
      : ''

  form.slug = pkg.slug || ''
  form.order = pkg.order ?? 0
  form.is_active = Boolean(
    pkg.is_active
  )

  form.package_name = {
    en: pkg.package_name?.en || '',
    bn: pkg.package_name?.bn || '',
  }

  form.package_price = {
    en: pkg.package_price?.en || '',
    bn: pkg.package_price?.bn || '',
  }

  form.package_duration = {
    en: pkg.package_duration?.en || '',
    bn: pkg.package_duration?.bn || '',
  }

  form.package_destination = {
    en: pkg.package_destination?.en || '',
    bn: pkg.package_destination?.bn || '',
  }

  form.header = {
    en: pkg.header?.en || '',
    bn: pkg.header?.bn || '',
  }

  form.sub_header = {
    en: pkg.sub_header?.en || '',
    bn: pkg.sub_header?.bn || '',
  }

  form.hero_image_title = {
    en: pkg.hero_image_title?.en || '',
    bn: pkg.hero_image_title?.bn || '',
  }

  form.hero_image_btn = {
    en: pkg.hero_image_btn?.en || '',
    bn: pkg.hero_image_btn?.bn || '',
  }

  form.package_image_title = {
    en: pkg.package_image_title?.en || '',
    bn: pkg.package_image_title?.bn || '',
  }

  previews.hero_image =
    pkg.hero_image || null

  previews.package_image =
    pkg.package_image || null

  previews.package_map_image =
    pkg.package_map_image || null

  showModal.value = true
}


// ======================================================
// CLOSE MODAL
// ======================================================

function closeModal() {

  showModal.value = false

  resetForm()
}


// ======================================================
// IMAGE SELECT
// ======================================================

function handleImage(
  event: Event,
  type: ImageType
) {

  const input =
    event.target as HTMLInputElement

  const file = input.files?.[0]

  if (!file) {
    return
  }

  files[type] = file

  previews[type] =
    URL.createObjectURL(file)
}


// ======================================================
// BUILD FORM DATA
// ======================================================

function buildFormData(): FormData {

  const data = new FormData()

  data.append(
    'category_id',
    form.category_id
  )

  data.append(
    'slug',
    form.slug
  )

  data.append(
    'order',
    String(form.order)
  )

  data.append(
    'is_active',
    form.is_active ? '1' : '0'
  )


  // Localized fields

  const fields = [
    'package_name',
    'package_price',
    'package_duration',
    'package_destination',
    'header',
    'sub_header',
    'hero_image_title',
    'hero_image_btn',
    'package_image_title',
  ] as const


  fields.forEach(field => {

    data.append(
      `${field}[en]`,
      form[field].en
    )

    data.append(
      `${field}[bn]`,
      form[field].bn
    )

  })


  // Images

  if (files.hero_image) {
    data.append(
      'hero_image',
      files.hero_image
    )
  }

  if (files.package_image) {
    data.append(
      'package_image',
      files.package_image
    )
  }

  if (files.package_map_image) {
    data.append(
      'package_map_image',
      files.package_map_image
    )
  }


  return data
}


// ======================================================
// CREATE / UPDATE
// ======================================================

async function submitForm() {

  saving.value = true
  errors.value = {}
  errorMessage.value = ''

  try {

    const data =
      buildFormData()


    // UPDATE

    if (editingId.value) {

      data.append(
        '_method',
        'PUT'
      )

      await api.post(
        `/admin/tour-package/${editingId.value}`,
        data
      )

      successMessage.value =
        'Package updated successfully.'

    }

    // CREATE

    else {

      await api.post(
        '/admin/tour-package',
        data
      )

      successMessage.value =
        'Package created successfully.'
    }


    closeModal()

    await loadPackages()

    setTimeout(() => {
      successMessage.value = ''
    }, 3000)

  } catch (error: any) {

    console.error(error)


    if (
      error?.response?.status === 422
    ) {

      const validationErrors =
        error.response.data.errors || {}

      errors.value =
        Object.fromEntries(
          Object.entries(
            validationErrors
          ).map(
            ([key, value]) => [
              key,
              Array.isArray(value)
                ? String(value[0])
                : String(value),
            ]
          )
        )

      errorMessage.value =
        'Please check the form fields.'

    } else {

      errorMessage.value =
        error?.response?.data?.message ||
        'Something went wrong.'
    }

  } finally {

    saving.value = false

  }
}


// ======================================================
// DELETE
// ======================================================

async function deletePackage(
  pkg: TourPackage
) {

  const confirmed =
    window.confirm(
      `Delete "${localized(pkg.package_name)}"?`
    )

  if (!confirmed) {
    return
  }


  try {

    await api.delete(
      `/admin/tour-package/${pkg.id}`
    )

    await loadPackages()

    successMessage.value =
      'Package deleted successfully.'

  } catch (error: any) {

    console.error(error)

    errorMessage.value =
      error?.response?.data?.message ||
      'Failed to delete package.'
  }
}


// ======================================================
// STATUS
// ======================================================

async function toggleStatus(
  pkg: TourPackage
) {

  if (
    statusUpdating.value !== null
  ) {
    return
  }


  const oldStatus =
    pkg.is_active

  pkg.is_active =
    !oldStatus

  statusUpdating.value =
    pkg.id


  try {

    const data =
      new FormData()

    data.append(
      'category_id',
      pkg.category_id
        ? String(pkg.category_id)
        : ''
    )

    data.append(
      'slug',
      pkg.slug
    )

    data.append(
      'package_name[en]',
      pkg.package_name?.en || ''
    )

    data.append(
      'package_name[bn]',
      pkg.package_name?.bn || ''
    )

    data.append(
      'is_active',
      pkg.is_active ? '1' : '0'
    )

    data.append(
      '_method',
      'PUT'
    )


    await api.post(
      `/admin/tour-package/${pkg.id}`,
      data
    )

    successMessage.value =
      pkg.is_active
        ? 'Package activated.'
        : 'Package deactivated.'

  } catch (error: any) {

    pkg.is_active =
      oldStatus

    errorMessage.value =
      error?.response?.data?.message ||
      'Failed to update status.'

  } finally {

    statusUpdating.value = null

  }
}


// ======================================================
// START
// ======================================================

onMounted(() => {

  loadCategories()
  loadPackages()

})

</script>
```
