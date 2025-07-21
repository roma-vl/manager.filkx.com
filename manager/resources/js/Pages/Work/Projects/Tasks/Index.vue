<script setup>
import {ref, reactive, computed} from 'vue';
import AppLayout from '../../../../Layouts/AppLayout.vue';
import {Link} from "@inertiajs/inertia-vue3";

const props = defineProps({
    project: Object,
    members: Object,
    filters: Object,
    tasks: Array,
    statuses: Array,
    type: Array,
    priority: Array,
    sort: String,
    direction: String,
    pagination: Object,
});



const text = ref(props.filters.text || '');
const type = ref(props.filters.type || '');
const status = ref(props.filters.status || '');
const priority = ref(props.filters.priority || '');
const author = ref(props.filters.author || '');
const executor = ref(props.filters.executor || '');
const roots = ref(props.filters.roots || false);

const tasks = ref(props.tasks || []);

const pagination = reactive({ ...props.pagination });
const sort = ref(props.filters.sort ?? '');
const direction = ref(props.filters.direction ?? 'asc');

function toggleSort(field) {
    if (sort.value === field) {
        direction.value = direction.value === 'asc' ? 'desc' : 'asc'
    } else {
        sort.value = field
        direction.value = 'asc'
    }

    submitFilters()
}

const groupedMembers = computed(() => {
    const groups = {}
    for (const member of props.members) {
        if (!groups[member.group]) groups[member.group] = []
        groups[member.group].push(member)
    }
    return Object.entries(groups).map(([label, members]) => ({ label, members }))
})

function submitFilters(page = 1) {
    if (typeof page !== 'number') {
        page = 1;
    }

    const query = new URLSearchParams({
        text: text.value,
        type: type.value,
        status: status.value,
        priority: priority.value,
        author: author.value,
        executor: executor.value,
        roots: roots.value ? 1 : '',
        page,
    }).toString();

    window.location.href = `/work/tasks?${query}`;
}


function resetFilters() {
    text.value = ''
    type.value = ''
    status.value = ''
    priority.value = ''
    author.value= ''
    executor.value= ''
    roots.value = ''
    submitFilters()
}

function formatStatus(status) {
    const map = {
        new: 'Нова',
        in_progress: 'В процесі',
        done: 'Завершена',
        failed: 'Провалена',
        help: 'Допомога',
    };

    return (map[status] ?? status).toUpperCase();
}

function formatPriority(priority) {
    switch(priority) {
        case 1: return 'LOW';
        case 2: return 'NORMAL';
        case 3: return 'FEATURE';
        case 4: return 'HIGH';
        case 5: return 'CRITICAL';
        case 6: return 'BLOCKER';
        default: return 'UNKNOWN';
    }
}

function priorityBadgeClass(priority) {
    switch(priority) {
        case 1: return 'bg-green-600 text-green-100';
        case 2: return 'bg-blue-600 text-blue-100';
        case 3: return 'bg-yellow-500 text-yellow-900';
        case 4: return 'bg-orange-600 text-orange-100';
        case 5: return 'bg-red-700 text-red-100';
        case 6: return 'bg-purple-700 text-purple-100';
        default: return 'bg-gray-600 text-gray-100';
    }
}


function formatType(type) {
    const map = {
        none: '',
        bug: 'Помилка',
        feature: 'Функціонал',
        task: 'Задача',
    };
    return (map[type] ?? type).toUpperCase();
}

function typeBadgeClass(type) {
    switch(type) {
        case 'none': return 'bg-gray-600 text-gray-100';
        case 'bug': return 'bg-red-600 text-red-100';
        case 'feature': return 'bg-green-600 text-green-100';
        case 'task': return 'bg-blue-600 text-blue-100';
        default: return 'bg-gray-600 text-gray-100';
    }
}


function statusBadgeClass(status) {
    switch(status) {
        case 'new': return 'bg-gray-500 text-gray-100';
        case 'in_progress': return 'bg-blue-600 text-blue-100';
        case 'done': return 'bg-green-600 text-green-100';
        case 'failed': return 'bg-red-600 text-red-100';
        case 'help': return 'bg-yellow-500 text-yellow-900';
        default: return 'bg-gray-600 text-gray-100';
    }
}


function paginationLink(page) {
    const query = new URLSearchParams({
        text: text.value ,
        type: type.value ,
        status: status.value ,
        priority: priority.value ,
        author: author.value ,
        executor: executor.value,
        roots: roots.value ? 1 : '',
        page,
    }).toString()

    return `/work/tasks?${query}`
}

