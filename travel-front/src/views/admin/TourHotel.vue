<template>
    <div class="p-6">
        <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
            <h1 class="text-xl font-semibold text-gray-800">Tour Offer Hotels</h1>
            <button
                type="button"
                class="rounded bg-blue-600 px-4 py-2 text-sm text-white hover:bg-blue-700"
                @click="openCreate"
            >
                + Add Hotel
            </button>
        </div>

        <!-- Filters -->
        <div class="mb-4 grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
            <input
                v-model.trim="search"
                type="text"
                placeholder="Search hotel / location / package"
                class="rounded border border-gray-300 px-3 py-2 text-sm"
            />
            <select v-model="packageFilter" class="rounded border border-gray-300 px-3 py-2 text-sm">
                <option value="">All packages</option>
                <option v-for="p in packageOptions" :key="p.id" :value="p.id">{{ p.name }}</option>
            </select>
            <select v-model="offerFilter" class="rounded border border-gray-300 px-3 py-2 text-sm">
                <option value="">All offers</option>
                <option v-for="o in offerOptions" :key="o.id" :value="o.id">{{ o.name }}</option>
            </select>
            <select v-model="statusFilter" class="rounded border border-gray-300 px-3 py-2 text-sm">
                <option value="">All status</option>
                <option value="1">Active</option>
                <option value="0">Inactive</option>
            </select>
        </div>

        <div v-if="error" class="mb-4 rounded bg-red-50 p-3 text-sm text-red-700">{{ error }}</div>

        <div class="overflow-x-auto rounded border border-gray-200 bg-white">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50 text-left text-xs uppercase text-gray-500">
                    <tr>
                        <th class="px-4 py-3">#</th>
                        <th class="px-4 py-3">Hotel</th>
                        <th class="px-4 py-3">Location</th>
                        <th class="px-4 py-3">Package</th>
                        <th class="px-4 py-3">Offer</th>
                        <th class="px-4 py-3">Order</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <tr v-if="loading">
                        <td colspan="8" class="px-4 py-8 text-center text-gray-500">Loading...</td>
                    </tr>
                    <tr v-else-if="!filtered.length">
                        <td colspan="8" class="px-4 py-8 text-center text-gray-500">No hotels found.</td>
                    </tr>
                    <tr v-for="(h, i) in filtered" :key="h.id" class="hover:bg-gray-50">
                        <td class="px-4 py-3">{{ i + 1 }}</td>
                        <td class="px-4 py-3">
                            <div class="font-medium text-gray-800">{{ text(h.hotel_name) }}</div>
                            <div v-if="h.hotel_name?.bn" class="text-xs text-gray-500">{{ h.hotel_name.bn }}</div>
                        </td>
                        <td class="px-4 py-3">
                            <div>{{ text(h.location) || '—' }}</div>
                            <div v-if="!Array.isArray(h.location) && h.location?.bn" class="text-xs text-gray-500">
                                {{ h.location.bn }}
                            </div>
                        </td>
                        <td class="px-4 py-3">{{ packageName(h) }}</td>
                        <td class="px-4 py-3">{{ offerName(h) }}</td>
                        <td class="px-4 py-3">{{ h.order }}</td>
                        <td class="px-4 py-3">
                            <button
                                type="button"
                                :disabled="togglingId === h.id"
                                class="rounded-full px-3 py-1 text-xs font-medium disabled:opacity-50"
                                :class="h.is_active ? 'bg-green-100 text-green-700' : 'bg-gray-200 text-gray-600'"
                                @click="toggleActive(h)"
                            >
                                {{ h.is_active ? 'Active' : 'Inactive' }}
                            </button>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <button type="button" class="mr-3 text-blue-600 hover:underline" @click="openEdit(h)">
                                Edit
                            </button>
                            <button type="button" class="text-red-600 hover:underline" @click="askDelete(h)">
                                Delete
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Create / Edit modal -->
        <TourHotelFormModal
            v-model="formOpen"
            :hotel="editingHotel"
            :packages="formPackages"
            :offers="formOffers"
            @saved="fetchHotels"
        />

        <!-- Delete modal -->
        <ConfirmModal
            v-model="deleteOpen"
            title="Delete hotel"
            :message="`Are you sure you want to delete &quot;${text(deleteTarget?.hotel_name)}&quot;? This cannot be undone.`"
            :loading="deleting"
            @confirm="confirmDelete"
        />
    </div>
</template>

<script setup lang="ts">
import { computed, onMounted, ref } from 'vue'
import api from '@/services/api'
import ConfirmModal from '@/components/admin/ConfirmModal.vue'
import TourHotelFormModal from '@/components/admin/TourHotelFormModal.vue'

// Change these to match your API routes
const endpoints = {
    hotels: '/admin/tour-hotel',
    packages: '/admin/tour-package',
    offers: '/admin/tour-price-offer',
}

interface Localized {
    en?: string | null
    bn?: string | null
}

type Maybe = string | Localized | null | undefined

interface Hotel {
    id: number
    tour_package_id: number
    tour_package?: { id: number; package_name?: Maybe } | null
    tour_package_offer_id: number
    tour_package_offer?: { id: number; offer_name?: Maybe } | null
    hotel_name: Localized
    location: Localized | []
    order: number
    is_active: boolean
}

