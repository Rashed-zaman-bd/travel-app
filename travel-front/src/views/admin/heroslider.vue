<template>
  <div class="p-6">
    <div class="flex items-center justify-between mb-6">
      <h1 class="text-xl font-semibold text-gray-800">Hero Slider</h1>
      <button
        @click="openCreate"
        class="px-4 py-2 bg-indigo-600 text-white rounded-md text-sm font-medium hover:bg-indigo-700"
      >
        + Add Slide
      </button>
    </div>

    <div v-if="loading" class="text-sm text-gray-500">Loading...</div>
    <div v-else-if="error" class="text-sm text-red-600">{{ error }}</div>

    <div v-else class="overflow-x-auto bg-white rounded-lg border border-gray-200">
      <table class="min-w-full divide-y divide-gray-200 text-sm">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-4 py-3 text-left font-medium text-gray-500 w-16">Order</th>
            <th class="px-4 py-3 text-left font-medium text-gray-500 w-24">Image</th>
            <th class="px-4 py-3 text-left font-medium text-gray-500">Title</th>
            <th class="px-4 py-3 text-left font-medium text-gray-500">CTA</th>
            <th class="px-4 py-3 text-left font-medium text-gray-500 w-24">Active</th>
            <th class="px-4 py-3 text-right font-medium text-gray-500 w-32">Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
          <tr v-for="slide in sortedSlides" :key="slide.id">
            <td class="px-4 py-3 text-gray-500">{{ slide.order }}</td>
            <td class="px-4 py-3">
              <img :src="slide.image" class="w-16 h-10 object-cover rounded" />
            </td>
            <td class="px-4 py-3 font-medium text-gray-800">
              {{ slide.translations?.title?.en || slide.title }}
              <div class="text-xs text-gray-400">{{ slide.translations?.title?.bn }}</div>
            </td>
            <td class="px-4 py-3 text-gray-500">{{ slide.translations?.cta_text?.en || "—" }}</td>
            <td class="px-4 py-3">
              <span
                class="px-2 py-1 rounded-full text-xs font-medium"
                :class="slide.is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500'"
              >
                {{ slide.is_active ? "Active" : "Hidden" }}
              </span>
            </td>
            <td class="px-4 py-3 text-right space-x-2">
              <button @click="openEdit(slide)" class="text-indigo-600 hover:underline">Edit</button>
              <button @click="openDelete(slide)" class="text-red-600 hover:underline">Delete</button>
            </td>
          </tr>
        </tbody>
      </table>

      <div v-if="!slides.length" class="p-8 text-center text-gray-400 text-sm">
        No hero slides yet.
      </div>
    </div>

    <HeroSlideFormModal
      v-if="showForm"
      :slide="editingSlide"
      @close="showForm = false"
      @saved="onSaved"
    />

    <HeroSlideDeleteModal
      v-if="deletingSlide"
      :slide="deletingSlide"
      @close="deletingSlide = null"
      @deleted="onDeleted"
    />
  </div>
</template>

<script setup lang="ts">
import { computed, ref, onMounted } from "vue";
import { useHeroSlides } from "@/composables/useHeroSlides";
import type { HeroSlide } from "@/types/heroSlide";
import HeroSlideFormModal from "@/components/admin/HeroSlideFormModal.vue";
import HeroSlideDeleteModal from "@/components/admin/HeroSlideDeleteModal.vue";

const { slides, loading, error, fetchAll } = useHeroSlides();

const sortedSlides = computed(() =>
  [...slides.value].sort((a, b) => a.order - b.order)
);

const showForm = ref(false);
const editingSlide = ref<HeroSlide | null>(null);
const deletingSlide = ref<HeroSlide | null>(null);

onMounted(fetchAll);

function openCreate() {
  editingSlide.value = null;
  showForm.value = true;
}

function openEdit(slide: HeroSlide) {
  editingSlide.value = slide;
  showForm.value = true;
}

function openDelete(slide: HeroSlide) {
  deletingSlide.value = slide;
}

function onSaved() {
  showForm.value = false;
  fetchAll();
}

function onDeleted() {
  deletingSlide.value = null;
}
</script>