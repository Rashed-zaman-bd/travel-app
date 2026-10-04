<template>
  <div class="min-h-screen bg-gray-50 p-4 md:p-6">

    <!-- Header -->
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
      <div>
        <h1 class="text-2xl font-bold text-gray-800">
          Tour Information
        </h1>

        <p class="mt-1 text-sm text-gray-500">
          Manage pickup notes, cancellation policies, included services and other information.
        </p>
      </div>

      <button
        type="button"
        @click="openCreateModal"
        class="inline-flex items-center justify-center gap-2 rounded-lg bg-amber-500 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-amber-600"
      >
        <i class="bi bi-plus-lg"></i>
        Add Information
      </button>
    </div>


    <!-- Stats -->
    <div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-3">

      <div class="rounded-xl bg-white p-5 shadow-sm ring-1 ring-gray-200">
        <p class="text-sm text-gray-500">
          Total
        </p>

        <p class="mt-1 text-2xl font-bold text-gray-800">
          {{ informations.length }}
        </p>
      </div>

      <div class="rounded-xl bg-white p-5 shadow-sm ring-1 ring-gray-200">
        <p class="text-sm text-gray-500">
          Active
        </p>

        <p class="mt-1 text-2xl font-bold text-green-600">
          {{ activeCount }}
        </p>
      </div>

      <div class="rounded-xl bg-white p-5 shadow-sm ring-1 ring-gray-200">
        <p class="text-sm text-gray-500">
          Inactive
        </p>

        <p class="mt-1 text-2xl font-bold text-red-500">
          {{ inactiveCount }}
        </p>
      </div>

    </div>


    <!-- Filters -->
    <div class="mb-6 rounded-xl bg-white p-4 shadow-sm ring-1 ring-gray-200">

      <div class="grid grid-cols-1 gap-4 md:grid-cols-3">

        <!-- Search -->
        <div>
          <label class="mb-1 block text-sm font-medium text-gray-700">
            Search
          </label>

          <div class="relative">
            <i
              class="bi bi-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"
            ></i>

            <input
              v-model="search"
              type="text"
              placeholder="Search information..."
              class="w-full rounded-lg border border-gray-300 py-2.5 pl-10 pr-3 text-sm outline-none transition focus:border-amber-500 focus:ring-1 focus:ring-amber-500"
            />
          </div>
        </div>


        <!-- Package -->
        <div>
          <label class="mb-1 block text-sm font-medium text-gray-700">
            Tour Package
          </label>

          <select
            v-model="selectedPackage"
            class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm outline-none transition focus:border-amber-500 focus:ring-1 focus:ring-amber-500"
          >
            <option value="">
              All Packages
            </option>

            <option
              v-for="packageItem in packages"
              :key="packageItem.id"
              :value="String(packageItem.id)"
            >
              {{ getName(packageItem.package_name) }}
            </option>
          </select>
        </div>


        <!-- Status -->
        <div>
          <label class="mb-1 block text-sm font-medium text-gray-700">
            Status
          </label>

          <select
            v-model="selectedStatus"
            class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm outline-none transition focus:border-amber-500 focus:ring-1 focus:ring-amber-500"
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

      </div>

    </div>


    <!-- Loading -->
    <div
      v-if="loading"
      class="rounded-xl bg-white p-12 text-center shadow-sm ring-1 ring-gray-200"
    >
      <div
        class="mx-auto h-8 w-8 animate-spin rounded-full border-4 border-gray-200 border-t-amber-500"
      ></div>

      <p class="mt-3 text-sm text-gray-500">
        Loading information...
      </p>
    </div>


    <!-- Table -->
    <div
      v-else
      class="overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-200"
    >

      <div class="overflow-x-auto">

        <table class="min-w-full divide-y divide-gray-200">

          <thead class="bg-gray-50">

            <tr>

              <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                #
              </th>

              <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                Title
              </th>

              <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                Tour Package
              </th>

              <th class="px-5 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-500">
                Order
              </th>

              <th class="px-5 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-500">
                Status
              </th>

              <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500">
                Actions
              </th>

            </tr>

          </thead>


          <tbody class="divide-y divide-gray-100">

            <tr
              v-for="(item, index) in filteredInformations"
              :key="item.id"
              class="transition hover:bg-gray-50"
            >

              <!-- Number -->
              <td class="whitespace-nowrap px-5 py-4 text-sm text-gray-500">
                {{ index + 1 }}
              </td>


              <!-- Title -->
              <td class="px-5 py-4">

                <div class="max-w-xs">

                  <p class="font-semibold text-gray-800">
                    {{ getName(item.title) }}
                  </p>

                  <p
                    v-if="item.title?.en && item.title?.bn"
                    class="mt-1 text-xs text-gray-400"
                  >
                    {{ item.title.en }}
                  </p>

                </div>

              </td>


              <!-- Package -->
              <td class="whitespace-nowrap px-5 py-4 text-sm text-gray-600">
                {{ getPackageName(item) }}
              </td>


              <!-- Order -->
              <td class="whitespace-nowrap px-5 py-4 text-center text-sm text-gray-600">
                {{ item.order }}
              </td>


              <!-- Status -->
              <td class="whitespace-nowrap px-5 py-4 text-center">

                <button
                  type="button"
                  @click="toggleStatus(item)"
                  :disabled="statusLoading === item.id"
                  class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold transition"
                  :class="
                    item.is_active
                      ? 'bg-green-100 text-green-700 hover:bg-green-200'
                      : 'bg-red-100 text-red-700 hover:bg-red-200'
                  "
                >
                  <span
                    class="mr-1.5 h-1.5 w-1.5 rounded-full"
                    :class="
                      item.is_active
                        ? 'bg-green-500'
                        : 'bg-red-500'
                    "
                  ></span>

                  {{ item.is_active ? 'Active' : 'Inactive' }}
                </button>

              </td>


              <!-- Actions -->
              <td class="whitespace-nowrap px-5 py-4 text-right">

                <div class="flex justify-end gap-2">

                  <button
                    type="button"
                    @click="openEditModal(item)"
                    class="rounded-lg p-2 text-blue-600 transition hover:bg-blue-50"
                    title="Edit"
                  >
                    <i class="bi bi-pencil-square"></i>
                  </button>

                  <button
                    type="button"
                    @click="deleteInformation(item)"
                    class="rounded-lg p-2 text-red-600 transition hover:bg-red-50"
                    title="Delete"
                  >
                    <i class="bi bi-trash3"></i>
                  </button>

                </div>

              </td>

            </tr>


            <!-- Empty -->
            <tr v-if="filteredInformations.length === 0">

              <td
                colspan="6"
                class="px-5 py-12 text-center"
              >

                <div class="flex flex-col items-center">

                  <i class="bi bi-info-circle text-4xl text-gray-300"></i>

                  <p class="mt-3 font-medium text-gray-500">
                    No tour information found.
                  </p>

                  <p class="mt-1 text-sm text-gray-400">
                    Try changing your search or filter.
                  </p>

                </div>

              </td>

            </tr>

          </tbody>

        </table>

      </div>

    </div>


    <!-- Modal -->
    <div
      v-if="showModal"
      class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4"
      @click.self="closeModal"
    >

      <div
        class="max-h-[95vh] w-full max-w-3xl overflow-y-auto rounded-2xl bg-white shadow-2xl"
      >

        <!-- Modal Header -->
        <div class="sticky top-0 z-10 flex items-center justify-between border-b bg-white px-6 py-4">

          <div>
            <h2 class="text-lg font-bold text-gray-800">
              {{ editingItem ? 'Edit Tour Information' : 'Add Tour Information' }}
            </h2>

            <p class="mt-1 text-xs text-gray-500">
              Add information for a tour package.
            </p>
          </div>

          <button
            type="button"
            @click="closeModal"
            class="rounded-lg p-2 text-gray-400 hover:bg-gray-100 hover:text-gray-600"
          >
            <i class="bi bi-x-lg"></i>
          </button>

        </div>


        <!-- Form -->
        <form
          @submit.prevent="saveInformation"
          class="space-y-6 p-6"
        >

          <!-- Tour Package -->
          <div>

            <label class="mb-1.5 block text-sm font-semibold text-gray-700">
              Tour Package
              <span class="text-red-500">*</span>
            </label>

            <select
              v-model="form.tour_package_id"
              required
              class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm outline-none transition focus:border-amber-500 focus:ring-1 focus:ring-amber-500"
            >

              <option value="">
                Select Tour Package
              </option>

              <option
                v-for="packageItem in packages"
                :key="packageItem.id"
                :value="packageItem.id"
              >
                {{ getName(packageItem.package_name) }}
              </option>

            </select>

            <p
              v-if="errors.tour_package_id"
              class="mt-1 text-xs text-red-500"
            >
              {{ errors.tour_package_id }}
            </p>

          </div>


          <!-- English / Bangla Title -->
          <div class="grid grid-cols-1 gap-5 md:grid-cols-2">

            <div>

              <label class="mb-1.5 block text-sm font-semibold text-gray-700">
                English Title
                <span class="text-red-500">*</span>
              </label>

              <input
                v-model="form.title.en"
                type="text"
                required
                maxlength="1000"
                placeholder="Example: Pickup Note"
                class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm outline-none transition focus:border-amber-500 focus:ring-1 focus:ring-amber-500"
              />

              <p
                v-if="errors['title.en']"
                class="mt-1 text-xs text-red-500"
              >
                {{ errors['title.en'] }}
              </p>

            </div>


            <div>

              <label class="mb-1.5 block text-sm font-semibold text-gray-700">
                Bangla Title
              </label>

              <input
                v-model="form.title.bn"
                type="text"
                maxlength="1000"
                placeholder="উদাহরণ: পিকআপ নোট"
                class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm outline-none transition focus:border-amber-500 focus:ring-1 focus:ring-amber-500"
              />

            </div>

          </div>


          <!-- English Content -->
          <div>

            <label class="mb-1.5 block text-sm font-semibold text-gray-700">
              English Content
              <span class="text-red-500">*</span>
            </label>

            <textarea
              v-model="form.content.en"
              rows="7"
              required
              maxlength="10000"
              placeholder="Enter information..."
              class="w-full resize-y rounded-lg border border-gray-300 px-3 py-2.5 text-sm leading-6 outline-none transition focus:border-amber-500 focus:ring-1 focus:ring-amber-500"
            ></textarea>

            <p
              v-if="errors['content.en']"
              class="mt-1 text-xs text-red-500"
            >
              {{ errors['content.en'] }}
            </p>

          </div>


          <!-- Bangla Content -->
          <div>

            <label class="mb-1.5 block text-sm font-semibold text-gray-700">
              Bangla Content
            </label>

            <textarea
              v-model="form.content.bn"
              rows="7"
              maxlength="10000"
              placeholder="বাংলায় তথ্য লিখুন..."
              class="w-full resize-y rounded-lg border border-gray-300 px-3 py-2.5 text-sm leading-6 outline-none transition focus:border-amber-500 focus:ring-1 focus:ring-amber-500"
            ></textarea>

          </div>


          <!-- Order + Status -->
          <div class="grid grid-cols-1 gap-5 md:grid-cols-2">

            <div>

              <label class="mb-1.5 block text-sm font-semibold text-gray-700">
                Order
              </label>

              <input
                v-model.number="form.order"
                type="number"
                min="0"
                class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm outline-none transition focus:border-amber-500 focus:ring-1 focus:ring-amber-500"
              />

            </div>


            <div>

              <label class="mb-1.5 block text-sm font-semibold text-gray-700">
                Status
              </label>

              <select
                v-model="form.is_active"
                class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm outline-none transition focus:border-amber-500 focus:ring-1 focus:ring-amber-500"
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


          <!-- Footer -->
          <div class="flex justify-end gap-3 border-t pt-5">

            <button
              type="button"
              @click="closeModal"
              class="rounded-lg border border-gray-300 px-5 py-2.5 text-sm font-semibold text-gray-700 transition hover:bg-gray-50"
            >
              Cancel
            </button>

            <button
              type="submit"
              :disabled="saving"
              class="inline-flex items-center gap-2 rounded-lg bg-amber-500 px-6 py-2.5 text-sm font-semibold text-white transition hover:bg-amber-600 disabled:cursor-not-allowed disabled:opacity-60"
            >

              <span
                v-if="saving"
                class="h-4 w-4 animate-spin rounded-full border-2 border-white/40 border-t-white"
              ></span>

              {{ saving ? 'Saving...' : editingItem ? 'Update' : 'Save' }}

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