const text = (v: Maybe | []): string => {
    if (!v) return ''
    if (typeof v === 'string') return v
    if (Array.isArray(v)) return ''
    return v.en || v.bn || ''
}

const packageName = (h: Hotel) => text(h.tour_package?.package_name) || `#${h.tour_package_id}`
const offerName = (h: Hotel) => text(h.tour_package_offer?.offer_name) || `#${h.tour_package_offer_id}`

const unwrap = (data: any) => (Array.isArray(data) ? data : data?.data ?? [])

const hotels = ref<Hotel[]>([])
const loading = ref(false)
const error = ref('')
const togglingId = ref<number | null>(null)

const search = ref('')
const packageFilter = ref<number | ''>('')
const offerFilter = ref<number | ''>('')
const statusFilter = ref<'' | '0' | '1'>('')

/* ---------- data ---------- */
const fetchHotels = async () => {
    loading.value = true
    error.value = ''
    try {
        const { data } = await api.get(endpoints.hotels, { params: { all_locales: 1 } })
        hotels.value = unwrap(data)
    } catch (e: any) {
        error.value = e?.response?.data?.message || 'Failed to load hotels.'
    } finally {
        loading.value = false
    }
}

// Options for the form modal dropdowns
const formPackages = ref<{ id: number; name: string }[]>([])
const formOffers = ref<{ id: number; name: string; packageId: number }[]>([])

const fetchFormOptions = async () => {
    try {
        const [pk, of] = await Promise.all([
            api.get(endpoints.packages, { params: { all_locales: 1 } }),
            api.get(endpoints.offers, { params: { all_locales: 1 } }),
        ])
        formPackages.value = unwrap(pk.data).map((p: any) => ({
            id: p.id,
            name: text(p.package_name) || `#${p.id}`,
        }))
        formOffers.value = unwrap(of.data).map((o: any) => ({
            id: o.id,
            name: text(o.offer_name) || `#${o.id}`,
            packageId: o.tour_package_id,
        }))
    } catch (e: any) {
        error.value = e?.response?.data?.message || 'Failed to load packages/offers for the form.'
    }
}

/* ---------- filters ---------- */
const packageOptions = computed(() => {
    const map = new Map<number, string>()
    hotels.value.forEach((h) => map.set(h.tour_package_id, packageName(h)))
    return [...map].map(([id, name]) => ({ id, name }))
})

const offerOptions = computed(() => {
    const map = new Map<number, string>()
    hotels.value.forEach((h) => map.set(h.tour_package_offer_id, offerName(h)))
    return [...map].map(([id, name]) => ({ id, name }))
})

const filtered = computed(() => {
    const q = search.value.toLowerCase()
    return hotels.value.filter((h) => {
        if (packageFilter.value !== '' && h.tour_package_id !== packageFilter.value) return false
        if (offerFilter.value !== '' && h.tour_package_offer_id !== offerFilter.value) return false
        if (statusFilter.value !== '' && Number(h.is_active) !== Number(statusFilter.value)) return false
        if (!q) return true

        const loc = Array.isArray(h.location) ? {} : h.location
        const pkg = h.tour_package?.package_name
        const offer = h.tour_package_offer?.offer_name
        const pkgText = typeof pkg === 'object' && pkg ? [pkg.en, pkg.bn] : [pkg]
        const offerText = typeof offer === 'object' && offer ? [offer.en, offer.bn] : [offer]

        return [h.hotel_name?.en, h.hotel_name?.bn, loc.en, loc.bn, ...pkgText, ...offerText]
            .filter((v): v is string => typeof v === 'string' && v !== '')
            .some((v) => v.toLowerCase().includes(q))
    })
})

/* ---------- create / edit ---------- */
const formOpen = ref(false)
const editingHotel = ref<Hotel | null>(null)

const openCreate = () => {
    editingHotel.value = null
    formOpen.value = true
}

const openEdit = (h: Hotel) => {
    editingHotel.value = h
    formOpen.value = true
}

/* ---------- toggle ---------- */
const toggleActive = async (h: Hotel) => {
    togglingId.value = h.id
    try {
        await api.put(`${endpoints.hotels}/${h.id}`, {
            tour_package_id: h.tour_package_id,
            tour_package_offer_id: h.tour_package_offer_id,
            hotel_name: h.hotel_name,
            location: Array.isArray(h.location) ? null : h.location,
            order: h.order,
            is_active: !h.is_active,
        })
        h.is_active = !h.is_active
    } catch (e: any) {
        error.value = e?.response?.data?.message || 'Failed to update status.'
    } finally {
        togglingId.value = null
    }
}

/* ---------- delete ---------- */
const deleteOpen = ref(false)
const deleteTarget = ref<Hotel | null>(null)
const deleting = ref(false)

const askDelete = (h: Hotel) => {
    deleteTarget.value = h
    deleteOpen.value = true
}

const confirmDelete = async () => {
    if (!deleteTarget.value) return
    deleting.value = true
    try {
        await api.delete(`${endpoints.hotels}/${deleteTarget.value.id}`)
        hotels.value = hotels.value.filter((x) => x.id !== deleteTarget.value!.id)
        deleteOpen.value = false
    } catch (e: any) {
        error.value = e?.response?.data?.message || 'Failed to delete.'
        deleteOpen.value = false
    } finally {
        deleting.value = false
    }
}

onMounted(() => {
    fetchHotels()
    fetchFormOptions()
})
</script>