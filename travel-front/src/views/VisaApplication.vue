
<template>
  <div class="w-full">
    <!-- Visa Application Banner -->
    <section
      class="relative flex h-36 w-full items-center justify-center overflow-hidden md:h-60"
    >
      <img
        src="/images/background.jpg"
        alt="Visa application background"
        class="absolute inset-0 h-full w-full object-cover object-center"
      />

      <!-- Dark Overlay -->
      <div class="absolute inset-0 bg-black/50"></div>

      <!-- Title -->
      <h1
        class="relative z-10 text-3xl font-bold text-white md:text-5xl"
      >
        Visa Application
      </h1>
    </section>

    <!-- Visa Search Form -->
    <section class="relative z-20 mx-auto -mt-6 max-w-7xl px-4 md:-mt-10 md:px-6">
      <div class="rounded-2xl bg-white p-4 shadow-lg md:p-6">
        <div class="grid grid-cols-1 gap-3 md:grid-cols-2">

          <!-- Country Search -->
          <div class="relative">
            <label
              class="mb-1 block text-xs font-medium uppercase tracking-wide text-gray-900"
            >
              Country
            </label>

            <button
              type="button"
              @click="toggleCountryDropdown"
              class="flex w-full items-center justify-between rounded-xl border border-gray-300 bg-white px-4 py-3 text-left transition hover:border-amber-500 focus:outline-none focus:ring-2 focus:ring-amber-200"
              :aria-expanded="countryOpen"
            >
              <span class="font-semibold text-gray-900">
                {{ selectedCountry ? countryLabel(selectedCountry) : 'Select country' }}
              </span>

              <i
                class="bi text-gray-500"
                :class="
                  countryOpen ? 'bi-chevron-up' : 'bi-chevron-down'
                "
              ></i>
            </button>

            <!-- Country Dropdown -->
            <div
              v-if="countryOpen"
              class="absolute left-0 right-0 top-full z-50 mt-2 overflow-hidden rounded-lg border border-gray-200 bg-white shadow-xl"
            >
              <!-- Search Input -->
              <div class="border-b border-gray-200 p-3">
                <div
                  class="flex items-center gap-2 rounded-md border border-gray-200 px-3"
                >
                  <i class="bi bi-search text-gray-400"></i>

                  <input
                    v-model="searchCountry"
                    type="text"
                    placeholder="Type to search"
                    class="w-full border-0 py-2 text-sm text-gray-800 outline-none focus:ring-0"
                    autocomplete="off"
                    autofocus
                  />
                </div>
              </div>

              <!-- Country List -->
              <ul class="max-h-64 overflow-y-auto py-1">
                <li v-if="loadingCountries" class="px-4 py-4 text-center text-sm text-gray-500">
                  Loading...
                </li>
                <li v-else-if="countriesError" class="px-4 py-4 text-center text-sm text-red-600">
                  {{ countriesError }}
                </li>

                <template v-else>
                  <li v-for="country in filteredCountries" :key="country.id">
                    <button
                      type="button"
                      @click="selectCountry(country)"
                      class="flex w-full items-center gap-3 px-4 py-3 text-left text-sm text-gray-900 transition hover:bg-gray-100"
                      :class="{ 'bg-slate-200': selectedCountry?.id === country.id }"
                    >
                      <i class="bi bi-geo-alt-fill text-gray-500"></i>
                      <span>{{ countryLabel(country) }}</span>
                    </button>
                  </li>

                  <li
                    v-if="filteredCountries.length === 0"
                    class="px-4 py-4 text-center text-sm text-gray-500"
                  >
                    No countries found.
                  </li>
                </template>
              </ul>
            </div>
          </div>

          <!-- Visa Type -->
          <div>
            <label
              for="visaType"
              class="mb-1 block text-xs font-medium uppercase tracking-wide text-gray-900"
            >
              Visa Type
            </label>

            <select
              id="visaType"
              v-model="selectedVisaTypeId"
              :disabled="loadingTypes"
              class="w-full rounded-xl border border-gray-300 bg-white px-4 py-3 font-semibold text-gray-900 outline-none transition focus:border-amber-500 focus:ring-2 focus:ring-amber-200 disabled:opacity-60"
            >
              <option value="" disabled>
                {{ loadingTypes ? 'Loading...' : 'Select visa type' }}
              </option>

              <option v-for="type in visaTypes" :key="type.id" :value="type.id">
                {{ localized(type.name) }}
              </option>
            </select>

            <p v-if="typesError" class="mt-1 text-xs text-red-600">{{ typesError }}</p>
          </div>
        </div>

        <!-- Search Button -->
        <div class="mt-4 flex justify-end">
          <button
            type="button"
            @click="searchVisa"
            class="inline-flex items-center justify-center rounded-lg bg-amber-600 px-6 py-3 font-semibold text-black transition hover:bg-amber-500 focus:outline-none focus:ring-2 focus:ring-amber-400 focus:ring-offset-2 cursor-pointer"
          >
            <i class="bi bi-search mr-2"></i>
            Search Visa
          </button>
        </div>
      </div>
    </section>
    <VisaServiceResult
      v-if="result"
      :country-id="result.countryId"
      :type-id="result.typeId"
    />

    <VisaDocumentList
      v-if="result"
      :country-id="result.countryId"
      :type-id="result.typeId"
    />

    
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import api from '@/services/api'
import { useI18n } from 'vue-i18n'

