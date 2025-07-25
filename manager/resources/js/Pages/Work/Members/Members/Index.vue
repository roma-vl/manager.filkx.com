<script setup>
  import { ref, computed } from 'vue'
  import { Head, Link } from '@inertiajs/inertia-vue3'
  import GroupsTabs from '@/Components/Work/Members/Groups/Tabs.vue'
  import AppLayout from '@/Layouts/AppLayout.vue'
  import { statusBadgeClass } from '../../../../Helpers/helpers.js'

  const props = defineProps({
    members: Array,
    filters: Object,
    groups: Array,
    statuses: Array,
    sort: String,
    direction: String,
    pagination: Object,
  })

  const name = ref(props.filters.name || '')
  const email = ref(props.filters.email || '')
  const group = ref(props.filters.group || '')
  const status = ref(props.filters.status || '')
  const sort = ref(props.sort || 'name')
  const direction = ref(props.direction || 'asc')

  const directionIcon = computed(() => (direction.value === 'asc' ? '↑' : '↓'))

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
      group: group.value,
      status: status.value,
      sort: sort.value,
      direction: direction.value,
      page,
    }).toString()

    window.location.href = `/work/members?${query}`
  }

  function resetFilters() {
    name.value = ''
    email.value = ''
    group.value = ''
    status.value = ''
    submitFilters()
  }

  function paginationLink(page) {
    const query = new URLSearchParams({
      name: name.value,
      email: email.value,
      group: group.value,
      status: status.value,
      sort: sort.value,
      direction: direction.value,
      page,
    }).toString()

    return `/work/members?${query}`
  }

  const paginationRange = computed(() => {
    const current = props.pagination.currentPage
    const last = props.pagination.lastPage
    const delta = 2
    const range = []

    for (let i = Math.max(1, current - delta); i <= Math.min(last, current + delta); i++) {
      range.push(i)
    }

    const result = []
    if (range[0] > 1) {
      result.push(1)
      if (range[0] > 2) result.push('...')
    }

    result.push(...range)

    if (range[range.length - 1] < last) {
      if (range[range.length - 1] < last - 1) result.push('...')
      result.push(last)
    }

    return result
  })
</script>

<template>
  <AppLayout>
    <Head title="Members" />

    <!-- Breadcrumb -->
    <nav class="text-sm text-white/60 mb-6" aria-label="Breadcrumb">
      <ol class="flex space-x-2">
        <li><Link href="/" class="hover:text-indigo-300">Home</Link><span>/</span></li>
        <li><Link href="/work" class="hover:text-indigo-300">Work</Link><span>/</span></li>
        <li class="text-white font-semibold">Members</li>
      </ol>
    </nav>

    <GroupsTabs />

    <!-- Filters -->
    <form
      @submit.prevent="submitFilters"
      class="bg-gradient-to-br from-gray-900 to-[#0e0f11] p-4 rounded-2xl shadow-md mb-8 grid grid-cols-1 md:grid-cols-5 gap-4 text-white/80"
    >
      <input v-model="name" placeholder="Name" class="input-dark" />
      <input v-model="email" placeholder="Email" class="input-dark" />
      <select v-model="group" class="input-dark">
        <option value="">All Groups</option>
        <option v-for="g in groups" :key="g.id" :value="g.id">{{ g.name }}</option>
      </select>
      <select v-model="status" class="input-dark">
        <option value="">All Statuses</option>
        <option v-for="s in statuses" :key="s.id" :value="s.id">{{ s.name }}</option>
      </select>
      <div class="flex gap-2">
        <button type="submit" class="btn-primary-dark w-full">Filter</button>
        <button type="button" @click="resetFilters" class="btn-outline-dark w-full">Reset</button>
      </div>
    </form>

    <!-- Table -->
    <div
      class="overflow-x-auto rounded-xl shadow-md bg-gray-900/80 backdrop-blur border border-gray-700 text-white"
    >
      <table class="min-w-full divide-y divide-gray-800">
        <thead class="bg-gray-800 text-left text-xs font-semibold text-white/70">
          <tr>
            <th @click="toggleSort('name')" class="th-sort">
              Name <span v-if="sort === 'name'">{{ directionIcon }}</span>
            </th>
            <th @click="toggleSort('email')" class="th-sort">
              Email <span v-if="sort === 'email'">{{ directionIcon }}</span>
            </th>
            <th class="px-6 py-3">Group</th>
            <th class="px-6 py-3 text-center">Projects</th>
            <th class="px-6 py-3">Status</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-800 text-sm">
          <tr
            v-for="member in members"
            :key="member.id"
            class="hover:bg-gray-800/70 transition duration-200"
          >
            <td class="px-6 py-4 font-medium text-indigo-300">
              <Link :href="`/work/members/${member.id}`">{{ member.name }}</Link>
            </td>
            <td class="px-6 py-4">{{ member.email }}</td>
            <td class="px-6 py-4">{{ member.group }}</td>
            <td class="px-6 py-4 text-center">{{ member.memberships_count }}</td>
            <td class="p-2">
              <span
                class="inline-block px-3 py-1 rounded-full text-xs font-semibold"
                :class="statusBadgeClass(member.status)"
              >
                {{ member.status }}
              </span>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Pagination -->
    <div v-if="pagination.lastPage > 1" class="mt-6 flex justify-center space-x-2">
      <Link
        v-if="pagination.currentPage > 1"
        :href="paginationLink(pagination.currentPage - 1)"
        class="pagination-link"
        >← Prev</Link
      >

      <template v-for="page in paginationRange" :key="page">
        <span v-if="page === '...'" class="px-3 py-1 text-gray-500">…</span>
        <Link
          v-else
          :href="paginationLink(page)"
          class="pagination-link"
          :class="{ 'bg-indigo-700 text-white': page === pagination.currentPage }"
        >
          {{ page }}
        </Link>
      </template>

      <Link
        v-if="pagination.currentPage < pagination.lastPage"
        :href="paginationLink(pagination.currentPage + 1)"
        class="pagination-link"
        >Next →</Link
      >
    </div>
  </AppLayout>
</template>

<style scoped>
  .input-dark {
    @apply bg-gray-800 border border-gray-700 rounded px-3 py-2 text-white placeholder-white/50 focus:outline-none focus:ring-2 focus:ring-indigo-500;
  }
  .btn-primary-dark {
    @apply bg-indigo-700 hover:bg-indigo-600 text-white font-semibold rounded px-4 py-2 transition-all;
  }
  .btn-outline-dark {
    @apply border border-white/30 text-white/70 hover:bg-white/10 font-medium rounded px-4 py-2 transition-all;
  }
  .th-sort {
    @apply px-6 py-3 cursor-pointer hover:text-indigo-300 transition-all;
  }
  .pagination-link {
    @apply px-3 py-1 border border-gray-700 rounded text-white hover:bg-indigo-600 hover:text-white transition-all;
  }
  ::-webkit-scrollbar {
    height: 8px;
    width: 8px;
  }
  ::-webkit-scrollbar-thumb {
    background: #4b5563;
    border-radius: 4px;
  }
</style>
