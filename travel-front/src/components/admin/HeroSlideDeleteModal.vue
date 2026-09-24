<template>
  <div class="fixed inset-0 bg-black/40 flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-lg w-full max-w-sm p-6">
      <h2 class="font-semibold text-gray-800 mb-2">Delete Hero Slide?</h2>
      <p class="text-sm text-gray-500 mb-6">
        This will permanently remove
        <strong>{{ slide.translations?.title?.en || slide.title }}</strong>. This action can't be undone.
      </p>
      <div class="flex justify-end gap-3">
        <button @click="$emit('close')" class="px-4 py-2 text-sm text-gray-600">Cancel</button>
        <button
          @click="confirm"
          :disabled="deleting"
          class="px-4 py-2 bg-red-600 text-white rounded-md text-sm font-medium hover:bg-red-700 disabled:opacity-50"
        >
          {{ deleting ? "Deleting..." : "Delete" }}
        </button>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from "vue";
import { useHeroSlides } from "@/composables/useHeroSlides";
import type { HeroSlide } from "@/types/heroSlide";

const props = defineProps<{ slide: HeroSlide }>();
const emit = defineEmits<{ close: []; deleted: [] }>();

const { remove } = useHeroSlides();
const deleting = ref(false);

async function confirm() {
  deleting.value = true;
  try {
    await remove(props.slide.id);
    emit("deleted");
  } catch (e: any) {
    alert(e?.response?.data?.message || "Failed to delete.");
  } finally {
    deleting.value = false;
  }
}
</script>