import VisaServiceResult from '@/components/visa/VisaServiceResult.vue'
import VisaDocumentList from '@/components/visa/VisaDocumentList.vue'

// set when the user clicks Search Visa
const result = ref<{ countryId: number; typeId: number } | null>(null)

type Localized = Record<string, string | null | undefined>

interface Country {
  id: number
  slug: string
  name: Localized
}

interface VisaType {
  id: number
  slug: string
  name: Localized
}

const { locale } = useI18n()

// Pick the current language, fall back to English
function localized(value?: Localized | null): string {
  return value?.[locale.value] || value?.en || ''
}

// Countries
const countryOpen = ref(false)
const searchCountry = ref('')
const countries = ref<Country[]>([])
const loadingCountries = ref(false)
const countriesError = ref('')
const selectedCountry = ref<Country | null>(null)

// Visa types
const visaTypes = ref<VisaType[]>([])
const loadingTypes = ref(false)
const typesError = ref('')
const selectedVisaTypeId = ref<number | ''>('')

const countryLabel = (c: Country) => localized(c.name)

const filteredCountries = computed(() => {
  const search = searchCountry.value.trim().toLowerCase()
  if (!search) return countries.value

  return countries.value.filter(
    (c) =>
      (c.name?.en ?? '').toLowerCase().includes(search) ||
      (c.name?.bn ?? '').toLowerCase().includes(search),
  )
})

async function fetchCountries() {
  loadingCountries.value = true
  countriesError.value = ''
  try {
    const { data } = await api.get('/visa-countries', {
      params: { all: 1, is_active: 1, all_locales: 1 },
    })
    countries.value = data.data
  } catch {
    countriesError.value = 'Could not load countries. Please try again.'
  } finally {
    loadingCountries.value = false
  }
}

async function fetchVisaTypes() {
  loadingTypes.value = true
  typesError.value = ''
  try {
    const { data } = await api.get('/visa-types', {
      params: { paginate: 0, is_active: 1, all_locales: 1 },
    })
    visaTypes.value = data.data

    // Preselect the first type
    if (visaTypes.value.length && selectedVisaTypeId.value === '') {
      selectedVisaTypeId.value = visaTypes.value[0]!.id
    }
  } catch {
    typesError.value = 'Could not load visa types. Please try again.'
  } finally {
    loadingTypes.value = false
  }
}

function toggleCountryDropdown() {
  countryOpen.value = !countryOpen.value
  if (countryOpen.value) searchCountry.value = ''
}

function selectCountry(country: Country) {
  selectedCountry.value = country
  countryOpen.value = false
  searchCountry.value = ''
}

function searchVisa() {
  const type = visaTypes.value.find((t) => t.id === selectedVisaTypeId.value)

  if (!selectedCountry.value || !type) {
    alert('Please select a country and visa type.')
    return
  }

  result.value = { countryId: selectedCountry.value.id, typeId: type.id }
}

onMounted(() => {
  fetchCountries()
  fetchVisaTypes()
})
</script>