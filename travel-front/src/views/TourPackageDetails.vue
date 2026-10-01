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
            <section class="mx-auto w-full max-w-7xl px-4 py-8 sm:px-6 lg:px-8">

                <!-- Back -->
                <router-link v-if="categorySlug" :to="`/country/${categorySlug}`"
                    class="mb-4 inline-block text-sm text-blue-600 hover:underline">
                    ← {{ t('package_details.back') }}
                </router-link>

                <!-- Header -->
                <h2 v-if="tr(pkg.header)" class="mb-2 text-xl font-semibold text-amber-500 sm:text-3xl">
                    {{ tr(pkg.header) }}
                </h2>

                <p v-if="tr(pkg.sub_header)" class="mb-6 text-gray-600 sm:text-lg">
                    {{ tr(pkg.sub_header) }}
                </p>

                <!-- ================================= -->
                <!-- 8 : 4 RESPONSIVE GRID -->
                <!-- ================================= -->
                <!-- Main Content -->
                <div class="flex w-full flex-col gap-6 lg:flex-row lg:items-start lg:gap-8">

                    <!-- ================================= -->
                    <!-- LEFT: 8/12 = 66.666% -->
                    <!-- ================================= -->
                    <main class=" min-w-0 w-70%">

                        <!-- Package Image -->
                        <div v-if="pkg.package_image" class="mb-6 overflow-hidden rounded-xl bg-gray-200 shadow">
                            <img :src="pkg.package_image" :alt="tr(pkg.package_image_title) ||
                                tr(pkg.package_name)
                                " class="block h-auto w-full object-cover" />

                            <p v-if="tr(pkg.package_image_title)"
                                class="bg-white p-3 text-center text-sm text-gray-500">
                                {{ tr(pkg.package_image_title) }}
                            </p>
                        </div>

                        <!-- Destination -->
                        <div v-if="tr(pkg.package_destination)" class="mb-6 rounded-xl bg-white p-5 shadow">
                            <h3 class="mb-3 text-lg font-semibold text-gray-800">
                                {{ t('package_details.destinations') }}
                            </h3>

                            <p class="whitespace-pre-line break-words text-gray-600">
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
                    <aside class=" min-w-0 w-30%">

                        <div class="rounded-xl bg-white p-5 shadow lg:sticky lg:top-6">

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
                                    {{ tr(pkg.package_price) }}

                                    <span class="ml-1 text-sm font-semibold">
                                        Tk.
                                    </span>
                                </span>
                            </div>

                            <!-- Book Now -->
                            <router-link :to="`/tour-package/${pkg.slug}/book`"
                                class="flex min-h-[46px] w-full items-center justify-center rounded-lg bg-blue-600 px-5 py-3 font-semibold text-white transition hover:bg-blue-700">
                                {{ t('worldwide_category.book_now') }} →
                            </router-link>

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
    category?: { slug: string } | null
}

const route = useRoute()
const { t, locale } = useI18n({ useScope: 'global' })

const pkg = ref<TourPackage | null>(null)
const loading = ref(true)
const error = ref('')

const tr = (value: Localized): string => {
    if (!value) return ''
    return value[locale.value] || value.en || value.bn || ''
}

// Only present if the API returns the category (see note below)
const categorySlug = computed(() => pkg.value?.category?.slug ?? '')

const fetchData = async () => {
    loading.value = true
    error.value = ''
    try {
        const { data } = await api.get(`/tour-package/${route.params.slug}`)
        pkg.value = data.data
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