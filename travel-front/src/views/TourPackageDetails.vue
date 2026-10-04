<template>
    <div class="min-h-screen w-full bg-gray-50">
        <!-- Loading -->
        <div v-if="loading" class="flex justify-center py-24">
            <div class="h-10 w-10 animate-spin rounded-full border-4 border-gray-300 border-t-blue-600"></div>
        </div>

        <!-- Error -->
        <div v-else-if="error" class="mx-auto w-full max-w-xl px-4 py-24 text-center">
            <p class="mb-4 text-red-600">{{ error }}</p>

            <button class="min-h-[44px] rounded bg-blue-600 px-6 py-2 text-white" @click="fetchData">
                Retry
            </button>
        </div>

        <template v-else-if="pkg">

            <!-- Main Container -->
            <section class="mx-auto w-full max-w-7xl px-4 py-10 sm:px-6 lg:px-8">

                <!-- Breadcrumb: Country / Duration / Package name -->
                <nav v-if="categorySlug" aria-label="Breadcrumb" class="mb-5">
                    <ol class="flex flex-wrap items-center gap-x-2 gap-y-1 text-sm sm:text-base">
                        <!-- Country (link) -->
                        <li>
                            <router-link :to="`/destination/${categorySlug}`" class="text-blue-600 hover:underline">
                                {{ countryName || t('package_details.back') }}
                            </router-link>
                        </li>

                        <!-- Duration (plain text, optional) -->
                        <template v-if="tr(pkg.package_duration)">
                            <li aria-hidden="true" class="text-gray-400">/</li>
                            <li class="text-gray-600">{{ tr(pkg.package_duration) }}</li>
                        </template>

                        <!-- Current package -->
                        <li aria-hidden="true" class="text-gray-400">/</li>
                        <li class="min-w-0 break-words text-gray-700" aria-current="page">
                            {{ tr(pkg.package_name) }}
                        </li>
                    </ol>
                </nav>

                <!-- Header -->
                <h2 v-if="tr(pkg.header)" class="mb-4 text-xl font-semibold text-amber-500 sm:text-3xl">
                    {{ tr(pkg.package_name) }}
                </h2>

                <p v-if="tr(pkg.sub_header)" class="mb-6 text-gray-600 sm:text-lg">
                    {{ tr(pkg.sub_header) }}
                </p>


                <!-- Main Content -->
                <div class="flex w-full flex-col gap-6 lg:flex-row lg:items-start lg:gap-2">

                    <main class="w-full min-w-0 lg:w-2/3">

                        <!-- Package Image -->
                        <div v-if="pkg.package_image" class="mb-2 overflow-hidden bg-gray-200 shadow">
                            <img :src="pkg.package_image" :alt="tr(pkg.package_image_title) ||
                                tr(pkg.package_name)
                                " class="block h-96 w-full object-cover" />
                        </div>
                        <!-- Descriptiontion -->
                        <div class="">
                            <p class="whitespace-pre-line break-words text-base sm:text-xl text-gray-700">
                                {{ tr(pkg.package_destination) }}
                            </p>
                        </div>

                        <!-- Highlights -->
                        <div v-if="activeHighlights.length" class=" pt-10 pb-10">
                            <h1 class="mb-3 text-xl sm:text-2xl font-semibold text-gray-800">
                                {{ t('package_details.highlights') }}
                            </h1>

                            <ul class="space-y-3">
                                <li v-for="h in activeHighlights" :key="h.id"
                                    class="flex items-start gap-3 text-gray-700">
                                    <span v-if="h.icon" class="text-3xl leading-6">{{ h.icon }}</span>
                                    <span v-else class="text-amber-500 text-3xl">✔</span>
                                    <span class="break-words text-base sm:text-xl">{{ trh(h.highlight) }}</span>
                                </li>
                            </ul>
                        </div>

                        <!-- Book Now -->
                        <div class="flex items-center justify-center pb-10 pt-5">
                            <router-link :to="`/tour-package/${pkg.slug}/book`"
                                 class="flex min-h-[46px] w-full sm:w-1/2 items-center justify-center bg-amber-500 px-5 py-3 font-semibold text-white transition hover:bg-amber-600">
                                    {{ t('worldwide_category.book_now') }} →
                            </router-link>
                        </div>

                        <div>
                            <h1 class="mb-3 text-xl sm:text-2xl font-semibold text-gray-800">
                                {{ t('package_details.itinerary') }}
                            </h1>
                        </div>

                        <!-- Itinerary -->
                        <div v-if="activeDays.length" class="pb-10">
                            <div class="overflow-x-auto">
                                <table class="min-w-full text-sm sm:text-base ">
                                    <thead class="bg-gray-50 text-left text-gray-700">
                                        <tr>
                                            <th class="px-4 py-3 whitespace-nowrap">Day</th>
                                            <th class="px-4 py-3">Highlight</th>
                                            <th class="px-4 py-3">Overnight</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="day in activeDays" :key="day.id" class="border-t align-top">
                                            <td class="px-4 py-3 font-semibold whitespace-nowrap text-gray-600">
                                                Day {{ day.day_number }}
                                            </td>
                                            <td class="px-4 py-3 text-gray-700">
                                                {{ tri(day.highlights) || '-' }}
                                            </td>
                                            <td class="px-4 py-3 text-gray-700">
                                                {{ tri(day.overnight) || '-' }}
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Tour Information -->
                        <div>
                            <h1 class=" text-xl sm:text-2xl font-semibold text-gray-800">
                                {{ t('package_details.information') }}
                            </h1>
                        </div>
                        <div
                            v-if="activeTourInformation.length"
                            class="pb-2 "
                        >
                            <div class="space-y-1">

                                <div
                                    v-for="item in activeTourInformation"
                                    :key="item.id"
                                    class="overflow-hidden border-b border-gray-200 bg-white"
                                >

                                    <!-- Title -->
                                    <button
                                        type="button"
                                        class="flex w-full items-center justify-between gap-4 px-3 py-4 text-left transition hover:bg-gray-50 cursor-pointer"
                                        @click="toggleInformation(item.id)"
                                    >

                                        <span
                                            class="text-sm font-semibold text-gray-800 sm:text-base"
                                        >
                                            {{ triInfo(item.title) }}
                                        </span>

                                        <i
                                            class="bi shrink-0 text-gray-500"
                                            :class="
                                                openInformation.includes(item.id)
                                                    ? 'bi-chevron-up'
                                                    : 'bi-chevron-down'
                                            "
                                        ></i>

                                    </button>


                                    <!-- Content -->
                                    <div
                                        v-if="openInformation.includes(item.id)"
                                        class="border-t border-gray-100 px-3 pb-5 pt-4"
                                    >

                                        <div
                                            class="whitespace-pre-line break-words text-sm leading-6 text-gray-700 sm:text-base"
                                        >
                                            {{ triInfo(item.content) }}
                                        </div>

                                    </div>

                                </div>

                            </div>
                        </div>

                        <!-- Day Activities -->
                        <div v-if="dayActivitys.length" class="pt-10 pb-10">
                                                        
                            <!-- Activities -->
                            <div class="space-y-8">
                                <article v-for="activity in dayActivitys" :key="activity.id"
                                    class="overflow-hidden">
                                    <!-- Image -->
                                    <div v-if="activity.image" class="overflow-hidden">
                                        <!-- Activity Title -->
                                        <h3 v-if="trc(activity.title)"
                                            class="mb-3 text-lg font-semibold text-gray-800 sm:text-xl">
                                            {{ trc(activity.title) }}
                                        </h3>
                                        <img :src="activity.image" :alt="trc(activity.title) ||
                                            t('activity.title')
                                            " class="block h-64 w-full object-cover sm:h-96" loading="lazy" 
                                        />
                                        <!-- Image Caption -->
                                        <p v-if="trc(activity.image_caption)" class="mb-4 text-sm italic text-gray-500">
                                            {{ trc(activity.image_caption) }}
                                        </p>
                                    </div>

                                    <!-- Content -->
                                    <div class="pt-5">
                                        <!-- Description -->
                                        <p v-if="trc(activity.description)"
                                            class="whitespace-pre-line break-words text-base leading-7 text-gray-700 sm:text-lg">
                                            {{ trc(activity.description) }}
                                        </p>

                                    </div>
                                </article>
                            </div>
                        </div>

                        <!-- Book Now -->
                        <div class="flex items-center justify-center pb-10 pt-5">
                            <router-link :to="`/tour-package/${pkg.slug}/book`"
                                 class="flex min-h-[46px] w-full sm:w-1/2 items-center justify-center bg-amber-500 px-5 py-3 font-semibold text-white transition hover:bg-amber-600">
                                    {{ t('worldwide_category.book_now') }} →
                            </router-link>
                        </div>

                        <!-- Map -->
                        <div v-if="pkg.package_map_image" class="rounded-xl bg-white p-5 shadow">
                            <h3 class="mb-3 text-lg font-semibold text-gray-800">
                                {{ t('package_details.map') }}
                            </h3>

                            <img :src="pkg.package_map_image" :alt="tr(pkg.package_name)"
                                class="block h-auto w-full rounded-lg object-cover" />
                        </div>

                    </main>


                    <!-- ================================= -->
                    <!-- RIGHT: 4/12 = 33.333% -->
                    <!-- ================================= -->
                    <aside class="w-full min-w-0 lg:w-1/3">

                        <div class="bg-white p-5 shadow lg:sticky lg:top-6">

                            <!-- Package Name -->
                            <h3 class="mb-4 break-words text-xl font-semibold leading-snug text-gray-800">
                                {{ tr(pkg.package_name) }}
                            </h3>

                            <!-- Duration -->
                            <div v-if="tr(pkg.package_duration)"
                                class="mb-4 flex items-center gap-2 font-semibold text-gray-700">
                                <span>🕒</span>

                                <span>
                                    {{ tr(pkg.package_duration) }}
                                </span>
                            </div>

                            <!-- Price -->
                            <div v-if="tr(pkg.package_price)" class="mb-6 border-b border-gray-100 pb-5">
                                <span class="text-gray-700">
                                    {{ t('worldwide_category.cost') }} -
                                </span>

                                <span class="ml-1 text-xl font-bold text-red-600">
                                    {{ tr(pkg.package_price) }} Tk.

                                    <span class="ml-1 text-sm font-semibold">

                                    </span>
                                </span>
                            </div>

                            <!-- Book Now -->
                            <div class="flex items-center justify-center">
                                <router-link :to="`/tour-package/${pkg.slug}/book`"
                                    class="flex min-h-[46px] w-full sm:w-1/2 items-center justify-center bg-amber-500 px-5 py-3 font-semibold text-white transition hover:bg-amber-600">
                                    {{ t('worldwide_category.book_now') }} →
                                </router-link>
                            </div>


                            <!-- Same-country packages -->
                            <div v-if="relatedPackages.length" class="mt-6 border-t border-gray-100 pt-5">
                                <h4 class="mb-4 text-lg font-semibold text-gray-800">
                                    {{ countryName }} {{ t('package_details.more_packages') }}
                                </h4>

                                <ul class="space-y-3">
                                    <li v-for="p in relatedPackages" :key="p.id" class="">
                                        <router-link :to="`/tour-package/${p.slug}`"
                                            class="group relative block h-20 cursor-pointer overflow-hidden pb-4">
                                            <img v-if="p.package_image" :src="p.package_image"
                                                :alt="tr(p.package_image_title) || tr(p.package_name)"
                                                class="absolute inset-0 h-full w-24 object-cover transition duration-500 ease-in-out group-hover:scale-110"
                                                loading="lazy" />

                                            <p
                                                class=" line-clamp-2 break-words ml-25 px-3 text-sm font-semibold text-gray-500">
                                                {{ tr(p.package_name) }}
                                            </p>
                                            <div>
                                                <p
                                                    class=" line-clamp-2 break-words ml-25 px-3 text-sm font-semibold text-red-500">
                                                    <span class="text-gray-500">{{ t('worldwide_category.cost') }}
                                                        -</span>{{
                                                    tr(p.package_price) }} Tk.
                                                </p>
                                            </div>
                                        </router-link>
                                        <div class="pb-4"></div>
                                    </li>
                                </ul>
                            </div>

                        </div>

                    </aside>

                </div>
            </section>

        </template>
    </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, watch } from 'vue'
