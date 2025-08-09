<script setup>
import { ref, computed } from 'vue'
import { Head, Link } from '@inertiajs/inertia-vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import RolesTabs from '@/Components/Work/Projects/Project/Roles/RolesTabs.vue'
import { statusBadgeClass } from '@/Helpers/helpers.js'
import Breadcrumbs from '@/Components/ui/Breadcrumbs.vue'
import PageMeta from "@/Components/Seo/PageMeta.vue";

const props = defineProps({
  projects: Array,
  filters: Object,
  statuses: Array,
  sort: String,
  direction: String,
  pagination: Object,
})

const name = ref(props.filters.name || '')
const status = ref(props.filters.status || '')
const sort = ref(props.sort || 'name')
const direction = ref(props.direction || 'asc')

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
    status: status.value,
    sort: sort.value,
    direction: direction.value,
    page: page,
  }).toString()
  window.location.href = `/work/projects?${query}`
}

function resetFilters() {
  name.value = ''
  status.value = ''
  submitFilters()
}

function paginationLink(page) {
  const query = new URLSearchParams({
    name: name.value,
    status: status.value,
    sort: sort.value,
    direction: direction.value,
    page,
  }).toString()
  return `/work/projects?${query}`
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
    <Head title="Projects" />
      <PageMeta
          :title="`Projects`"
          :description="`Page Projects`"
      />
    <Breadcrumbs
      :items="[
        { label: 'Home', href: '/' },
        { label: 'Work', href: '/work' },
        { label: 'Projects' },
      ]"
    />

    <RolesTabs />

    <div class="mb-6">
      <Link
        href="/work/projects/create"
        class="bg-indigo-700 hover:bg-indigo-600 text-white px-4 py-2 rounded shadow transition-all"
      >
        ➕ Add Project
      </Link>
    </div>

    <form
      class="bg-gradient-to-br from-gray-900 to-indigo-900 text-white/90 p-4 rounded-xl shadow-md grid grid-cols-1 md:grid-cols-4 gap-4"
      @submit.prevent="submitFilters()"
    >
      <input
        v-model="name"
        placeholder="🔍 Project name..."
        class="bg-gray-800 border border-indigo-700 rounded px-3 py-2 text-white/80 focus:outline-none focus:ring-2 focus:ring-indigo-600"
      />

      <select
        v-model="status"
        class="bg-gray-800 border border-indigo-700 rounded px-3 py-2 text-white/80 focus:outline-none focus:ring-2 focus:ring-indigo-600"
      >
        <option value="">All Statuses</option>
        <option v-for="s in statuses" :key="s.id" :value="s.id">{{ s.name }}</option>
      </select>

      <div class="flex gap-2 col-span-1 md:col-span-2">
        <button
          type="submit"
          class="bg-indigo-600 hover:bg-indigo-500 text-white px-4 py-2 rounded w-full transition"
        >
          🔎 Filter
        </button>
        <button
          type="button"
          class="border border-indigo-600 text-white px-4 py-2 rounded w-full hover:bg-indigo-700 transition"
          @click="resetFilters"
        >
          ♻️ Reset
        </button>
      </div>
    </form>

    <div class="overflow-x-auto bg-gray-900 rounded-xl shadow-inner mt-6">
      <table class="min-w-full divide-y divide-indigo-800 text-white/80 text-sm">
        <thead class="bg-indigo-950 text-indigo-300 uppercase text-xs tracking-wider">
          <tr>
            <th
              class="px-6 py-3 cursor-pointer hover:text-indigo-400 transition"
              @click="toggleSort('name')"
            >
              Name <span v-if="sort === 'name'">{{ direction === 'asc' ? '↑' : '↓' }}</span>
            </th>
            <th
              class="px-6 py-3 cursor-pointer hover:text-indigo-400 transition"
              @click="toggleSort('status')"
            >
              Status <span v-if="sort === 'status'">{{ direction === 'asc' ? '↑' : '↓' }}</span>
            </th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-800">
          <tr v-for="project in projects" :key="project.id" class="hover:bg-indigo-950 transition">
            <td class="px-6 py-4 font-medium text-indigo-200">
              <Link :href="`/work/projects/${project.id}`">{{ project.name }}</Link>
            </td>
            <td class="px-6 py-4">
              <span
                class="inline-block px-3 py-1 rounded-full text-xs font-semibold"
                :class="statusBadgeClass(project.status)"
              >
                {{ project.status }}
              </span>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <div v-if="pagination.lastPage > 1" class="mt-8 flex justify-center gap-1 text-white/70">
      <Link
        v-if="pagination.currentPage > 1"
        :href="paginationLink(pagination.currentPage - 1)"
        class="px-3 py-1 border border-indigo-600 rounded hover:bg-indigo-800 transition"
      >
        ← Prev
      </Link>

      <template v-for="page in paginationRange" :key="page">
        <span v-if="page === '...'" class="px-3 py-1">…</span>
        <Link
          v-else
          :href="paginationLink(page)"
          class="px-3 py-1 border border-indigo-600 rounded hover:bg-indigo-800 transition"
          :class="{ 'bg-indigo-700 text-white': page === pagination.currentPage }"
        >
          {{ page }}
        </Link>
      </template>

      <Link
        v-if="pagination.currentPage < pagination.lastPage"
        :href="paginationLink(pagination.currentPage + 1)"
        class="px-3 py-1 border border-indigo-600 rounded hover:bg-indigo-800 transition"
      >
        Next →
      </Link>
    </div>
  </AppLayout>
</template>

<style scoped>
  /* Кастомний скрол */
  ::-webkit-scrollbar {
    width: 8px;
  }
  ::-webkit-scrollbar-track {
    background: #1f2937; /* gray-800 */
  }
  ::-webkit-scrollbar-thumb {
    background-color: #4f46e5; /* indigo-600 */
    border-radius: 4px;
  }
</style>