/*
|--------------------------------------------------------------------------
| Types
|--------------------------------------------------------------------------
*/

interface LocalizedValue {
  en: string
  bn: string
}

interface TourPackage {
  id: number
  package_name: LocalizedValue | string
}

interface TourInformation {
  id: number
  tour_package_id: number
  tour_package?: TourPackage
  title: LocalizedValue
  content: LocalizedValue
  order: number
  is_active: boolean
}

interface FormData {
  tour_package_id: number | string
  title: LocalizedValue
  content: LocalizedValue
  order: number
  is_active: boolean
}


/*
|--------------------------------------------------------------------------
| State
|--------------------------------------------------------------------------
*/

const informations = ref<TourInformation[]>([])

const packages = ref<TourPackage[]>([])

const loading = ref(false)

const saving = ref(false)

const statusLoading = ref<number | null>(null)

const showModal = ref(false)

const editingItem = ref<TourInformation | null>(null)

const search = ref('')

const selectedPackage = ref('')

const selectedStatus = ref('')

const errors = ref<Record<string, string>>({})


/*
|--------------------------------------------------------------------------
| Form
|--------------------------------------------------------------------------
*/

const emptyForm = (): FormData => ({
  tour_package_id: '',
  title: {
    en: '',
    bn: '',
  },
  content: {
    en: '',
    bn: '',
  },
  order: 0,
  is_active: true,
})