import { useRoute } from 'vue-router'
import { useI18n } from 'vue-i18n'
import api from '@/services/api'

type Localized = Record<string, string> | null | undefined

interface TourPackage {
    id: number
    slug: string
    hero_image: string | null
    hero_image_title: Localized
    hero_image_btn: Localized
    header: Localized
    sub_header: Localized
    package_name: Localized
    package_image: string | null
    package_image_title: Localized
    package_price: Localized
    package_duration: Localized
    package_map_image: string | null
    package_destination: Localized
    category?: { slug: string; country_name: Localized } | null
    highlights: Highlight[]
    days?: Itinerary[]
    activities?: Activities[]
}

interface RelatedPackage {
    id: number
    slug: string
    package_name: Localized
    package_image: string | null
    package_image_title: Localized
    package_price: Localized
}

interface Highlight {
    id: number
    highlight: string | Record<string, string> | null
    icon: string | null
    order: number
    is_active: boolean
}

type TourInformationValue =
    string | Record<string, string | null> | null | undefined

interface TourInformation {
    id: number
    tour_package_id: number
    title: TourInformationValue
    content: TourInformationValue
    order: number
    is_active: boolean
}

type LocalizedValue = string | Record<string, string | null> | null | undefined

interface Itinerary {
    id: number
    day_number: number
    highlights: LocalizedValue
    overnight: LocalizedValue
    description: LocalizedValue
    map_image: string | null
    image_caption: LocalizedValue
    order: number
    is_active: boolean
}

