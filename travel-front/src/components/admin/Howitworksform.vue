<template>
  <Teleport to="body">
    <div v-if="open" class="fixed inset-0 z-50 flex items-center justify-center p-4">
      <div class="absolute inset-0 bg-black/40" @click="close"></div>

      <div class="relative bg-white rounded-lg shadow-lg w-full max-w-2xl max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between px-6 py-4 border-b">
          <h2 class="text-lg font-semibold">{{ isEdit ? "Edit Step" : "Add Step" }}</h2>
          <button type="button" @click="close" class="text-gray-400 hover:text-gray-600 text-xl leading-none">
            &times;
          </button>
        </div>

        <form @submit.prevent="submit" class="p-6 space-y-5">
          <!-- Locale tabs -->
          <div class="flex gap-2 border-b mb-4">
            <button
              v-for="locale in locales"
              :key="locale"
              type="button"
              @click="activeLocale = locale"
              :class="[
                'px-3 py-2 text-sm border-b-2 -mb-px',
                activeLocale === locale
                  ? 'border-blue-600 text-blue-600 font-medium'
                  : 'border-transparent text-gray-500 hover:text-gray-700',
              ]"
            >
              {{ locale.toUpperCase() }}
            </button>
          </div>

          <div v-for="locale in locales" :key="locale" v-show="activeLocale === locale" class="space-y-4">
            <div>
              <label class="block text-sm font-medium mb-1">
                Heading <span v-if="locale === 'en'" class="text-red-500">*</span>
              </label>
              <input
                v-model="form.heading[locale]"
                type="text"
                class="w-full border rounded px-3 py-2"
                :required="locale === 'en'"
              />
            </div>

            <div>
              <label class="block text-sm font-medium mb-1">Topline</label>
              <input v-model="form.topline[locale]" type="text" class="w-full border rounded px-3 py-2" />
            </div>

            <div>
              <label class="block text-sm font-medium mb-1">
                Title <span v-if="locale === 'en'" class="text-red-500">*</span>
              </label>
              <input
                v-model="form.title[locale]"
                type="text"
                class="w-full border rounded px-3 py-2"
                :required="locale === 'en'"
              />
            </div>

            <div>
              <label class="block text-sm font-medium mb-1">Description</label>
              <textarea v-model="form.description[locale]" rows="3" class="w-full border rounded px-3 py-2"></textarea>
            </div>
          </div>

          <!-- Non-translatable fields -->
          <div class="grid grid-cols-2 gap-4 pt-2 border-t">
            <div>
              <label class="block text-sm font-medium mb-1">Icon (e.g. fas:plane)</label>
              <input v-model="iconInput" type="text" class="w-full border rounded px-3 py-2" placeholder="fas:plane" />
            </div>
            <div>
              <label class="block text-sm font-medium mb-1">Order</label>
              <input v-model.number="form.order" type="number" min="0" class="w-full border rounded px-3 py-2" />
            </div>
          </div>

          <label class="flex items-center gap-2 text-sm">
            <input v-model="form.is_active" type="checkbox" />
            Active (visible on site)
          </label>

          <p v-if="error" class="text-red-600 text-sm">{{ error }}</p>

          <div class="flex justify-end gap-3 pt-2 border-t mt-2">
            <button type="button" @click="close" class="px-4 py-2 border rounded hover:bg-gray-50">
              Cancel
            </button>
            <button
              type="submit"
              :disabled="saving"
              class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 disabled:opacity-50"
            >
              {{ saving ? "Saving..." : "Save" }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </Teleport>
</template>

<script setup lang="ts">
import { reactive, ref, computed, watch } from "vue";
import { howItWorksApi, type HowItWorksStep, type HowItWorksStepPayload } from "@/types/Howitworks";

const locales = ["en", "bn"]; // must match backend HowItWorksStepRequest::locales()

const props = defineProps<{
  open: boolean;
  step?: HowItWorksStep | null; // null/undefined = create mode
}>();

const emit = defineEmits<{
  (e: "update:open", value: boolean): void;
  (e: "saved", step: HowItWorksStep): void;
}>();

const isEdit = computed(() => !!props.step);
const saving = ref(false);
const error = ref("");
const activeLocale = ref(locales[0]);
const iconInput = ref("");

function emptyLocaleMap() {
  return Object.fromEntries(locales.map((l) => [l, ""]));
}

function blankForm(): HowItWorksStepPayload {
  return {
    heading: emptyLocaleMap(),
    topline: emptyLocaleMap(),
    title: emptyLocaleMap(),
    description: emptyLocaleMap(),
    icon: "",
    order: 0,
    is_active: true,
  };
}

const form = reactive<HowItWorksStepPayload>(blankForm());

// Reset/populate the form whenever the modal is opened (for either create or edit).
watch(
  () => props.open,
  (isOpen) => {
    if (!isOpen) return;

    error.value = "";
    activeLocale.value = locales[0];

    if (props.step) {
      const t = props.step.translations;
      form.heading = { ...emptyLocaleMap(), ...(t?.heading ?? {}) };
      form.topline = { ...emptyLocaleMap(), ...(t?.topline ?? {}) };
      form.title = { ...emptyLocaleMap(), ...(t?.title ?? {}) };
      form.description = { ...emptyLocaleMap(), ...(t?.description ?? {}) };
      iconInput.value = props.step.icon.join(":");
      form.order = props.step.order;
      form.is_active = props.step.is_active;
    } else {
      Object.assign(form, blankForm());
      iconInput.value = "";
    }
  }
);

function close() {
  emit("update:open", false);
}

async function submit() {
  saving.value = true;
  error.value = "";
  form.icon = iconInput.value || "fas:circle";

  try {
    const { data } = props.step
      ? await howItWorksApi.update(props.step.id, form)
      : await howItWorksApi.create(form);

    emit("saved", data.data);
    close();
  } catch (e: any) {
    error.value = e?.response?.data?.message || "Failed to save step.";
  } finally {
    saving.value = false;
  }
}
</script>