<script setup>
  import { ref, computed } from 'vue'
  import { Head, Link } from '@inertiajs/inertia-vue3'
  import GroupsTabs from '@/Components/Work/Members/Groups/Tabs.vue'
  import AppLayout from '@/Layouts/AppLayout.vue'
  import { statusBadgeClass } from '../../../../Helpers/helpers.js'
  import Breadcrumbs from '@/Components/ui/Breadcrumbs.vue'
  import PageMeta from '@/Components/Seo/PageMeta.vue'
  import Pagination from "@/Components/ui/Pagination.vue";
  import {Check, RefreshCw} from "lucide-vue-next";

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

      if (typeof page !== 'number') {
          page = 1
      }
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

</script>

<template>
  <AppLayout>
    <PageMeta :title="`Members`" :description="`Page Members`" />
    <Breadcrumbs
      :items="[
        { label: 'Home', href: '/' },
        { label: 'Work', href: '/work' },
        { label: 'Members' },
      ]"
    />

    <GroupsTabs />

    <form
      class="mt-5 bg-gradient-to-br from-gray-900 to-[#0e0f11] p-4 rounded-2xl shadow-md mb-8 grid grid-cols-1 md:grid-cols-5 gap-4 text-white/80"
      @submit.prevent="submitFilters"
    >
      <input v-model="name" placeholder="Name" class="filter-select" />
      <input v-model="email" placeholder="Email" class="filter-select" />
      <select v-model="group" class="filter-select">
        <option value="">All Groups</option>
        <option v-for="g in groups" :key="g.id" :value="g.id">{{ g.name }}</option>
      </select>
      <select v-model="status" class="filter-select">
        <option value="">All Statuses</option>
        <option v-for="s in statuses" :key="s.id" :value="s.id">{{ s.name }}</option>
      </select>
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

    <!-- Table -->
    <div
      class="overflow-x-auto rounded-xl shadow-md bg-gray-900/80 backdrop-blur border border-gray-700 text-white"
    >
      <table class="min-w-full divide-y divide-gray-800">
        <thead class="bg-gray-800 text-left text-xs font-semibold text-white/70">
          <tr>
            <th class="th-sort" @click="toggleSort('name')">
              Name <span v-if="sort === 'name'">{{ directionIcon }}</span>
            </th>
            <th class="th-sort" @click="toggleSort('email')">
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
      <Pagination :pagination="pagination" :link-builder="paginationLink" />
  </AppLayout>
</template>

<style scoped>
.filter-select {
    @apply px-3 py-2 text-sm rounded-md border bg-white dark:bg-gray-800 dark:text-white border-gray-300 dark:border-gray-700 focus:ring-indigo-500 focus:border-indigo-500;
}
</style>
