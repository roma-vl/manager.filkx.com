<script setup>
  import { computed } from 'vue'

  const props = defineProps({
    tasks: Array,
    statuses: Array,
  })

  const tasksByStatus = statusId => {
    return props.tasks.filter(task => task.status === statusId)
  }

  const formatType = type => {
    const map = {
      bug: '🐞',
      feature: '✨',
      task: '📋',
    }
    return map[type] ?? ''
  }

  const priorityClass = priority => {
    switch (priority) {
      case 'low':
        return 'bg-green-500'
      case 'medium':
        return 'bg-yellow-500'
      case 'high':
        return 'bg-red-500'
      default:
        return 'bg-gray-400'
    }
  }
</script>

<template>
  <div class="flex gap-6 overflow-x-auto pb-4">
    <div
      v-for="status in statuses"
      :key="status.id"
      class="w-80 shrink-0 bg-gray-900 rounded-lg shadow p-4"
    >
      <h2 class="text-sm font-bold text-white uppercase mb-3 tracking-wide">
        {{ status.name }}
      </h2>

      <div
        v-for="task in tasksByStatus(status.id)"
        :key="task.id"
        class="bg-gray-800 hover:bg-gray-700 transition rounded-lg p-4 mb-3"
      >
        <div class="flex justify-between items-center mb-2">
          <span class="text-sm font-semibold text-indigo-300 truncate">
            {{ formatType(task.type) }} {{ task.name }}
          </span>
          <span :class="['w-2 h-2 rounded-full', priorityClass(task.priority)]"></span>
        </div>
        <p class="text-xs text-gray-400 truncate">
          #{{ task.id }} • {{ task.project?.name ?? 'Без проєкту' }}
        </p>
        <div class="mt-2 flex items-center justify-between text-xs text-gray-400">
          <span>👤 {{ task.author?.name ?? 'Анонім' }}</span>
          <span>⏱ {{ task.estimate ?? '—' }}</span>
        </div>
      </div>
    </div>
  </div>
</template>
