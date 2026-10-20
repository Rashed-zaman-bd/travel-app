<template>
    <div class="space-y-4">
        <!-- Header -->
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h1 class="text-xl font-semibold text-gray-900">Document categories</h1>
                <p class="text-sm text-gray-500">Group the documents required for each visa service.</p>
            </div>
            <button
                type="button"
                class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700"
                @click="openCreate"
            >
                Add category
            </button>
        </div>

        <!-- Notice -->
        <div
            v-if="notice.text"
            :class="[
                'rounded-lg px-4 py-2 text-sm',
                notice.type === 'error' ? 'bg-red-50 text-red-700' : 'bg-green-50 text-green-700',
            ]"
        >
            {{ notice.text }}
        </div>

        <!-- Filters -->
        <div class="grid gap-3 rounded-xl border border-gray-200 bg-white p-3 sm:grid-cols-3">
            <input
                v-model="filters.search"
                type="search"
                placeholder="Search by title (EN / BN)"
                class="rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none"
            />
            <select
                v-model="filters.visa_service_id"
                class="rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none"
            >
                <option value="">All services</option>
                <option v-for="s in services" :key="s.id" :value="s.id">{{ s.title?.en }}</option>
            </select>
            <select
                v-model="filters.is_active"
                class="rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none"
            >
                <option value="">All statuses</option>
                <option value="1">Active</option>
                <option value="0">Inactive</option>
            </select>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto rounded-xl border border-gray-200 bg-white">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50 text-left text-gray-600">
                    <tr>
                        <th class="px-4 py-3 font-medium">Title</th>
                        <th class="px-4 py-3 font-medium">Service</th>
                        <th class="px-4 py-3 font-medium">Documents</th>
                        <th class="px-4 py-3 font-medium">Order</th>
                        <th class="px-4 py-3 font-medium">Status</th>
                        <th class="px-4 py-3 text-right font-medium">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <tr v-if="loading">
                        <td colspan="6" class="px-4 py-10 text-center text-gray-500">Loading…</td>
                    </tr>
                    <tr v-else-if="!items.length">
                        <td colspan="6" class="px-4 py-10 text-center text-gray-500">
                            No categories found. Add one to get started.
                        </td>
                    </tr>
                    <tr v-for="row in items" v-else :key="row.id" class="hover:bg-gray-50">
                        <td class="px-4 py-3">
                            <div class="font-medium text-gray-900">{{ row.title?.en }}</div>
                            <div v-if="row.title?.bn" class="text-gray-500">{{ row.title.bn }}</div>
                        </td>
                        <td class="px-4 py-3 text-gray-700">{{ row.service?.title?.en ?? '—' }}</td>
                        <td class="px-4 py-3 text-gray-700">{{ row.documents_count ?? 0 }}</td>
                        <td class="px-4 py-3 text-gray-700">{{ row.sort_order }}</td>
                        <td class="px-4 py-3">
                            <button
                                type="button"
                                :disabled="togglingId === row.id"
                                :class="[
                                    'rounded-full px-2.5 py-1 text-xs font-medium disabled:opacity-50',
                                    row.is_active
                                        ? 'bg-green-100 text-green-700'
                                        : 'bg-gray-100 text-gray-600',
                                ]"
                                @click="toggleActive(row)"
                            >
                                {{ row.is_active ? 'Active' : 'Inactive' }}
                            </button>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <button class="mr-3 text-indigo-600 hover:underline" @click="openEdit(row)">Edit</button>
                            <button class="text-red-600 hover:underline" @click="confirmDelete(row)">Delete</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="flex flex-wrap items-center justify-between gap-3 text-sm text-gray-600">
            <div class="flex items-center gap-2">
                <span>Rows per page</span>
                <select v-model.number="perPage" class="rounded-lg border border-gray-300 px-2 py-1">
                    <option :value="10">10</option>
                    <option :value="15">15</option>
                    <option :value="25">25</option>
                    <option :value="50">50</option>
                </select>
                <span>· {{ meta.total }} total</span>
            </div>
            <div class="flex items-center gap-2">
                <button
                    class="rounded-lg border border-gray-300 px-3 py-1 disabled:opacity-40"
                    :disabled="meta.current_page <= 1 || loading"
                    @click="page--"
                >
                    Previous
                </button>
                <span>Page {{ meta.current_page }} of {{ meta.last_page }}</span>
                <button
                    class="rounded-lg border border-gray-300 px-3 py-1 disabled:opacity-40"
                    :disabled="meta.current_page >= meta.last_page || loading"
                    @click="page++"
                >
                    Next
                </button>
            </div>
        </div>

        <!-- Create / Edit modal -->
        <div
            v-if="formOpen"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4"
            @keydown.esc="closeForm"
        >
            <form
                class="w-full max-w-lg space-y-4 rounded-xl bg-white p-6 shadow-xl"
                @submit.prevent="save"
            >
                <h2 class="text-lg font-semibold text-gray-900">
                    {{ editingId ? 'Edit category' : 'Add category' }}
                </h2>

                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Service</label>
                    <select
                        v-model="form.visa_service_id"
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none"
                    >
                        <option value="" disabled>Select a service</option>
                        <option v-for="s in services" :key="s.id" :value="s.id">{{ s.title?.en }}</option>
                    </select>
                    <p v-if="err('visa_service_id')" class="mt-1 text-xs text-red-600">{{ err('visa_service_id') }}</p>
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">Title (English)</label>
                        <input
                            v-model="form.title.en"
                            type="text"
                            maxlength="255"
                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none"
                        />
                        <p v-if="err('title.en')" class="mt-1 text-xs text-red-600">{{ err('title.en') }}</p>
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">Title (বাংলা)</label>
                        <input
                            v-model="form.title.bn"
                            type="text"
                            maxlength="255"
                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none"
                        />
                        <p v-if="err('title.bn')" class="mt-1 text-xs text-red-600">{{ err('title.bn') }}</p>
                    </div>
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">Sort order</label>
                        <input
                            v-model.number="form.sort_order"
                            type="number"
                            min="0"
                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none"
                        />
                        <p v-if="err('sort_order')" class="mt-1 text-xs text-red-600">{{ err('sort_order') }}</p>
                    </div>
                    <label class="flex items-center gap-2 pt-6 text-sm text-gray-700">
                        <input v-model="form.is_active" type="checkbox" class="h-4 w-4 rounded border-gray-300" />
                        Active
                    </label>
                </div>

                <div class="flex justify-end gap-2 pt-2">
                    <button
                        type="button"
                        class="rounded-lg border border-gray-300 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50"
                        @click="closeForm"
                    >
                        Cancel
                    </button>
                    <button
                        type="submit"
                        :disabled="saving"
                        class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700 disabled:opacity-50"
                    >
                        {{ saving ? 'Saving…' : 'Save category' }}
                    </button>
                </div>
            </form>
        </div>

        <!-- Delete modal -->
        <div v-if="deleting" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4">
            <div class="w-full max-w-sm space-y-4 rounded-xl bg-white p-6 shadow-xl">
                <h2 class="text-lg font-semibold text-gray-900">Delete category?</h2>
                <p class="text-sm text-gray-600">
                    “{{ deleting.title?.en }}” will be removed. Categories that still have documents can't be deleted.
                </p>
                <div class="flex justify-end gap-2">
                    <button
                        class="rounded-lg border border-gray-300 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50"
                        @click="deleting = null"
                    >
                        Cancel
                    </button>
                    <button
                        :disabled="deletingBusy"
                        class="rounded-lg bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-700 disabled:opacity-50"
                        @click="remove"
                    >
                        {{ deletingBusy ? 'Deleting…' : 'Delete category' }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { onBeforeUnmount, onMounted, reactive, ref, watch } from 'vue'
import api from '@/services/api'

interface Localized {
    en: string
    bn?: string | null
}

interface VisaService {
    id: number
    title: Localized
}

interface Category {
    id: number
    visa_service_id: number
    title: Localized
    is_active: boolean
    sort_order: number
    service?: { id: number; title: Localized } | null
    documents_count?: number
}

interface FormState {
    visa_service_id: number | ''
    title: { en: string; bn: string }
    is_active: boolean
    sort_order: number
}

// ---- Endpoints (adjust if your routes differ) ----
const CATEGORY_URL = '/admin/visa-document-categories'
const SERVICE_URL = '/admin/visa-services'

// ---- List state ----
const items = ref<Category[]>([])
const services = ref<VisaService[]>([])
const loading = ref(false)
const page = ref(1)
const perPage = ref(15)
const meta = reactive({ current_page: 1, last_page: 1, total: 0 })
const filters = reactive({ search: '', visa_service_id: '' as number | '', is_active: '' })

// ---- Form state ----
const formOpen = ref(false)
const editingId = ref<number | null>(null)
const saving = ref(false)
const errors = ref<Record<string, string[]>>({})

const blankForm = (): FormState => ({
    visa_service_id: '',
    title: { en: '', bn: '' },
    is_active: true,
    sort_order: 0,
})
const form = reactive<FormState>(blankForm())

// ---- Delete / toggle / notice ----
const deleting = ref<Category | null>(null)
const deletingBusy = ref(false)
const togglingId = ref<number | null>(null)
const notice = reactive({ text: '', type: 'success' as 'success' | 'error' })
let noticeTimer: ReturnType<typeof setTimeout> | undefined

function showNotice(text: string, type: 'success' | 'error' = 'success') {
    notice.text = text
    notice.type = type
    clearTimeout(noticeTimer)
    noticeTimer = setTimeout(() => (notice.text = ''), 4000)
}

function err(key: string): string | undefined {
    return errors.value[key]?.[0]
}

function apiMessage(e: any, fallback: string): string {
    return e?.response?.data?.message ?? fallback
}

// ---- Data loading ----
async function fetchServices() {
    try {
        const { data } = await api.get(SERVICE_URL, { params: { paginate: 0, per_page: 100 } })
        services.value = data.data ?? data
    } catch {
        showNotice('Could not load visa services.', 'error')
    }
}

async function fetchItems() {
    loading.value = true
    try {
        const { data } = await api.get(CATEGORY_URL, {
            params: {
                page: page.value,
                per_page: perPage.value,
                all_locales: 1, // get {en, bn} so the edit form has both
                search: filters.search || undefined,
                visa_service_id: filters.visa_service_id || undefined,
                is_active: filters.is_active === '' ? undefined : filters.is_active,
            },
        })
        items.value = data.data
        meta.current_page = data.meta?.current_page ?? 1
        meta.last_page = data.meta?.last_page ?? 1
        meta.total = data.meta?.total ?? data.data.length
    } catch (e) {
        showNotice(apiMessage(e, 'Could not load categories.'), 'error')
    } finally {
        loading.value = false
    }
}

// Reset to page 1 when filters change (search is debounced)
let searchTimer: ReturnType<typeof setTimeout> | undefined
watch(
    () => filters.search,
    () => {
        clearTimeout(searchTimer)
        searchTimer = setTimeout(() => {
            page.value === 1 ? fetchItems() : (page.value = 1)
        }, 350)
    },
)
watch(
    () => [filters.visa_service_id, filters.is_active, perPage.value],
    () => {
        page.value === 1 ? fetchItems() : (page.value = 1)
    },
)
watch(page, fetchItems)

// ---- Create / edit ----
function openCreate() {
    editingId.value = null
    Object.assign(form, blankForm())
    errors.value = {}
    formOpen.value = true
}

function openEdit(row: Category) {
    editingId.value = row.id
    Object.assign(form, {
        visa_service_id: row.visa_service_id,
        title: { en: row.title?.en ?? '', bn: row.title?.bn ?? '' },
        is_active: !!row.is_active,
        sort_order: row.sort_order ?? 0,
    })
    errors.value = {}
    formOpen.value = true
}

function closeForm() {
    formOpen.value = false
}

async function save() {
    saving.value = true
    errors.value = {}
    try {
        const payload = {
            visa_service_id: form.visa_service_id,
            title: { en: form.title.en, bn: form.title.bn || null },
            is_active: form.is_active,
            sort_order: form.sort_order || 0,
        }
        if (editingId.value) {
            await api.put(`${CATEGORY_URL}/${editingId.value}`, payload)
            showNotice('Category updated.')
        } else {
            await api.post(CATEGORY_URL, payload)
            showNotice('Category created.')
        }
        formOpen.value = false
        await fetchItems()
    } catch (e: any) {
        if (e?.response?.status === 422) {
            errors.value = e.response.data.errors ?? {}
        } else {
            showNotice(apiMessage(e, 'Could not save the category.'), 'error')
        }
    } finally {
        saving.value = false
    }
}

// ---- Quick status toggle ----
async function toggleActive(row: Category) {
    togglingId.value = row.id
    try {
        await api.put(`${CATEGORY_URL}/${row.id}`, { is_active: !row.is_active })
        row.is_active = !row.is_active
    } catch (e) {
        showNotice(apiMessage(e, 'Could not update status.'), 'error')
    } finally {
        togglingId.value = null
    }
}

// ---- Delete ----
function confirmDelete(row: Category) {
    deleting.value = row
}

async function remove() {
    if (!deleting.value) return
    deletingBusy.value = true
    try {
        await api.delete(`${CATEGORY_URL}/${deleting.value.id}`)
        deleting.value = null
        showNotice('Category deleted.')
        // Step back a page if we just removed the last row on this one
        if (items.value.length === 1 && page.value > 1) page.value--
        else await fetchItems()
    } catch (e) {
        // Backend returns 422 when the category still has documents
        deleting.value = null
        showNotice(apiMessage(e, 'Could not delete the category.'), 'error')
    } finally {
        deletingBusy.value = false
    }
}

onMounted(() => {
    fetchServices()
    fetchItems()
})

onBeforeUnmount(() => {
    clearTimeout(searchTimer)
    clearTimeout(noticeTimer)
})
</script>