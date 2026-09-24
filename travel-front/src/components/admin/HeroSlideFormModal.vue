<template>
  <div class="fixed inset-0 bg-black/40 flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-lg w-full max-w-2xl max-h-[90vh] overflow-y-auto">
      <div class="flex items-center justify-between px-6 py-4 border-b">
        <h2 class="font-semibold text-gray-800">
          {{ isEdit ? "Edit Slide" : "New Slide" }}
        </h2>
        <button @click="$emit('close')" class="text-gray-400 hover:text-gray-600">✕</button>
      </div>

      <form @submit.prevent="submit" class="px-6 py-4 space-y-5">
        <!-- Locale tabs -->
        <div class="flex gap-2 border-b mb-2">
          <button
            type="button"
            v-for="loc in ['en', 'bn']"
            :key="loc"
            @click="activeLocale = loc as 'en' | 'bn'"
            class="px-3 py-2 text-sm font-medium border-b-2 -mb-px"
            :class="activeLocale === loc ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-gray-500'"
          >
            {{ loc === "en" ? "English" : "বাংলা" }}
          </button>
        </div>

        <div class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Title</label>
            <input
              v-model="form.title[activeLocale]"
              type="text"
              class="w-full border rounded-md px-3 py-2 text-sm"
              :required="activeLocale === 'en'"
            />
            <p v-if="errors['title.en']" class="text-xs text-red-600 mt-1">{{ errors['title.en'][0] }}</p>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
            <textarea
              v-model="form.description[activeLocale]"
              rows="3"
              class="w-full border rounded-md px-3 py-2 text-sm"
              :required="activeLocale === 'en'"
            />
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Company Name</label>
              <input v-model="form.com_name[activeLocale]" type="text" class="w-full border rounded-md px-3 py-2 text-sm" />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">CTA Text</label>
              <input v-model="form.cta_text[activeLocale]" type="text" class="w-full border rounded-md px-3 py-2 text-sm" />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Photo Caption</label>
              <input v-model="form.photo_text[activeLocale]" type="text" class="w-full border rounded-md px-3 py-2 text-sm" />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Location</label>
              <input v-model="form.location[activeLocale]" type="text" class="w-full border rounded-md px-3 py-2 text-sm" />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Author Name</label>
              <input v-model="form.author_name[activeLocale]" type="text" class="w-full border rounded-md px-3 py-2 text-sm" />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Photo Date</label>
              <input v-model="form.photo_date[activeLocale]" type="text" class="w-full border rounded-md px-3 py-2 text-sm" />
            </div>
          </div>
        </div>

        <hr />

        <!-- Non-translatable fields -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Background Image</label>
          <input type="file" accept="image/*" @change="onFileChange" class="text-sm" />
          <img v-if="previewUrl || form.image" :src="previewUrl || form.image" class="mt-2 h-24 rounded object-cover" />
          <p v-if="errors.image" class="text-xs text-red-600 mt-1">{{ errors.image[0] }}</p>
          <p v-if="!isEdit && !pendingFile" class="text-xs text-gray-400 mt-1">An image is required for a new slide.</p>
        </div>

        <div class="grid grid-cols-3 gap-4">
          <div class="col-span-2">
            <label class="block text-sm font-medium text-gray-700 mb-1">CTA URL</label>
            <input v-model="form.cta_url" type="text" class="w-full border rounded-md px-3 py-2 text-sm" />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Order</label>
            <input v-model.number="form.order" type="number" min="0" class="w-full border rounded-md px-3 py-2 text-sm" />
          </div>
        </div>

        <label class="flex items-center gap-2 text-sm text-gray-700">
          <input v-model="form.is_active" type="checkbox" class="rounded" />
          Active
        </label>

        <div class="flex justify-end gap-3 pt-2">
          <button type="button" @click="$emit('close')" class="px-4 py-2 text-sm text-gray-600">
            Cancel
          </button>
          <button
            type="submit"
            :disabled="saving"
            class="px-4 py-2 bg-indigo-600 text-white rounded-md text-sm font-medium hover:bg-indigo-700 disabled:opacity-50"
          >
            {{ saving ? "Saving..." : isEdit ? "Update Slide" : "Create Slide" }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive, computed } from "vue";
import { useHeroSlides } from "@/composables/useHeroSlides";
import { emptyHeroSlideForm } from "@/types/heroSlide";
import type { HeroSlide, HeroSlideFormData } from "@/types/heroSlide";

const props = defineProps<{ slide: HeroSlide | null }>();
const emit = defineEmits<{ close: []; saved: [] }>();

const { create, update } = useHeroSlides();

const isEdit = computed(() => !!props.slide);
const activeLocale = ref<"en" | "bn">("en");
const saving = ref(false);
const errors = ref<Record<string, string[]>>({});
const previewUrl = ref<string | null>(null);
const pendingFile = ref<File | null>(null);

const form = reactive<HeroSlideFormData>(
  props.slide
    ? {
        title: { en: props.slide.translations?.title?.en || "", bn: props.slide.translations?.title?.bn || "" },
        description: { en: props.slide.translations?.description?.en || "", bn: props.slide.translations?.description?.bn || "" },
        com_name: { en: props.slide.translations?.com_name?.en || "", bn: props.slide.translations?.com_name?.bn || "" },
        cta_text: { en: props.slide.translations?.cta_text?.en || "", bn: props.slide.translations?.cta_text?.bn || "" },
        photo_text: { en: props.slide.translations?.photo_text?.en || "", bn: props.slide.translations?.photo_text?.bn || "" },
        location: { en: props.slide.translations?.location?.en || "", bn: props.slide.translations?.location?.bn || "" },
        author_name: { en: props.slide.translations?.author_name?.en || "", bn: props.slide.translations?.author_name?.bn || "" },
        photo_date: { en: props.slide.translations?.photo_date?.en || "", bn: props.slide.translations?.photo_date?.bn || "" },
        image: props.slide.image,
        cta_url: props.slide.cta_url || "",
        order: props.slide.order,
        is_active: props.slide.is_active,
      }
    : emptyHeroSlideForm()
);

function onFileChange(e: Event) {
  const file = (e.target as HTMLInputElement).files?.[0];
  if (!file) return;
  pendingFile.value = file;
  previewUrl.value = URL.createObjectURL(file);
}

async function submit() {
  saving.value = true;
  errors.value = {};
  try {
    if (!isEdit.value && !pendingFile.value) {
      errors.value = { image: ["Please choose an image."] };
      saving.value = false;
      return;
    }

    if (isEdit.value && props.slide) {
      await update(props.slide.id, form, pendingFile.value);
    } else {
      await create(form, pendingFile.value as File);
    }
    emit("saved");
  } catch (e: any) {
    if (e?.response?.status === 422) {
      errors.value = e.response.data.errors;
    } else {
      alert(e?.response?.data?.message || "Something went wrong.");
    }
  } finally {
    saving.value = false;
  }
}
</script>