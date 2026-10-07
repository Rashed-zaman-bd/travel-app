<template>
  <div class="mx-auto w-full max-w-7xl p-4 sm:p-6">
    <!-- Header -->
    <div class="mb-5 flex flex-wrap items-center justify-between gap-3">
      <h1 class="text-2xl font-bold text-gray-800">
        Reviews
        <span class="text-base font-normal text-gray-500">({{ total }})</span>
      </h1>
    </div>

    <!-- Toast -->
    <div
      v-if="toast.text"
      class="mb-4 rounded-md px-4 py-3 text-sm"
      :class="toast.error ? 'bg-red-50 text-red-700' : 'bg-green-50 text-green-700'"
    >
      {{ toast.text }}
    </div>

    <!-- Filters -->
    <div class="mb-4 flex flex-wrap items-center gap-3">
      <div class="inline-flex overflow-hidden rounded-md border border-gray-300 bg-white">
        <button
          v-for="tab in tabs"
          :key="tab.value"
          type="button"
          class="cursor-pointer px-4 py-2 text-sm font-medium transition"
          :class="filters.status === tab.value ? 'bg-amber-600 text-white' : 'text-gray-600 hover:bg-gray-50'"
          @click="setStatus(tab.value)"
        >
          {{ tab.label }}
        </button>
      </div>

      <select
        v-model="filters.rating"
        class="rounded-md border border-gray-300 bg-white px-3 py-2 text-sm"
        @change="resetAndFetch"
      >
        <option value="">All ratings</option>
        <option v-for="n in [5, 4, 3, 2, 1]" :key="n" :value="String(n)">{{ n }} ★</option>
      </select>

      <input
        v-model="filters.search"
        type="search"
        placeholder="Search name, comment, package..."
        class="min-w-[220px] flex-1 rounded-md border border-gray-300 bg-white px-3 py-2 text-sm"
        @input="onSearchInput"
      />
    </div>

    <!-- Table -->
    <div class="overflow-x-auto rounded-lg border border-gray-200 bg-white shadow-sm">
      <table class="min-w-[960px] w-full text-left text-sm">
        <thead class="bg-gray-50 text-xs uppercase tracking-wide text-gray-500">
          <tr>
            <th class="px-4 py-3">Reviewer</th>
            <th class="px-4 py-3">Package</th>
            <th class="px-4 py-3">Rating</th>
            <th class="w-1/3 px-4 py-3">Review</th>
            <th class="px-4 py-3">Date</th>
            <th class="px-4 py-3">Status</th>
            <th class="px-4 py-3 text-right">Actions</th>
          </tr>
        </thead>

        <tbody>
          <tr v-if="loading">
            <td colspan="7" class="px-4 py-12 text-center text-gray-500">Loading...</td>
          </tr>

          <tr v-else-if="loadError">
            <td colspan="7" class="px-4 py-12 text-center">
              <p class="mb-3 text-red-600">{{ loadError }}</p>
              <button
                type="button"
                class="cursor-pointer rounded bg-amber-600 px-4 py-2 text-white hover:bg-amber-700"
                @click="fetchReviews"
              >
                Retry
              </button>
            </td>
          </tr>

          <tr v-else-if="!reviews.length">
            <td colspan="7" class="px-4 py-12 text-center text-gray-500">No reviews found.</td>
          </tr>

          <tr v-for="r in reviews" v-else :key="r.id" class="border-t align-top hover:bg-gray-50">
            <!-- Reviewer -->
            <td class="px-4 py-3">
              <div class="flex items-center gap-3">
                <img
                  :src="r.user?.avatar_url || fallbackAvatar"
                  :alt="r.user?.name || ''"
                  class="h-10 w-10 shrink-0 rounded-full object-cover"
                />
                <div class="min-w-0">
                  <p class="truncate font-semibold text-gray-800">{{ r.user?.name || 'Deleted user' }}</p>
                  <p v-if="r.traveler_location" class="truncate text-xs text-gray-500">
                    {{ r.traveler_location }}
                  </p>
                </div>
              </div>
            </td>

            <!-- Package -->
            <td class="px-4 py-3 text-gray-700">
              <a
                v-if="r.package?.slug"
                :href="`/tour-package/${r.package.slug}`"
                target="_blank"
                rel="noopener"
                class="text-amber-700 hover:underline"
              >
                {{ tr(r.package.package_name) || `#${r.tour_package_id}` }}
              </a>
              <span v-else>#{{ r.tour_package_id }}</span>
            </td>

            <!-- Rating -->
            <td class="whitespace-nowrap px-4 py-3 text-base tracking-wider text-yellow-400">
              {{ '★'.repeat(r.rating) }}<span class="text-gray-300">{{ '★'.repeat(5 - r.rating) }}</span>
            </td>

            <!-- Comment -->
            <td class="px-4 py-3 text-gray-700">
              <template v-if="r.comment">
                <p class="break-words" :class="expanded[r.id] ? '' : 'line-clamp-2'">{{ r.comment }}</p>
                <button
                  v-if="r.comment.length > 90"
                  type="button"
                  class="cursor-pointer text-xs text-blue-700 hover:underline"
                  @click="expanded[r.id] = !expanded[r.id]"
                >
                  {{ expanded[r.id] ? 'show less' : 'read more' }}
                </button>
              </template>
              <span v-else class="text-gray-400">-</span>

              <p v-if="r.formatted_travel_info" class="mt-1 text-xs text-gray-500">
                {{ r.formatted_travel_info }}
              </p>
            </td>

            <!-- Date -->
            <td class="whitespace-nowrap px-4 py-3 text-gray-600">{{ formatDate(r.created_at) }}</td>

            <!-- Status -->
            <td class="px-4 py-3">
              <span
                class="inline-block rounded-full px-2.5 py-1 text-xs font-semibold"
                :class="r.is_approved ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700'"
              >
                {{ r.is_approved ? 'Approved' : 'Pending' }}
              </span>
            </td>

            <!-- Actions -->
            <td class="whitespace-nowrap px-4 py-3 text-right">
              <button
                type="button"
                :disabled="busyId === r.id"
                class="cursor-pointer rounded border px-3 py-1.5 text-xs font-semibold transition disabled:opacity-50"
                :class="
                  r.is_approved
                    ? 'border-yellow-500 text-yellow-700 hover:bg-yellow-50'
                    : 'border-green-600 text-green-700 hover:bg-green-50'
                "
                @click="toggleApproval(r)"
              >
                {{ r.is_approved ? 'Hide' : 'Approve' }}
              </button>

              <button
                type="button"
                :disabled="busyId === r.id"
                class="ml-2 cursor-pointer rounded border border-red-500 px-3 py-1.5 text-xs font-semibold text-red-600 transition hover:bg-red-50 disabled:opacity-50"
                @click="confirmDelete = r"
              >
                Delete
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Pagination -->
    <div
      v-if="lastPage > 1"
      class="mt-4 flex flex-wrap items-center justify-between gap-3 text-sm text-gray-600"
    >
      <p>Showing {{ from }}-{{ to }} of {{ total }}</p>

      <div class="flex items-center gap-1">
        <button
          type="button"
          :disabled="page <= 1"
          class="cursor-pointer rounded border bg-white px-3 py-1.5 hover:bg-gray-50 disabled:cursor-not-allowed disabled:opacity-40"
          @click="goTo(page - 1)"
        >
          Prev
        </button>

        <button
          v-for="p in pageNumbers"
          :key="p"
          type="button"
          class="min-w-[36px] cursor-pointer rounded border px-3 py-1.5"
          :class="p === page ? 'border-amber-600 bg-amber-600 text-white' : 'bg-white hover:bg-gray-50'"
          @click="goTo(p)"
        >
          {{ p }}
        </button>

        <button
          type="button"
          :disabled="page >= lastPage"
          class="cursor-pointer rounded border bg-white px-3 py-1.5 hover:bg-gray-50 disabled:cursor-not-allowed disabled:opacity-40"
          @click="goTo(page + 1)"
        >
          Next
        </button>
      </div>
    </div>

    <!-- Delete confirmation -->
    <Teleport to="body">
      <div
        v-if="confirmDelete"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4"
        @click.self="confirmDelete = null"
      >
        <div class="w-full max-w-sm rounded-xl bg-white p-6 shadow-xl">
          <h3 class="mb-2 text-lg font-bold text-gray-800">Delete this review?</h3>
          <p class="mb-5 text-sm text-gray-600">
            The review by <strong>{{ confirmDelete.user?.name || 'this user' }}</strong> will be
            permanently removed.
          </p>

          <div class="flex justify-end gap-3">
            <button
              type="button"
              class="cursor-pointer px-4 py-2 text-sm text-gray-600 hover:text-gray-800"
              @click="confirmDelete = null"
            >
              Cancel
            </button>
            <button
              type="button"
              :disabled="busyId === confirmDelete.id"
              class="cursor-pointer rounded bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-700 disabled:opacity-50"
              @click="removeReview"
            >
              Delete
            </button>
          </div>
        </div>
      </div>
    </Teleport>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive, computed, onMounted, onBeforeUnmount } from 'vue'
