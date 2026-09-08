<template>
  <Teleport to="body">
    <div
      v-if="category"
      class="fixed inset-0 z-[99999] flex items-center justify-center bg-black/50 p-4"
      @click.self="close"
    >
      <div class="w-full max-w-sm rounded-2xl bg-white p-6 shadow-2xl">
        <h2 class="text-lg font-semibold text-gray-800">Delete User</h2>
        <p class="mt-2 text-sm text-gray-500">
          Are you sure you want to delete
          <span class="font-medium text-gray-700">{{ category?.name }}</span>?
          This can be restored later from the trashed list.
        </p>

        <div class="mt-6 flex justify-end gap-2">
          <button @click="close" class="px-4 py-2 rounded-lg text-sm text-gray-600 hover:bg-gray-100">
            Cancel
          </button>
          <button
            @click="remove"
            :disabled="deleting"
            class="px-4 py-2 rounded-lg text-sm bg-red-600 text-white hover:bg-red-700 disabled:opacity-50"
          >
            {{ deleting ? 'Deleting...' : 'Delete' }}
          </button>
        </div>
      </div>
    </div>
  </Teleport>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import api from '@/services/api'
import Swal from 'sweetalert2'
import type { AdminUser } from '@/types/user'

const props = defineProps<{ category: AdminUser | null }>()
const emit = defineEmits<{ close: []; deleted: [] }>()

const deleting = ref(false)

const close = () => emit('close')

const remove = async () => {
  if (!props.category) return
  deleting.value = true
  try {
    await api.delete(`/admin/users/${props.category.id}`)
    Swal.fire({ icon: 'success', title: 'User deleted', timer: 1500, showConfirmButton: false })
    emit('deleted')
  } catch (error: any) {
    Swal.fire({ icon: 'error', title: 'Delete failed', text: error?.response?.data?.message })
  } finally {
    deleting.value = false
  }
}
</script>