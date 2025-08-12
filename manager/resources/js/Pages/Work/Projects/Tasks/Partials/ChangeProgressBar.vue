<script setup>
import axios from 'axios'
import { ref, watch, computed } from 'vue'

const props = defineProps({
  taskId: Number,
  progress: {
    type: Array,
    default: () => [],
  },
  currentProgress: Number,
})

const progress = ref(props.currentProgress ?? 0)
const isSubmitting = ref(false)
const isEditing = ref(false)
const error = ref(null)
const dragging = ref(false)

const onMouseUp = async () => {
  if (!dragging.value) return
  dragging.value = false
  isSubmitting.value = true
  error.value = null

  try {
    await axios.post(`/work/projects/tasks/${props.taskId}/progress`, {
      progress: progress.value,
    })
    isEditing.value = false
  } catch (e) {
    error.value = e.response?.data?.error ?? 'Unknown error'
  } finally {
    isSubmitting.value = false
  }
}

const progressIds = computed(() => props.progress.map(p => p.id))
const min = computed(() => Math.min(...progressIds.value))
const max = computed(() => Math.max(...progressIds.value))
const step = computed(() => {
  if (progressIds.value.length < 2) return 1
  return progressIds.value[1] - progressIds.value[0]
})

watch(
  () => props.currentProgress,
  val => {
    progress.value = val
  },
)

const startEdit = () => {
  if (!isEditing.value) {
    isEditing.value = true
  }
}
</script>

<template>
  <div class="w-full">
    <div v-if="isEditing">
      <input
        v-model="progress"
        type="range"
        :min="min"
        :max="max"
        :step="step"
        class="w-full h-2 bg-indigo-300 rounded-full appearance-none cursor-pointer transition"
        @mousedown="dragging = true"
        @mouseup="onMouseUp"
        @touchstart="dragging = true"
        @touchend="onMouseUp"
      />
      <div class="flex justify-between text-xs text-indigo-300 mt-1">
        <span v-for="point in props.progress" :key="point.id">{{ point.name }}</span>
      </div>
    </div>
    <div
      v-else
      class="relative w-full bg-indigo-900 rounded-full h-4 mt-2 overflow-hidden cursor-pointer group"
      @click="startEdit"
    >
      <div
        class="bg-indigo-500 h-4 rounded-full transition-all duration-500 ease-in-out"
        :style="{ width: ((progress - min) / (max - min)) * 100 + '%' }"
      />
      <div
        class="absolute inset-0 flex items-center justify-center text-xs font-semibold text-indigo-100 select-none group-hover:text-white transition"
      >
        {{ progress }}%
      </div>
    </div>

    <span v-if="isSubmitting" class="text-sm text-indigo-400 animate-pulse">Saving...</span>
    <span v-if="error" class="text-sm text-red-500">{{ error }}</span>
  </div>
</template>