const form = reactive<FormData>(emptyForm())


/*
|--------------------------------------------------------------------------
| Helpers
|--------------------------------------------------------------------------
*/

const getName = (
  value: LocalizedValue | string | null | undefined
): string => {
  if (!value) return ''

  if (typeof value === 'string') {
    return value
  }

  return value.en || value.bn || ''
}


const getPackageName = (
  item: TourInformation
): string => {
  return getName(item.tour_package?.package_name)
}


/*
|--------------------------------------------------------------------------
| Computed
|--------------------------------------------------------------------------
*/

const filteredInformations = computed(() => {
  const term = search.value.trim().toLowerCase()

  return informations.value.filter((item) => {

    const titleEn = item.title?.en?.toLowerCase() || ''
    const titleBn = item.title?.bn?.toLowerCase() || ''

    const packageName = getPackageName(item).toLowerCase()

    const matchesSearch =
      !term ||
      titleEn.includes(term) ||
      titleBn.includes(term) ||
      packageName.includes(term)

    const matchesPackage =
      !selectedPackage.value ||
      String(item.tour_package_id) === selectedPackage.value

    const matchesStatus =
      !selectedStatus.value ||
      (
        selectedStatus.value === 'active'
          ? item.is_active
          : !item.is_active
      )

    return (
      matchesSearch &&
      matchesPackage &&
      matchesStatus
    )
  })
})