interface Activities {
    id: number
    tour_package_itinerary_id: number

    title?: LocalizedValue
    description?: LocalizedValue

    image: string | null
    image_caption?: LocalizedValue

    order: number
    is_active: boolean

    created_at?: string
    updated_at?: string
}

const route = useRoute()
const { t, locale } = useI18n({ useScope: 'global' })

const pkg = ref<TourPackage | null>(null)
const related = ref<RelatedPackage[]>([])
const relatedTitle = ref('')

const tourInformation = ref<TourInformation[]>([])
const openInformation = ref<number[]>([])

const loading = ref(true)
const error = ref('')

const tr = (value: Localized): string => {
    if (!value) return ''
    return value[locale.value] || value.en || value.bn || ''
}

// highlight can be a plain string (server-translated) or an {en, bn} object
const trh = (value: Highlight['highlight']): string => {
    if (!value) return ''
    if (typeof value === 'string') return value
    return value[locale.value] || value.en || value.bn || ''
}

const activeHighlights = computed(() =>
    (pkg.value?.highlights ?? []).filter((h) => h.is_active),
)

const activeTourInformation = computed(() =>
    tourInformation.value
        .filter((item) => item.is_active)
        .sort((a, b) => a.order - b.order)
)

const triInfo = (value: TourInformationValue): string => {
    if (!value) return ''

    if (typeof value === 'string') {
        return value
    }

    return (
        value[locale.value] ||
        value.en ||
        value.bn ||
        ''
    )
}

