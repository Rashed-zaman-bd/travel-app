<template>
    <Teleport to="body">
        <div
            v-if="modelValue"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4"
            @mousedown.self="close"
        >
            <div class="w-full max-w-sm rounded bg-white p-5 shadow-xl">
                <h3 class="text-lg font-semibold text-gray-800">{{ title }}</h3>
                <p class="mt-2 text-sm text-gray-600">{{ message }}</p>

                <div class="mt-5 flex justify-end gap-2">
                    <button
                        type="button"
                        class="rounded border border-gray-300 px-4 py-2 text-sm hover:bg-gray-50"
                        :disabled="loading"
                        @click="close"
                    >
                        Cancel
                    </button>
                    <button
                        type="button"
                        class="rounded bg-red-600 px-4 py-2 text-sm text-white hover:bg-red-700 disabled:opacity-50"
                        :disabled="loading"
                        @click="emit('confirm')"
                    >
                        {{ loading ? 'Deleting...' : confirmText }}
                    </button>
                </div>
            </div>
        </div>
    </Teleport>
</template>

<script setup lang="ts">
withDefaults(
    defineProps<{
        modelValue: boolean
        title?: string
        message?: string
        confirmText?: string
        loading?: boolean
    }>(),
    {
        title: 'Are you sure?',
        message: '',
        confirmText: 'Delete',
        loading: false,
    },
)

const emit = defineEmits<{
    (e: 'update:modelValue', v: boolean): void
    (e: 'confirm'): void
}>()

const close = () => emit('update:modelValue', false)
</script>