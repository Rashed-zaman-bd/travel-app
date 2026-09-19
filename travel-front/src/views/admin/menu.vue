<template>
  <div class="p-6">
    <div class="flex items-center justify-between mb-6">
      <h1 class="text-xl font-semibold text-gray-800">Navigation Menu</h1>
      <button
        class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700"
        @click="openCreate()"
      >
        + Add Nav Item
      </button>
    </div>

    <div v-if="loading" class="text-sm text-gray-500">Loading...</div>

    
    <div v-else class="overflow-hidden rounded-lg border border-gray-200">
      <table class="w-full text-sm">
        <thead class="bg-gray-50 text-left text-gray-500">
          <tr>
            <th class="px-4 py-3">Title</th>
            <th class="px-4 py-3">URL</th>
            <th class="px-4 py-3">Order</th>
            <th class="px-4 py-3">Active</th>
            <th class="px-4 py-3">New Tab</th>
            <th class="px-4 py-3 text-right">Actions</th>
          </tr>
        </thead>
        <tbody>
          <template v-for="item in navItems" :key="item.id">
            <tr class="border-t border-gray-100">
              <td class="px-4 py-3 font-medium text-gray-800">{{ item.title }}</td>
              <td class="px-4 py-3 text-gray-500">{{ item.url || '—' }}</td>
              <td class="px-4 py-3">{{ item.order }}</td>
              <td class="px-4 py-3">
                <span :class="item.is_active ? 'text-green-600' : 'text-gray-400'">
                  {{ item.is_active ? 'Yes' : 'No' }}
                </span>
              </td>
              <td class="px-4 py-3">{{ item.open_new_tab ? 'Yes' : 'No' }}</td>
              <td class="px-4 py-3 text-right space-x-2">
                <button class="text-blue-600 hover:underline" @click="openCreate(item.id)">+ Child</button>
                <button class="text-blue-600 hover:underline" @click="openEdit(item)">Edit</button>
                <button class="text-red-600 hover:underline" @click="remove(item)">Delete</button>
              </td>
            </tr>

            <tr v-for="child in item.children" :key="child.id" class="border-t border-gray-50 bg-gray-50/50">
              <td class="px-4 py-3 pl-10 text-gray-700">↳ {{ child.title }}</td>
              <td class="px-4 py-3 text-gray-500">{{ child.url || '—' }}</td>
              <td class="px-4 py-3">{{ child.order }}</td>
              <td class="px-4 py-3">
                <span :class="child.is_active ? 'text-green-600' : 'text-gray-400'">
                  {{ child.is_active ? 'Yes' : 'No' }}
                </span>
              </td>
              <td class="px-4 py-3">{{ child.open_new_tab ? 'Yes' : 'No' }}</td>
              <td class="px-4 py-3 text-right space-x-2">
                <button class="text-blue-600 hover:underline" @click="openEdit(child)">Edit</button>
                <button class="text-red-600 hover:underline" @click="remove(child)">Delete</button>
              </td>
            </tr>
          </template>
        </tbody>
      </table>
    </div>

    <!-- Slide-over form -->
    <div v-if="formOpen" class="fixed inset-0 z-50 flex justify-end bg-black/30" @click.self="formOpen = false">
      <div class="h-full w-full max-w-md overflow-y-auto bg-white p-6 shadow-xl">
        <h2 class="mb-4 text-lg font-semibold text-gray-800">
          {{ form.id ? 'Edit Nav Item' : 'New Nav Item' }}
        </h2>

        <form class="space-y-4" @submit.prevent="save">
          <div>
            <label class="block text-sm font-medium text-gray-700">Title</label>
            <input v-model="form.title" type="text" required
              class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm" />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700">URL</label>
            <input v-model="form.url" type="text" placeholder="/flights or #"
              class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm" />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700">Parent</label>
            <select v-model="form.parent_id"
              class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
              <option :value="null">None (top-level)</option>
              <option v-for="p in topLevelItems" :key="p.id" :value="p.id" :disabled="p.id === form.id">
                {{ p.title }}
              </option>
            </select>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700">Order</label>
            <input v-model.number="form.order" type="number" min="0"
              class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm" />
          </div>

          <div class="flex items-center gap-2">
            <input v-model="form.is_active" type="checkbox" id="is_active" />
            <label for="is_active" class="text-sm text-gray-700">Active</label>
          </div>

          <div class="flex items-center gap-2">
            <input v-model="form.open_new_tab" type="checkbox" id="open_new_tab" />
            <label for="open_new_tab" class="text-sm text-gray-700">Open in new tab</label>
          </div>

          <p v-if="errorMsg" class="text-sm text-red-600">{{ errorMsg }}</p>

          <div class="flex justify-end gap-2 pt-2">
            <button type="button" class="rounded-lg px-4 py-2 text-sm text-gray-600 hover:bg-gray-100" @click="formOpen = false">
              Cancel
            </button>
            <button type="submit" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">
              Save
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, reactive } from 'vue'
import api from '@/services/api'

