<script setup>
  import { ref, onMounted } from 'vue'

  const props = defineProps({
    options: {
      type: Object,
      required: true,
    },
    id: String,
    name: String,
    required: Boolean,
  })

  const model = defineModel({ type: String, required: true })
  const select = ref(null)

  onMounted(() => {
    if (select.value?.hasAttribute('autofocus')) {
      select.value.focus()
    }
  })

  defineExpose({ focus: () => select.value?.focus() })
</script>

<template>
  <select
    :id="props.id"
    ref="select"
    v-model="model"
    :name="name"
    :required="required"
    class="w-full rounded-md border border-gray-300 bg-white px-4 py-2 pr-8 text-gray-900 shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-gray-100 dark:border-gray-600 dark:focus:ring-indigo-400 dark:focus:border-indigo-400 transition duration-150 ease-in-out appearance-none cursor-pointer"
  >
    <option disabled value="">-- Select --</option>
    <option v-for="(label, value) in options" :key="value" :value="value">
      {{ label }}
    </option>
  </select>
</template>
