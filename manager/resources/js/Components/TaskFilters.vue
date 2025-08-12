<script setup>
import { ref, computed } from 'vue'
import { Search, RefreshCw, Check } from 'lucide-vue-next'

const props = defineProps({
  filters: Object,
  members: Object,
  types: Array,
  statuses: Array,
  priorities: Array,
})

const emit = defineEmits(['submit', 'reset'])

const text = ref(props.filters.text || '')
const type = ref(props.filters.type || '')
const status = ref(props.filters.status || '')
const priority = ref(props.filters.priority || '')
const author = ref(props.filters.author || '')
const executor = ref(props.filters.executor || '')
const roots = ref(props.filters.roots || false)

const groupedMembers = computed(() => {
  const groups = {}
  for (const member of props.members) {
    if (!groups[member.group]) groups[member.group] = []
    groups[member.group].push(member)
  }
  return Object.entries(groups).map(([label, members]) => ({ label, members }))
})

function submit() {
  emit('submit', {
    text: text.value,
    type: type.value,
    status: status.value,
    priority: priority.value,
    author: author.value,
    executor: executor.value,
    roots: roots.value,
  })
}

function reset() {
  text.value = ''
  type.value = ''
  status.value = ''
  priority.value = ''
  author.value = ''
  executor.value = ''
  roots.value = false
  emit('reset')
}
</script>

<template>
  <form
    class="flex flex-wrap items-center gap-3 p-4 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg shadow-sm"
    @submit.prevent="submit"
  >
    <!-- Text Search -->
    <div class="relative">
      <Search class="absolute top-2.5 left-3 w-4 h-4 text-gray-400" />
      <input
        v-model="text"
        type="text"
        class="w-64 md:w-80 lg:w-96 pl-9 pr-3 py-2 text-sm rounded-md bg-white dark:bg-gray-800 dark:text-white border-gray-300 dark:border-gray-700 focus:ring-indigo-500 focus:border-indigo-500"
        placeholder="Пошук..."
      />
    </div>

    <!-- Type -->
    <select v-model="type" class="filter-select border-none">
      <option value="">Тип</option>
      <option v-for="item in types" :key="item.id" :value="item.id">{{ item.name }}</option>
    </select>

    <!-- Status -->
    <select v-model="status" class="filter-select border-none">
      <option value="">Статус</option>
      <option v-for="item in statuses" :key="item.id" :value="item.id">{{ item.name }}</option>
    </select>

    <!-- Priority -->
    <select v-model="priority" class="filter-select border-none">
      <option value="">Пріоритет</option>
      <option v-for="item in priorities" :key="item.id" :value="item.id">{{ item.name }}</option>
    </select>

    <!-- Author -->
    <select v-model="author" class="filter-select border-none">
      <option value="">Автор</option>
      <optgroup v-for="(group, i) in groupedMembers" :key="'a' + i" :label="group.label">
        <option v-for="member in group.members" :key="member.id" :value="member.id">
          {{ member.name }}
        </option>
      </optgroup>
    </select>

    <!-- Executor -->
    <select v-model="executor" class="filter-select border-none">
      <option value="">Виконавець</option>
      <optgroup v-for="(group, i) in groupedMembers" :key="'e' + i" :label="group.label">
        <option v-for="member in group.members" :key="member.id" :value="member.id">
          {{ member.name }}
        </option>
      </optgroup>
    </select>

    <!-- Checkbox: Only Roots -->
    <label
      class="inline-flex items-center text-sm text-gray-600 dark:text-gray-300 cursor-pointer border-none"
    >
      <input
        v-model="roots"
        type="checkbox"
        class="w-4 h-4 text-indigo-600 border-gray-300 dark:border-gray-600 rounded focus:ring-indigo-500"
      />
      <span class="ml-2">Тільки кореневі</span>
    </label>

    <!-- Buttons -->
    <div class="flex items-center gap-2 ml-auto">
      <button
        type="button"
        class="border-none flex items-center gap-1 px-3 py-1.5 text-sm border rounded-md border-gray-300 dark:border-gray-600 text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 transition"
        @click="reset"
      >
        <RefreshCw class="w-4 h-4" /> Скинути
      </button>

      <button
        type="submit"
        class="border-none flex items-center gap-1 px-4 py-1.5 text-sm rounded-md bg-indigo-600 hover:bg-indigo-700 text-white transition"
      >
        <Check class="w-4 h-4" /> Застосувати
      </button>
    </div>
  </form>
</template>

<style scoped>
  .filter-select {
    @apply px-3 py-2 text-sm rounded-md border bg-white dark:bg-gray-800 dark:text-white border-gray-300 dark:border-gray-700 focus:ring-indigo-500 focus:border-indigo-500;
  }
</style>