const paginationRange = computed(() => {
    const current = props.pagination.currentPage
    const last = props.pagination.lastPage
    const delta = 2
    const range = []

    for (let i = Math.max(1, current - delta); i <= Math.min(last, current + delta); i++) {
        range.push(i)
    }

    const result = []
    if (range[0] > 1) {
        result.push(1)
        if (range[0] > 2) result.push('...')
    }

    result.push(...range)

    if (range[range.length - 1] < last) {
        if (range[range.length - 1] < last - 1) result.push('...')
        result.push(last)
    }

    return result
})

</script>

<template>
    <AppLayout>
        <div
            class="max-w-7xl mx-auto p-6 rounded-lg
             bg-gradient-to-br from-indigo-900 via-gray-900 to-[#0e0f11]
             shadow-md shadow-indigo-900/40
             text-indigo-200
             transition-all duration-300 ease-in-out"
            role="main"
        >
            <!-- Breadcrumbs -->
            <nav aria-label="Breadcrumb" class="mb-6">
                <ol class="flex flex-wrap gap-x-2 text-white/80 text-sm" role="list">
                    <li><a href="/" class="hover:text-indigo-300 transition-colors">Home</a> /</li>
                    <li><a href="/work" class="hover:text-indigo-300 transition-colors">Work</a> /</li>
                    <li v-if="project">
                        <a
                            :href="`/work/projects/${project.id}`"
                            class="hover:text-indigo-300 transition-colors"
                        >{{ project.name }}</a>
                        /
                    </li>
                    <li aria-current="page" class="text-white font-semibold">Tasks</li>
                </ol>
            </nav>

            <!-- Tabs -->
            <component :is="project ? 'ProjectTabs' : 'WorkTabs'" :project="project" />

            <!-- Add Task Button -->
            <div v-if="project" class="mb-6">
                <a
                    :href="`/work/projects/${project.id}/tasks/create`"
                    class="inline-block rounded p-3 bg-indigo-800 hover:bg-indigo-700
                 shadow-lg shadow-indigo-700/40 text-white transition-colors"
                >Add Task</a
                >
            </div>

            <!-- Filters Form -->
            <form
                @submit.prevent="submitFilters"
                class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8"
                aria-label="Task Filters"
            >
                <input
                    v-model="text"
                    type="text"
                    placeholder="Search..."
                    class="input-dark"
                    aria-label="Filter by text"
                />
                <!-- Type select -->
                <select v-model="type" class="input-dark" aria-label="Filter by type">
                    <option value="">All Types</option>
                    <option v-for="item in props.type" :key="item.id" :value="item.id">
                        {{ item.name }}
                    </option>
                </select>

                <!-- Status select -->
                <select v-model="status" class="input-dark" aria-label="Filter by status">
                    <option value="">All Statuses</option>
                    <option v-for="item in props.statuses" :key="item.id" :value="item.id">
                        {{ item.name }}
                    </option>
                </select>

                <!-- Priority select -->
                <select v-model="priority" class="input-dark" aria-label="Filter by priority">
                    <option value="">All Priorities</option>
                    <option v-for="item in props.priority" :key="item.id" :value="item.id">
                        {{ item.name }}
                    </option>
                </select>

                <select v-model="author" class="form-select w-full">
                    <option value="">Select Author ...</option>
                    <template v-for="(group, index) in groupedMembers" :key="index">
                        <optgroup :label="group.label">
                            <option v-for="member in group.members" :key="member.id" :value="member.id">
                                {{ member.name }}
                            </option>
                        </optgroup>
                    </template>
                </select>

                <select v-model="executor" class="form-select w-full">
                    <option value="">Select Executor...</option>
                    <template v-for="(group, index) in groupedMembers" :key="index">
                        <optgroup :label="group.label">
                            <option v-for="member in group.members" :key="member.id" :value="member.id">
                                {{ member.name }}
                            </option>
                        </optgroup>
                    </template>
                </select>

                <label
                    class="flex items-center space-x-2 text-indigo-300 cursor-pointer select-none"
                >
                    <input
                        type="checkbox"
                        v-model="roots"
                        class="accent-indigo-500 focus:ring-indigo-400"
                        aria-checked="roots"
                    />
                    <span>Roots only</span>
                </label>

                <div class="flex gap-2">
                    <button type="submit" class="btn btn-primary w-full">Filter</button>
                    <button type="button" @click="resetFilters" class="btn btn-outline w-full">Reset</button>
                </div>
            </form>

            <!-- Tasks Table -->
            <div
                class="overflow-auto rounded-lg shadow-lg shadow-indigo-800/40"
                tabindex="0"
                aria-label="Tasks list table container"
            >
                <table
                    class="min-w-full border-collapse border border-indigo-800 text-indigo-200"
                    role="table"
                >
                    <thead class="bg-indigo-800 sticky top-0 z-10">
                    <tr>
                        <th
                            class="border border-indigo-700 p-3 text-left text-sm font-semibold tracking-wide select-none"
                            scope="col"
                        >
                            ID
                        </th>
                        <th
                            class="border border-indigo-700 p-3 text-left text-sm font-semibold tracking-wide select-none"
                            scope="col"
                        >
                            Date
                        </th>
                        <th
                            class="border border-indigo-700 p-3 text-left text-sm font-semibold tracking-wide select-none"
                            scope="col"
                        >
                            Author
                        </th>
                        <th
                            v-if="!project"
                            class="border border-indigo-700 p-3 text-left text-sm font-semibold tracking-wide select-none"
                            scope="col"
                        >
                            Project
                        </th>
                        <th
                            class="border border-indigo-700 p-3 text-left text-sm font-semibold tracking-wide select-none"
                            scope="col"
                        >
                            Name
                        </th>
                        <th
                            class="border border-indigo-700 p-3 text-left text-sm font-semibold tracking-wide select-none"
                            scope="col"
                        >
                            Type
                        </th>
                        <th
                            class="border border-indigo-700 p-3 text-left text-sm font-semibold tracking-wide select-none"
                            scope="col"
                        >
                            Priority
                        </th>
                        <th
                            class="border border-indigo-700 p-3 text-left text-sm font-semibold tracking-wide select-none"
                            scope="col"
                        >
                            Executors
                        </th>
                        <th
                            class="border border-indigo-700 p-3 text-left text-sm font-semibold tracking-wide select-none"
                            scope="col"
                        >
                            Plan Date
                        </th>
                        <th
                            class="border border-indigo-700 p-3 text-left text-sm font-semibold tracking-wide select-none"
                            scope="col"
                        >
                            Status
                        </th>
                        <th
                            class="border border-indigo-700 p-3 text-left text-sm font-semibold tracking-wide select-none"
                            scope="col"
                        >
                            Progress
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr
                        v-for="task in tasks"
                        :key="task.id"
                        class="border-t border-indigo-700 hover:bg-indigo-900 transition-colors"
                        tabindex="0"
                    >

                        <td class="border border-indigo-700 p-2 text-sm font-mono">{{ task.id }}</td>
                        <td class="border border-indigo-700 p-2 text-sm">
                            {{ new Date(task.date).toLocaleDateString() }}
                        </td>
                        <td class="border border-indigo-700 p-2 text-sm">{{ task.author_name }}</td>
                        <td v-if="!project" class="border border-indigo-700 p-2 text-sm">
                            <a
                                :href="`/work/projects/${task.project_id}`"
                                class="hover:text-indigo-300 transition-colors"
                            >
                                {{ task.project_name }}
                            </a>
                        </td>
                        <td class="border border-indigo-700 p-2 text-sm">
                            <span v-if="task.parent" class="mr-1">
                              <i class="fas fa-angle-double-right"></i>
                            </span>
                            <a
                                :href="`/work/tasks/${task.id}`"
                                class="text-indigo-300 hover:underline transition-colors"
                            >
                                {{ task.name }}
                            </a>
                        </td>
                        <td class="border border-indigo-700 p-2 text-sm">
                          <span :class="['inline-block px-2 py-1 rounded text-xs font-semibold', typeBadgeClass(task.type)]">
                            {{ formatType(task.type) || 'NONE' }}
                          </span>
                        </td>

                        <td class="border border-indigo-700 p-2 text-sm text-center">
                          <span :class="['inline-block px-2 py-1 rounded text-xs font-semibold', priorityBadgeClass(task.priority)]">
                            {{ formatPriority(task.priority) || 'NONE' }}
                          </span>
                        </td>

                        <td class="border border-indigo-700 p-2 text-sm  gap-1">
                            <span
                                v-for="executor in task.executors"
                                :key="executor.name"
                                class="inline-block bg-indigo-700 text-indigo-100 px-2 py-0.5 rounded select-none"
                            >
                              {{ executor.name }}
                            </span>
                        </td>
                        <td class="border border-indigo-700 p-2 text-sm">
                            {{ task.plan_date ? new Date(task.plan_date).toLocaleDateString() : '' }}
                        </td>

                        <td class="border border-indigo-700 p-2 text-sm">
                          <span :class="['inline-block px-2 py-1 rounded text-xs font-semibold', statusBadgeClass(task.status)]">
                            {{ formatStatus(task.status) || 'NONE' }}
                          </span>
                        </td>

                        <td class="border border-indigo-700 p-2 text-sm text-center">
                            <div class="w-full bg-indigo-900 rounded-full h-4 relative overflow-hidden">
                                <div
                                    class="bg-indigo-500 h-4 rounded-full transition-all duration-500 ease-in-out"
                                    :style="{ width: (task.progress ?? 0) + '%' }"
                                ></div>
                                <div class="absolute inset-0 flex items-center justify-center text-xs font-semibold text-indigo-100 select-none">
                                    {{ task.progress ? task.progress + '%' : '0%' }}
                                </div>
                            </div>
                        </td>

                    </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div v-if="pagination.lastPage > 1" class="mt-6 flex justify-center space-x-2">
                <Link
                    v-if="pagination.currentPage > 1"
                    :href="paginationLink(pagination.currentPage - 1)"
                    class="pagination-link"
                >← Prev</Link
                >

                <template v-for="page in paginationRange" :key="page">
                    <span v-if="page === '...'" class="px-3 py-1 text-gray-500">…</span>
                    <Link
                        v-else
                        :href="paginationLink(page)"
                        class="pagination-link"
                        :class="{ 'bg-indigo-700 text-white': page === pagination.currentPage }"
                    >
                        {{ page }}
                    </Link>
                </template>

                <Link
                    v-if="pagination.currentPage < pagination.lastPage"
                    :href="paginationLink(pagination.currentPage + 1)"
                    class="pagination-link"
                >Next →</Link
                >
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
.input-dark {
    @apply bg-gray-900 text-indigo-200 placeholder-indigo-400 rounded p-3
    shadow-sm shadow-indigo-900/50 border border-indigo-800
    focus:outline-none focus:ring-2 focus:ring-indigo-500
    transition-all duration-300 ease-in-out;
    scrollbar-width: thin;
    scrollbar-color: #4c51bf #1a202c;
}

.input-dark::-webkit-scrollbar {
    width: 8px;
    height: 8px;
}
.input-dark::-webkit-scrollbar-track {
    background: #1a202c;
    border-radius: 8px;
}
.input-dark::-webkit-scrollbar-thumb {
    background-color: #4c51bf;
    border-radius: 8px;
    border: 2px solid #1a202c;
}

.btn-primary {
    @apply bg-indigo-700 hover:bg-indigo-600 text-white font-semibold rounded
    px-6 py-3 shadow-md shadow-indigo-700/60 transition-all duration-300 ease-in-out;
}

.btn-primary:disabled {
    @apply opacity-50 cursor-not-allowed;
}

.btn-secondary {
    @apply bg-gray-800 hover:bg-gray-700 text-indigo-300 font-semibold rounded
    px-6 py-3 shadow-md shadow-indigo-900/40 transition-all duration-300 ease-in-out;
}

.btn-secondary:disabled {
    @apply opacity-50 cursor-not-allowed;
}

.btn-pagination {
    @apply bg-indigo-700 hover:bg-indigo-600 text-white font-semibold px-4 py-2
    shadow-md shadow-indigo-700/60 transition-all duration-300 ease-in-out;
}

.btn-pagination:disabled {
    @apply opacity-50 cursor-not-allowed;
}

/* Custom scrollbar for table container */
div[tabindex='0']::-webkit-scrollbar {
    height: 8px;
    width: 8px;
}

div[tabindex='0']::-webkit-scrollbar-track {
    background: #0e0f11;
    border-radius: 8px;
}

div[tabindex='0']::-webkit-scrollbar-thumb {
    background: #4c51bf;
    border-radius: 8px;
    border: 2px solid #0e0f11;
}
div[tabindex='0']::-webkit-scrollbar-thumb:hover {
    background: #6366f1;
}
</style>
