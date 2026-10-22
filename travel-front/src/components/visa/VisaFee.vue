<template>
  <Teleport to="body">
    <div
      v-if="modelValue"
      class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4"
      @click.self="close"
    >
      <div class="relative max-h-[90vh] w-full max-w-4xl overflow-y-auto rounded-lg bg-white p-6 shadow-xl">
        <button
          type="button"
          class="absolute right-4 top-3 text-2xl leading-none text-gray-400 hover:text-gray-700"
          aria-label="Close"
          @click="close"
        >
          &times;
        </button>

        <div v-if="loading" class="py-12 text-center text-gray-500">Loading...</div>
        <div v-else-if="error" class="py-12 text-center text-red-600">{{ error }}</div>
        <div v-else-if="!options.length" class="py-12 text-center text-gray-500">
          No add-on services available.
        </div>

        <div v-else class="overflow-x-auto">
          <table class="min-w-full text-left">
            <thead>
              <tr class="border-b border-gray-800 text-lg font-bold text-black">
                <th class="px-3 py-4 text-center">Name of Service</th>
                <th class="px-3 py-4 text-center">Description</th>
                <th class="px-3 py-4 text-center">Price</th>
              </tr>
            </thead>
            <tbody class="text-sm text-gray-700">
              <tr v-for="opt in options" :key="opt.id">
                <td class="w-1/4 px-3 py-6 align-middle">{{ localized(opt.name) }}</td>
                <td class="px-3 py-6 align-middle">{{ localized(opt.description) }}</td>
                <td class="whitespace-nowrap px-3 py-6 text-center align-middle">
                  <template v-if="opt.fee !== null">
                    {{ currency || 'BDT' }} {{ formatFee(opt.fee) }}
                  </template>
                  <template v-else>-</template>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </Teleport>
</template>

<script setup lang="ts">
import { ref, watch, onMounted, onBeforeUnmount } from 'vue'
import { useI18n } from 'vue-i18n'
import api from '@/services/api'

type Localized = Record<string, string | null | undefined>

interface FeeOption {
  id: number
  name: Localized | null
  description: Localized | null
  fee: string | number | null
}

const props = defineProps<{
  modelValue: boolean
  countryId: number
  typeId: number
  currency?: string
}>()

const emit = defineEmits<{
  (e: 'update:modelValue', value: boolean): void
}>()

const { locale } = useI18n()

const options = ref<FeeOption[]>([])
const loading = ref(false)
const error = ref('')
const loaded = ref(false)

const localized = (v?: Localized | null) => v?.[locale.value] || v?.en || ''
const formatFee = (n: string | number) => Number(n).toLocaleString()
const close = () => emit('update:modelValue', false)

async function load() {
  loading.value = true
  error.value = ''
  try {
    const { data } = await api.get('/visa-fee', {
      params: {
        visa_country_id: props.countryId,
        visa_type_id: props.typeId,
        is_active: 1,
        per_page: 100,
        all_locales: 1,
      },
    })
    options.value = data.data
    loaded.value = true
  } catch {
    error.value = 'Could not load add-on services. Please try again.'
  } finally {
    loading.value = false
  }
}

// Fetch the first time it opens
watch(
  () => props.modelValue,
  (open) => {
    if (open && !loaded.value) load()
  },
)

// Country/type changed: drop the cache, and reload if it's currently open
watch(
  () => [props.countryId, props.typeId],
  () => {
    loaded.value = false
    options.value = []
    if (props.modelValue) load()
  },
)

function onKeydown(e: KeyboardEvent) {
  if (e.key === 'Escape' && props.modelValue) close()
}
onMounted(() => window.addEventListener('keydown', onKeydown))
onBeforeUnmount(() => window.removeEventListener('keydown', onKeydown))
</script>