<template>
  <Teleport to="body">
    <div
      v-if="open"
      class="fixed inset-0 z-[99999] flex items-center justify-center bg-black/50 p-4"
      @click.self="close"
    >
      <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between mb-4">
          <h2 class="text-lg font-semibold text-gray-800">
            {{ isEdit ? 'Edit User' : 'New User' }}
          </h2>
          <button @click="close" class="text-gray-400 hover:text-gray-600">✕</button>
        </div>

        <form @submit.prevent="submit" class="space-y-4">
          <p v-if="errors.general" class="rounded-lg bg-red-50 px-3 py-2 text-sm text-red-600">
            {{ errors.general }}
          </p>

          <div>
            <label class="block text-xs font-semibold text-gray-700">Name</label>
            <input v-model="form.name" type="text" required
              class="mt-1 w-full rounded-lg border px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-300">
            <p v-if="errors.name" class="text-red-500 text-xs mt-1">{{ errors.name[0] }}</p>
          </div>

          <div>
            <label class="block text-xs font-semibold text-gray-700">Email</label>
            <input v-model="form.email" type="email" required
              class="mt-1 w-full rounded-lg border px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-300">
            <p v-if="errors.email" class="text-red-500 text-xs mt-1">{{ errors.email[0] }}</p>
          </div>

          <div>
            <label class="block text-xs font-semibold text-gray-700">Phone</label>
            <input v-model="form.phone" type="text"
              class="mt-1 w-full rounded-lg border px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-300">
            <p v-if="errors.phone" class="text-red-500 text-xs mt-1">{{ errors.phone[0] }}</p>
          </div>

          <div>
            <label class="block text-xs font-semibold text-gray-700">
              Password {{ isEdit ? '(leave blank to keep unchanged)' : '' }}
            </label>
            <input v-model="form.password" type="password" :required="!isEdit"
              class="mt-1 w-full rounded-lg border px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-300">
            <p v-if="errors.password" class="text-red-500 text-xs mt-1">{{ errors.password[0] }}</p>
          </div>

          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block text-xs font-semibold text-gray-700">Role</label>
              <select v-model="form.role"
                class="mt-1 w-full rounded-lg border px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-300">
                <option value="user">User</option>
                <option value="admin">Admin</option>
                <option value="super_admin">Super Admin</option>
              </select>
            </div>
          </div>

          <div>
            <label class="block text-xs font-semibold text-gray-700">Avatar</label>
            <input type="file" accept="image/jpeg,image/jpg,image/png" @change="handleFile" class="mt-1 w-full text-sm">
            <p v-if="errors.avatar" class="text-red-500 text-xs mt-1">{{ errors.avatar[0] }}</p>
            <img v-if="preview" :src="preview" class="mt-2 w-16 h-16 rounded-full object-cover border">
          </div>

          <button type="submit" :disabled="submitting"
            class="w-full rounded-lg bg-emerald-600 py-2.5 text-sm font-semibold text-white hover:bg-emerald-700 disabled:opacity-50">
            {{ submitting ? 'Saving...' : (isEdit ? 'Save Changes' : 'Create User') }}
          </button>
        </form>
      </div>
    </div>
  </Teleport>
</template>

<script setup lang="ts">
import { reactive, ref, computed, watch } from 'vue'
import api from '@/services/api'
import Swal from 'sweetalert2'
import type { AdminUser } from '@/types/user'

const props = defineProps<{
  open: boolean
  editingUser: AdminUser | null
}>()

const emit = defineEmits<{
  close: []
  saved: []
}>()

const isEdit = computed(() => !!props.editingUser)

const form = reactive({
  name: '',
  email: '',
  phone: '',
  password: '',
  role: 'user',
})

const avatarFile = ref<File | null>(null)
const preview = ref<string | null>(null)
const submitting = ref(false)
const errors = reactive<Record<string, string[] | string>>({})

watch(() => props.open, (isOpen) => {
  if (isOpen) resetForm()
})

const resetForm = () => {
  Object.assign(form, {
    name: props.editingUser?.name || '',
    email: props.editingUser?.email || '',
    phone: props.editingUser?.phone || '',
    password: '',
    role: props.editingUser?.role || 'user',
  })
  avatarFile.value = null
  preview.value = props.editingUser?.avatar || null
  Object.keys(errors).forEach((k) => delete errors[k])
}

const handleFile = (e: Event) => {
  const file = (e.target as HTMLInputElement).files?.[0]
  if (!file) return
  avatarFile.value = file
  preview.value = URL.createObjectURL(file)
}

const close = () => emit('close')

const submit = async () => {
  submitting.value = true
  Object.keys(errors).forEach((k) => delete errors[k])

  const payload = new FormData()
  payload.append('name', form.name)
  payload.append('email', form.email)
  payload.append('phone', form.phone || '')
  if (form.password) payload.append('password', form.password)
  payload.append('role', form.role)
  if (avatarFile.value) payload.append('avatar', avatarFile.value)

  try {
    if (isEdit.value && props.editingUser) {
      payload.append('_method', 'PUT') // spoof PUT so multipart survives
      await api.post(`/admin/users/${props.editingUser.id}`, payload, {
        headers: { 'Content-Type': 'multipart/form-data' },
      })
    } else {
      await api.post('/admin/users', payload, {
        headers: { 'Content-Type': 'multipart/form-data' },
      })
    }

    Swal.fire({
      icon: 'success',
      title: isEdit.value ? 'User updated' : 'User created',
      timer: 1500,
      showConfirmButton: false,
    })
    emit('saved')
  } catch (error: any) {
    if (error?.response?.status === 422) {
      Object.assign(errors, error.response.data.errors || {})
    } else {
      errors.general = error?.response?.data?.message || 'Something went wrong.'
    }
  } finally {
    submitting.value = false
  }
}
</script>