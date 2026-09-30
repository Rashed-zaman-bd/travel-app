<template>
  <div class="category-manager p-6 max-w-7xl mx-auto">
    <!-- Header & Action Button -->
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-2xl font-bold text-gray-800">Category Management</h1>
      <button
        @click="openModal()"
        class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg font-medium shadow-sm transition"
      >
        + Add Category
      </button>
    </div>

    <!-- Loading & Error States -->
    <div v-if="loading" class="text-center py-8 text-gray-500">Loading categories...</div>
    <div v-else-if="errorMessage" class="bg-red-50 text-red-600 p-4 rounded-lg mb-6">
      {{ errorMessage }}
    </div>

    <!-- Category Datatable -->
    <div v-else class="bg-white shadow rounded-lg overflow-hidden border border-gray-200">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Image</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Slug</th>
            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Order</th>
            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Status</th>
            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-for="category in categories" :key="category.id" class="hover:bg-gray-50">
            <td class="px-6 py-4 whitespace-nowrap">
              <img
                v-if="category.image"
                :src="category.image"
                alt="Category"
                class="w-12 h-12 object-cover rounded-md border"
              />
              <div v-else class="w-12 h-12 bg-gray-100 flex items-center justify-center text-gray-400 text-xs rounded-md">
                No Image
              </div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
              {{ formatCountryName(category.country_name) }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
              {{ category.slug }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-500">
              {{ category.order }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-center">
              <span
                :class="category.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'"
                class="px-2 py-1 text-xs font-semibold rounded-full"
              >
                {{ category.is_active ? 'Active' : 'Inactive' }}
              </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
              <button
                @click="editCategory(category)"
                class="text-indigo-600 hover:text-indigo-900 font-medium"
              >
                Edit
              </button>
              <button
                @click="deleteCategory(category.id)"
                class="text-red-600 hover:text-red-900 font-medium"
              >
                Delete
              </button>
            </td>
          </tr>
          <tr v-if="categories.length === 0">
            <td colspan="6" class="text-center py-6 text-gray-500">No categories found.</td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Modal Form (Create / Edit) -->
    <div
      v-if="showModal"
      class="fixed inset-0 bg-black/50 flex items-center justify-center p-4 z-50 overflow-y-auto"
    >
      <div class="bg-white rounded-lg shadow-xl w-full max-w-lg p-6">
        <h2 class="text-xl font-bold mb-4 text-gray-800">
          {{ isEditing ? "Edit Category" : "Add New Category" }}
        </h2>

        <form @submit.prevent="saveCategory" class="space-y-4">
          <!-- Translatable Country Name Fields -->
          <div>
            <label class="block text-sm font-medium text-gray-700">Name (English)</label>
            <input
              v-model="form.country_name.en"
              type="text"
              required
              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm border p-2 text-sm focus:ring-indigo-500 focus:border-indigo-500"
            />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700">Name (Bengali / Other)</label>
            <input
              v-model="form.country_name.bn"
              type="text"
              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm border p-2 text-sm focus:ring-indigo-500 focus:border-indigo-500"
            />
          </div>

          <!-- Slug -->
          <div>
            <label class="block text-sm font-medium text-gray-700">Slug</label>
            <input
                v-model="form.slug"
                type="text"
                placeholder="Auto generated"
                readonly
                class="mt-1 block w-full rounded-md border border-gray-300 bg-gray-100 p-2 text-sm"
            />
          </div>

          <!-- Order -->
          <div>
            <label class="block text-sm font-medium text-gray-700">Display Order</label>
            <input
              v-model.number="form.order"
              type="number"
              min="0"
              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm border p-2 text-sm focus:ring-indigo-500 focus:border-indigo-500"
            />
          </div>

          <!-- Image File Input -->
          <div>
            <label class="block text-sm font-medium text-gray-700">Category Image</label>
            <input
              type="file"
              accept="image/*"
              @change="handleFileUpload"
              class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100"
            />
            <div v-if="imagePreview" class="mt-2">
              <img :src="imagePreview" class="w-16 h-16 object-cover rounded border" />
            </div>
          </div>

          <!-- Active Status Checkbox -->
          <div class="flex items-center space-x-2">
            <input
              id="is_active"
              v-model="form.is_active"
              type="checkbox"
              class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
            />
            <label for="is_active" class="text-sm font-medium text-gray-700">Is Active</label>
          </div>

          <!-- Validation Errors -->
          <div v-if="formErrors" class="text-red-600 text-sm bg-red-50 p-2 rounded">
            <ul>
              <li v-for="(err, key) in formErrors" :key="key">{{ err[0] }}</li>
            </ul>
          </div>

          <!-- Actions -->
          <div class="flex justify-end space-x-3 pt-4 border-t">
            <button
              type="button"
              @click="closeModal"
              class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50"
            >
              Cancel
            </button>
            <button
              type="submit"
              :disabled="submitting"
              class="px-4 py-2 bg-indigo-600 text-white rounded-md text-sm font-medium hover:bg-indigo-700 disabled:opacity-50"
            >
              {{ submitting ? "Saving..." : "Save Category" }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive, onMounted } from "vue";
import api from "@/services/api";

interface CountryName {
  en: string;
  bn?: string;
}

interface Category {
  id: number;
  slug: string;
  country_name: CountryName | string;
  image: string | null;
  order: number;
  is_active: boolean;
  created_at?: string;
  updated_at?: string;
}

// State variables
const categories = ref<Category[]>([]);
const loading = ref<boolean>(false);
const submitting = ref<boolean>(false);
const showModal = ref<boolean>(false);
const isEditing = ref<boolean>(false);
const editingId = ref<number | null>(null);
const errorMessage = ref<string>("");
const formErrors = ref<Record<string, string[]> | null>(null);
const imageFile = ref<File | null>(null);
const imagePreview = ref<string | null>(null);
const formError = ref("");

// Reactive Form Object
const form = reactive({
  slug: "",
  country_name: {
    en: "",
    bn: "",
  } as CountryName,
  order: 0,
  is_active: true,
});

// Fetch all categories (requesting all locales for admin edit form)
const fetchCategories = async () => {
  loading.value = true;

  try {
    const response = await api.get("/category");

    categories.value =
      response.data?.data || [];
  } catch (error: any) {
    console.error("Failed to load category:", error);

    formError.value =
      error.response?.data?.message ||
      "Failed to load category.";
  } finally {
    loading.value = false;
  }
};

// Open Modal for Create or Edit
const openModal = (category: Category | null = null) => {
  formErrors.value = null;
  imageFile.value = null;
  imagePreview.value = null;

  if (category) {
    isEditing.value = true;
    editingId.value = category.id;
    form.slug = category.slug;
    form.order = category.order;
    form.is_active = category.is_active;

    // Handle string or object structure for country_name
    if (typeof category.country_name === "object" && category.country_name !== null) {
      form.country_name = {
        en: category.country_name.en || "",
        bn: category.country_name.bn || "",
      };
    } else {
      form.country_name = { en: (category.country_name as string) || "", bn: "" };
    }

    imagePreview.value = category.image;
  } else {
    isEditing.value = false;
    editingId.value = null;
    form.slug = "";
    form.order = 0;
    form.is_active = true;
    form.country_name = { en: "", bn: "" };
  }

  showModal.value = true;
};

const closeModal = () => {
  showModal.value = false;
};

const editCategory = (category: Category) => {
  openModal(category);
};

// Handle File Selection
const handleFileUpload = (event: Event) => {
  const target = event.target as HTMLInputElement;
  if (target.files && target.files[0]) {
    const file = target.files[0];
    imageFile.value = file;
    imagePreview.value = URL.createObjectURL(file);
  }
};

// Submit Form (Create / Update)
const saveCategory = async () => {
  submitting.value = true;
  formErrors.value = null;

  try {
    const formData = new FormData();

    // Country name
    formData.append(
      "country_name[en]",
      form.country_name.en.trim()
    );

    formData.append(
      "country_name[bn]",
      form.country_name.bn?.trim() || ""
    );

    // Only send slug when editing
    if (isEditing.value && form.slug.trim()) {
      formData.append(
        "slug",
        form.slug.trim()
      );
    }

    // Order
    formData.append(
      "order",
      String(form.order)
    );

    // Active
    formData.append(
      "is_active",
      form.is_active ? "1" : "0"
    );

    // Image
    if (imageFile.value) {
      formData.append(
        "image",
        imageFile.value
      );
    }

    if (isEditing.value) {
      // Laravel method spoofing
      formData.append("_method", "PUT");

      await api.post(
        `/admin/category/${editingId.value}`,
        formData
      );
    } else {
      await api.post(
        "/admin/category",
        formData
      );
    }

    await fetchCategories();

    closeModal();

  } catch (error: any) {
    console.error(
      "SAVE CATEGORY ERROR:",
      error
    );

    if (error.response?.status === 422) {
      formErrors.value =
        error.response.data.errors || {};
    } else {
      alert(
        error.response?.data?.message ||
        "Failed to save category."
      );
    }
  } finally {
    submitting.value = false;
  }
};

// Delete Category
const deleteCategory = async (id: number) => {
  if (!confirm("Are you sure you want to delete this category?")) {
    return;
  }

  try {
    await api.delete(
      `/admin/category/${id}`
    );

    await fetchCategories();

  } catch (error: any) {
    console.error(
      "DELETE CATEGORY ERROR:",
      error
    );

    alert(
      error.response?.data?.message ||
      "Failed to delete category."
    );
  }
};

// Helper function to render name in list view safely
const formatCountryName = (name: CountryName | string): string => {
  if (typeof name === "string") return name;
  if (typeof name === "object" && name !== null) {
    return name.en || name.bn || Object.values(name)[0] || "—";
  }
  return "—";
};

onMounted(() => {
  fetchCategories();
});
</script>