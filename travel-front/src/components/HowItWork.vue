<template>
  <section class="w-full border-y border-gray-200 bg-white">
    <div class="mx-auto w-full max-w-7xl px-4 sm:px-6 lg:px-8">
      <div class="flex flex-col gap-8 py-8 lg:flex-row lg:items-center lg:gap-0">

        <!-- =========================
             Left Label
        ========================== -->
        <div
          class="flex items-center justify-center lg:w-40 lg:shrink-0 lg:justify-start lg:border-r lg:border-gray-200 lg:pr-6"
        >
          <span class="text-sm font-bold tracking-wide text-slate-600">
            {{ sectionLabel }}
          </span>
        </div>

        <!-- =========================
             Loading / Error states
        ========================== -->
        <div v-if="loading" class="flex-1 py-4 text-sm text-slate-400 lg:pl-10">
          Loading...
        </div>
        <div v-else-if="error" class="flex-1 py-4 text-sm text-red-500 lg:pl-10">
          {{ error }}
        </div>

        <!-- =========================
             Steps
        ========================== -->
        <div
          v-else
          class="grid w-full flex-1 grid-cols-1 gap-8 sm:grid-cols-3 lg:pl-10"
        >
          <div
            v-for="(step, index) in steps"
            :key="step.id"
            class="flex flex-col items-center gap-2 text-center sm:items-start sm:text-left"
          >

            <!-- Icon + Step -->
            <div class="flex flex-col items-center gap-2 sm:flex-row">
              <font-awesome-icon
                :icon="step.icon"
                class="text-4xl text-amber-400"
              />

              <span class="text-base font-bold tracking-wide text-amber-400">
                {{ step.topline }} {{ index + 1 }}
              </span>
            </div>

            <!-- Title -->
            <h3 class="mt-1 text-base font-bold leading-snug text-slate-800">
              {{ step.title }}
            </h3>

            <!-- Description -->
            <p class="max-w-xs text-sm leading-relaxed text-slate-500">
              {{ step.description }}
            </p>

          </div>
        </div>

      </div>
    </div>
  </section>
</template>

<script setup lang="ts">
import { ref, computed, watch, onMounted } from "vue";
import { useI18n } from "vue-i18n";
import { howItWorksApi, type HowItWorksStep } from "@/types/Howitworks";

const { locale } = useI18n();

const steps = ref<HowItWorksStep[]>([]);
const loading = ref(true);
const error = ref("");

// Falls back to a static label until the first step loads, then mirrors
// the first step's translated heading (same value across all steps).
const sectionLabel = computed(() => steps.value[0]?.heading ?? "HOW IT WORKS");

async function fetchSteps() {
  loading.value = true;
  error.value = "";
  try {
    const { data } = await howItWorksApi.list();
    steps.value = data.data;
  } catch (e) {
    error.value = "Couldn't load this section right now.";
  } finally {
    loading.value = false;
  }
}

// Re-fetch whenever the app's active locale changes, so the localized
// title/description/topline/heading strings actually update in place.
watch(locale, fetchSteps);

onMounted(fetchSteps);
</script>