const toggleInformation = (id: number) => {
    if (openInformation.value.includes(id)) {
        openInformation.value =
            openInformation.value.filter(
                itemId => itemId !== id
            )
    } else {
        openInformation.value.push(id)
    }
}

const tri = (value: LocalizedValue): string => {
    if (!value) return ''
    if (typeof value === 'string') return value
    return value[locale.value] || value.en || value.bn || ''
}

const activeDays = computed(() =>
    (pkg.value?.days ?? [])
        .filter((d) => d.is_active)
        .sort((a, b) => a.day_number - b.day_number),
)

const trc = (
    value?: string | Record<string, string | null> | null,
): string => {
    if (!value) return ''

    if (typeof value === 'string') {
        return value
    }

    return (
        value[locale.value] ||
        value.en ||
        value.bn ||
        ''
    )
}

const dayActivitys = computed(() =>
    (pkg.value?.activities ?? [])
        .filter((activity) => activity.is_active)
        .sort((a, b) => a.order - b.order),
)

// Only present if the API returns the category
const categorySlug = computed(() => pkg.value?.category?.slug ?? '')

const countryName = computed(() => tr(pkg.value?.category?.country_name))

// Same country, without the package currently open
const relatedPackages = computed(() =>
    related.value.filter((p) => p.slug !== pkg.value?.slug),
)

// A sidebar failure must never break the main page
const fetchRelated = async () => {
    related.value = []
    relatedTitle.value = ''
    if (!categorySlug.value) return
    try {
        const { data } = await api.get(`/category/${categorySlug.value}/tour-package`)
        related.value = data.data ?? []
        relatedTitle.value = tr(data.category?.country_name)
    } catch {
        related.value = []
    }
}

const fetchTourInformation = async (
    tourPackageId: number
) => {
    try {
        const { data } = await api.get(
            '/tour-information',
            {
                params: {
                    tour_package_id: tourPackageId,
                },
            }
        )

        tourInformation.value = data.data ?? []

    } catch (error) {
        console.error(
            'Failed to load tour information:',
            error
        )

        tourInformation.value = []
        openInformation.value = []
    }
}

const fetchData = async () => {
    loading.value = true
    error.value = ''

    try {
        const { data } = await api.get(
            `/tour-package/${route.params.slug}`
        )

        pkg.value = data.data

        // Load tour information
        if (pkg.value?.id) {
            await fetchTourInformation(pkg.value.id)
        }

        // Load related packages
        fetchRelated()

    } catch (e: any) {
        error.value =
            e?.response?.status === 404
                ? 'Package not found.'
                : 'Failed to load package. Please try again.'
    } finally {
        loading.value = false
    }
}

onMounted(fetchData)
watch(() => route.params.slug, (n, o) => n && n !== o && fetchData())
</script>