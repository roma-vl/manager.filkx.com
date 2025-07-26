<script setup>
  import axios from 'axios'
  import { ref, watch } from 'vue'
  import { formatStatus, statusBadgeClass } from '../../../../../Helpers/tasks.helper.js'

  const props = defineProps({
    taskId: Number,
    currentStatus: String,
    statuses: {
      type: Array,
      default: () => [],
    },
  })

  const selectedStatus = ref(props.currentStatus)
  console.log(selectedStatus.value)

  const dropdownOpen = ref(false)
  const isSubmitting = ref(false)
  const error = ref(null)

  watch(
    () => props.currentStatus,
    newVal => {
      selectedStatus.value = newVal
    }
  )

  const toggleDropdown = () => {
    dropdownOpen.value = !dropdownOpen.value
  }

  const setStatus = async statusId => {
    if (statusId === selectedStatus.value) {
      dropdownOpen.value = false
      return
    }

    isSubmitting.value = true
    error.value = null

    try {
      await axios.post(`/work/projects/tasks/${props.taskId}/status`, {
        status: statusId,
      })
      selectedStatus.value = statusId
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
      @click="toggleDropdown"
      :class="[
        'px-2 py-1 rounded text-xs font-semibold flex items-center gap-1',
        statusBadgeClass(selectedStatus),
      ]"
    >
      {{ formatStatus(selectedStatus) }}
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
      class="absolute z-10 mt-1 w-40 bg-white dark:bg-gray-900 border dark:border-gray-700 rounded shadow-md"
    >
      <ul class="py-1">
        <li v-for="status in statuses" :key="status.id">
          <button
            @click="setStatus(status.id)"
            class="w-full text-left px-3 py-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-800"
            :class="{
              'font-semibold': status.id === selectedStatus.value,
            }"
          >
            {{ formatStatus(status.name) }}
          </button>
        </li>
      </ul>
    </div>

    <span v-if="isSubmitting" class="text-sm text-indigo-500 ml-2 animate-pulse">Saving...</span>
    <span v-if="error" class="text-sm text-red-500 ml-2">{{ error }}</span>
  </div>
</template>
