<template>
    <div>
        <!-- Header -->
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-gray-800">User Management</h1>
            <button
                @click="openCreateModal"
                class="flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg transition"
            >
                <i class="bi bi-plus-lg"></i>
                New User
            </button>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-lg shadow p-4 mb-4 flex flex-col sm:flex-row gap-3">
            <input
                v-model="filters.search"
                @input="debouncedSearch"
                type="text"
                placeholder="search name, email or phone..."
                class="flex-1 border rounded-md px-3 py-2 outline-none focus:ring-2 focus:ring-emerald-300"
            >

            <select
                v-model="filters.role"
                @change="fetchUsers(1)"
                class="border rounded-md px-3 py-2 outline-none focus:ring-2 focus:ring-emerald-300"
            >
                <option value="">Roles</option>
                <option value="super_admin">Super Admin</option>
                <option value="admin">Admin</option>
                <option value="user">User</option>
            </select>

            <select
                v-model="filters.status"
                @change="fetchUsers(1)"
                class="border rounded-md px-3 py-2 outline-none focus:ring-2 focus:ring-emerald-300"
            >
                <option value="">Status</option>
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
            </select>

            <label class="flex items-center gap-2 text-sm text-gray-600 whitespace-nowrap px-2">
                <input type="checkbox" v-model="filters.trashed" @change="fetchUsers(1)">
                Delete User
            </label>
        </div>

        <!-- Table -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div v-if="loading" class="p-10 text-center text-gray-500">
                <i class="bi bi-arrow-repeat animate-spin text-2xl"></i>
                <p class="mt-2">loding...</p>
            </div>

            <div v-else-if="users.length === 0" class="p-10 text-center text-gray-500">
                    no user found
            </div>

            <table v-else class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-600 text-left border-b">
                    <tr>
                        <th class="px-4 py-3">Name</th>
                        <th class="px-4 py-3">Phone</th>
                        <th class="px-4 py-3">Role</th>
                        <th class="px-4 py-3">created</th>
                        <th class="px-4 py-3 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    <tr v-for="u in users" :key="u.id" class="hover:bg-gray-50">
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-3">
                                <img
                                    v-if="u.avatar"
                                    :src="u.avatar"
                                    class="w-9 h-9 rounded-full object-cover border"
                                >
                                <i v-else class="bi bi-person-circle text-2xl text-gray-400"></i>
                                <div>
                                    <p class="font-medium text-gray-800">{{ u.name }}</p>
                                    <p class="text-xs text-gray-500">{{ u.email }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-gray-600">{{ u.phone || '—' }}</td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 rounded-full text-xs font-medium" :class="roleBadge(u.role)">
                                {{ roleLabel(u.role) }}
                            </span>
                        </td>

                        <td class="px-4 py-3 text-gray-500">{{ formatDate(u.created_at) }}</td>
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-end gap-2">
                                <template v-if="!filters.trashed">
                                    <button
                                        @click="openEditModal(u)"
                                        class="p-2 rounded hover:bg-gray-100 text-gray-600"
                                        title="Edit"
                                    >
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <button
                                        @click="confirmDelete(u)"
                                        class="p-2 rounded hover:bg-red-50 text-red-600"
                                        title="Delete"
                                    >
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </template>
                                <template v-else>
                                    <button
                                        @click="restoreUser(u)"
                                        class="p-2 rounded hover:bg-emerald-50 text-emerald-600"
                                        title="Restore User"
                                    >
                                        <i class="bi bi-arrow-counterclockwise"></i>
                                    </button>
                                </template>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>

            <!-- Pagination -->
            <div v-if="meta && meta.last_page > 1" class="flex items-center justify-between px-4 py-3 border-t">
                <p class="text-sm text-gray-500">
                    মোট {{ meta.total }} জনের মধ্যে {{ meta.from }}–{{ meta.to }} দেখানো হচ্ছে
                </p>
                <div class="flex gap-1">
                    <button
                        v-for="page in meta.last_page"
                        :key="page"
                        @click="fetchUsers(page)"
                        class="px-3 py-1 rounded text-sm"
                        :class="page === meta.current_page ? 'bg-emerald-600 text-white' : 'hover:bg-gray-100 text-gray-600'"
                    >
                        {{ page }}
                    </button>
                </div>
            </div>
        </div>

        <!-- Modals section — replace your existing one -->

        <UserFormModal
            :open="showFormModal"
            :editing-user="editingUser"
            @close="showFormModal = false"
            @saved="handleSaved"
        />

        <UserDeleteModal
            :category="deletingUser"
            @close="deletingUser = null"
            @deleted="handleDeleted"
        />

    </div>
</template>
<script setup lang="ts">
import { ref, reactive, onMounted } from 'vue'
import api from '@/services/api'
import Swal from 'sweetalert2'

import UserFormModal from '@/components/admin/user/UserFormModal.vue'
import UserDeleteModal from '@/components/admin/user/UserDeleteModal.vue'
import type { AdminUser, PaginationMeta } from '@/types/user'

const users = ref<AdminUser[]>([])
const meta = ref<PaginationMeta | null>(null)
const loading = ref(false)

const filters = reactive({
  search: '',
  role: '',
  status: '',
  trashed: false,
})

let searchTimeout: ReturnType<typeof setTimeout> | null = null
const debouncedSearch = () => {
  if (searchTimeout) clearTimeout(searchTimeout)
  searchTimeout = setTimeout(() => fetchUsers(1), 400)
}

const fetchUsers = async (page = 1) => {
  loading.value = true
  try {
    const { data } = await api.get('/admin/users', {
      params: {
        page,
        search: filters.search || undefined,
        role: filters.role || undefined,
        status: filters.status || undefined,
        trashed: filters.trashed ? 1 : undefined,
      },
    })
    users.value = data.data
    meta.value = data.meta
  } catch (e) {
    console.error(e)
    Swal.fire({ icon: 'error', title: 'Failed to load users' })
  } finally {
    loading.value = false
  }
}

// --- modal state ---
const viewingUser = ref<AdminUser | null>(null)
const editingUser = ref<AdminUser | null>(null)
const deletingUser = ref<AdminUser | null>(null)
const showFormModal = ref(false)

const openCreateModal = () => {
  editingUser.value = null
  showFormModal.value = true
}

const openEditModal = (u: AdminUser) => {
  editingUser.value = u
  showFormModal.value = true
}

const confirmDelete = (u: AdminUser) => {
  deletingUser.value = u
}

const handleSaved = () => {
  showFormModal.value = false
  fetchUsers(meta.value?.current_page || 1)
}

const handleDeleted = () => {
  deletingUser.value = null
  fetchUsers(meta.value?.current_page || 1)
}

const restoreUser = async (u: AdminUser) => {
  try {
    await api.patch(`/admin/users/${u.id}/restore`)
    Swal.fire({ icon: 'success', title: 'User restored', timer: 1500, showConfirmButton: false })
    fetchUsers(meta.value?.current_page || 1)
  } catch (e) {
    console.error(e)
    Swal.fire({ icon: 'error', title: 'Restore failed' })
  }
}

// --- display helpers ---
const roleLabel = (role: string) => ({
  super_admin: 'Super Admin',
  admin: 'Admin',
  user: 'User',
} as Record<string, string>)[role] ?? role

const roleBadge = (role: string) => ({
  super_admin: 'bg-purple-100 text-purple-700',
  admin: 'bg-blue-100 text-blue-700',
  user: 'bg-gray-100 text-gray-700',
} as Record<string, string>)[role] ?? 'bg-gray-100 text-gray-700'

const statusLabel = (status: string) => (status === 'active' ? 'Active' : 'Inactive')

const statusBadge = (status: string) =>
  status === 'active' ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700'

const formatDate = (date: string) => {
  if (!date) return '—'
  return new Date(date).toLocaleDateString('en-GB', {
    day: '2-digit', month: 'short', year: 'numeric',
  })
}

onMounted(() => fetchUsers())
</script>