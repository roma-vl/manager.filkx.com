<script setup>
import {
  formatPriority,
  formatStatus,
  formatType,
  priorityBadgeClass,
  typeBadgeClass,
} from '../../Helpers/tasks.helper.js'
import { statusBadgeClass } from '../../Helpers/helpers.js'

const props = defineProps({
  children: Array,
  projectId: [String, Number],
})
</script>

<template>
  <table class="min-w-full text-indigo-200 rounded-lg mt-2" role="table">
    <thead class="bg-indigo-800 sticky top-0">
      <tr>
        <th
          class="rounded-t-xl p-3 text-left font-semibold tracking-wide select-none"
          scope="col"
          colspan="10"
        >
          Sub tasks
        </th>
      </tr>
    </thead>
    <thead class="bg-indigo-800 top-0">
      <tr>
        <th
          class="border-indigo-700 p-1 text-sm font-semibold tracking-wide select-none hover:bg-indigo-600"
          scope="col"
        >
          ID
        </th>
        <th
          class="border-indigo-700 p-1 text-sm font-semibold tracking-wide select-none hover:bg-indigo-600"
          scope="col"
        >
          Date
        </th>
        <th
          class="border-indigo-700 p-1 text-sm font-semibold tracking-wide select-none hover:bg-indigo-600"
          scope="col"
        >
          Project
        </th>
        <th
          class="border-indigo-700 p-1 text-sm font-semibold tracking-wide select-none hover:bg-indigo-600"
          scope="col"
        >
          Name
        </th>
        <th
          class="border-indigo-700 p-1 text-sm font-semibold tracking-wide select-none hover:bg-indigo-600"
          scope="col"
        >
          Type
        </th>
        <th
          class="border-indigo-700 p-1 text-sm font-semibold tracking-wide select-none hover:bg-indigo-600"
          scope="col"
        >
          Priority
        </th>
        <th
          class="border-indigo-700 p-1 text-sm font-semibold tracking-wide select-none hover:bg-indigo-600"
          scope="col"
        >
          Executors
        </th>
        <th
          class="border-indigo-700 p-1 text-sm font-semibold tracking-wide select-none hover:bg-indigo-600"
          scope="col"
        >
          Plan Date
        </th>
        <th
          class="border-indigo-700 p-1 text-sm font-semibold tracking-wide select-none hover:bg-indigo-600"
          scope="col"
        >
          Status
        </th>
        <th
          class="border-indigo-700 p-1 text-sm font-semibold tracking-wide select-none hover:bg-indigo-600"
          scope="col"
        >
          Progress
        </th>
      </tr>
    </thead>
    <tbody>
      <tr
        v-for="task in props.children"
        :key="task.id"
        class="hover:bg-indigo-900 transition-colors"
        tabindex="0"
      >
        <td class="p-2 text-sm font-mono">{{ task.id }}</td>
        <td class="p-2 text-sm">
          {{ new Date(task.date).toLocaleDateString() }}
        </td>
        <td class="p-2 text-sm">
          <a
            :href="`/work/projects/${task.project_id}`"
            class="hover:text-indigo-300 transition-colors"
          >
            {{ task.project_name }}
          </a>
        </td>
        <td class="p-2 text-sm">
          <span v-if="task.parent" class="mr-1">
            <i class="fas fa-angle-double-right" />
          </span>
          <a
            :href="`/work/projects/tasks/${task.id}`"
            class="text-indigo-300 hover:underline transition-colors"
          >
            {{ task.name }}
          </a>
        </td>
        <td class="p-2 text-sm">
          <span
            :class="[
              'inline-block px-2 py-1 rounded text-xs font-semibold',
              typeBadgeClass(task.type),
            ]"
          >
            {{ formatType(task.type) || 'NONE' }}
          </span>
        </td>

        <td class="p-2 text-sm text-center">
          <span
            :class="[
              'inline-block px-2 py-1 rounded text-xs font-semibold',
              priorityBadgeClass(task.priority),
            ]"
          >
            {{ formatPriority(task.priority) || 'NONE' }}
          </span>
        </td>

        <td class="p-2 text-sm gap-1">
          <span
            v-for="executor in task.executors"
            :key="executor.name"
            class="inline-block bg-indigo-700 text-indigo-100 px-2 py-0.5 rounded select-none"
          >
            {{ executor.name }}
          </span>
        </td>
        <td class="p-2 text-sm">
          {{ task.plan_date ? new Date(task.plan_date).toLocaleDateString() : '' }}
        </td>

        <td class="p-2 text-sm">
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
          <div class="w-full bg-indigo-900 rounded-full h-4 relative overflow-hidden">
            <div
              class="bg-indigo-500 h-4 rounded-full transition-all duration-500 ease-in-out"
              :style="{ width: (task.progress ?? 0) + '%' }"
            />
            <div
              class="absolute inset-0 flex items-center justify-center text-xs font-semibold text-indigo-100 select-none"
            >
              {{ task.progress ? task.progress + '%' : '0%' }}
            </div>
          </div>
        </td>
      </tr>
    </tbody>
  </table>
</template>
