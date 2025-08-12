<script setup>
  import { computed } from 'vue'

  const props = defineProps({
    field: String,
    sort: String,
    direction: String,
  })

  const isActive = computed(() => props.field === props.sort)
</script>

<template>
  <span v-if="isActive">
    <svg
      v-if="direction === 'asc'"
      xmlns="http://www.w3.org/2000/svg"
      class="inline w-3 h-3 ml-1"
      fill="none"
      viewBox="0 0 24 24"
      stroke="currentColor"
    >
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
    </svg>
    <svg
      v-else
      xmlns="http://www.w3.org/2000/svg"
      class="inline w-3 h-3 ml-1"
      fill="none"
      viewBox="0 0 24 24"
      stroke="currentColor"
    >
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
    </svg>
  </span>
</template>