interface NavItem {
  id: number
  parent_id: number | null
  title: string
  url: string | null
  icon: string | null
  order: number
  is_active: boolean
  open_new_tab: boolean
  children: NavItem[]
}

interface NavItemListResponse {
  status: boolean
  message?: string
  data: NavItem[]
}

const navItems = ref<NavItem[]>([])
const loading = ref(true)
const formOpen = ref(false)
const errorMsg = ref('')

const topLevelItems = computed(() => navItems.value)

const form = reactive<{
  id: number | null
  parent_id: number | null
  title: string
  url: string
  icon: string
  order: number
  is_active: boolean
  open_new_tab: boolean
}>({
  id: null,
  parent_id: null,
  title: '',
  url: '',
  icon: '',
  order: 0,
  is_active: true,
  open_new_tab: false,
})

const resetForm = () => {
  form.id = null
  form.parent_id = null
  form.title = ''
  form.url = ''
  form.icon = ''
  form.order = 0
  form.is_active = true
  form.open_new_tab = false
  errorMsg.value = ''
}

const openCreate = (parentId: number | null = null) => {
  resetForm()
  form.parent_id = parentId
  formOpen.value = true
}

const openEdit = (item: NavItem) => {
  form.id = item.id
  form.parent_id = item.parent_id
  form.title = item.title
  form.url = item.url ?? ''
  form.icon = item.icon ?? ''
  form.order = item.order
  form.is_active = item.is_active
  form.open_new_tab = item.open_new_tab
  errorMsg.value = ''
  formOpen.value = true
}

const fetchNavItems = async () => {
  loading.value = true
  try {
    const response = await api.get('/admin/nav-items')

    // 1. Check if the backend returned Laravel's wrapped resource ({ data: [...] })
    if (response.data && Array.isArray(response.data.data)) {
      navItems.value = response.data.data
    } 
    // 2. Fallback check if backend returns a direct array ([...])
    else if (Array.isArray(response.data)) {
      navItems.value = response.data
    } 
    else {
      navItems.value = []
    }
  } catch (error) {
    console.error('Failed to load nav items:', error)
    navItems.value = []
  } finally {
    loading.value = false
  }
}

const save = async () => {
  errorMsg.value = ''
  try {
    const payload = {
      parent_id: form.parent_id,
      title: form.title,
      url: form.url || null,
      icon: form.icon || null,
      order: form.order,
      is_active: form.is_active,
      open_new_tab: form.open_new_tab,
    }

    if (form.id) {
      await api.put(`/admin/nav-items/${form.id}`, payload)
    } else {
      await api.post('/admin/nav-items', payload)
    }

    formOpen.value = false
    await fetchNavItems()
  } catch (error: any) {
    errorMsg.value = error?.response?.data?.message || 'Something went wrong.'
  }
}

const remove = async (item: NavItem) => {
  if (!confirm(`Delete "${item.title}"? ${item.children?.length ? 'Its children will become top-level items.' : ''}`)) return
  try {
    await api.delete(`/admin/nav-items/${item.id}`)
    await fetchNavItems()
  } catch (error) {
    console.error('Failed to delete nav item:', error)
  }
}

onMounted(fetchNavItems)
</script>