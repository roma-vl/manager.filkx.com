<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'

const tasks = ref([])

onMounted(async () => {
  try {
    const { data } = await axios.get('/api/widgets/work/projects/me-tasks')
    tasks.value = data.tasks
  } catch (e) {
    console.error('Error loading own tasks:', e)
  }
})
</script>

<template>
  <div
    class="dark:bg-[#0e0f11] bg-gradient-to-br from-gray-900 to-indigo-900 text-white mt-6 rounded-2xl shadow-md transition-all duration-300 ease-in-outmx-auto p-2"
  >
    <div class="px-2 py-2 border-b dark:border-gray-700 flex items-center justify-between">
      <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Own Tasks</h2>
      <a
        href="/work/projects/tasks/me"
        class="text-sm text-gray-500 dark:text-gray-400 hover:underline"
      >
        View All
      </a>
    </div>

    <div v-if="tasks.length" class="overflow-x-auto">
      <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
        <thead>
          <tr class="text-xs text-gray-500 dark:text-gray-400 text-left">
            <th class="px-3 py-2">#</th>
            <th class="px-3 py-2">Project</th>
            <th class="px-3 py-2">Name</th>
            <th class="px-3 py-2">Status</th>
          </tr>
        </thead>
        <tbody class="text-sm divide-y divide-gray-100 dark:divide-gray-700">
          <tr v-for="task in tasks" :key="task.id">
            <td class="px-3 py-2">{{ task.id }}</td>
            <td class="px-3 py-2">
              <a :href="`/work/projects/${task.project_id}`" class="text-blue-600 hover:underline">
                {{ task.project_name }}
              </a>
            </td>
            <td class="px-3 py-2">
              <a :href="`/work/projects/tasks/${task.id}`" class="text-blue-600 hover:underline">
                {{ task.name }}
              </a>
            </td>
            <td class="px-3 py-2">{{ task.status }}</td>
          </tr>
        </tbody>
      </table>
    </div>

    <div v-else class="text-sm text-gray-500 dark:text-gray-400">No tasks found.</div>
  </div>
</template>