const activeCount = computed(() => {
  return informations.value.filter(
    item => item.is_active
  ).length
})


const inactiveCount = computed(() => {
  return informations.value.filter(
    item => !item.is_active
  ).length
})


/*
|--------------------------------------------------------------------------
| Load Tour Packages
|--------------------------------------------------------------------------
*/

const fetchPackages = async () => {
  try {
    const response = await api.get('/tour-package')

    packages.value =
      response.data?.data ||
      response.data ||
      []

  } catch (error) {
    console.error(
      'Failed to load tour packages:',
      error
    )
  }
}


/*
|--------------------------------------------------------------------------
| Load Information
|--------------------------------------------------------------------------
*/

const fetchInformations = async () => {

  loading.value = true

  try {

    const response = await api.get(
      '/admin/tour-information',
      {
        params: {
          all_locales: true,
        },
      }
    )

    informations.value =
      response.data?.data ||
      response.data ||
      []

  } catch (error) {

    console.error(
      'Failed to load tour information:',
      error
    )

  } finally {

    loading.value = false

  }
}


/*
|--------------------------------------------------------------------------
| Create Modal
|--------------------------------------------------------------------------
*/

const openCreateModal = () => {

  editingItem.value = null

  Object.assign(
    form,
    emptyForm()
  )

  errors.value = {}

  showModal.value = true
}