import api from '@/services/api'

type Localized = Record<string, string | null> | string | null | undefined

interface Review {
  id: number
  tour_package_id: number
  rating: number
  comment: string | null
  is_approved: boolean
  user: { id: number; name: string | null; avatar_url: string | null }
  package?: { id: number; slug: string; package_name: Localized }
  traveler_location: string | null
  destination_visited: string | null
  travel_type: string | null
  travel_date: string | null
  formatted_travel_info: string
  created_at: string
}

const fallbackAvatar =
  'data:image/svg+xml;utf8,' +
  encodeURIComponent(
    `<svg xmlns='http://www.w3.org/2000/svg' width='48' height='48'><rect width='48' height='48' fill='#d1d5db'/><circle cx='24' cy='19' r='8' fill='#fff'/><path d='M8 44c2-10 10-14 16-14s14 4 16 14z' fill='#fff'/></svg>`
  )

const tr = (v: Localized): string => {
  if (!v) return ''
  if (typeof v === 'string') return v
  return v.en || v.bn || ''
}

const formatDate = (iso: string) =>
  iso
    ? new Date(iso).toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' })
    : ''

// ---- state ----
const reviews = ref<Review[]>([])
const loading = ref(true)
const loadError = ref('')
const busyId = ref<number | null>(null)
const confirmDelete = ref<Review | null>(null)
const expanded = ref<Record<number, boolean>>({})

