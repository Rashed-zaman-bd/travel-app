<template>
  <div class="p-6">
    <div class="flex items-center justify-between mb-4">
      <h1 class="text-xl font-semibold">How It Works — Steps</h1>
      <button
        @click="openCreate"
        class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700"
      >
        + Add Step
      </button>
    </div>

    <div v-if="loading" class="text-gray-500">Loading...</div>
    <div v-else-if="error" class="text-red-600">{{ error }}</div>

    <table v-else class="w-full border-collapse bg-white shadow-sm rounded">
      <thead>
        <tr class="text-left border-b bg-gray-50">
          <th class="p-3 w-16">Order</th>
          <th class="p-3">Icon</th>
          <th class="p-3">Title (EN)</th>
          <th class="p-3">Heading (EN)</th>
          <th class="p-3 w-24">Active</th>
          <th class="p-3 w-40">Actions</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="step in steps" :key="step.id" class="border-b hover:bg-gray-50">
          <td class="p-3">{{ step.order }}</td>
          <td class="p-3">
            <i :class="[step.icon[0], `fa-${step.icon[1]}`]"></i>
          </td>
          <td class="p-3">{{ step.translations?.title?.en ?? step.title }}</td>
          <td class="p-3">{{ step.translations?.heading?.en ?? step.heading }}</td>
          <td class="p-3">
            <button
              @click="toggleActive(step)"
              :class="step.is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500'"
              class="px-2 py-1 rounded text-sm"
            >
              {{ step.is_active ? "Active" : "Hidden" }}
            </button>
          </td>
          <td class="p-3 space-x-3">
            <button @click="openEdit(step)" class="text-blue-600 hover:underline text-sm">
              Edit
            </button>
            <button @click="openDelete(step)" class="text-red-600 hover:underline text-sm">
              Delete
            </button>
          </td>
        </tr>
        <tr v-if="steps.length === 0">
          <td colspan="6" class="p-6 text-center text-gray-400">
            No steps yet. Add your first one.
          </td>
        </tr>
      </tbody>
    </table>

    <!-- Create / Edit modal -->
    <StepFormModal
      v-model:open="showFormModal"
      :step="editingStep"
      @saved="handleSaved"
    />

    <!-- Delete confirmation modal -->
    <ConfirmDeleteModal
      v-model:open="showDeleteModal"
      title="Delete step"
      :message="`Delete step ${deleteLabel}? This can't be undone.`"
      :loading="deleting"
      @confirm="confirmDelete"
    />
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from "vue";
import { howItWorksApi, type HowItWorksStep } from "@/types/Howitworks";
import StepFormModal from "@/components/admin/Howitworksform.vue";
import ConfirmDeleteModal from "@/components/admin/HowitworksDeleteModal.vue";

const steps = ref<HowItWorksStep[]>([]);
const loading = ref(true);
const error = ref("");

// Create/edit modal state
const showFormModal = ref(false);
const editingStep = ref<HowItWorksStep | null>(null);

// Delete modal state
const showDeleteModal = ref(false);
const stepPendingDelete = ref<HowItWorksStep | null>(null);
const deleting = ref(false);

const deleteLabel = computed(
  () => stepPendingDelete.value?.translations?.title?.en ?? stepPendingDelete.value?.title ?? ""
);

async function fetchSteps() {
  loading.value = true;
  error.value = "";
  try {
    const { data } = await howItWorksApi.adminList();
    steps.value = data.data;
  } catch (e) {
    error.value = "Failed to load steps.";
  } finally {
    loading.value = false;
  }
}

function openCreate() {
  editingStep.value = null;
  showFormModal.value = true;
}

function openEdit(step: HowItWorksStep) {
  editingStep.value = step;
  showFormModal.value = true;
}

function handleSaved(saved: HowItWorksStep) {
  const index = steps.value.findIndex((s) => s.id === saved.id);
  if (index !== -1) {
    steps.value[index] = saved;
  } else {
    steps.value.push(saved);
    steps.value.sort((a, b) => a.order - b.order);
  }
}

async function toggleActive(step: HowItWorksStep) {
  const previous = step.is_active;
  step.is_active = !step.is_active; // optimistic
  try {
    await howItWorksApi.update(step.id, { is_active: step.is_active });
  } catch (e) {
    step.is_active = previous; // revert on failure
    error.value = "Failed to update status.";
  }
}

function openDelete(step: HowItWorksStep) {
  stepPendingDelete.value = step;
  showDeleteModal.value = true;
}

async function confirmDelete() {
  if (!stepPendingDelete.value) return;
  deleting.value = true;
  try {
    await howItWorksApi.destroy(stepPendingDelete.value.id);
    steps.value = steps.value.filter((s) => s.id !== stepPendingDelete.value!.id);
    showDeleteModal.value = false;
  } catch (e) {
    error.value = "Failed to delete step.";
  } finally {
    deleting.value = false;
  }
}

onMounted(fetchSteps);
</script>