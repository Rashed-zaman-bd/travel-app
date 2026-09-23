<template>
  <div class="min-h-screen bg-gray-50 p-4 md:p-6">
    <div class="mx-auto max-w-7xl">

      <!-- Header -->
      <div
        class="mb-6 flex flex-col gap-4 rounded-xl bg-white p-5 shadow-sm sm:flex-row sm:items-center sm:justify-between"
      >
        <div>
          <h1 class="text-2xl font-bold text-gray-800">
            Navigation Items
          </h1>

          <p class="mt-1 text-sm text-gray-500">
            Manage your website header navigation menu.
          </p>
        </div>

        <button
          type="button"
          @click="openCreateModal"
          class="inline-flex items-center justify-center rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-medium text-white transition hover:bg-blue-700 cursor-pointer"
        >
          <span class="mr-2 text-lg">+</span>
          Add Navigation
        </button>
      </div>

      <!-- Success -->
      <div
        v-if="successMessage"
        class="mb-5 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700"
      >
        {{ successMessage }}
      </div>

      <!-- Error -->
      <div
        v-if="errorMessage"
        class="mb-5 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700"
      >
        {{ errorMessage }}
      </div>

      <!-- Loading -->
      <div
        v-if="loading"
        class="rounded-xl bg-white p-10 text-center shadow-sm"
      >
        <div class="text-gray-500">
          Loading navigation items...
        </div>
      </div>

      <!-- Empty -->
      <div
        v-else-if="navItems.length === 0"
        class="rounded-xl bg-white p-10 text-center shadow-sm"
      >
        <div class="mb-3 text-4xl">
          ☰
        </div>

        <h2 class="text-lg font-semibold text-gray-800">
          No navigation items
        </h2>

        <p class="mt-1 text-sm text-gray-500">
          Create your first navigation item.
        </p>

        <button
          type="button"
          @click="openCreateModal"
          class="mt-5 rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-medium text-white hover:bg-blue-700"
        >
          Add Navigation
        </button>
      </div>

      <!-- Table -->
      <div
        v-else
        class="overflow-hidden rounded-xl bg-white shadow-sm"
      >
        <div class="overflow-x-auto">
          <table class="w-full min-w-[850px] text-left">
            <thead class="border-b bg-gray-50">
              <tr>
                <th class="px-5 py-4 text-xs font-semibold uppercase text-gray-500">
                  Order
                </th>

                <th class="px-5 py-4 text-xs font-semibold uppercase text-gray-500">
                  Navigation
                </th>

                <th class="px-5 py-4 text-xs font-semibold uppercase text-gray-500">
                  URL
                </th>

                <th class="px-5 py-4 text-xs font-semibold uppercase text-gray-500">
                  Status
                </th>

                <th class="px-5 py-4 text-xs font-semibold uppercase text-gray-500">
                  New Tab
                </th>

                <th class="px-5 py-4 text-right text-xs font-semibold uppercase text-gray-500">
                  Actions
                </th>
              </tr>
            </thead>

            <tbody class="divide-y divide-gray-100">

              <!-- Parent -->
              <template
                v-for="item in navItems"
                :key="item.id"
              >
                <tr class="hover:bg-gray-50">

                  <td class="px-5 py-4">
                    <span
                      class="inline-flex h-8 w-8 items-center justify-center rounded-md bg-gray-100 text-sm font-semibold text-gray-700"
                    >
                      {{ item.order }}
                    </span>
                  </td>

                  <td class="px-5 py-4">
                    <div class="flex items-center gap-3">
                      <span
                        v-if="item.icon"
                        class="text-lg"
                      >
                        {{ item.icon }}
                      </span>

                      <div>
                        <div class="font-semibold text-gray-800">
                          {{ item.title.en }}
                        </div>

                        <div class="text-xs text-gray-400">
                          {{ item.title.bn }} · ID: {{ item.id }}
                        </div>
                      </div>
                    </div>
                  </td>

                  <td class="px-5 py-4">
                    <span
                      v-if="item.url"
                      class="text-sm text-gray-600"
                    >
                      {{ item.url }}
                    </span>

                    <span
                      v-else
                      class="text-sm italic text-gray-400"
                    >
                      Dropdown
                    </span>
                  </td>

                  <td class="px-5 py-4">
                    <button
                      type="button"
                      @click="toggleActive(item)"
                      class="rounded-full px-3 py-1 text-xs font-medium"
                      :class="
                        item.is_active
                          ? 'bg-green-100 text-green-700'
                          : 'bg-gray-100 text-gray-500'
                      "
                    >
                      {{ item.is_active ? "Active" : "Inactive" }}
                    </button>
                  </td>

                  <td class="px-5 py-4 text-sm text-gray-600">
                    {{ item.open_new_tab ? "Yes" : "No" }}
                  </td>

                  <td class="px-5 py-4">
                    <div class="flex justify-end gap-2">
                      <button
                        type="button"
                        @click="openEditModal(item)"
                        class="rounded-lg bg-blue-50 px-3 py-2 text-sm font-medium text-blue-600 hover:bg-blue-100 cursor-pointer"
                      >
                        Edit
                      </button>

                      <button
                        type="button"
                        @click="deleteNavItem(item)"
                        :disabled="deleting"
                        class="rounded-lg bg-red-50 px-3 py-2 text-sm font-medium text-red-600 hover:bg-red-100 disabled:opacity-50 cursor-pointer"
                      >
                        Delete
                      </button>
                    </div>
                  </td>
                </tr>

                <!-- Children -->
                <tr
                  v-for="child in getChildren(item)"
                  :key="child.id"
                  class="bg-gray-50/70 hover:bg-gray-100"
                >
                  <td class="px-5 py-3">
                    <span class="ml-5 text-sm text-gray-500">
                      └ {{ child.order }}
                    </span>
                  </td>

                  <td class="px-5 py-3">
                    <div class="ml-6 flex items-center gap-3">
                      <span class="text-gray-400">
                        ↳
                      </span>

                      <div>
                        <div class="font-medium text-gray-700">
                          {{ child.title.en }}
                        </div>

                        <div class="text-xs text-gray-400">
                          {{ child.title.bn }} · ID: {{ child.id }}
                        </div>
                      </div>
                    </div>
                  </td>

                  <td class="px-5 py-3">
                    <span
                      v-if="child.url"
                      class="text-sm text-gray-600"
                    >
                      {{ child.url }}
                    </span>

                    <span
                      v-else
                      class="text-sm italic text-gray-400"
                    >
                      Dropdown
                    </span>
                  </td>

                  <td class="px-5 py-3">
                    <button
                      type="button"
                      @click="toggleActive(child)"
                      class="rounded-full px-3 py-1 text-xs font-medium"
                      :class="
                        child.is_active
                          ? 'bg-green-100 text-green-700'
                          : 'bg-gray-100 text-gray-500'
                      "
                    >
                      {{ child.is_active ? "Active" : "Inactive" }}
                    </button>
                  </td>

                  <td class="px-5 py-3 text-sm text-gray-600">
                    {{ child.open_new_tab ? "Yes" : "No" }}
                  </td>

                  <td class="px-5 py-3">
                    <div class="flex justify-end gap-2">
                      <button
                        type="button"
                        @click="openEditModal(child)"
                        class="rounded-lg bg-blue-50 px-3 py-2 text-sm font-medium text-blue-600 hover:bg-blue-100 cursor-pointer"
                      >
                        Edit
                      </button>

                      <button
                        type="button"
                        @click="deleteNavItem(child)"
                        :disabled="deleting"
                        class="rounded-lg bg-red-50 px-3 py-2 text-sm font-medium text-red-600 hover:bg-red-100 cursor-pointer"
                      >
                        Delete
                      </button>
                    </div>
                  </td>
                </tr>
              </template>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- =========================
         CREATE / EDIT MODAL
    ========================== -->
    <div
      v-if="showModal"
      class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4"
      @click.self="closeModal"
    >
      <div
        class="w-full max-w-2xl overflow-hidden rounded-2xl bg-white shadow-xl"
      >
        <!-- Modal Header -->
        <div
          class="flex items-center justify-between border-b px-6 py-4"
        >
          <div>
            <h2 class="text-xl font-bold text-gray-800">
              {{ editingItem ? "Edit Navigation" : "Add Navigation" }}
            </h2>

            <p class="mt-1 text-sm text-gray-500">
              {{
                editingItem
                  ? "Update navigation item information."
                  : "Create a new navigation item."
              }}
            </p>
          </div>

          <button
            type="button"
            @click="closeModal"
            class="rounded-lg p-2 text-gray-400 hover:bg-gray-100 hover:text-gray-700 cursor-pointer"
          >
            ✕
          </button>
        </div>

        <!-- Form -->
        <form
          @submit.prevent="saveNavItem"
          class="max-h-[75vh] overflow-y-auto p-6"
        >
          <div class="grid gap-5 md:grid-cols-2">

            <!-- Title (English) -->
            <div>
              <label class="mb-2 block text-sm font-medium text-gray-700">
                Title (English)
                <span class="text-red-500">*</span>
              </label>

              <input
                v-model="form.title_en"
                type="text"
                placeholder="Example: Home"
                class="w-full rounded-lg border border-gray-300 px-4 py-2.5 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                required
              />
            </div>

            <!-- Title (Bangla) -->
            <div>
              <label class="mb-2 block text-sm font-medium text-gray-700">
                Title (বাংলা)
                <span class="text-red-500">*</span>
              </label>

              <input
                v-model="form.title_bn"
                type="text"
                placeholder="উদাহরণ: হোম"
                class="w-full rounded-lg border border-gray-300 px-4 py-2.5 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                required
              />
            </div>

            <!-- Parent -->
            <div>
              <label class="mb-2 block text-sm font-medium text-gray-700">
                Parent
              </label>

              <select
                v-model="form.parent_id"
                class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
              >
                <option :value="null">
                  No Parent
                </option>

                <option
                  v-for="parent in parentOptions"
                  :key="parent.id"
                  :value="parent.id"
                >
                  {{ parent.title.en }}
                </option>
              </select>
            </div>

            <!-- Order -->
            <div>
              <label class="mb-2 block text-sm font-medium text-gray-700">
                Order
              </label>

              <input
                v-model.number="form.order"
                type="number"
                min="0"
                class="w-full rounded-lg border border-gray-300 px-4 py-2.5 outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
              />
            </div>

            <!-- URL -->
            <div>
              <label class="mb-2 block text-sm font-medium text-gray-700">
                URL
              </label>

              <input
                v-model="form.url"
                type="text"
                placeholder="/about"
                class="w-full rounded-lg border border-gray-300 px-4 py-2.5 outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
              />

              <p class="mt-1 text-xs text-gray-400">
                Leave empty if this item is only a dropdown parent.
              </p>
            </div>

            <!-- Icon -->
            <div>
              <label class="mb-2 block text-sm font-medium text-gray-700">
                Icon
              </label>

              <input
                v-model="form.icon"
                type="text"
                placeholder="home"
                class="w-full rounded-lg border border-gray-300 px-4 py-2.5 outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
              />

              <p class="mt-1 text-xs text-gray-400">
                Example: home, user, settings
              </p>
            </div>

            <!-- Active -->
            <div
              class="rounded-lg border border-gray-200 p-4"
            >
              <label class="flex cursor-pointer items-center gap-3">
                <input
                  v-model="form.is_active"
                  type="checkbox"
                  class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                />

                <div>
                  <div class="text-sm font-medium text-gray-700">
                    Active
                  </div>

                  <div class="text-xs text-gray-400">
                    Show this item on the website.
                  </div>
                </div>
              </label>
            </div>

            <!-- New Tab -->
            <div
              class="rounded-lg border border-gray-200 p-4"
            >
              <label class="flex cursor-pointer items-center gap-3">
                <input
                  v-model="form.open_new_tab"
                  type="checkbox"
                  class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                />

                <div>
                  <div class="text-sm font-medium text-gray-700">
                    Open New Tab
                  </div>

                  <div class="text-xs text-gray-400">
                    Open this URL in a new browser tab.
                  </div>
                </div>
              </label>
            </div>
          </div>

          <!-- Validation error -->
          <div
            v-if="errorMessage"
            class="mt-5 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700"
          >
            {{ errorMessage }}
          </div>

          <!-- Buttons -->
          <div
            class="mt-6 flex justify-end gap-3 border-t pt-5"
          >
            <button
              type="button"
              @click="closeModal"
              :disabled="saving"
              class="rounded-lg border border-gray-300 px-5 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 disabled:opacity-50 cursor-pointer"
            >
              Cancel
            </button>

            <button
              type="submit"
              :disabled="saving"
              class="rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-medium text-white hover:bg-blue-700 disabled:cursor-not-allowed disabled:opacity-50 cursor-pointer"
            >
              {{
                saving
                  ? "Saving..."
                  : editingItem
                    ? "Update"
                    : "Create"
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

interface NavTitle {
  en: string;
  bn: string;
}

interface NavItem {
  id: number;
  parent_id: number | null;
  title: NavTitle;
  url: string | null;
  icon: string | null;
  order: number;
  is_active: boolean;
  open_new_tab: boolean;
  children?: NavItem[];
  created_at?: string | null;
  updated_at?: string | null;
}

interface NavItemForm {
  parent_id: number | null;
  title_en: string;
  title_bn: string;
  url: string;
  icon: string;
  order: number;
  is_active: boolean;
  open_new_tab: boolean;
}

const navItems = ref<NavItem[]>([]);
const loading = ref(false);
const saving = ref(false);
const deleting = ref(false);

const showModal = ref(false);
const editingItem = ref<NavItem | null>(null);

const errorMessage = ref("");
const successMessage = ref("");

const form = reactive<NavItemForm>({
  parent_id: null,
  title_en: "",
  title_bn: "",
  url: "",
  icon: "",
  order: 0,
  is_active: true,
  open_new_tab: false,
});

// --------------------------------------------------
// Flatten items for parent dropdown
// --------------------------------------------------

const parentOptions = computed(() => {
  const options: NavItem[] = [];

  const addItems = (items: NavItem[]) => {
    for (const item of items) {
      options.push(item);

      if (item.children?.length) {
        addItems(item.children);
      }
    }
  };

  addItems(navItems.value);

  return options.filter((item) => {
    // Do not allow current item to become its own parent
    return !editingItem.value || item.id !== editingItem.value.id;
  });
});

// --------------------------------------------------
// Load nav items
// --------------------------------------------------

const fetchNavItems = async () => {
  loading.value = true;
  errorMessage.value = "";

  try {
    const response = await api.get("/admin/nav-items");

    navItems.value = response.data.data || [];
  } catch (error: any) {
    console.error(error);

    errorMessage.value =
      error?.response?.data?.message ||
      "Failed to load navigation items.";
  } finally {
    loading.value = false;
  }
};

// --------------------------------------------------
// Open create modal
// --------------------------------------------------

const openCreateModal = () => {
  editingItem.value = null;

  Object.assign(form, {
    parent_id: null,
    title_en: "",
    title_bn: "",
    url: "",
    icon: "",
    order: 0,
    is_active: true,
    open_new_tab: false,
  });

  errorMessage.value = "";
  showModal.value = true;
};

// --------------------------------------------------
// Open edit modal
// --------------------------------------------------

const openEditModal = (item: NavItem) => {
  editingItem.value = item;

  Object.assign(form, {
    parent_id: item.parent_id,
    title_en: item.title?.en || "",
    title_bn: item.title?.bn || "",
    url: item.url || "",
    icon: item.icon || "",
    order: item.order ?? 0,
    is_active: item.is_active,
    open_new_tab: item.open_new_tab,
  });

  errorMessage.value = "";
  showModal.value = true;
};

// --------------------------------------------------
// Close modal
// --------------------------------------------------

const closeModal = () => {
  if (saving.value) return;

  showModal.value = false;
  editingItem.value = null;
};

// --------------------------------------------------
// Save
// --------------------------------------------------

const saveNavItem = async () => {
  errorMessage.value = "";
  successMessage.value = "";

  if (!form.title_en.trim() || !form.title_bn.trim()) {
    errorMessage.value = "Both English and Bangla titles are required.";
    return;
  }

  saving.value = true;

  try {
    const payload = {
      parent_id: form.parent_id || null,
      title: {
        en: form.title_en.trim(),
        bn: form.title_bn.trim(),
      },
      url: form.url.trim() || null,
      icon: form.icon.trim() || null,
      order: Number(form.order),
      is_active: form.is_active,
      open_new_tab: form.open_new_tab,
    };

    if (editingItem.value) {
      const response = await api.put(
        `/admin/nav-items/${editingItem.value.id}`,
        payload
      );

      successMessage.value =
        response?.data?.message ||
        "Navigation item updated successfully.";
    } else {
      const response = await api.post("/admin/nav-items", payload);

      successMessage.value =
        response?.data?.message ||
        "Navigation item created successfully.";
    }

    showModal.value = false;

    await fetchNavItems();

    setTimeout(() => {
      successMessage.value = "";
    }, 3000);
  } catch (error: any) {
    console.error(error);

    if (error?.response?.status === 422) {
      const errors = error.response.data?.errors;

      if (errors) {
        errorMessage.value = Object.values(errors)
          .flat()
          .join(" ");
      } else {
        errorMessage.value =
          error.response.data?.message || "Validation failed.";
      }
    } else {
      errorMessage.value =
        error?.response?.data?.message ||
        "Something went wrong.";
    }
  } finally {
    saving.value = false;
  }
};

// --------------------------------------------------
// Delete
// --------------------------------------------------

const deleteNavItem = async (item: NavItem) => {
  if (item.children?.length) {
    alert(
      "This navigation item has child items. Please delete or move the child items first."
    );

    return;
  }

  const confirmed = window.confirm(
    `Are you sure you want to delete "${item.title.en}"?`
  );

  if (!confirmed) return;

  deleting.value = true;
  errorMessage.value = "";

  try {
    const response = await api.delete(
      `/admin/nav-items/${item.id}`
    );

    successMessage.value =
      response?.data?.message ||
      "Navigation item deleted successfully.";

    await fetchNavItems();

    setTimeout(() => {
      successMessage.value = "";
    }, 3000);
  } catch (error: any) {
    console.error(error);

    errorMessage.value =
      error?.response?.data?.message ||
      "Failed to delete navigation item.";
  } finally {
    deleting.value = false;
  }
};

// --------------------------------------------------
// Toggle active status
// --------------------------------------------------

const toggleActive = async (item: NavItem) => {
  try {
    await api.put(`/admin/nav-items/${item.id}`, {
      parent_id: item.parent_id,
      title: item.title,
      url: item.url,
      icon: item.icon,
      order: item.order,
      is_active: !item.is_active,
      open_new_tab: item.open_new_tab,
    });

    item.is_active = !item.is_active;
  } catch (error: any) {
    console.error(error);

    errorMessage.value =
      error?.response?.data?.message ||
      "Failed to update status.";
  }
};

// --------------------------------------------------
// Tree row component helper
// --------------------------------------------------

const getChildren = (item: NavItem) => {
  return item.children || [];
};

onMounted(() => {
  fetchNavItems();
});
</script>