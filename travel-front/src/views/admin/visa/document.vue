<template>
    <div class="space-y-4">
        <!-- Header -->
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h1 class="text-xl font-semibold text-gray-900">Visa documents</h1>
                <p class="text-sm text-gray-500">Manage the documents required per country, visa type and category.</p>
            </div>
            <button
                type="button"
                class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700"
                @click="openCreate"
            >
                Add document
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
        <div class="grid gap-3 rounded-xl border border-gray-200 bg-white p-3 sm:grid-cols-2 lg:grid-cols-5">
            <input
                v-model="filters.search"
                type="search"
                placeholder="Search document name"
                class="rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none"
            />
            <select v-model="filters.visa_country_id" class="rounded-lg border border-gray-300 px-3 py-2 text-sm">
                <option value="">All countries</option>
                <option v-for="c in countries" :key="c.id" :value="c.id">{{ txt(c.name) }}</option>
            </select>
            <select v-model="filters.visa_type_id" class="rounded-lg border border-gray-300 px-3 py-2 text-sm">
                <option value="">All visa types</option>
                <option v-for="t in types" :key="t.id" :value="t.id">{{ txt(t.name) }}</option>
            </select>
            <select
                v-model="filters.visa_document_category_id"
                class="rounded-lg border border-gray-300 px-3 py-2 text-sm"
            >
                <option value="">All categories</option>
                <option v-for="c in categories" :key="c.id" :value="c.id">{{ txt(c.title) }}</option>
            </select>
            <select v-model="filters.is_active" class="rounded-lg border border-gray-300 px-3 py-2 text-sm">
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
                        <th class="px-4 py-3 font-medium">Document</th>
                        <th class="px-4 py-3 font-medium">Country</th>
                        <th class="px-4 py-3 font-medium">Visa type</th>
                        <th class="px-4 py-3 font-medium">Category</th>
                        <th class="px-4 py-3 font-medium">Order</th>
                        <th class="px-4 py-3 font-medium">Status</th>
                        <th class="px-4 py-3 text-right font-medium">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <tr v-if="loading">
                        <td colspan="7" class="px-4 py-10 text-center text-gray-500">Loading…</td>
                    </tr>
                    <tr v-else-if="!items.length">
                        <td colspan="7" class="px-4 py-10 text-center text-gray-500">
                            No documents found. Add one to get started.
                        </td>
                    </tr>
                    <tr v-for="row in items" v-else :key="row.id" class="hover:bg-gray-50">
                        <td class="max-w-xs px-4 py-3">
                            <div class="font-medium text-gray-900">{{ row.document_name?.en }}</div>
                            <div v-if="row.document_name?.bn" class="text-gray-500">{{ row.document_name.bn }}</div>
                            <div v-if="row.description?.en" class="mt-1 line-clamp-2 text-xs text-gray-400">
                                {{ row.description.en }}
                            </div>
                        </td>
                        <td class="px-4 py-3 text-gray-700">{{ txt(row.country?.name) || '—' }}</td>
                        <td class="px-4 py-3 text-gray-700">{{ txt(row.type?.name) || '—' }}</td>
                        <td class="px-4 py-3 text-gray-700">{{ txt(row.category?.title) || '—' }}</td>
                        <td class="px-4 py-3 text-gray-700">{{ row.sort_order }}</td>
                        <td class="px-4 py-3">
                            <button
                                type="button"
                                :disabled="togglingId === row.id"
                                :class="[
                                    'rounded-full px-2.5 py-1 text-xs font-medium disabled:opacity-50',
                                    row.is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600',
                                ]"
                                @click="toggleActive(row)"
                            >
                                {{ row.is_active ? 'Active' : 'Inactive' }}
                            </button>
                        </td>
                        <td class="whitespace-nowrap px-4 py-3 text-right">
                            <button class="mr-3 text-indigo-600 hover:underline" @click="openEdit(row)">Edit</button>
                            <button class="text-red-600 hover:underline" @click="deleting = row">Delete</button>
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
            class="fixed inset-0 z-50 flex items-start justify-center overflow-y-auto bg-black/40 p-4"
            @keydown.esc="formOpen = false"
        >
            <form class="my-8 w-full max-w-2xl space-y-4 rounded-xl bg-white p-6 shadow-xl" @submit.prevent="save">
                <h2 class="text-lg font-semibold text-gray-900">
                    {{ editingId ? 'Edit document' : 'Add document' }}
                </h2>

                <div class="grid gap-4 sm:grid-cols-3">
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">Country</label>
                        <select v-model="form.visa_country_id" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                            <option value="" disabled>Select country</option>
                            <option v-for="c in countries" :key="c.id" :value="c.id">{{ txt(c.name) }}</option>
                        </select>
                        <p v-if="err('visa_country_id')" class="mt-1 text-xs text-red-600">{{ err('visa_country_id') }}</p>
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">Visa type</label>
                        <select v-model="form.visa_type_id" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                            <option value="" disabled>Select type</option>
                            <option v-for="t in types" :key="t.id" :value="t.id">{{ txt(t.name) }}</option>
                        </select>
                        <p v-if="err('visa_type_id')" class="mt-1 text-xs text-red-600">{{ err('visa_type_id') }}</p>
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">Category</label>
                        <select
                            v-model="form.visa_document_category_id"
                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm"
                        >
                            <option value="" disabled>Select category</option>
                            <option v-for="c in categories" :key="c.id" :value="c.id">{{ txt(c.title) }}</option>
                        </select>
                        <p v-if="err('visa_document_category_id')" class="mt-1 text-xs text-red-600">
                            {{ err('visa_document_category_id') }}
                        </p>
                    </div>
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">Document name (English)</label>
                        <input
                            v-model="form.document_name.en"
                            type="text"
                            maxlength="1000"
                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none"
                        />
                        <p v-if="err('document_name.en')" class="mt-1 text-xs text-red-600">{{ err('document_name.en') }}</p>
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">Document name (বাংলা)</label>
                        <input
                            v-model="form.document_name.bn"
                            type="text"
                            maxlength="1000"
                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none"
                        />
                        <p v-if="err('document_name.bn')" class="mt-1 text-xs text-red-600">{{ err('document_name.bn') }}</p>
                    </div>
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">Description (English)</label>
                        <textarea
                            v-model="form.description.en"
                            rows="3"
                            maxlength="2000"
                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none"
                        />
                        <p v-if="err('description.en')" class="mt-1 text-xs text-red-600">{{ err('description.en') }}</p>
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">Description (বাংলা)</label>
                        <textarea
                            v-model="form.description.bn"
                            rows="3"
                            maxlength="2000"
                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none"
                        />
                        <p v-if="err('description.bn')" class="mt-1 text-xs text-red-600">{{ err('description.bn') }}</p>
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
                        @click="formOpen = false"
                    >
                        Cancel
                    </button>
                    <button
                        type="submit"
                        :disabled="saving"
                        class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700 disabled:opacity-50"
                    >
                        {{ saving ? 'Saving…' : 'Save document' }}
                    </button>
                </div>
            </form>
        </div>

        <!-- Delete modal -->
        <div v-if="deleting" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4">
            <div class="w-full max-w-sm space-y-4 rounded-xl bg-white p-6 shadow-xl">
                <h2 class="text-lg font-semibold text-gray-900">Delete document?</h2>
                <p class="text-sm text-gray-600">“{{ deleting.document_name?.en }}” will be permanently removed.</p>
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
                        {{ deletingBusy ? 'Deleting…' : 'Delete document' }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { onBeforeUnmount, onMounted, reactive, ref, watch } from 'vue'
import api from '@/services/api'

type Localized = { en?: string; bn?: string | null } | string | null | undefined

interface Option {
    id: number
    name?: Localized
    title?: Localized
}

interface VisaDoc {
    id: number
    visa_country_id: number
    visa_type_id: number
    visa_document_category_id: number
    document_name: { en?: string; bn?: string | null }
    description: { en?: string | null; bn?: string | null }
    is_active: boolean
    sort_order: number
    country?: { id: number; name: Localized } | null
    type?: { id: number; name: Localized } | null
    category?: { id: number; title: Localized } | null
}

interface FormState {
    visa_country_id: number | ''
    visa_type_id: number | ''
    visa_document_category_id: number | ''
    document_name: { en: string; bn: string }
    description: { en: string; bn: string }
    is_active: boolean
    sort_order: number
}

// ---- Endpoints (adjust if your routes differ) ----
const DOCUMENT_URL = '/admin/visa-documents'
const CATEGORY_URL = '/admin/visa-document-categories'
const COUNTRY_URL = '/admin/visa-countries'
const TYPE_URL = '/admin/visa-types'

// ---- Helpers ----
// Names/titles can be a plain string or a {en, bn} object
function txt(v: Localized): string {
    if (!v) return ''
    if (typeof v === 'string') return v
    return v.en || v.bn || ''
}

function apiMessage(e: any, fallback: string): string {
    return e?.response?.data?.message ?? fallback
}

// ---- List state ----
const items = ref<VisaDoc[]>([])
const countries = ref<Option[]>([])
const types = ref<Option[]>([])
const categories = ref<Option[]>([])
const loading = ref(false)
const page = ref(1)
const perPage = ref(15)
const meta = reactive({ current_page: 1, last_page: 1, total: 0 })
const filters = reactive({
    search: '',
    visa_country_id: '' as number | '',
    visa_type_id: '' as number | '',
    visa_document_category_id: '' as number | '',
    is_active: '',
})

// ---- Form state ----
const formOpen = ref(false)
const editingId = ref<number | null>(null)
const saving = ref(false)
const errors = ref<Record<string, string[]>>({})

const blankForm = (): FormState => ({
    visa_country_id: '',
    visa_type_id: '',
    visa_document_category_id: '',
    document_name: { en: '', bn: '' },
    description: { en: '', bn: '' },
    is_active: true,
    sort_order: 0,
})
const form = reactive<FormState>(blankForm())

function err(key: string): string | undefined {
    return errors.value[key]?.[0]
}

// ---- Delete / toggle / notice ----
const deleting = ref<VisaDoc | null>(null)
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

// ---- Data loading ----
async function fetchOptions() {
    const params = { paginate: 0, per_page: 100, all_locales: 1 }
    const [c, t, cat] = await Promise.allSettled([
        api.get(COUNTRY_URL, { params }),
        api.get(TYPE_URL, { params }),
        api.get(CATEGORY_URL, { params }),
    ])
    if (c.status === 'fulfilled') countries.value = c.value.data.data ?? c.value.data
    if (t.status === 'fulfilled') types.value = t.value.data.data ?? t.value.data
    if (cat.status === 'fulfilled') categories.value = cat.value.data.data ?? cat.value.data
    if ([c, t, cat].some((r) => r.status === 'rejected')) {
        showNotice('Some dropdown options could not be loaded.', 'error')
    }
}

async function fetchItems() {
    loading.value = true
    try {
        const { data } = await api.get(DOCUMENT_URL, {
            params: {
                page: page.value,
                per_page: perPage.value,
                all_locales: 1, // get {en, bn} for name and description
                search: filters.search || undefined,
                visa_country_id: filters.visa_country_id || undefined,
                visa_type_id: filters.visa_type_id || undefined,
                visa_document_category_id: filters.visa_document_category_id || undefined,
                is_active: filters.is_active === '' ? undefined : filters.is_active,
            },
        })
        items.value = data.data
        meta.current_page = data.meta?.current_page ?? 1
        meta.last_page = data.meta?.last_page ?? 1
        meta.total = data.meta?.total ?? data.data.length
    } catch (e) {
        showNotice(apiMessage(e, 'Could not load documents.'), 'error')
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
    () => [
        filters.visa_country_id,
        filters.visa_type_id,
        filters.visa_document_category_id,
        filters.is_active,
        perPage.value,
    ],
    () => {
        page.value === 1 ? fetchItems() : (page.value = 1)
    },
)
watch(page, fetchItems)

// ---- Create / edit ----
function openCreate() {
    editingId.value = null
    Object.assign(form, blankForm())
    // Pre-fill from active filters to speed up bulk entry
    if (filters.visa_country_id) form.visa_country_id = filters.visa_country_id
    if (filters.visa_type_id) form.visa_type_id = filters.visa_type_id
    if (filters.visa_document_category_id) form.visa_document_category_id = filters.visa_document_category_id
    errors.value = {}
    formOpen.value = true
}

function openEdit(row: VisaDoc) {
    editingId.value = row.id
    Object.assign(form, {
        visa_country_id: row.visa_country_id,
        visa_type_id: row.visa_type_id,
        visa_document_category_id: row.visa_document_category_id,
        document_name: { en: row.document_name?.en ?? '', bn: row.document_name?.bn ?? '' },
        description: { en: row.description?.en ?? '', bn: row.description?.bn ?? '' },
        is_active: !!row.is_active,
        sort_order: row.sort_order ?? 0,
    })
    errors.value = {}
    formOpen.value = true
}

async function save() {
    saving.value = true
    errors.value = {}
    try {
        const payload = {
            visa_country_id: form.visa_country_id,
            visa_type_id: form.visa_type_id,
            visa_document_category_id: form.visa_document_category_id,
            document_name: { en: form.document_name.en, bn: form.document_name.bn || null },
            description: { en: form.description.en || null, bn: form.description.bn || null },
            is_active: form.is_active,
            sort_order: form.sort_order || 0,
        }
        if (editingId.value) {
            await api.put(`${DOCUMENT_URL}/${editingId.value}`, payload)
            showNotice('Document updated.')
        } else {
            await api.post(DOCUMENT_URL, payload)
            showNotice('Document created.')
        }
        formOpen.value = false
        await fetchItems()
    } catch (e: any) {
        if (e?.response?.status === 422) {
            errors.value = e.response.data.errors ?? {}
        } else {
            showNotice(apiMessage(e, 'Could not save the document.'), 'error')
        }
    } finally {
        saving.value = false
    }
}

// ---- Quick status toggle ----
async function toggleActive(row: VisaDoc) {
    togglingId.value = row.id
    try {
        await api.put(`${DOCUMENT_URL}/${row.id}`, { is_active: !row.is_active })
        row.is_active = !row.is_active
    } catch (e) {
        showNotice(apiMessage(e, 'Could not update status.'), 'error')
    } finally {
        togglingId.value = null
    }
}

// ---- Delete ----
async function remove() {
    if (!deleting.value) return
    deletingBusy.value = true
    try {
        await api.delete(`${DOCUMENT_URL}/${deleting.value.id}`)
        deleting.value = null
        showNotice('Document deleted.')
        // Step back a page if we just removed the last row on this one
        if (items.value.length === 1 && page.value > 1) page.value--
        else await fetchItems()
    } catch (e) {
        deleting.value = null
        showNotice(apiMessage(e, 'Could not delete the document.'), 'error')
    } finally {
        deletingBusy.value = false
    }
}

onMounted(() => {
    fetchOptions()
    fetchItems()
})

onBeforeUnmount(() => {
    clearTimeout(searchTimer)
    clearTimeout(noticeTimer)
})
</script>