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
                <h2 v-if="tr(pkg.header)" class="mb-2 text-xl font-semibold text-amber-500 sm:text-3xl">
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
                                " class="block h-96 w-full object-cover" 
                            />
                        </div>
                        <!-- Descriptiontion -->
                        <div  class="">
                            <p class="whitespace-pre-line break-words text-xl text-gray-700">
                                {{ tr(pkg.package_destination) }}
                            </p>
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
                                    class="flex min-h-[46px] w-1/2 items-center justify-center bg-amber-500 px-5 py-3 font-semibold text-white transition hover:bg-amber-600">
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
                                <router-link
                                    :to="`/tour-package/${p.slug}`"
                                    class="group relative block h-20 cursor-pointer overflow-hidden pb-4"
                                >
                                    <img
                                    v-if="p.package_image"
                                    :src="p.package_image"
                                    :alt="tr(p.package_image_title) || tr(p.package_name)"
                                    class="absolute inset-0 h-full w-24 object-cover transition duration-500 ease-in-out group-hover:scale-110"
                                    loading="lazy"
                                    />
                                    
                                    <p class=" line-clamp-2 break-words ml-25 px-3 text-sm font-semibold text-gray-500">
                                    {{ tr(p.package_name) }}
                                    </p>
                                    <div>
                                        <p  class=" line-clamp-2 break-words ml-25 px-3 text-sm font-semibold text-red-500">
                                            <span class="text-gray-500">{{ t('worldwide_category.cost') }} -</span>{{ tr(p.package_price) }} Tk.
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
}

interface RelatedPackage {
    id: number
    slug: string
    package_name: Localized
    package_image: string | null
    package_image_title: Localized
    package_price: Localized
}

const route = useRoute()
const { t, locale } = useI18n({ useScope: 'global' })

const pkg = ref<TourPackage | null>(null)
const related = ref<RelatedPackage[]>([])
const relatedTitle = ref('')
const loading = ref(true)
const error = ref('')

const tr = (value: Localized): string => {
    if (!value) return ''
    return value[locale.value] || value.en || value.bn || ''
}

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

const fetchData = async () => {
    loading.value = true
    error.value = ''
    try {
        const { data } = await api.get(`/tour-package/${route.params.slug}`)
        pkg.value = data.data
        fetchRelated() // not awaited, so the page shows immediately
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