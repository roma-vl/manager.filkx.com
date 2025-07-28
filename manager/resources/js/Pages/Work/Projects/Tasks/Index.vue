<script setup>
  import { ref, reactive, computed } from 'vue'
  import AppLayout from '../../../../Layouts/AppLayout.vue'
  import SortIcon from '../../../../Components/Icons/SortIcon.vue'
  import Pagination from '@/Components/ui/Pagination.vue'
  import TaskFilters from '../../../../Components/TaskFilters.vue'
  import Breadcrumbs from '../../../../Components/ui/Breadcrumbs.vue'
  import RolesTabs from '../../../../Components/Work/Projects/Project/Roles/RolesTabs.vue'
  import TasksTabs from '../../../../Components/Work/Projects/TasksTabs.vue'
  import {
    formatPriority,
    formatStatus,
    formatType,
    priorityBadgeClass,
    typeBadgeClass,
  } from '../../../../Helpers/tasks.helper.js'
  import { statusBadgeClass } from '../../../../Helpers/helpers.js'
  const props = defineProps({
    project: Object,
    members: Object,
    filters: Object,
    tasks: Array,
    statuses: Array,
    type: Array,
    priority: Array,
    sort: String,
    direction: String,
    pagination: Object,
  })

  const text = ref(props.filters.text || '')
  const type = ref(props.filters.type || '')
  const status = ref(props.filters.status || '')
  const priority = ref(props.filters.priority || '')
  const author = ref(props.filters.author || '')
  const executor = ref(props.filters.executor || '')
  const roots = ref(props.filters.roots || false)
  const sort = ref(props.filters.sort || false)
  const direction = ref(props.filters.direction ?? 'asc')

  const tasks = ref(props.tasks || [])
  const pagination = reactive({ ...props.pagination })

  function toggleSort(field) {
    if (sort.value === field) {
      direction.value = direction.value === 'asc' ? 'desc' : 'asc'
    } else {
      sort.value = field
      direction.value = 'asc'
    }
    submitFilters(1)
  }

  function submitFilters(page = 1) {
    if (typeof page !== 'number') {
      page = 1
    }

    const query = new URLSearchParams({
      text: text.value,
      type: type.value,
      status: status.value,
      priority: priority.value,
      author: author.value,
      executor: executor.value,
      roots: roots.value ? 1 : '',
      page,
      sort: typeof sort.value === 'string' ? sort.value : 't.id',
      direction: direction.value,
    }).toString()

    window.location.href = `/work/projects/tasks?${query}`
  }

  function resetFilters() {
    text.value = ''
    type.value = ''
    status.value = ''
    priority.value = ''
    author.value = ''
    executor.value = ''
    roots.value = ''
    submitFilters()
  }

  function paginationLink(page) {
    const query = new URLSearchParams({
      text: text.value,
      type: type.value,
      status: status.value,
      priority: priority.value,
      author: author.value,
      executor: executor.value,
      roots: roots.value ? 1 : '',
      page,
      sort: typeof sort.value === 'string' ? sort.value : 't.id',
      direction: direction.value,
    }).toString()

    return `/work/projects/tasks?${query}`
  }
  function handleSubmit(updatedFilters) {
    text.value = updatedFilters.text
    type.value = updatedFilters.type
    status.value = updatedFilters.status
    priority.value = updatedFilters.priority
    author.value = updatedFilters.author
    executor.value = updatedFilters.executor
    roots.value = updatedFilters.roots
    submitFilters(1)
  }
</script>

