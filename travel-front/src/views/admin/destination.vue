```vue
<template>
  <div class="p-4 md:p-6">
    <!-- Header -->
    <div
      class="mb-6 flex flex-col gap-4 rounded-xl bg-white p-5 shadow-sm md:flex-row md:items-center md:justify-between"
    >
      <div>
        <h1 class="text-2xl font-bold text-gray-800">
          Destinations
        </h1>

        <p class="mt-1 text-sm text-gray-500">
          Manage your travel destinations.
        </p>
      </div>

      <button
        type="button"
        @click="openCreate"
        class="rounded-lg bg-emerald-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-emerald-700"
      >
        + Add Destination
      </button>
    </div>

    <!-- Search -->
    <div class="mb-5 rounded-xl bg-white p-4 shadow-sm">
      <input
        v-model="search"
        type="text"
        placeholder="Search destination..."
        class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500"
      />
    </div>

    <!-- Loading -->
    <div
      v-if="loading"
      class="rounded-xl bg-white p-10 text-center shadow-sm"
    >
      <div
        class="mx-auto h-8 w-8 animate-spin rounded-full border-4 border-gray-200 border-t-emerald-600"
      ></div>

      <p class="mt-3 text-sm text-gray-500">
        Loading destinations...
      </p>
    </div>

    <!-- Empty -->
    <div
      v-else-if="filteredDestinations.length === 0"
      class="rounded-xl bg-white p-10 text-center shadow-sm"
    >
      <p class="text-gray-500">
        No destinations found.
      </p>
    </div>

    <!-- Table -->
    <div
      v-else
      class="overflow-hidden rounded-xl bg-white shadow-sm"
    >
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th
                class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-500"
              >
                Order
              </th>

              <th
                class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-500"
              >
                Image
              </th>

              <th
                class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-500"
              >
                Destination
              </th>

              <th
                class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-500"
              >
                Slug
              </th>

              <th
                class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-500"
              >
                Status
              </th>

              <th
                class="px-4 py-3 text-right text-xs font-semibold uppercase text-gray-500"
              >
                Actions
              </th>
            </tr>
          </thead>

          <tbody class="divide-y divide-gray-200">
            <tr
              v-for="destination in filteredDestinations"
              :key="destination.id"
              class="hover:bg-gray-50"
            >
              <!-- Order -->
              <td class="whitespace-nowrap px-4 py-4">
                <span
                  class="inline-flex min-w-10 items-center justify-center rounded-lg bg-gray-100 px-3 py-1.5 text-sm font-semibold text-gray-700"
                >
                  {{ destination.order }}
                </span>
              </td>

              <!-- Image -->
              <td class="px-4 py-4">
                <img
                  v-if="destination.image"
                  :src="destination.image"
                  :alt="getText(destination.destination?.name)"
                  class="h-14 w-20 rounded-lg object-cover"
                />

                <div
                  v-else
                  class="flex h-14 w-20 items-center justify-center rounded-lg bg-gray-100 text-xs text-gray-400"
                >
                  No Image
                </div>
              </td>

              <!-- Destination -->
              <td class="px-4 py-4">
                <div class="font-semibold text-gray-800">
                  {{ getText(destination.destination?.name) }}
                </div>

                <div class="mt-1 text-xs text-gray-500">
                  {{ getText(destination.title) }}
                </div>
              </td>

              <!-- Slug -->
              <td class="px-4 py-4">
                <span class="text-sm text-gray-600">
                  {{ destination.slug }}
                </span>
              </td>

              <!-- Status -->
              <td class="px-4 py-4">
                <button
                  type="button"
                  @click="toggleStatus(destination)"
                  :disabled="statusLoading === destination.id"
                  class="inline-flex items-center gap-2 rounded-full px-3 py-1.5 text-xs font-semibold transition"
                  :class="
                    destination.is_active
                      ? 'bg-emerald-100 text-emerald-700 hover:bg-emerald-200'
                      : 'bg-red-100 text-red-700 hover:bg-red-200'
                  "
                >
                  <span
                    class="h-2 w-2 rounded-full"
                    :class="
                      destination.is_active
                        ? 'bg-emerald-500'
                        : 'bg-red-500'
                    "
                  ></span>

                  {{
                    destination.is_active
                      ? "Active"
                      : "Inactive"
                  }}
                </button>
              </td>

              <!-- Actions -->
              <td class="px-4 py-4 text-right">
                <div class="flex justify-end gap-2">
                  <button
                    type="button"
                    @click="openEdit(destination)"
                    class="rounded-lg bg-blue-50 px-3 py-2 text-xs font-semibold text-blue-600 transition hover:bg-blue-100"
                  >
                    Edit
                  </button>

                  <button
                    type="button"
                    @click="deleteDestination(destination)"
                    class="rounded-lg bg-red-50 px-3 py-2 text-xs font-semibold text-red-600 transition hover:bg-red-100"
                  >
                    Delete
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Modal -->
    <div
      v-if="showModal"
      class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4"
    >
      <div
        class="max-h-[95vh] w-full max-w-5xl overflow-y-auto rounded-2xl bg-white shadow-2xl"
      >
        <!-- Modal Header -->
        <div
          class="sticky top-0 z-10 flex items-center justify-between border-b bg-white px-6 py-4"
        >
          <div>
            <h2 class="text-xl font-bold text-gray-800">
              {{
                editingDestination
                  ? "Edit Destination"
                  : "Add Destination"
              }}
            </h2>

            <p class="mt-1 text-xs text-gray-500">
              Fill in the destination information below.
            </p>
          </div>

          <button
            type="button"
            @click="closeModal"
            class="text-2xl leading-none text-gray-400 hover:text-gray-700"
          >
            &times;
          </button>
        </div>

        <!-- Form -->
        <form
          @submit.prevent="submitForm"
          enctype="multipart/form-data"
          class="p-6"
        >
          <!-- Error -->
          <div
            v-if="formError"
            class="mb-5 rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-700"
          >
            {{ formError }}
          </div>

          <!-- Main Information -->
          <div class="mb-6">
            <h3
              class="mb-4 border-b pb-2 text-lg font-semibold text-gray-800"
            >
              Main Information
            </h3>

            <div class="grid gap-5 md:grid-cols-2">
              <!-- English Title -->
              <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">
                  Title (English) *
                </label>

                <input
                  v-model="form.title.en"
                  type="text"
                  class="input"
                  placeholder="Enter title"
                />

                <ErrorText :error="errors['title.en']" />
              </div>

              <!-- Bangla Title -->
              <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">
                  Title (Bangla)
                </label>

                <input
                  v-model="form.title.bn"
                  type="text"
                  class="input"
                  placeholder="বাংলা শিরোনাম"
                />
              </div>

              <!-- English Subtitle -->
              <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">
                  Sub Title (English) *
                </label>

                <textarea
                  v-model="form.sub_title.en"
                  rows="3"
                  class="input"
                  placeholder="Enter sub title"
                ></textarea>

                <ErrorText :error="errors['sub_title.en']" />
              </div>

              <!-- Bangla Subtitle -->
              <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">
                  Sub Title (Bangla)
                </label>

                <textarea
                  v-model="form.sub_title.bn"
                  rows="3"
                  class="input"
                  placeholder="বাংলা সাবটাইটেল"
                ></textarea>
              </div>

              <!-- Slug -->
              <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">
                  Slug
                </label>

                <input
                  v-model="form.slug"
                  type="text"
                  class="input"
                  placeholder="Leave empty to generate automatically"
                />

                <ErrorText :error="errors.slug" />
              </div>

              <!-- Destination Name English -->
              <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">
                  Destination Name (English) *
                </label>

                <input
                  v-model="form.destination_name.en"
                  type="text"
                  class="input"
                  placeholder="Dubai"
                />

                <ErrorText :error="errors['destination_name.en']" />
              </div>

              <!-- Destination Name Bangla -->
              <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">
                  Destination Name (Bangla)
                </label>

                <input
                  v-model="form.destination_name.bn"
                  type="text"
                  class="input"
                  placeholder="দুবাই"
                />
              </div>

              <!-- Destination Title -->
              <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">
                  Destination Title (English)
                </label>

                <input
                  v-model="form.destination_title.en"
                  type="text"
                  class="input"
                />
              </div>

              <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">
                  Destination Title (Bangla)
                </label>

                <input
                  v-model="form.destination_title.bn"
                  type="text"
                  class="input"
                />
              </div>

              <!-- Destination Subtitle -->
              <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">
                  Destination Sub Title (English)
                </label>

                <textarea
                  v-model="form.destination_sub_title.en"
                  rows="3"
                  class="input"
                ></textarea>
              </div>

              <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">
                  Destination Sub Title (Bangla)
                </label>

                <textarea
                  v-model="form.destination_sub_title.bn"
                  rows="3"
                  class="input"
                ></textarea>
              </div>
            </div>
          </div>

          <!-- Images -->
          <div class="mb-6">
            <h3
              class="mb-4 border-b pb-2 text-lg font-semibold text-gray-800"
            >
              Images
            </h3>

            <div class="grid gap-5 md:grid-cols-3">
              <!-- Main Image -->
              <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">
                  Main Image
                </label>

                <input
                  type="file"
                  accept="image/*"
                  @change="handleFile($event, 'image')"
                  class="file-input"
                />

                <img
                  v-if="imagePreview"
                  :src="imagePreview"
                  class="mt-3 h-32 w-full rounded-lg object-cover"
                />
              </div>

              <!-- Hero -->
              <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">
                  Hero Image
                </label>

                <input
                  type="file"
                  accept="image/*"
                  @change="
                    handleFile($event, 'destination_hero_image')
                  "
                  class="file-input"
                />

                <img
                  v-if="heroPreview"
                  :src="heroPreview"
                  class="mt-3 h-32 w-full rounded-lg object-cover"
                />
              </div>

              <!-- Map -->
              <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">
                  Map Image
                </label>

                <input
                  type="file"
                  accept="image/*"
                  @change="handleFile($event, 'map_image')"
                  class="file-input"
                />

                <img
                  v-if="mapPreview"
                  :src="mapPreview"
                  class="mt-3 h-32 w-full rounded-lg object-cover"
                />
              </div>
            </div>
          </div>

          <!-- Hero Information -->
          <div class="mb-6">
            <h3
              class="mb-4 border-b pb-2 text-lg font-semibold text-gray-800"
            >
              Hero Information
            </h3>

            <div class="grid gap-5 md:grid-cols-2">
              <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">
                  Hero Title (English)
                </label>

                <input
                  v-model="form.destination_hero_title.en"
                  type="text"
                  class="input"
                />
              </div>

              <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">
                  Hero Title (Bangla)
                </label>

                <input
                  v-model="form.destination_hero_title.bn"
                  type="text"
                  class="input"
                />
              </div>

              <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">
                  Hero Button (English)
                </label>

                <input
                  v-model="form.destination_hero_btn.en"
                  type="text"
                  class="input"
                />
              </div>

              <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">
                  Hero Button (Bangla)
                </label>

                <input
                  v-model="form.destination_hero_btn.bn"
                  type="text"
                  class="input"
                />
              </div>
            </div>
          </div>

          <!-- Tour -->
          <div class="mb-6">
            <h3
              class="mb-4 border-b pb-2 text-lg font-semibold text-gray-800"
            >
              Tour Information
            </h3>

            <div class="grid gap-5 md:grid-cols-2">
              <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">
                  Tour Slug
                </label>

                <input
                  v-model="form.tour_slug"
                  type="text"
                  class="input"
                  placeholder="tour-slug"
                />
              </div>

              <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">
                  Tour Title (English)
                </label>

                <input
                  v-model="form.tour_title.en"
                  type="text"
                  class="input"
                />
              </div>

              <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">
                  Tour Title (Bangla)
                </label>

                <input
                  v-model="form.tour_title.bn"
                  type="text"
                  class="input"
                />
              </div>

              <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">
                  Tour Description (English)
                </label>

                <textarea
                  v-model="form.tour_description.en"
                  rows="4"
                  class="input"
                ></textarea>
              </div>

              <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">
                  Tour Description (Bangla)
                </label>

                <textarea
                  v-model="form.tour_description.bn"
                  rows="4"
                  class="input"
                ></textarea>
              </div>
            </div>
          </div>

          <!-- Order & Status -->
          <div class="mb-6">
            <h3
              class="mb-4 border-b pb-2 text-lg font-semibold text-gray-800"
            >
              Display Settings
            </h3>

            <div class="grid gap-5 md:grid-cols-2">
              <!-- Order -->
              <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">
                  Order
                </label>

                <input
                  v-model.number="form.order"
                  type="number"
                  min="0"
                  class="input"
                />

                <p class="mt-1 text-xs text-gray-500">
                  Smaller numbers appear first.
                </p>
              </div>

              <!-- Active -->
              <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">
                  Status
                </label>

                <label
                  class="flex cursor-pointer items-center gap-3 rounded-lg border border-gray-200 p-3"
                >
                  <input
                    v-model="form.is_active"
                    type="checkbox"
                    class="h-5 w-5 rounded border-gray-300 text-emerald-600 focus:ring-emerald-500"
                  />

                  <span
                    class="text-sm font-medium"
                    :class="
                      form.is_active
                        ? 'text-emerald-600'
                        : 'text-gray-500'
                    "
                  >
                    {{
                      form.is_active
                        ? "Active"
                        : "Inactive"
                    }}
                  </span>
                </label>
              </div>
            </div>
          </div>

          <!-- Buttons -->
          <div
            class="flex flex-col-reverse gap-3 border-t pt-5 sm:flex-row sm:justify-end"
          >
            <button
              type="button"
              @click="closeModal"
              class="rounded-lg border border-gray-300 px-5 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-50"
            >
              Cancel
            </button>

            <button
              type="submit"
              :disabled="saving"
              class="rounded-lg bg-emerald-600 px-6 py-2.5 text-sm font-semibold text-white hover:bg-emerald-700 disabled:cursor-not-allowed disabled:opacity-60"
            >
              {{
                saving
                  ? "Saving..."
                  : editingDestination
                    ? "Update Destination"
                    : "Create Destination"
              }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed, onMounted, reactive, ref } from "vue";
import api from "@/services/api";

/*
|--------------------------------------------------------------------------
| Types
|--------------------------------------------------------------------------
*/

interface Destination {
  id: number;
  slug: string;

  title: {
    en?: string;
    bn?: string;
  };

  sub_title: {
    en?: string;
    bn?: string;
  };

  image: string | null;

  destination: {
    name: {
      en?: string;
      bn?: string;
    };

    title: {
      en?: string;
      bn?: string;
    };

    sub_title: {
      en?: string;
      bn?: string;
    };

    hero: {
      image: string | null;

      title: {
        en?: string;
        bn?: string;
      };

      btn: {
        en?: string;
        bn?: string;
      };
    };
  };

  tour: {
    slug: string | null;

    title: {
      en?: string;
      bn?: string;
    };

    description: {
      en?: string;
      bn?: string;
    };

    map_image: string | null;
  };

  order: number;
  is_active: boolean;
}

/*
|--------------------------------------------------------------------------
| State
|--------------------------------------------------------------------------
*/

const destinations = ref<Destination[]>([]);

const loading = ref(false);
const saving = ref(false);

const showModal = ref(false);
const editingDestination = ref<Destination | null>(null);

const search = ref("");

const formError = ref("");

const errors = ref<Record<string, string>>({});

const statusLoading = ref<number | null>(null);

const imageFile = ref<File | null>(null);
const heroImageFile = ref<File | null>(null);
const mapImageFile = ref<File | null>(null);

const imagePreview = ref<string | null>(null);
const heroPreview = ref<string | null>(null);
const mapPreview = ref<string | null>(null);

/*
|--------------------------------------------------------------------------
| Form
|--------------------------------------------------------------------------
*/

const emptyForm = () => ({
  slug: "",

  title: {
    en: "",
    bn: "",
  },

  sub_title: {
    en: "",
    bn: "",
  },

  image_title: {
    en: "",
    bn: "",
  },

  destination_name: {
    en: "",
    bn: "",
  },

  destination_title: {
    en: "",
    bn: "",
  },

  destination_sub_title: {
    en: "",
    bn: "",
  },

  destination_hero_title: {
    en: "",
    bn: "",
  },

  destination_hero_btn: {
    en: "",
    bn: "",
  },

  tour_slug: "",

  tour_title: {
    en: "",
    bn: "",
  },

  tour_description: {
    en: "",
    bn: "",
  },

  order: 0,

  is_active: true,
});

const form = reactive(emptyForm());

/*
|--------------------------------------------------------------------------
| Computed
|--------------------------------------------------------------------------
*/

const filteredDestinations = computed(() => {
  const keyword = search.value.trim().toLowerCase();

  if (!keyword) {
    return destinations.value;
  }

  return destinations.value.filter((destination) => {
    const name =
      getText(destination.destination?.name) || "";

    const title =
      getText(destination.title) || "";

    const slug =
      destination.slug || "";

    return (
      name.toLowerCase().includes(keyword) ||
      title.toLowerCase().includes(keyword) ||
      slug.toLowerCase().includes(keyword)
    );
  });
});

/*
|--------------------------------------------------------------------------
| Helpers
|--------------------------------------------------------------------------
*/

function getText(
  value?: {
    en?: string;
    bn?: string;
  } | null
): string {
  if (!value) {
    return "";
  }

  return value.en || value.bn || "";
}

/*
|--------------------------------------------------------------------------
| Load Destinations
|--------------------------------------------------------------------------
*/

async function fetchDestinations() {
  loading.value = true;

  try {
    const response = await api.get("/destinations");

    destinations.value =
      response.data?.data || [];
  } catch (error: any) {
    console.error("Failed to load destinations:", error);

    formError.value =
      error.response?.data?.message ||
      "Failed to load destinations.";
  } finally {
    loading.value = false;
  }
}

/*
|--------------------------------------------------------------------------
| Open Create
|--------------------------------------------------------------------------
*/

function openCreate() {
  editingDestination.value = null;

  Object.assign(form, emptyForm());

  errors.value = {};
  formError.value = "";

  imageFile.value = null;
  heroImageFile.value = null;
  mapImageFile.value = null;

  imagePreview.value = null;
  heroPreview.value = null;
  mapPreview.value = null;

  showModal.value = true;
}

/*
|--------------------------------------------------------------------------
| Open Edit
|--------------------------------------------------------------------------
*/

function openEdit(destination: Destination) {
  editingDestination.value = destination;

  form.slug = destination.slug || "";

  form.title = {
    en: destination.title?.en || "",
    bn: destination.title?.bn || "",
  };

  form.sub_title = {
    en: destination.sub_title?.en || "",
    bn: destination.sub_title?.bn || "",
  };

  form.image_title = {
    en: "",
    bn: "",
  };

  form.destination_name = {
    en: destination.destination?.name?.en || "",
    bn: destination.destination?.name?.bn || "",
  };

  form.destination_title = {
    en: destination.destination?.title?.en || "",
    bn: destination.destination?.title?.bn || "",
  };

  form.destination_sub_title = {
    en: destination.destination?.sub_title?.en || "",
    bn: destination.destination?.sub_title?.bn || "",
  };

  form.destination_hero_title = {
    en: destination.destination?.hero?.title?.en || "",
    bn: destination.destination?.hero?.title?.bn || "",
  };

  form.destination_hero_btn = {
    en: destination.destination?.hero?.btn?.en || "",
    bn: destination.destination?.hero?.btn?.bn || "",
  };

  form.tour_slug =
    destination.tour?.slug || "";

  form.tour_title = {
    en: destination.tour?.title?.en || "",
    bn: destination.tour?.title?.bn || "",
  };

  form.tour_description = {
    en: destination.tour?.description?.en || "",
    bn: destination.tour?.description?.bn || "",
  };

  form.order = destination.order ?? 0;

  form.is_active =
    destination.is_active ?? true;

  imageFile.value = null;
  heroImageFile.value = null;
  mapImageFile.value = null;

  imagePreview.value =
    destination.image || null;

  heroPreview.value =
    destination.destination?.hero?.image || null;

  mapPreview.value =
    destination.tour?.map_image || null;

  errors.value = {};
  formError.value = "";

  showModal.value = true;
}

/*
|--------------------------------------------------------------------------
| Close Modal
|--------------------------------------------------------------------------
*/

function closeModal() {
  if (saving.value) {
    return;
  }

  showModal.value = false;
}

/*
|--------------------------------------------------------------------------
| File Handler
|--------------------------------------------------------------------------
*/

function handleFile(
  event: Event,
  type:
    | "image"
    | "destination_hero_image"
    | "map_image"
) {
  const target =
    event.target as HTMLInputElement;

  const file =
    target.files?.[0] || null;

  if (!file) {
    return;
  }

  const preview =
    URL.createObjectURL(file);

  if (type === "image") {
    imageFile.value = file;
    imagePreview.value = preview;
  }

  if (
    type === "destination_hero_image"
  ) {
    heroImageFile.value = file;
    heroPreview.value = preview;
  }

  if (type === "map_image") {
    mapImageFile.value = file;
    mapPreview.value = preview;
  }
}

/*
|--------------------------------------------------------------------------
| Build FormData
|--------------------------------------------------------------------------
*/

function appendFormData(
  formData: FormData,
  key: string,
  value: any
) {
  if (
    value === null ||
    value === undefined
  ) {
    return;
  }

  if (typeof value === "object" && !(value instanceof File)) {
    Object.keys(value).forEach((childKey) => {
      appendFormData(
        formData,
        `${key}[${childKey}]`,
        value[childKey]
      );
    });

    return;
  }

  formData.append(key, String(value));
}

/*
|--------------------------------------------------------------------------
| Submit
|--------------------------------------------------------------------------
*/

async function submitForm() {
  saving.value = true;

  errors.value = {};
  formError.value = "";

  try {
    const formData = new FormData();

    appendFormData(formData, "slug", form.slug);

    appendFormData(
      formData,
      "title",
      form.title
    );

    appendFormData(
      formData,
      "sub_title",
      form.sub_title
    );

    appendFormData(
      formData,
      "image_title",
      form.image_title
    );

    appendFormData(
      formData,
      "destination_name",
      form.destination_name
    );

    appendFormData(
      formData,
      "destination_title",
      form.destination_title
    );

    appendFormData(
      formData,
      "destination_sub_title",
      form.destination_sub_title
    );

    appendFormData(
      formData,
      "destination_hero_title",
      form.destination_hero_title
    );

    appendFormData(
      formData,
      "destination_hero_btn",
      form.destination_hero_btn
    );

    appendFormData(
      formData,
      "tour_slug",
      form.tour_slug
    );

    appendFormData(
      formData,
      "tour_title",
      form.tour_title
    );

    appendFormData(
      formData,
      "tour_description",
      form.tour_description
    );

    appendFormData(
      formData,
      "order",
      form.order
    );

    appendFormData(
      formData,
      "is_active",
      form.is_active ? 1 : 0
    );

    if (imageFile.value) {
      formData.append(
        "image",
        imageFile.value
      );
    }

    if (heroImageFile.value) {
      formData.append(
        "destination_hero_image",
        heroImageFile.value
      );
    }

    if (mapImageFile.value) {
      formData.append(
        "map_image",
        mapImageFile.value
      );
    }

    if (editingDestination.value) {
      formData.append("_method", "PUT");

      await api.post(
        `/admin/destinations/${editingDestination.value.slug}`,
        formData,
        {
          headers: {
            "Content-Type":
              "multipart/form-data",
          },
        }
      );
    } else {
      await api.post(
        "/admin/destinations",
        formData,
        {
          headers: {
            "Content-Type":
              "multipart/form-data",
          },
        }
      );
    }

    showModal.value = false;

    await fetchDestinations();
  } catch (error: any) {
    console.error(
      "Destination save error:",
      error
    );

    if (error.response?.status === 422) {
      errors.value =
        error.response.data.errors || {};

      formError.value =
        error.response.data.message ||
        "Please check the form.";
    } else {
      formError.value =
        error.response?.data?.message ||
        "Something went wrong.";
    }
  } finally {
    saving.value = false;
  }
}

/*
|--------------------------------------------------------------------------
| Toggle Status
|--------------------------------------------------------------------------
*/

async function toggleStatus(
  destination: Destination
) {
  statusLoading.value = destination.id;

  try {
    const formData = new FormData();

    formData.append(
      "_method",
      "PUT"
    );

    formData.append(
      "is_active",
      destination.is_active ? "0" : "1"
    );

    formData.append(
      "order",
      String(destination.order)
    );

    await api.post(
      `/admin/destinations/${destination.slug}`,
      formData,
      {
        headers: {
          "Content-Type":
            "multipart/form-data",
        },
      }
    );

    destination.is_active =
      !destination.is_active;
  } catch (error: any) {
    console.error(
      "Status update failed:",
      error
    );

    alert(
      error.response?.data?.message ||
        "Failed to update status."
    );
  } finally {
    statusLoading.value = null;
  }
}

/*
|--------------------------------------------------------------------------
| Delete
|--------------------------------------------------------------------------
*/

async function deleteDestination(
  destination: Destination
) {
  const name =
    getText(destination.destination?.name) ||
    destination.slug;

  if (
    !confirm(
      `Are you sure you want to delete "${name}"?`
    )
  ) {
    return;
  }

  try {
    await api.delete(
      `/admin/destinations/${destination.slug}`
    );

    destinations.value =
      destinations.value.filter(
        (item) =>
          item.id !== destination.id
      );
  } catch (error: any) {
    console.error(
      "Delete failed:",
      error
    );

    alert(
      error.response?.data?.message ||
        "Failed to delete destination."
    );
  }
}

/*
|--------------------------------------------------------------------------
| Mounted
|--------------------------------------------------------------------------
*/

onMounted(() => {
  fetchDestinations();
});
</script>

<style scoped>
.input {
  width: 100%;
  border-radius: 0.5rem;
  border: 1px solid #d1d5db;
  padding: 0.625rem 0.75rem;
  font-size: 0.875rem;
  outline: none;
}

.input:focus {
  border-color: #10b981;
  box-shadow: 0 0 0 1px #10b981;
}

.file-input {
  width: 100%;
  border-radius: 0.5rem;
  border: 1px solid #d1d5db;
  padding: 0.5rem;
  font-size: 0.875rem;
}
</style>
```
