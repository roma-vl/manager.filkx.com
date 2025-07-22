<script setup>
import { ref, computed } from 'vue'

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
        @submit.prevent="submit"
        class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3 mb-8 p-4 rounded-md shadow-md
          bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-700"
        aria-label="Фільтри задач"
    >
        <!-- Пошук -->
        <input
            v-model="text"
            type="text"
            placeholder="Пошук..."
            class="input-style"
            aria-label="Пошук тексту"
        />

        <!-- Тип задачі -->
        <select v-model="type" class="select-style" aria-label="Тип">
            <option value="">Всі типи</option>
            <option v-for="item in types" :key="item.id" :value="item.id">{{ item.name }}</option>
        </select>

        <!-- Статус -->
        <select v-model="status" class="select-style" aria-label="Статус">
            <option value="">Всі статуси</option>
            <option v-for="item in statuses" :key="item.id" :value="item.id">{{ item.name }}</option>
        </select>

        <!-- Пріоритет -->
        <select v-model="priority" class="select-style" aria-label="Пріоритет">
            <option value="">Всі пріоритети</option>
            <option v-for="item in priorities" :key="item.id" :value="item.id">{{ item.name }}</option>
        </select>

        <!-- Автор -->
        <select v-model="author" class="select-style" aria-label="Автор">
            <option value="">Автор...</option>
            <template v-for="(group, index) in groupedMembers" :key="index">
                <optgroup :label="group.label">
                    <option v-for="member in group.members" :key="member.id" :value="member.id">
                        {{ member.name }}
                    </option>
                </optgroup>
            </template>
        </select>

        <!-- Виконавець -->
        <select v-model="executor" class="select-style" aria-label="Виконавець">
            <option value="">Виконавець...</option>
            <template v-for="(group, index) in groupedMembers" :key="index">
                <optgroup :label="group.label">
                    <option v-for="member in group.members" :key="member.id" :value="member.id">
                        {{ member.name }}
                    </option>
                </optgroup>
            </template>
        </select>

        <!-- Чекбокс -->
        <label class="flex items-center space-x-2 text-sm text-gray-700 dark:text-gray-300 col-span-full">
            <input
                type="checkbox"
                v-model="roots"
                class="w-4 h-4 text-indigo-600 dark:bg-gray-800 dark:border-gray-600"
            />
            <span>Тільки кореневі</span>
        </label>

        <!-- Кнопки -->
        <div class="flex gap-2 col-span-full mt-2">
            <button
                type="submit"
                class="w-full py-1.5 px-3 text-sm rounded-md bg-indigo-600 hover:bg-indigo-700 text-white transition"
            >
                Застосувати
            </button>
            <button
                type="button"
                @click="reset"
                class="w-full py-1.5 px-3 text-sm rounded-md border border-indigo-400 text-indigo-600 dark:text-indigo-300 hover:bg-indigo-100 dark:hover:bg-gray-800 transition"
            >
                Скинути
            </button>
        </div>
    </form>
</template>

<style scoped>
.input-style {
    @apply w-full px-3 py-1.5 text-sm bg-white text-gray-800 border border-gray-300 rounded-md shadow-sm
    placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500
    dark:bg-gray-800 dark:text-gray-100 dark:border-gray-700 dark:placeholder-gray-500;
}

.select-style {
    @apply w-full px-3 py-1.5 text-sm bg-white text-gray-800 border border-gray-300 rounded-md shadow-sm
    focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500
    dark:bg-gray-800 dark:text-gray-100 dark:border-gray-700;
}
</style>
