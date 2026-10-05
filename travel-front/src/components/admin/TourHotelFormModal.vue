<template>
    <Teleport to="body">
        <div
            v-if="modelValue"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4"
            @mousedown.self="close"
        >
            <div class="max-h-[90vh] w-full max-w-lg overflow-y-auto rounded bg-white shadow-xl">
                <div class="flex items-center justify-between border-b px-5 py-3">
                    <h2 class="text-lg font-semibold text-gray-800">
                        {{ isEdit ? 'Edit Hotel' : 'Add Hotel' }}
                    </h2>
                    <button type="button" class="text-gray-500 hover:text-gray-700" @click="close">✕</button>
                </div>

                <form class="space-y-4 px-5 py-4" @submit.prevent="submit">
                    <div v-if="generalError" class="rounded bg-red-50 p-3 text-sm text-red-700">
                        {{ generalError }}
                    </div>

                    <!-- Package -->
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">Tour Package *</label>
                        <select
                            v-model.number="form.tour_package_id"
                            class="w-full rounded border border-gray-300 px-3 py-2 text-sm"
                            @change="form.tour_package_offer_id = ''"
                        >
                            <option value="">Select package</option>
                            <option v-for="p in packages" :key="p.id" :value="p.id">{{ p.name }}</option>
                        </select>
                        <p v-if="err('tour_package_id')" class="mt-1 text-xs text-red-600">{{ err('tour_package_id') }}</p>
                    </div>

                    <!-- Offer (filtered by package) -->
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">Offer *</label>
                        <select
                            v-model.number="form.tour_package_offer_id"
                            :disabled="form.tour_package_id === ''"
                            class="w-full rounded border border-gray-300 px-3 py-2 text-sm disabled:bg-gray-100"
                        >
                            <option value="">Select offer</option>
                            <option v-for="o in filteredOffers" :key="o.id" :value="o.id">{{ o.name }}</option>
                        </select>
                        <p v-if="err('tour_package_offer_id')" class="mt-1 text-xs text-red-600">
                            {{ err('tour_package_offer_id') }}
                        </p>
                    </div>

                    <!-- Hotel name -->
                    <div class="grid gap-3 sm:grid-cols-2">
                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">Hotel name (EN) *</label>
                            <input
                                v-model="form.hotel_name_en"
                                type="text"
                                maxlength="100"
                                class="w-full rounded border border-gray-300 px-3 py-2 text-sm"
                            />
                            <p v-if="err('hotel_name.en')" class="mt-1 text-xs text-red-600">{{ err('hotel_name.en') }}</p>
                        </div>
                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">Hotel name (BN)</label>
                            <input
                                v-model="form.hotel_name_bn"
                                type="text"
                                maxlength="100"
                                class="w-full rounded border border-gray-300 px-3 py-2 text-sm"
                            />
                            <p v-if="err('hotel_name.bn')" class="mt-1 text-xs text-red-600">{{ err('hotel_name.bn') }}</p>
                        </div>
                    </div>

                    <!-- Location -->
                    <div class="grid gap-3 sm:grid-cols-2">
                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">Location (EN)</label>
                            <input
                                v-model="form.location_en"
                                type="text"
                                maxlength="100"
                                class="w-full rounded border border-gray-300 px-3 py-2 text-sm"
                            />
                            <p v-if="err('location.en')" class="mt-1 text-xs text-red-600">{{ err('location.en') }}</p>
                        </div>
                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">Location (BN)</label>
                            <input
                                v-model="form.location_bn"
                                type="text"
                                maxlength="100"
                                class="w-full rounded border border-gray-300 px-3 py-2 text-sm"
                            />
                            <p v-if="err('location.bn')" class="mt-1 text-xs text-red-600">{{ err('location.bn') }}</p>
                        </div>
                    </div>

                    <!-- Order + Active -->
                    <div class="grid items-end gap-3 sm:grid-cols-2">
                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">Order</label>
                            <input
                                v-model.number="form.order"
                                type="number"
                                min="0"
                                class="w-full rounded border border-gray-300 px-3 py-2 text-sm"
                            />
                            <p v-if="err('order')" class="mt-1 text-xs text-red-600">{{ err('order') }}</p>
                        </div>
                        <label class="flex items-center gap-2 pb-2 text-sm text-gray-700">
                            <input v-model="form.is_active" type="checkbox" class="h-4 w-4" />
                            Active
                        </label>
                    </div>

                    <div class="flex justify-end gap-2 border-t pt-4">
                        <button
                            type="button"
                            class="rounded border border-gray-300 px-4 py-2 text-sm hover:bg-gray-50"
                            :disabled="saving"
                            @click="close"
                        >
                            Cancel
                        </button>
                        <button
                            type="submit"
                            class="rounded bg-blue-600 px-4 py-2 text-sm text-white hover:bg-blue-700 disabled:opacity-50"
                            :disabled="saving"
                        >
                            {{ saving ? 'Saving...' : isEdit ? 'Update' : 'Create' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </Teleport>
</template>

<script setup lang="ts">
import { computed, reactive, ref, watch } from 'vue'
import api from '@/services/api'

interface Localized {
    en?: string | null
    bn?: string | null
}

export interface HotelRow {
    id: number
    tour_package_id: number
    tour_package_offer_id: number
    hotel_name: Localized
    location: Localized | []
    order: number
    is_active: boolean
}

interface PackageOption {
    id: number
    name: string
}

interface OfferOption {
    id: number
    name: string
    packageId: number
}

const props = defineProps<{
    modelValue: boolean
    hotel: HotelRow | null
    packages: PackageOption[]
    offers: OfferOption[]
}>()

const emit = defineEmits<{
    (e: 'update:modelValue', v: boolean): void
    (e: 'saved'): void
}>()

const isEdit = computed(() => !!props.hotel)

const blank = () => ({
    tour_package_id: '' as number | '',
    tour_package_offer_id: '' as number | '',
    hotel_name_en: '',
    hotel_name_bn: '',
    location_en: '',
    location_bn: '',
    order: 0,
    is_active: true,
})

const form = reactive(blank())
const saving = ref(false)
const errors = ref<Record<string, string[]>>({})
const generalError = ref('')

const err = (key: string) => errors.value[key]?.[0] ?? ''

const filteredOffers = computed(() =>
    form.tour_package_id === '' ? [] : props.offers.filter((o) => o.packageId === form.tour_package_id),
)

// Reset / populate every time the modal opens
watch(
    () => props.modelValue,
    (open) => {
        if (!open) return
        errors.value = {}
        generalError.value = ''
        Object.assign(form, blank())

        const h = props.hotel
        if (h) {
            const loc = Array.isArray(h.location) ? {} : h.location
            Object.assign(form, {
                tour_package_id: h.tour_package_id,
                tour_package_offer_id: h.tour_package_offer_id,
                hotel_name_en: h.hotel_name?.en ?? '',
                hotel_name_bn: h.hotel_name?.bn ?? '',
                location_en: loc.en ?? '',
                location_bn: loc.bn ?? '',
                order: h.order ?? 0,
                is_active: !!h.is_active,
            })
        }
    },
)

const close = () => {
    if (saving.value) return
    emit('update:modelValue', false)
}

const submit = async () => {
    saving.value = true
    errors.value = {}
    generalError.value = ''

    const payload = {
        tour_package_id: form.tour_package_id,
        tour_package_offer_id: form.tour_package_offer_id,
        hotel_name: {
            en: form.hotel_name_en,
            bn: form.hotel_name_bn || null,
        },
        location: {
            en: form.location_en || null,
            bn: form.location_bn || null,
        },
        order: form.order,
        is_active: form.is_active,
    }

    try {
        if (props.hotel) {
            await api.put(`/admin/tour-hotel/${props.hotel.id}`, payload)
        } else {
            await api.post('/admin/tour-hotel', payload)
        }
        emit('saved')
        emit('update:modelValue', false)
    } catch (e: any) {
        if (e?.response?.status === 422) {
            errors.value = e.response.data.errors ?? {}
        } else {
            generalError.value = e?.response?.data?.message || 'Something went wrong.'
        }
    } finally {
        saving.value = false
    }
}
</script>