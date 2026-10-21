<template>
    <section class="mx-auto max-w-7xl px-2 py-8 md:px-6">
        <h2 class="text-2xl font-bold text-blue-950">List of Documents Needed</h2>

        <!-- Loading -->
        <div v-if="loading" class="mt-6 space-y-6" aria-busy="true">
            <div v-for="n in 2" :key="n" class="animate-pulse space-y-3">
                <div class="h-4 w-56 rounded bg-gray-200"></div>
                <div class="h-3 w-full max-w-2xl rounded bg-gray-100"></div>
                <div class="h-3 w-full max-w-xl rounded bg-gray-100"></div>
                <div class="h-3 w-full max-w-2xl rounded bg-gray-100"></div>
            </div>
        </div>

        <!-- Error -->
        <div v-else-if="error" class="mt-6 rounded-lg bg-red-50 px-4 py-3 text-sm text-red-700">
            {{ error }}
            <button type="button" class="ml-2 font-semibold underline" @click="fetchDocuments">Try again</button>
        </div>

        <!-- Empty -->
        <p v-else-if="!categories.length" class="mt-6 text-sm text-gray-500">
            No document list has been published for this country and visa type yet.
        </p>

        <!-- Categories -->
        <div v-else class="mt-6 space-y-8">
            <article v-for="category in categories" :key="category.id">
                <h3 class="text-base font-semibold text-gray-700">
                    {{ localized(category.title) }}
                </h3>

                <ol class="mt-3 list-decimal space-y-2 pl-5 text-sm leading-relaxed text-gray-600 marker:text-gray-500">
                    <li v-for="doc in category.documents ?? []" :key="doc.id">
                        <p v-if="localized(doc.description)" class="mt-0.5 text-base text-gray-400">
                            {{ localized(doc.description) }}
                        </p>
                    </li>
                </ol>
            </article>
        </div>

        <div class="pt-6">
            <p class="text-gray-600"><span class="text-black">N.B</span>: All documents must be submitted in physical form, in person at our office.</p>
        </div>
    </section>
</template>

<script setup lang="ts">
import { ref, watch } from 'vue'
import { useI18n } from 'vue-i18n'
import api from '@/services/api'

type Localized = Record<string, string | null | undefined> | string | null | undefined

interface VisaDocumentItem {
    id: number
    document_name: Localized
    description: Localized
    sort_order: number
}

interface DocumentCategory {
    id: number
    title: Localized
    sort_order: number
    documents?: VisaDocumentItem[]
}

const props = defineProps<{
    countryId: number
    typeId: number
}>()

const { locale } = useI18n()

// We request all locales and pick the language here, so switching
// language updates the list instantly without another API call.
function localized(value?: Localized): string {
    if (!value) return ''
    if (typeof value === 'string') return value
    return value[locale.value] || value.en || ''
}

const categories = ref<DocumentCategory[]>([])
const loading = ref(false)
const error = ref('')

// Ignore responses from outdated requests (user searches again quickly)
let requestId = 0

async function fetchDocuments() {
    const current = ++requestId
    loading.value = true
    error.value = ''

    try {
        const { data } = await api.get('/visa-documents/grouped', {
            params: {
                visa_country_id: props.countryId,
                visa_type_id: props.typeId,
                all_locales: 1,
            },
        })
        if (current !== requestId) return
        categories.value = data.data ?? []
    } catch {
        if (current !== requestId) return
        categories.value = []
        error.value = 'Could not load the document list. Please try again.'
    } finally {
        if (current === requestId) loading.value = false
    }
}

watch(() => [props.countryId, props.typeId], fetchDocuments, { immediate: true })
</script>