/*
|--------------------------------------------------------------------------
| Edit Modal
|--------------------------------------------------------------------------
*/

const openEditModal = (
  item: TourInformation
) => {

  editingItem.value = item

  Object.assign(form, {
    tour_package_id: item.tour_package_id,

    title: {
      en: item.title?.en || '',
      bn: item.title?.bn || '',
    },

    content: {
      en: item.content?.en || '',
      bn: item.content?.bn || '',
    },

    order: item.order ?? 0,

    is_active: item.is_active ?? true,
  })

  errors.value = {}

  showModal.value = true
}


/*
|--------------------------------------------------------------------------
| Close Modal
|--------------------------------------------------------------------------
*/

const closeModal = () => {

  if (saving.value) return

  showModal.value = false

  editingItem.value = null

  errors.value = {}

}


/*
|--------------------------------------------------------------------------
| Save
|--------------------------------------------------------------------------
*/

const saveInformation = async () => {
  saving.value = true
  errors.value = {}

  try {
    const payload = {
      tour_package_id: Number(form.tour_package_id),

      title: {
        en: form.title.en,
        bn: form.title.bn || null,
      },

      content: {
        en: form.content.en,
        bn: form.content.bn || null,
      },

      order: Number(form.order) || 0,

      is_active: Boolean(form.is_active),
    }

    // Update
    if (editingItem.value) {
      await api.put(
        `/admin/tour-information/${editingItem.value.id}`,
        payload
      )
    }

    // Create
    else {
      await api.post(
        '/admin/tour-information',
        payload
      )
    }

    // IMPORTANT:
    // Turn saving off before calling closeModal()
    saving.value = false

    // Close modal after successful save
    closeModal()

    // Refresh table
    await fetchInformations()

  } catch (error: any) {
    console.error(
      'Failed to save tour information:',
      error
    )

    if (error.response?.status === 422) {
      const validationErrors =
        error.response.data?.errors || {}

      const mappedErrors: Record<string, string> = {}

      Object.keys(validationErrors).forEach((key) => {
        mappedErrors[key] =
          validationErrors[key]?.[0] || ''
      })

      errors.value = mappedErrors

    } else {
      alert(
        error.response?.data?.message ||
        'Something went wrong.'
      )
    }

    saving.value = false
  }
}

/*
|--------------------------------------------------------------------------
| Toggle Status
|--------------------------------------------------------------------------
*/

const toggleStatus = async (
  item: TourInformation
) => {

  statusLoading.value = item.id

  try {

    await api.patch(
      `/admin/tour-information/${item.id}`,
      {
        tour_package_id: item.tour_package_id,

        title: item.title,

        content: item.content,

        order: item.order,

        is_active: !item.is_active,
      }
    )

    item.is_active = !item.is_active

  } catch (error: any) {

    console.error(
      'Failed to update status:',
      error
    )

    alert(
      error.response?.data?.message ||
      'Failed to update status.'
    )

  } finally {

    statusLoading.value = null

  }
}


/*
|--------------------------------------------------------------------------
| Delete
|--------------------------------------------------------------------------
*/

const deleteInformation = async (
  item: TourInformation
) => {

  const confirmed = window.confirm(
    `Are you sure you want to delete "${getName(item.title)}"?`
  )

  if (!confirmed) return

  try {

    await api.delete(
      `/admin/tour-information/${item.id}`
    )

    informations.value =
      informations.value.filter(
        information =>
          information.id !== item.id
      )

  } catch (error: any) {

    console.error(
      'Failed to delete information:',
      error
    )

    alert(
      error.response?.data?.message ||
      'Failed to delete information.'
    )

  }

}


/*
|--------------------------------------------------------------------------
| Initial Load
|--------------------------------------------------------------------------
*/

onMounted(() => {

  fetchInformations()

  fetchPackages()

})
</script>