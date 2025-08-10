<script setup>
  import { Head, Link } from '@inertiajs/inertia-vue3'
  import { computed, ref, watch } from 'vue'
  import AppLayout from '@/Layouts/AppLayout.vue'
  import { roleBadgeClass, statusBadgeClass } from '@/Helpers/helpers.js'
  import Breadcrumbs from '@/Components/ui/Breadcrumbs.vue'
  import PageMeta from '@/Components/Seo/PageMeta.vue'

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
      page: page,
    }).toString()

    return `/users?${query}`
  }

  const paginationRange = computed(() => {
    const current = props.pagination.currentPage
    const last = props.pagination.lastPage
    const delta = 2
    const range = []
    const rangeWithDots = []

    for (let i = Math.max(1, current - delta); i <= Math.min(last, current + delta); i++) {
      range.push(i)
    }

    if (range[0] > 2) {
      rangeWithDots.push(1)
      rangeWithDots.push('...')
    } else {
      for (let i = 1; i < range[0]; i++) {
        rangeWithDots.push(i)
      }
    }

    rangeWithDots.push(...range)

    if (range[range.length - 1] < last - 1) {
      rangeWithDots.push('...')
      rangeWithDots.push(last)
    } else {
      for (let i = range[range.length - 1] + 1; i <= last; i++) {
        rangeWithDots.push(i)
      }
    }

    return rangeWithDots
  })
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
        class="grid grid-cols-1 md:grid-cols-5 gap-4 bg-white p-4 shadow rounded"
        @submit.prevent="submitFilters"
      >
        <input v-model="name" placeholder="Name" class="form-input" />
        <input v-model="email" placeholder="Email" class="form-input" />
        <select v-model="role" class="form-select">
          <option value="">All Roles</option>
          <option value="ROLE_USER">User</option>
          <option value="ROLE_MODERATOR">Moderator</option>
          <option value="ROLE_ADMIN">Admin</option>
        </select>
        <select v-model="status" class="form-select">
          <option value="">All Statuses</option>
          <option value="wait">Wait</option>
          <option value="active">Active</option>
          <option value="blocked">Blocked</option>
        </select>
        <div class="flex gap-2">
          <button type="submit" class="btn btn-primary w-full">Filter</button>
          <button type="button" class="btn btn-outline w-full" @click="resetFilters">Reset</button>
        </div>
      </form>

      <!-- Users Table -->
      <div class="overflow-auto rounded shadow">
        <table class="table-auto w-full">
          <thead class="bg-gray-100 text-left">
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

          <tbody>
            <tr v-for="user in users" :key="user.id" class="hover:bg-gray-50">
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

      <!-- Pagination -->
      <div
        v-if="props.pagination.lastPage > 1"
        class="mt-4 flex justify-center space-x-1 items-center"
      >
        <span
          v-if="pagination.currentPage === 1"
          class="px-3 py-1 border rounded text-gray-400 cursor-not-allowed select-none"
        >
          ← Prev
        </span>
        <Link
          v-else
          :href="paginationLink(pagination.currentPage - 1)"
          class="px-3 py-1 border rounded hover:bg-gray-100"
        >
          ← Prev
        </Link>

        <template v-for="page in paginationRange" :key="page">
          <span v-if="page === '...'" class="px-3 py-1 text-gray-500">...</span>
          <Link
            v-else
            :href="paginationLink(page)"
            class="px-3 py-1 border rounded hover:bg-gray-100"
            :class="{ 'bg-blue-600 text-white': page === pagination.currentPage }"
          >
            {{ page }}
          </Link>
        </template>

        <span
          v-if="pagination.currentPage === pagination.lastPage"
          class="px-3 py-1 border rounded text-gray-400 cursor-not-allowed select-none"
        >
          Next →
        </span>
        <Link
          v-else
          :href="paginationLink(pagination.currentPage + 1)"
          class="px-3 py-1 border rounded hover:bg-gray-100"
        >
          Next →
        </Link>
      </div>
    </div>
  </AppLayout>
</template>

<style scoped>
  .form-input,
  .form-select {
    @apply border border-gray-300 rounded px-2 py-1 w-full;
  }
  .btn {
    @apply px-3 py-1 rounded;
  }
  .btn-primary {
    @apply bg-blue-600 text-white hover:bg-blue-700;
  }
  .btn-outline {
    @apply border border-gray-400 text-gray-700 hover:bg-gray-100;
  }
  .badge {
    @apply inline-block px-3 py-1 rounded-full text-sm font-semibold;
  }
</style>
