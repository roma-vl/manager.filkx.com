<script setup>
  import axios from 'axios'
  import { ref, watch } from 'vue'
  import {
    formatStatus,
    formatType,
    statusBadgeClass,
    typeBadgeClass,
  } from '../../../../../Helpers/tasks.helper.js'

  const props = defineProps({
    taskId: Number,
    currentType: String,
    types: {
      type: Array,
      default: () => [],
    },
  })

  const selectedType = ref(props.currentType)
  const dropdownOpen = ref(false)
  const isSubmitting = ref(false)
  const error = ref(null)

  watch(
    () => props.currentType,
    newVal => {
      selectedType.value = newVal
    }
  )

  const toggleDropdown = () => {
    dropdownOpen.value = !dropdownOpen.value
  }

  const setType = async typeId => {
    if (typeId === selectedType.value) {
      dropdownOpen.value = false
      return
    }

    isSubmitting.value = true
    error.value = null

    try {
      await axios.post(`/work/projects/tasks/${props.taskId}/type`, {
        type: typeId,
      })
      selectedType.value = typeId
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
        typeBadgeClass(selectedType),
      ]"
    >
      {{ formatType(selectedType) }}
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
        <li v-for="type in types" :key="type.id">
          <button
            @click="setType(type.id)"
            class="w-full text-left px-3 py-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-800"
            :class="{ 'font-semibold': type.id === selectedType }"
          >
            {{ type.name }}
          </button>
        </li>
      </ul>
    </div>

    <span v-if="isSubmitting" class="text-sm text-indigo-500 ml-2 animate-pulse">Saving...</span>
    <span v-if="error" class="text-sm text-red-500 ml-2">{{ error }}</span>
  </div>
</template>
