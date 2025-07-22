<script setup>
import { ref, computed } from 'vue';

const props = defineProps({
    filters: Object,
    members: Object,
    types: Array,
    statuses: Array,
    priorities: Array,
});

console.log(props, 'props')
const emit = defineEmits(['submit', 'reset']);

const text = ref(props.filters.text || '');
const type = ref(props.filters.type || '');
const status = ref(props.filters.status || '');
const priority = ref(props.filters.priority || '');
const author = ref(props.filters.author || '');
const executor = ref(props.filters.executor || '');
const roots = ref(props.filters.roots || false);

const groupedMembers = computed(() => {
    const groups = {};
    for (const member of props.members) {
        if (!groups[member.group]) groups[member.group] = [];
        groups[member.group].push(member);
    }
    return Object.entries(groups).map(([label, members]) => ({ label, members }));
});

function submit() {
    emit('submit', {
        text: text.value,
        type: type.value,
        status: status.value,
        priority: priority.value,
        author: author.value,
        executor: executor.value,
        roots: roots.value,
    });
}

function reset() {
    text.value = '';
    type.value = '';
    status.value = '';
    priority.value = '';
    author.value = '';
    executor.value = '';
    roots.value = false;
    emit('reset');
}
</script>

<template>
    <form
        @submit.prevent="submit"
        class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8 p-4 bg-gradient-to-br from-indigo-950 via-gray-900 to-[#0e0f11] rounded-lg shadow-lg shadow-indigo-900/40"
        aria-label="Task filters"
    >
        <!-- Input -->
        <input
            v-model="text"
            type="text"
            placeholder="Search..."
            class="w-full px-4 py-2 bg-gray-800 text-indigo-200 border border-indigo-800 rounded-md shadow-sm placeholder:text-indigo-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition"
            aria-label="Search text"
        />

        <!-- Selects -->
        <select
            v-model="type"
            class="select-dark"
            aria-label="Select type"
        >
            <option value="">All Types</option>
            <option v-for="item in types" :key="item.id" :value="item.id">
                {{ item.name }}
            </option>
        </select>

        <select
            v-model="status"
            class="select-dark"
            aria-label="Select status"
        >
            <option value="">All Statuses</option>
            <option v-for="item in statuses" :key="item.id" :value="item.id">
                {{ item.name }}
            </option>
        </select>

        <select
            v-model="priority"
            class="select-dark"
            aria-label="Select priority"
        >
            <option value="">All Priorities</option>
            <option v-for="item in priorities" :key="item.id" :value="item.id">
                {{ item.name }}
            </option>
        </select>

        <select
            v-model="author"
            class="select-dark"
            aria-label="Select author"
        >
            <option value="">Select Author...</option>
            <template v-for="(group, index) in groupedMembers" :key="index">
                <optgroup :label="group.label">
                    <option v-for="member in group.members" :key="member.id" :value="member.id">
                        {{ member.name }}
                    </option>
                </optgroup>
            </template>
        </select>

        <select
            v-model="executor"
            class="select-dark"
            aria-label="Select executor"
        >
            <option value="">Select Executor...</option>
            <template v-for="(group, index) in groupedMembers" :key="index">
                <optgroup :label="group.label">
                    <option v-for="member in group.members" :key="member.id" :value="member.id">
                        {{ member.name }}
                    </option>
                </optgroup>
            </template>
        </select>

        <!-- Checkbox -->
        <label class="flex items-center space-x-2 text-indigo-300">
            <input
                type="checkbox"
                v-model="roots"
                class="w-4 h-4 text-indigo-500 bg-gray-800 border border-indigo-700 rounded focus:ring-2 focus:ring-indigo-500 transition"
                aria-label="Show root only"
            />
            <span class="text-sm">Roots only</span>
        </label>

        <!-- Buttons -->
        <div class="flex gap-2 md:col-span-3">
            <button
                type="submit"
                class="w-full py-2 px-4 bg-indigo-600 hover:bg-indigo-700 text-white rounded-md shadow-md transition-colors focus:outline-none focus:ring-2 focus:ring-indigo-400 text-sm"
            >
                Filter
            </button>
            <button
                type="button"
                @click="reset"
                class="w-full py-2 px-4 border border-indigo-500 text-indigo-300 hover:bg-indigo-800 hover:text-white rounded-md transition-colors focus:outline-none focus:ring-2 focus:ring-indigo-400 text-sm"
            >
                Reset
            </button>
        </div>
    </form>
</template>

<style scoped>
.select-dark {
    @apply w-full px-4 py-2 bg-gray-800 text-indigo-200 border border-indigo-800 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition;
}
</style>