<template>
  <AppLayout>
    <Breadcrumbs
      :items="[
        { label: 'Home', href: '/' },
        { label: 'Work', href: '/work' },
        { label: 'Projects', href: '/work/projects' },
        { label: 'Tasks' },
      ]"
    />

    <RolesTabs />
    <TaskFilters
      :filters="props.filters"
      :types="props.type"
      :statuses="props.statuses"
      :priorities="props.priority"
      :members="props.members"
      :project="props.project"
      @submit="handleSubmit"
      @reset="resetFilters"
    />
    <TasksTabs />

    <div
      class="overflow-auto shadow-indigo-800/40 bg-white dark:bg-gray-900 rounded-lg border border-gray-200 dark:border-indigo-800 shadow-sm"
      tabindex="0"
      aria-label="Tasks list table container"
    >
      <table
        class="min-w-full border-collapse border border-gray-300 dark:border-indigo-800 text-gray-800 dark:text-indigo-200"
        role="table"
      >
        <thead class="bg-gray-100 dark:bg-indigo-800 sticky top-0 z-10">
          <tr>
            <th
              @click="toggleSort('t.id')"
              class="border border-gray-300 dark:border-indigo-700 p-3 text-left text-sm font-semibold tracking-wide select-none cursor-pointer"
              scope="col"
            >
              ID
              <SortIcon :field="'t.id'" />
            </th>
            <th
              @click="toggleSort('t.date')"
              class="border border-gray-300 dark:border-indigo-700 p-3 text-left text-sm font-semibold tracking-wide select-none cursor-pointer"
              scope="col"
            >
              Date
              <SortIcon :field="'t.date'" />
            </th>
            <th
              class="border border-gray-300 dark:border-indigo-700 p-3 text-left text-sm font-semibold tracking-wide select-none cursor-pointer"
              scope="col"
            >
              Author
            </th>
            <th
              v-if="!project"
              class="border border-gray-300 dark:border-indigo-700 p-3 text-left text-sm font-semibold tracking-wide select-none cursor-pointer"
              scope="col"
            >
              Project
            </th>
            <th
              class="border border-gray-300 dark:border-indigo-700 p-3 text-left text-sm font-semibold tracking-wide select-none cursor-pointer"
              scope="col"
            >
              Name
            </th>
            <th
              class="border border-gray-300 dark:border-indigo-700 p-3 text-left text-sm font-semibold tracking-wide select-none cursor-pointer"
              scope="col"
            >
              Type
            </th>
            <th
              class="border border-gray-300 dark:border-indigo-700 p-3 text-left text-sm font-semibold tracking-wide select-none cursor-pointer"
              scope="col"
            >
              Priority
            </th>
            <th
              class="border border-gray-300 dark:border-indigo-700 p-3 text-left text-sm font-semibold tracking-wide select-none cursor-pointer"
              scope="col"
            >
              Executors
            </th>
            <th
              class="border border-gray-300 dark:border-indigo-700 p-3 text-left text-sm font-semibold tracking-wide select-none cursor-pointer"
              scope="col"
            >
              Plan Date
            </th>
            <th
              class="border border-gray-300 dark:border-indigo-700 p-3 text-left text-sm font-semibold tracking-wide select-none cursor-pointer"
              scope="col"
            >
              Status
            </th>
            <th
              class="border border-gray-300 dark:border-indigo-700 p-3 text-left text-sm font-semibold tracking-wide select-none cursor-pointer"
              scope="col"
            >
              Progress
            </th>
          </tr>
        </thead>
        <tbody>
          <tr
            v-for="task in tasks"
            :key="task.id"
            class="border-t border-indigo-700 hover:bg-indigo-900 transition-colors"
            tabindex="0"
          >
            <td class="border border-indigo-700 p-2 text-sm font-mono">{{ task.id }}</td>
            <td class="border border-indigo-700 p-2 text-sm">
              {{ new Date(task.date).toLocaleDateString() }}
            </td>
            <td class="border border-indigo-700 p-2 text-sm">{{ task.author_name }}</td>
            <td v-if="!project" class="border border-indigo-700 p-2 text-sm">
              <a
                :href="`/work/projects/${task.project_id}`"
                class="hover:text-indigo-300 transition-colors"
              >
                {{ task.project_name }}
              </a>
            </td>
            <td class="border border-indigo-700 p-2 text-sm">
              <span v-if="task.root" class="mr-1"> ➔ </span>
              <a
                :href="`/work/projects/tasks/${task.id}`"
                class="text-indigo-300 hover:underline transition-colors"
              >
                {{ task.name }}
              </a>
            </td>
            <td class="border border-indigo-700 p-2 text-sm">
              <span
                :class="[
                  'inline-block px-2 py-1 rounded text-xs font-semibold',
                  typeBadgeClass(task.type),
                ]"
              >
                {{ formatType(task.type) || 'NONE' }}
              </span>
            </td>

            <td class="border border-indigo-700 p-2 text-sm text-center">
              <span
                :class="[
                  'inline-block px-2 py-1 rounded text-xs font-semibold',
                  priorityBadgeClass(task.priority),
                ]"
              >
                {{ formatPriority(task.priority) || 'NONE' }}
              </span>
            </td>

            <td class="border border-indigo-700 p-2 text-sm gap-1">
              <span
                v-for="executor in task.executors"
                :key="executor.name"
                class="inline-block bg-indigo-700 text-indigo-100 px-2 py-0.5 rounded select-none"
              >
                {{ executor.name }}
              </span>
            </td>
            <td class="border border-indigo-700 p-2 text-sm">
              {{ task.plan_date ? new Date(task.plan_date).toLocaleDateString() : '' }}
            </td>

            <td class="border border-indigo-700 p-2 text-sm">
              <span
                :class="[
                  'inline-block px-2 py-1 rounded text-xs font-semibold',
                  statusBadgeClass(task.status),
                ]"
              >
                {{ formatStatus(task.status) || 'NONE' }}
              </span>
            </td>

            <td class="p-2 text-sm text-center">
              <div
                class="w-full bg-gray-200 dark:bg-indigo-900 rounded-full h-4 relative overflow-hidden"
              >
                <div
                  class="bg-indigo-600 h-4 rounded-full transition-all duration-500 ease-in-out"
                  :style="{ width: (task.progress ?? 0) + '%' }"
                ></div>
                <div
                  class="absolute inset-0 flex items-center justify-center text-xs font-semibold text-gray-800 dark:text-indigo-100 select-none"
                >
                  {{ task.progress ? task.progress + '%' : '0%' }}
                </div>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <Pagination :pagination="pagination" :linkBuilder="paginationLink" />
  </AppLayout>
</template>

<style scoped></style>
