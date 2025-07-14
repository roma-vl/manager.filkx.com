<script setup>
defineProps({
  value: {
    type: String,
    default: '',
  },
});
</script>

<template>
  <label class="block text-sm font-medium text-gray-700 pt-2 pb-2">
    <span v-if="value">{{ value }}</span>
    <span v-else><slot /></span>
  </label>
</template>
