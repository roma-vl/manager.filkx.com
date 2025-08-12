<script setup>
  import axios from 'axios'
  import { ref, watch } from 'vue'
  import { formatPriority, priorityBadgeClass } from '@/Helpers/tasks.helper.js'

  const props = defineProps({
    taskId: Number,
    currentPriority: String,
    priorities: {
      type: Array,
      default: () => [],
    },
  })

  const selectedPriority = ref(props.currentPriority)
  const dropdownOpen = ref(false)
  const isSubmitting = ref(false)
  const error = ref(null)

  watch(
    () => props.currentPriority,
    newVal => {
      selectedPriority.value = newVal
    }
  )

  const toggleDropdown = () => {
    dropdownOpen.value = !dropdownOpen.value
  }

  const setPriority = async priorityId => {
    if (priorityId === selectedPriority.value) {
      dropdownOpen.value = false
      return
    }

    isSubmitting.value = true
    error.value = null

    try {
      await axios.post(`/work/projects/tasks/${props.taskId}/priority`, {
        priority: priorityId,
      })
      selectedPriority.value = priorityId
      dropdownOpen.value = false
    } catch (e) {
      error.value = e.response?.data?.error ?? 'Unknown error'
    } finally {
      isSubmitting.value = false
    }
  }
</script>

<template>
  <div class="relative inline-block text-left">
    <button
      :class="[
        'px-2 py-1 rounded text-xs font-semibold flex items-center gap-1',
        priorityBadgeClass(selectedPriority),
      ]"
      @click="toggleDropdown"
    >
      {{ formatPriority(selectedPriority) }}
      <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
        <path
          fill-rule="evenodd"
          d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414L10 13.414 5.293 8.707a1 1 0 010-1.414z"
          clip-rule="evenodd"
        />
      </svg>
    </button>

    <div
      v-if="dropdownOpen"
      class="absolute z-10 mt-1 w-48 bg-white dark:bg-gray-900 border dark:border-gray-700 rounded shadow-md"
    >
      <ul class="py-1">
        <li v-for="priority in priorities" :key="priority.id">
          <button
            class="w-full text-left px-3 py-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-800"
            :class="{ 'font-semibold': priority.id === selectedPriority }"
            @click="setPriority(priority.id)"
          >
            {{ priority.name }}
          </button>
        </li>
      </ul>
    </div>

    <span v-if="isSubmitting" class="text-sm text-indigo-500 ml-2 animate-pulse">Saving...</span>
    <span v-if="error" class="text-sm text-red-500 ml-2">{{ error }}</span>
  </div>
</template>
