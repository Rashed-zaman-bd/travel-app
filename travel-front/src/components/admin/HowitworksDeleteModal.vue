<template>
  <Teleport to="body">
    <div v-if="open" class="fixed inset-0 z-50 flex items-center justify-center">
      <div class="absolute inset-0 bg-black/40" @click="cancel"></div>

      <div class="relative bg-white rounded-lg shadow-lg w-full max-w-sm p-6">
        <h2 class="text-lg font-semibold mb-2">{{ title }}</h2>
        <p class="text-gray-600 text-sm mb-6">{{ message }}</p>

        <div class="flex justify-end gap-3">
          <button type="button" @click="cancel" class="px-4 py-2 border rounded hover:bg-gray-50">
            Cancel
          </button>
          <button
            type="button"
            @click="confirm"
            :disabled="loading"
            class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 disabled:opacity-50"
          >
            {{ loading ? "Deleting..." : "Delete" }}
          </button>
        </div>
      </div>
    </div>
  </Teleport>
</template>

<script setup lang="ts">
defineProps<{
  open: boolean;
  title: string;
  message: string;
  loading?: boolean;
}>();

const emit = defineEmits<{
  (e: "update:open", value: boolean): void;
  (e: "confirm"): void;
}>();

function cancel() {
  emit("update:open", false);
}

function confirm() {
  emit("confirm");
}
</script>