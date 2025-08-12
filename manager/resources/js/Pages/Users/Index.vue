<script setup>
import { Link } from '@inertiajs/inertia-vue3'
import { ref, watch } from 'vue'
import AppLayout from '@/Layouts/AppLayout.vue'
import { roleBadgeClass, statusBadgeClass } from '@/Helpers/helpers.js'
import Breadcrumbs from '@/Components/ui/Breadcrumbs.vue'
import PageMeta from '@/Components/Seo/PageMeta.vue'
import Pagination from '@/Components/ui/Pagination.vue'
import {Check, RefreshCw} from 'lucide-vue-next'

const props = defineProps({
  users: Array,
  pagination: Object,
  filters: {
    type: Object,
    default: () => ({ name: '', email: '', role: '', status: '' }),
  },
  sort: String,
  direction: String,
})

const name = ref(props.filters.name || '')
const email = ref(props.filters.email || '')
const role = ref(props.filters.role || '')
const status = ref(props.filters.status || '')
const sort = ref(props.sort || 'date')
const direction = ref(props.direction || 'desc')

function toggleSort(field) {
  if (sort.value === field) {
    direction.value = direction.value === 'asc' ? 'desc' : 'asc'
  } else {
    sort.value = field
    direction.value = 'asc'
  }

  submitFilters()
}

function submitFilters(page = 1) {
  if (typeof page !== 'number') {
    page = 1
  }
  const query = new URLSearchParams({
    name: name.value,
    email: email.value,
    role: role.value,
    status: status.value,
    sort: sort.value,
    direction: direction.value,
    page: page,
  }).toString()

  window.location.href = `/users?${query}`
}

function resetFilters() {
  name.value = ''
  email.value = ''
  role.value = ''
  status.value = ''
  submitFilters()
}

watch(role, () => submitFilters())
watch(status, () => submitFilters())

function paginationLink(page) {
  const query = new URLSearchParams({
    name: name.value,
    email: email.value,
    role: role.value,
    status: status.value,
    page,
  }).toString()

  return `/users?${query}`
}

</script>

<template>
  <AppLayout>
    <PageMeta
      title="Користувачі"
      description="Сторінка управління користувачами системи"
      keywords="користувачі, управління, адміністрування"
      image="/images/og-users.jpg"
    />
    <Breadcrumbs :items="[{ label: 'Home', href: '/' }, { label: 'Users' }]" />
    <div class="space-y-4">
      <div class="flex justify-between items-center">
        <h1 class="text-2xl font-bold">Users</h1>

        <Link :href="'/users/create'" class="btn btn-primary">Create</Link>
      </div>

      <!-- Filters -->
      <form
        class="mt-5 bg-gradient-to-br from-gray-900 to-[#0e0f11] p-4 rounded-2xl shadow-md mb-8 grid grid-cols-1 md:grid-cols-5 gap-4 text-white/80" @submit.prevent="submitFilters"
      >
        <input v-model="name" placeholder="Name" class=" filter-select" />
        <input v-model="email" placeholder="Email" class="filter-select" />
        <select v-model="role" class="filter-select">
          <option value="">All Roles</option>
          <option value="ROLE_USER">User</option>
          <option value="ROLE_MODERATOR">Moderator</option>
          <option value="ROLE_ADMIN">Admin</option>
        </select>
        <select v-model="status" class="filter-select">
          <option value="">All Statuses</option>
          <option value="wait">Wait</option>
          <option value="active">Active</option>
          <option value="blocked">Blocked</option>
        </select>
        <!-- Buttons -->
        <div class="flex items-center gap-2 ml-auto">
          <button
            type="button"
            class="border-none flex items-center gap-1 px-3 py-1.5 text-sm border rounded-md border-gray-300 dark:border-gray-600 text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 transition"
            @click="resetFilters"
          >
            <RefreshCw class="w-4 h-4" /> Скинути
          </button>

          <button
            type="submit"
            class="border-none flex items-center gap-1 px-4 py-1.5 text-sm rounded-md bg-indigo-600 hover:bg-indigo-700 text-white transition"
          >
            <Check class="w-4 h-4" /> Застосувати
          </button>
        </div>
      </form>

      <!-- Users Table -->
      <div
        class="overflow-x-auto rounded-xl shadow-md bg-gray-900/80 backdrop-blur border border-gray-700 text-white"
      >
        <table class="min-w-full divide-y divide-gray-800">
          <thead class="bg-gray-800 text-left text-xs font-semibold text-white/70">
            <tr>
              <th class="p-2 cursor-pointer" @click="toggleSort('date')">
                Date
                <span v-if="sort === 'date'">{{ direction === 'asc' ? '↑' : '↓' }}</span>
              </th>
              <th class="p-2 cursor-pointer" @click="toggleSort('name')">
                Name
                <span v-if="sort === 'name'">{{ direction === 'asc' ? '↑' : '↓' }}</span>
              </th>
              <th class="p-2 cursor-pointer" @click="toggleSort('email')">
                Email
                <span v-if="sort === 'email'">{{ direction === 'asc' ? '↑' : '↓' }}</span>
              </th>
              <th class="p-2 cursor-pointer" @click="toggleSort('role')">
                Role
                <span v-if="sort === 'role'">{{ direction === 'asc' ? '↑' : '↓' }}</span>
              </th>
              <th class="p-2 cursor-pointer" @click="toggleSort('status')">
                Status
                <span v-if="sort === 'status'">{{ direction === 'asc' ? '↑' : '↓' }}</span>
              </th>
            </tr>
          </thead>

          <tbody class="divide-y divide-gray-800 text-sm">
            <tr v-for="user in users" :key="user.id" class="hover:bg-gray-800/70 transition duration-200">
              <td class="p-2">{{ user.date }}</td>
              <td class="p-2">
                <Link :href="`/users/${user.id}`" class="text-blue-600 hover:underline">
                  {{ user.name }}
                </Link>
              </td>
              <td class="p-2">{{ user.email }}</td>
              <td class="p-2">
                <span :class="roleBadgeClass(user.role)" class="badge">{{ user.role }}</span>
              </td>
              <td class="p-2">
                <span :class="statusBadgeClass(user.status)" class="badge">{{ user.status }}</span>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      <Pagination :pagination="pagination" :link-builder="paginationLink" />
    </div>
  </AppLayout>
</template>

<style scoped>
.filter-select {
    @apply px-3 py-2 text-sm rounded-md border bg-white dark:bg-gray-800 dark:text-white border-gray-300 dark:border-gray-700 focus:ring-indigo-500 focus:border-indigo-500;
}
.badge {
    @apply inline-block px-2  rounded-lg text-sm font-semibold;
}
</style>