const page = ref(1)
const lastPage = ref(1)
const total = ref(0)
const from = ref(0)
const to = ref(0)
const PER_PAGE = 15

const tabs = [
  { value: '', label: 'All' },
  { value: 'pending', label: 'Pending' },
  { value: 'approved', label: 'Approved' },
]

const filters = reactive({ status: '', rating: '', search: '' })

// ---- toast ----
const toast = reactive({ text: '', error: false })
let toastTimer: ReturnType<typeof setTimeout> | null = null

const notify = (text: string, error = false) => {
  toast.text = text
  toast.error = error
  if (toastTimer) clearTimeout(toastTimer)
  toastTimer = setTimeout(() => (toast.text = ''), 3500)
}

const errorText = (e: any, fallback: string) => {
  const status = e?.response?.status
  if (status === 401) return 'Your session has expired. Please log in again.'
  if (status === 403) return 'You do not have permission to do this.'
  return e?.response?.data?.message || fallback
}

// ---- list ----
let requestId = 0

const fetchReviews = async () => {
  const current = ++requestId
  loading.value = true
  loadError.value = ''

  try {
    const params: Record<string, any> = {
      include_unapproved: 1, // honoured by the API only for admin tokens
      per_page: PER_PAGE,
      page: page.value,
    }
    if (filters.status) params.status = filters.status
    if (filters.rating) params.rating = filters.rating
    if (filters.search.trim()) params.search = filters.search.trim()

    const { data } = await api.get('/reviews', { params })
    if (current !== requestId) return // a newer request replaced this one

    // the last item of a page was removed: go back one page
    if (!data.data?.length && page.value > 1) {
      page.value--
      return fetchReviews()
    }

    reviews.value = data.data ?? []
    lastPage.value = data.meta?.last_page ?? 1
    total.value = data.meta?.total ?? reviews.value.length
    from.value = data.meta?.from ?? 0
    to.value = data.meta?.to ?? 0
  } catch (e: any) {
    if (current !== requestId) return
    reviews.value = []
    loadError.value = errorText(e, 'Failed to load reviews.')
  } finally {
    if (current === requestId) loading.value = false
  }
}

const resetAndFetch = () => {
  page.value = 1
  fetchReviews()
}

const setStatus = (value: string) => {
  filters.status = value
  resetAndFetch()
}

let searchTimer: ReturnType<typeof setTimeout> | null = null
const onSearchInput = () => {
  if (searchTimer) clearTimeout(searchTimer)
  searchTimer = setTimeout(resetAndFetch, 400)
}

const goTo = (p: number) => {
  if (p < 1 || p > lastPage.value || p === page.value) return
  page.value = p
  fetchReviews()
}

// up to 5 page buttons around the current page
const pageNumbers = computed(() => {
  const start = Math.max(1, Math.min(page.value - 2, lastPage.value - 4))
  const end = Math.min(lastPage.value, start + 4)
  return Array.from({ length: end - start + 1 }, (_, i) => start + i)
})

// ---- actions ----
const toggleApproval = async (review: Review) => {
  busyId.value = review.id
  try {
    const { data } = await api.patch(`/reviews/${review.id}/toggle-approval`)
    review.is_approved = data.is_approved
    notify(data.message || 'Review updated.')

    // on the Pending / Approved tab the row no longer belongs there
    if (filters.status) await fetchReviews()
  } catch (e: any) {
    notify(errorText(e, 'Could not update the review.'), true)
  } finally {
    busyId.value = null
  }
}

const removeReview = async () => {
  const review = confirmDelete.value
  if (!review) return

  busyId.value = review.id
  try {
    const { data } = await api.delete(`/reviews/${review.id}`)
    confirmDelete.value = null
    notify(data.message || 'Review deleted.')
    await fetchReviews()
  } catch (e: any) {
    notify(errorText(e, 'Could not delete the review.'), true)
  } finally {
    busyId.value = null
  }
}

onMounted(fetchReviews)

onBeforeUnmount(() => {
  if (searchTimer) clearTimeout(searchTimer)
  if (toastTimer) clearTimeout(toastTimer)
})
</script>