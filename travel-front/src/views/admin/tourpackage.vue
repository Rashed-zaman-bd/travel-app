
<template>
  <div class="min-h-screen bg-slate-50 p-4 md:p-6">
    <div class="mx-auto max-w-7xl">

      <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

        <div>
          <h1 class="text-2xl font-bold text-slate-800">
            Tour Packages
          </h1>

          <p class="mt-1 text-sm text-slate-500">
            Manage your tour packages and countries.
          </p>
        </div>

        <button
          type="button"
          @click="openCreateModal"
          class="inline-flex items-center justify-center rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-700"
        >
          + Add Tour Package
        </button>

      </div>


      <div
        v-if="successMessage"
        class="mb-5 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700"
      >
        {{ successMessage }}
      </div>


      <!-- =========================================================
           Error Message
      ========================================================== -->
      <div
        v-if="errorMessage"
        class="mb-5 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700"
      >
        {{ errorMessage }}
      </div>


      <!-- =========================================================
           Search
      ========================================================== -->
      <div class="mb-5 rounded-xl bg-white p-4 shadow-sm">

        <div class="grid gap-4 md:grid-cols-3">

          <div class="md:col-span-2">
            <label class="mb-1 block text-sm font-medium text-slate-700">
              Search
            </label>

            <input
              v-model="search"
              type="text"
              placeholder="Search package name..."
              class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-sm outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
            />
          </div>

          <div>
            <label class="mb-1 block text-sm font-medium text-slate-700">
              Country
            </label>

            <select
              v-model="filterCategory"
              class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-sm outline-none focus:border-blue-500"
            >
              <option value="">
                All Countries
              </option>

              <option
                v-for="category in categories"
                :key="category.id"
                :value="String(category.id)"
              >
                {{ getLocalized(category.country_name) }}
              </option>
            </select>
          </div>

        </div>

      </div>


      <!-- =========================================================
           Loading
      ========================================================== -->
      <div
        v-if="loading"
        class="rounded-xl bg-white p-10 text-center shadow-sm"
      >
        <div class="mx-auto h-8 w-8 animate-spin rounded-full border-4 border-slate-200 border-t-blue-600"></div>

        <p class="mt-3 text-sm text-slate-500">
          Loading tour packages...
        </p>
      </div>


      <!-- =========================================================
           Empty
      ========================================================== -->
      <div
        v-else-if="filteredPackages.length === 0"
        class="rounded-xl bg-white p-10 text-center shadow-sm"
      >
        <p class="text-slate-500">
          No tour packages found.
        </p>
      </div>


      <!-- =========================================================
           Desktop Table
      ========================================================== -->
      <div
        v-else
        class="hidden overflow-hidden rounded-xl bg-white shadow-sm md:block"
      >
        <div class="overflow-x-auto">

          <table class="min-w-full">

            <thead class="border-b bg-slate-50">
              <tr>

                <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-slate-500">
                  #
                </th>

                <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-slate-500">
                  Package
                </th>

                <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-slate-500">
                  Country
                </th>

                <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-slate-500">
                  Price
                </th>

                <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-slate-500">
                  Duration
                </th>

                <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-slate-500">
                  Status
                </th>

                <th class="px-4 py-3 text-right text-xs font-semibold uppercase text-slate-500">
                  Actions
                </th>

              </tr>
            </thead>

            <tbody class="divide-y divide-slate-100">

              <tr
                v-for="(tourPackage, index) in filteredPackages"
                :key="tourPackage.id"
                class="transition hover:bg-slate-50"
              >

                <td class="px-4 py-4 text-sm text-slate-500">
                  {{ index + 1 }}
                </td>

                <!-- Package -->
                <td class="px-4 py-4">

                  <div class="flex items-center gap-3">

                    <img
                      v-if="tourPackage.package_image"
                      :src="tourPackage.package_image"
                      :alt="getLocalized(tourPackage.package_name)"
                      class="h-14 w-20 rounded-lg object-cover"
                    />

                    <div
                      v-else
                      class="flex h-14 w-20 items-center justify-center rounded-lg bg-slate-100 text-xs text-slate-400"
                    >
                      No Image
                    </div>

                    <div>
                      <p class="font-semibold text-slate-800">
                        {{ getLocalized(tourPackage.package_name) }}
                      </p>

                      <p class="mt-1 text-xs text-slate-400">
                        {{ tourPackage.slug }}
                      </p>
                    </div>

                  </div>

                </td>


                <!-- Country -->
                <td class="px-4 py-4 text-sm text-slate-600">
                  {{ getCategoryName(tourPackage.category_id) }}
                </td>


                <!-- Price -->
                <td class="px-4 py-4 text-sm font-medium text-slate-700">
                  {{ getLocalized(tourPackage.package_price) || '-' }}
                </td>


                <!-- Duration -->
                <td class="px-4 py-4 text-sm text-slate-600">
                  {{ getLocalized(tourPackage.package_duration) || '-' }}
                </td>


                <!-- Status -->
                <td class="px-4 py-4">

                  <span
                    :class="
                      tourPackage.is_active
                        ? 'bg-green-100 text-green-700'
                        : 'bg-red-100 text-red-700'
                    "
                    class="inline-flex rounded-full px-3 py-1 text-xs font-semibold"
                  >
                    {{ tourPackage.is_active ? 'Active' : 'Inactive' }}
                  </span>

                </td>


                <!-- Actions -->
                <td class="px-4 py-4">

                  <div class="flex justify-end gap-2">

                    <button
                      type="button"
                      @click="editPackage(tourPackage)"
                      class="rounded-lg bg-blue-50 px-3 py-2 text-xs font-semibold text-blue-600 hover:bg-blue-100"
                    >
                      Edit
                    </button>

                    <button
                      type="button"
                      @click="deletePackage(tourPackage)"
                      class="rounded-lg bg-red-50 px-3 py-2 text-xs font-semibold text-red-600 hover:bg-red-100"
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


      <!-- =========================================================
           Mobile Cards
      ========================================================== -->
      <div
        v-if="!loading && filteredPackages.length"
        class="space-y-4 md:hidden"
      >

        <div
          v-for="tourPackage in filteredPackages"
          :key="tourPackage.id"
          class="rounded-xl bg-white p-4 shadow-sm"
        >

          <div class="flex gap-3">

            <img
              v-if="tourPackage.package_image"
              :src="tourPackage.package_image"
              :alt="getLocalized(tourPackage.package_name)"
              class="h-20 w-24 rounded-lg object-cover"
            />

            <div class="min-w-0 flex-1">

              <h3 class="font-semibold text-slate-800">
                {{ getLocalized(tourPackage.package_name) }}
              </h3>

              <p class="mt-1 text-sm text-slate-500">
                {{ getCategoryName(tourPackage.category_id) }}
              </p>

              <p class="mt-1 text-sm font-medium text-blue-600">
                {{ getLocalized(tourPackage.package_price) || '-' }}
              </p>

            </div>

          </div>

          <div class="mt-4 flex gap-2 border-t pt-3">

            <button
              type="button"
              @click="editPackage(tourPackage)"
              class="flex-1 rounded-lg bg-blue-50 py-2 text-sm font-semibold text-blue-600"
            >
              Edit
            </button>

            <button
              type="button"
              @click="deletePackage(tourPackage)"
              class="flex-1 rounded-lg bg-red-50 py-2 text-sm font-semibold text-red-600"
            >
              Delete
            </button>

          </div>

        </div>

      </div>

    </div>


    <!-- ===========================================================
         Modal
    ============================================================ -->
    <div
      v-if="showModal"
      class="fixed inset-0 z-50 flex items-start justify-center overflow-y-auto bg-black/50 p-4"
    >

      <div
        class="my-6 w-full max-w-5xl rounded-2xl bg-white shadow-2xl"
      >

        <!-- Modal Header -->
        <div class="flex items-center justify-between border-b px-6 py-4">

          <div>
            <h2 class="text-xl font-bold text-slate-800">
              {{ editingId ? 'Edit Tour Package' : 'Add Tour Package' }}
            </h2>

            <p class="mt-1 text-sm text-slate-500">
              {{ editingId
                ? 'Update the tour package information.'
                : 'Create a new tour package.'
              }}
            </p>
          </div>

          <button
            type="button"
            @click="closeModal"
            class="text-2xl leading-none text-slate-400 hover:text-slate-700"
          >
            ×
          </button>

        </div>


        <!-- Form -->
        <form
          @submit.prevent="submitForm"
          class="p-6"
        >

          <div class="grid gap-6 lg:grid-cols-2">

            <!-- =====================================================
                 Country
            ====================================================== -->
            <div class="lg:col-span-2">

              <label class="mb-2 block text-sm font-semibold text-slate-700">
                Country
              </label>

              <select
                v-model="form.category_id"
                class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
              >
                <option value="">
                  Select Country
                </option>

                <option
                  v-for="category in categories"
                  :key="category.id"
                  :value="category.id"
                >
                  {{ getLocalized(category.country_name) }}
                </option>

              </select>

              <p
                v-if="errors.category_id"
                class="mt-1 text-xs text-red-500"
              >
                {{ errors.category_id }}
              </p>

            </div>


            <!-- =====================================================
                 Package Name EN
            ====================================================== -->
            <div>

              <label class="mb-2 block text-sm font-semibold text-slate-700">
                Package Name (English) *
              </label>

              <input
                v-model="form.package_name.en"
                type="text"
                placeholder="Example: Bangkok 7 Days Tour"
                class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
              />

              <p
                v-if="errors['package_name.en']"
                class="mt-1 text-xs text-red-500"
              >
                {{ errors['package_name.en'] }}
              </p>

            </div>


            <!-- =====================================================
                 Package Name BN
            ====================================================== -->
            <div>

              <label class="mb-2 block text-sm font-semibold text-slate-700">
                Package Name (Bangla)
              </label>

              <input
                v-model="form.package_name.bn"
                type="text"
                placeholder="বাংলা প্যাকেজের নাম"
                class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
              />

            </div>


            <!-- =====================================================
                 Slug
            ====================================================== -->
            <div>

              <label class="mb-2 block text-sm font-semibold text-slate-700">
                Slug
              </label>

              <input
                v-model="form.slug"
                type="text"
                placeholder="Auto generated from package name"
                class="w-full rounded-lg border border-slate-300 bg-slate-50 px-4 py-3 text-sm outline-none focus:border-blue-500"
              />

              <p class="mt-1 text-xs text-slate-400">
                Leave empty to generate automatically.
              </p>

              <p
                v-if="errors.slug"
                class="mt-1 text-xs text-red-500"
              >
                {{ errors.slug }}
              </p>

            </div>


            <!-- =====================================================
                 Price
            ====================================================== -->
            <div>

              <label class="mb-2 block text-sm font-semibold text-slate-700">
                Price (English)
              </label>

              <input
                v-model="form.package_price.en"
                type="text"
                placeholder="Example: $500"
                class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm outline-none focus:border-blue-500"
              />

            </div>


            <div>

              <label class="mb-2 block text-sm font-semibold text-slate-700">
                Price (Bangla)
              </label>

              <input
                v-model="form.package_price.bn"
                type="text"
                placeholder="উদাহরণ: ৫০,০০০ টাকা"
                class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm outline-none focus:border-blue-500"
              />

            </div>


            <!-- =====================================================
                 Duration
            ====================================================== -->
            <div>

              <label class="mb-2 block text-sm font-semibold text-slate-700">
                Duration (English)
              </label>

              <input
                v-model="form.package_duration.en"
                type="text"
                placeholder="Example: 7 Days / 6 Nights"
                class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm outline-none focus:border-blue-500"
              />

            </div>


            <div>

              <label class="mb-2 block text-sm font-semibold text-slate-700">
                Duration (Bangla)
              </label>

              <input
                v-model="form.package_duration.bn"
                type="text"
                placeholder="৭ দিন / ৬ রাত"
                class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm outline-none focus:border-blue-500"
              />

            </div>


            <!-- =====================================================
                 Header
            ====================================================== -->
            <div>

              <label class="mb-2 block text-sm font-semibold text-slate-700">
                Header (English)
              </label>

              <input
                v-model="form.header.en"
                type="text"
                placeholder="Package header"
                class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm outline-none focus:border-blue-500"
              />

            </div>


            <div>

              <label class="mb-2 block text-sm font-semibold text-slate-700">
                Header (Bangla)
              </label>

              <input
                v-model="form.header.bn"
                type="text"
                placeholder="প্যাকেজ হেডার"
                class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm outline-none focus:border-blue-500"
              />

            </div>


            <!-- =====================================================
                 Sub Header
            ====================================================== -->
            <div>

              <label class="mb-2 block text-sm font-semibold text-slate-700">
                Sub Header (English)
              </label>

              <textarea
                v-model="form.sub_header.en"
                rows="3"
                placeholder="Sub header"
                class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm outline-none focus:border-blue-500"
              ></textarea>

            </div>


            <div>

              <label class="mb-2 block text-sm font-semibold text-slate-700">
                Sub Header (Bangla)
              </label>

              <textarea
                v-model="form.sub_header.bn"
                rows="3"
                placeholder="সাব হেডার"
                class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm outline-none focus:border-blue-500"
              ></textarea>

            </div>

            <!-- =====================================================
                 Hero Image
            ====================================================== -->
            <div>

              <label class="mb-2 block text-sm font-semibold text-slate-700">
                Hero Image
              </label>

              <input
                type="file"
                accept="image/jpeg,image/png,image/webp"
                @change="handleImage($event, 'hero_image')"
                class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm"
              />

              <img
                v-if="heroPreview"
                :src="heroPreview"
                class="mt-3 h-32 w-full rounded-lg object-cover"
              />

            </div>


            <!-- =====================================================
                 Package Image
            ====================================================== -->
            <div>

              <label class="mb-2 block text-sm font-semibold text-slate-700">
                Package Image {{ editingId ? '' : '*' }}
              </label>

              <input
                type="file"
                accept="image/jpeg,image/png,image/webp"
                @change="handleImage($event, 'package_image')"
                class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm"
              />

              <img
                v-if="packagePreview"
                :src="packagePreview"
                class="mt-3 h-32 w-full rounded-lg object-cover"
              />

              <p
                v-if="errors.package_image"
                class="mt-1 text-xs text-red-500"
              >
                {{ errors.package_image }}
              </p>

            </div>


            <!-- =====================================================
                 Map Image
            ====================================================== -->
            <div>

              <label class="mb-2 block text-sm font-semibold text-slate-700">
                Package Map Image
              </label>

              <input
                type="file"
                accept="image/jpeg,image/png,image/webp"
                @change="handleImage($event, 'package_map_image')"
                class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm"
              />

              <img
                v-if="mapPreview"
                :src="mapPreview"
                class="mt-3 h-32 w-full rounded-lg object-contain bg-slate-50"
              />

            </div>


            <!-- =====================================================
                 Hero Title
            ====================================================== -->
            <div>

              <label class="mb-2 block text-sm font-semibold text-slate-700">
                Hero Title (English)
              </label>

              <input
                v-model="form.hero_image_title.en"
                type="text"
                class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm outline-none focus:border-blue-500"
              />

            </div>


            <div>

              <label class="mb-2 block text-sm font-semibold text-slate-700">
                Hero Title (Bangla)
              </label>

              <input
                v-model="form.hero_image_title.bn"
                type="text"
                class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm outline-none focus:border-blue-500"
              />

            </div>


            <!-- =====================================================
                 Hero Button
            ====================================================== -->
            <div>

              <label class="mb-2 block text-sm font-semibold text-slate-700">
                Hero Button (English)
              </label>

              <input
                v-model="form.hero_image_btn.en"
                type="text"
                placeholder="Book Now"
                class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm outline-none focus:border-blue-500"
              />

            </div>


            <div>

              <label class="mb-2 block text-sm font-semibold text-slate-700">
                Hero Button (Bangla)
              </label>

              <input
                v-model="form.hero_image_btn.bn"
                type="text"
                placeholder="বুক করুন"
                class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm outline-none focus:border-blue-500"
              />

            </div>


            <!-- =====================================================
                 Package Image Title
            ====================================================== -->
            <div>

              <label class="mb-2 block text-sm font-semibold text-slate-700">
                Package Image Title (English)
              </label>

              <input
                v-model="form.package_image_title.en"
                type="text"
                class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm outline-none focus:border-blue-500"
              />

            </div>


            <div>

              <label class="mb-2 block text-sm font-semibold text-slate-700">
                Package Image Title (Bangla)
              </label>

              <input
                v-model="form.package_image_title.bn"
                type="text"
                class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm outline-none focus:border-blue-500"
              />

            </div>


            <!-- =====================================================
                 Order
            ====================================================== -->
            <div>

              <label class="mb-2 block text-sm font-semibold text-slate-700">
                Order
              </label>

              <input
                v-model.number="form.order"
                type="number"
                min="0"
                class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm outline-none focus:border-blue-500"
              />

            </div>


            <!-- =====================================================
                 Status
            ====================================================== -->
            <div class="flex items-center pt-8">

              <label class="flex cursor-pointer items-center gap-3">

                <input
                  v-model="form.is_active"
                  type="checkbox"
                  class="h-5 w-5 rounded border-slate-300 text-blue-600 focus:ring-blue-500"
                />

                <span class="text-sm font-semibold text-slate-700">
                  Active Package
                </span>

              </label>

            </div>

          </div>


          <!-- =====================================================
               Submit
          ====================================================== -->
          <div class="mt-8 flex justify-end gap-3 border-t pt-5">

            <button
              type="button"
              @click="closeModal"
              class="rounded-lg border border-slate-300 px-5 py-2.5 text-sm font-semibold text-slate-600 hover:bg-slate-50"
            >
              Cancel
            </button>

            <button
              type="submit"
              :disabled="saving"
              class="rounded-lg bg-blue-600 px-6 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-700 disabled:cursor-not-allowed disabled:opacity-60"
            >
              {{ saving
                ? 'Saving...'
                : editingId
                  ? 'Update Package'
                  : 'Create Package'
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

interface LocalizedValue {
  en: string;
  bn: string;
}

interface Category {
  id: number;
  country_name: LocalizedValue;
  slug?: string;
  image?: string | null;
}

interface TourPackage {
  id: number;
  category_id: number | null;

  hero_image: string | null;
  hero_image_title: LocalizedValue | null;
  hero_image_btn: LocalizedValue | null;

  header: LocalizedValue | null;
  sub_header: LocalizedValue | null;

  package_name: LocalizedValue;
  slug: string;

  package_image: string | null;
  package_price: LocalizedValue | null;
  package_duration: LocalizedValue | null;
  package_image_title: LocalizedValue | null;

  package_map_image: string | null;
  package_destination: LocalizedValue | null;

  order: number;
  is_active: boolean;
}


/*
|--------------------------------------------------------------------------
| State
|--------------------------------------------------------------------------
*/

const loading = ref(false);
const saving = ref(false);

const showModal = ref(false);

const editingId = ref<number | null>(null);

const search = ref("");
const filterCategory = ref("");

const tourPackages = ref<TourPackage[]>([]);
const categories = ref<Category[]>([]);

const successMessage = ref("");
const errorMessage = ref("");

const errors = ref<Record<string, string>>({});


/*
|--------------------------------------------------------------------------
| Image files
|--------------------------------------------------------------------------
*/

const heroImageFile = ref<File | null>(null);
const packageImageFile = ref<File | null>(null);
const mapImageFile = ref<File | null>(null);

const heroPreview = ref<string | null>(null);
const packagePreview = ref<string | null>(null);
const mapPreview = ref<string | null>(null);


/*
|--------------------------------------------------------------------------
| Form
|--------------------------------------------------------------------------
*/

const createEmptyForm = () => ({
  category_id: "",

  hero_image_title: { en: "", bn: ""},

  hero_image_btn: {en: "", bn: "" },

  header: { en: "", bn: "" },

  sub_header: { en: "", bn: "" },

  package_name: { en: "", bn: "" },

  slug: "",

  package_price: { en: "", bn: "" },

  package_duration: { en: "", bn: "" },

  package_image_title: { en: "", bn: "" },

  package_destination: { en: "", bn: "" },

  order: 0,

  is_active: true,
});


const form = reactive(createEmptyForm());


/*
|--------------------------------------------------------------------------
| Localized helper
|--------------------------------------------------------------------------
*/

const getLocalized = (
  value: LocalizedValue | null | undefined
): string => {

  if (!value) {
    return "";
  }

  return value.en || value.bn || "";
};


/*
|--------------------------------------------------------------------------
| Category name
|--------------------------------------------------------------------------
*/

const getCategoryName = (
  categoryId: number | null
): string => {

  if (!categoryId) {
    return "-";
  }

  const category = categories.value.find(
    item => item.id === categoryId
  );

  return category
    ? getLocalized(category.country_name)
    : "-";
};


/*
|--------------------------------------------------------------------------
| Filtered packages
|--------------------------------------------------------------------------
*/

const filteredPackages = computed(() => {

  const keyword = search.value
    .trim()
    .toLowerCase();

  return tourPackages.value.filter((item) => {

    const packageName = getLocalized(
      item.package_name
    ).toLowerCase();

    const matchesSearch =
      !keyword ||
      packageName.includes(keyword) ||
      item.slug.toLowerCase().includes(keyword);

    const matchesCategory =
      !filterCategory.value ||
      String(item.category_id) === filterCategory.value;

    return matchesSearch && matchesCategory;
  });
});


/*
|--------------------------------------------------------------------------
| Load Categories
|--------------------------------------------------------------------------
*/

const loadCategories = async () => {

  try {

    const response = await api.get(
      "/admin/category"
    );

    categories.value =
      response.data.data ?? [];

  } catch (error) {

    console.error(
      "Failed to load categories:",
      error
    );
  }
};


/*
|--------------------------------------------------------------------------
| Load Tour Packages
|--------------------------------------------------------------------------
*/

const loadPackages = async () => {

  loading.value = true;

  errorMessage.value = "";

  try {

    const response = await api.get(
      "/admin/tour-package"
    );

    tourPackages.value =
      response.data.data ?? [];

  } catch (error: any) {

    console.error(error);

    errorMessage.value =
      error?.response?.data?.message ||
      "Failed to load tour packages.";

  } finally {

    loading.value = false;
  }
};


/*
|--------------------------------------------------------------------------
| Reset Form
|--------------------------------------------------------------------------
*/

const resetForm = () => {

  Object.assign(
    form,
    createEmptyForm()
  );

  editingId.value = null;

  errors.value = {};

  heroImageFile.value = null;
  packageImageFile.value = null;
  mapImageFile.value = null;

  heroPreview.value = null;
  packagePreview.value = null;
  mapPreview.value = null;
};


/*
|--------------------------------------------------------------------------
| Open Create Modal
|--------------------------------------------------------------------------
*/

const openCreateModal = () => {

  resetForm();

  showModal.value = true;
};


/*
|--------------------------------------------------------------------------
| Edit Package
|--------------------------------------------------------------------------
*/

const editPackage = (
  tourPackage: TourPackage
) => {

  resetForm();

  editingId.value = tourPackage.id;

  form.category_id =
    tourPackage.category_id
      ? String(tourPackage.category_id)
      : "";

  form.hero_image_title = {
    en: tourPackage.hero_image_title?.en || "",
    bn: tourPackage.hero_image_title?.bn || "",
  };

  form.hero_image_btn = {
    en: tourPackage.hero_image_btn?.en || "",
    bn: tourPackage.hero_image_btn?.bn || "",
  };

  form.header = {
    en: tourPackage.header?.en || "",
    bn: tourPackage.header?.bn || "",
  };

  form.sub_header = {
    en: tourPackage.sub_header?.en || "",
    bn: tourPackage.sub_header?.bn || "",
  };

  form.package_name = {
    en: tourPackage.package_name?.en || "",
    bn: tourPackage.package_name?.bn || "",
  };

  form.slug =
    tourPackage.slug || "";

  form.package_price = {
    en: tourPackage.package_price?.en || "",
    bn: tourPackage.package_price?.bn || "",
  };

  form.package_duration = {
    en: tourPackage.package_duration?.en || "",
    bn: tourPackage.package_duration?.bn || "",
  };

  form.package_image_title = {
    en: tourPackage.package_image_title?.en || "",
    bn: tourPackage.package_image_title?.bn || "",
  };

  form.package_destination = {
    en: tourPackage.package_destination?.en || "",
    bn: tourPackage.package_destination?.bn || "",
  };

  form.order =
    tourPackage.order ?? 0;

  form.is_active =
    Boolean(tourPackage.is_active);

  heroPreview.value =
    tourPackage.hero_image || null;

  packagePreview.value =
    tourPackage.package_image || null;

  mapPreview.value =
    tourPackage.package_map_image || null;

  showModal.value = true;
};


/*
|--------------------------------------------------------------------------
| Image Handler
|--------------------------------------------------------------------------
*/

const handleImage = (
  event: Event,
  type:
    | "hero_image"
    | "package_image"
    | "package_map_image"
) => {

  const target =
    event.target as HTMLInputElement;

  const file =
    target.files?.[0];

  if (!file) {
    return;
  }

  const preview =
    URL.createObjectURL(file);

  if (type === "hero_image") {

    heroImageFile.value = file;
    heroPreview.value = preview;

  } else if (type === "package_image") {

    packageImageFile.value = file;
    packagePreview.value = preview;

  } else {

    mapImageFile.value = file;
    mapPreview.value = preview;
  }
};


/*
|--------------------------------------------------------------------------
| Build FormData
|--------------------------------------------------------------------------
*/

const buildFormData = (): FormData => {

  const data = new FormData();

  /*
  |--------------------------------------------------------------------------
  | Basic fields
  |--------------------------------------------------------------------------
  */

  data.append(
    "category_id",
    form.category_id
  );

  data.append(
    "slug",
    form.slug
  );

  data.append(
    "order",
    String(form.order)
  );

  data.append(
    "is_active",
    form.is_active ? "1" : "0"
  );


  /*
  |--------------------------------------------------------------------------
  | Localized fields
  |--------------------------------------------------------------------------
  */

  data.append(
    "package_name[en]",
    form.package_name.en
  );

  data.append(
    "package_name[bn]",
    form.package_name.bn
  );

  data.append(
    "hero_image_title[en]",
    form.hero_image_title.en
  );

  data.append(
    "hero_image_title[bn]",
    form.hero_image_title.bn
  );

  data.append(
    "hero_image_btn[en]",
    form.hero_image_btn.en
  );

  data.append(
    "hero_image_btn[bn]",
    form.hero_image_btn.bn
  );

  data.append(
    "header[en]",
    form.header.en
  );

  data.append(
    "header[bn]",
    form.header.bn
  );

  data.append(
    "sub_header[en]",
    form.sub_header.en
  );

  data.append(
    "sub_header[bn]",
    form.sub_header.bn
  );

  data.append(
    "package_price[en]",
    form.package_price.en
  );

  data.append(
    "package_price[bn]",
    form.package_price.bn
  );

  data.append(
    "package_duration[en]",
    form.package_duration.en
  );

  data.append(
    "package_duration[bn]",
    form.package_duration.bn
  );

  data.append(
    "package_image_title[en]",
    form.package_image_title.en
  );

  data.append(
    "package_image_title[bn]",
    form.package_image_title.bn
  );

  data.append(
    "package_destination[en]",
    form.package_destination.en
  );

  data.append(
    "package_destination[bn]",
    form.package_destination.bn
  );


  /*
  |--------------------------------------------------------------------------
  | Images
  |--------------------------------------------------------------------------
  */

  if (heroImageFile.value) {

    data.append(
      "hero_image",
      heroImageFile.value
    );
  }

  if (packageImageFile.value) {

    data.append(
      "package_image",
      packageImageFile.value
    );
  }

  if (mapImageFile.value) {

    data.append(
      "package_map_image",
      mapImageFile.value
    );
  }

  return data;
};


/*
|--------------------------------------------------------------------------
| Submit Form
|--------------------------------------------------------------------------
*/

const submitForm = async () => {

  saving.value = true;

  errors.value = {};

  successMessage.value = "";
  errorMessage.value = "";

  try {

    const data =
      buildFormData();

    let response;

    /*
    |--------------------------------------------------------------------------
    | Create
    |--------------------------------------------------------------------------
    */

    if (!editingId.value) {

      response = await api.post(
        "/admin/tour-package",
        data
      );

      successMessage.value =
        "Tour package created successfully.";

    }

    /*
    |--------------------------------------------------------------------------
    | Update
    |--------------------------------------------------------------------------
    */

    else {

      /*
       * Laravel handles multipart PUT/PATCH
       * more reliably using POST + _method.
       */

      data.append(
        "_method",
        "PUT"
      );

      response = await api.post(
        `/admin/tour-package/${editingId.value}`,
        data
      );

      successMessage.value =
        "Tour package updated successfully.";
    }


    /*
    |--------------------------------------------------------------------------
    | Close + Reload
    |--------------------------------------------------------------------------
    */

    closeModal();

    await loadPackages();

    window.scrollTo({
      top: 0,
      behavior: "smooth",
    });

  } catch (error: any) {

    console.error(error);

    /*
    |--------------------------------------------------------------------------
    | Validation Errors
    |--------------------------------------------------------------------------
    */

    if (
      error?.response?.status === 422
    ) {

      errors.value =
        Object.fromEntries(
          Object.entries(
            error.response.data.errors || {}
          ).map(([key, value]) => [
            key,
            Array.isArray(value)
              ? value[0]
              : String(value),
          ])
        );

      errorMessage.value =
        "Please check the form fields.";

    } else {

      errorMessage.value =
        error?.response?.data?.message ||
        "Something went wrong.";
    }

  } finally {

    saving.value = false;
  }
};


/*
|--------------------------------------------------------------------------
| Delete Package
|--------------------------------------------------------------------------
*/

const deletePackage = async (
  tourPackage: TourPackage
) => {

  const name =
    getLocalized(
      tourPackage.package_name
    );

  if (
    !confirm(
      `Are you sure you want to delete "${name}"?`
    )
  ) {
    return;
  }

  try {

    await api.delete(
      `/admin/tour-package/${tourPackage.id}`
    );

    successMessage.value =
      "Tour package deleted successfully.";

    await loadPackages();

  } catch (error: any) {

    console.error(error);

    errorMessage.value =
      error?.response?.data?.message ||
      "Failed to delete tour package.";
  }
};


/*
|--------------------------------------------------------------------------
| Close Modal
|--------------------------------------------------------------------------
*/

const closeModal = () => {

  showModal.value = false;

  resetForm();
};


/*
|--------------------------------------------------------------------------
| Mounted
|--------------------------------------------------------------------------
*/

onMounted(async () => {

  await Promise.all([
    loadCategories(),
    loadPackages(),
  ]);

});

</script>

