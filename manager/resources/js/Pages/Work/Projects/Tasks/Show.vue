<script setup>
import { useForm, router, Link } from '@inertiajs/inertia-vue3'
import AppLayout from '../../../../Layouts/AppLayout.vue'
import {onBeforeUnmount, onMounted, ref} from 'vue'
import Breadcrumbs from "../../../../Components/ui/Breadcrumbs.vue";
import {
    formatPriority,
    formatStatus,
    formatType,
    priorityBadgeClass,
    typeBadgeClass
} from "../../../../Helpers/tasks.helper.js";
import {statusBadgeClass} from "../../../../Helpers/helpers.js";
import axios from "axios";
const openDropdown = ref(false);

function toggleDropdown() {
    openDropdown.value = !openDropdown.value;
}

function closeDropdown(event) {
    // Якщо клік був поза меню — закриваємо
    if (!event.target.closest('.dropdown-container')) {
        openDropdown.value = false;
    }
}

onMounted(() => {
    document.addEventListener('click', closeDropdown);
});

onBeforeUnmount(() => {
    document.removeEventListener('click', closeDropdown);
});

// Props від Inertia
const props = defineProps({
    task: Object,
    project: Object,
    member: Object,
    children: Array,
    feed: Object,
    csrf_token: String,
})

console.log(props,'props')

// Простий метод підтвердження дії перед формою
function confirmAndSubmit(url) {
    if (!window.confirm('Are you sure?')) return
    axios.post(url)
}

// Форматування дати (приклад)
function formatDate(date) {
    return new Date(date).toLocaleDateString()
}

// Заглушки функцій для типів, статусів і пріоритетів — заміни на реальні
function workProjectsTaskType(type) {
    const map = { bug: 'Bug', feature: 'Feature', none: '' }
    return map[type] || type
}
function workProjectsTaskPriority(priority) {
    const map = { low: 'Low', medium: 'Medium', high: 'High' }
    return map[priority] || priority
}
function workProjectsTaskStatus(status) {
    const map = { new: 'New', in_progress: 'In Progress', done: 'Done' }
    return map[status] || status
}
function workProjectsTaskProgress(progress) {
    return progress + '%'
}
const revoke = async (memberId) => {
    try {
        await axios.post(route('work.projects.tasks.revoke', { id: taskId }), {
            member_id: memberId,
        });
        // оновити стан, видалити з візуального списку і т.п.
    } catch (e) {
        console.error('Помилка при відкликанні виконавця', e.response?.data?.error);
    }
}
const form = useForm({});

function revokeExecutor(memberId) {
    if (!confirm('Are you sure?')) return;

    form.post(`/work/projects/tasks/${props.task.id}/revoke/${memberId}`, {
        preserveScroll: true,
    });
}


</script>

<template>
    <AppLayout>
        <Breadcrumbs :items="[
          { label: 'Home', href: '/' },
          { label: 'Work', href: '/work' },
          { label: 'Projects', href: '/work/projects' },
          { label: project.name, href: `/work/projects/${project.id}` },
          { label: 'Tasks', href: `/work/projects/${project.id}/tasks` },
          { label: task.name }
        ]" />

        <!-- Заголовок -->
        <h1 class="mt-0 mb-3 font-xl">
            <template v-if="task.parent">
                <Link :href="`/work/projects/tasks/${task.parent.id}`">{{ task.parent.name }}</Link> /
            </template>
            {{ task.name }}
        </h1>

        <!-- Controls -->
        <div class="flex flex-wrap items-center gap-2 mb-6">
            <template v-if="task.hasExecutor && task.hasExecutor(member.id)">
                <template v-if="task.isNew">
                    <button
                        class="bg-green-600 hover:bg-green-700 text-white font-medium px-2 py-1 rounded transition"
                        @click="confirmAndSubmit(`/work/projects/tasks/start/${task.id}`, 'start')"
                    >
                        Start
                    </button>
                </template>
            </template>
            <template v-else>
                <button
                    class="bg-green-600 hover:bg-green-700 text-white font-medium px-2 py-1 rounded transition"
                    @click="confirmAndSubmit(`/work/projects/tasks/take/${task.id}`, 'take')"
                >
                    Take
                </button>

                <template v-if="task.isNew">
                    <button
                        class="bg-green-600 hover:bg-green-700 text-white font-medium px-2 py-1 rounded transition"
                        @click="confirmAndSubmit(`/work/projects/tasks/take_and_start/${task.id}`, 'take-and-start')"
                    >
                        Take & Start
                    </button>
                </template>
            </template>

            <Link
                :href="`/work/projects/tasks/${task.id}/assign`"
                class="bg-indigo-600 hover:bg-indigo-700 text-white font-medium px-2 py-1 rounded transition"
            >
                Assign Executors
            </Link>

            <Link
                :href="`/work/projects/${project.id}/tasks/create?parent=${task.id}`"
                class="bg-indigo-600 hover:bg-indigo-700 text-white font-medium px-2 py-1 rounded transition"
            >
                Add SubTasks
            </Link>

            <Link
                :href="`/work/projects/tasks/${task.id}/edit`"
                class="bg-indigo-600 hover:bg-indigo-700 text-white font-medium px-2 py-1 rounded transition"
            >
                Edit
            </Link>

            <!-- Dropdown -->
            <div class="relative dropdown-container">
                <button
                    class="bg-indigo-600 hover:bg-indigo-700 text-white font-medium px-2 py-1 rounded transition flex items-center gap-1"
                    @click.stop="toggleDropdown"
                >
                    More
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M19 9l-7 7-7-7" />
                    </svg>
                </button>

                <div
                    v-if="openDropdown"
                    class="absolute z-50 mt-2 w-48 bg-white border border-gray-200 rounded shadow-lg"
                >
                    <Link
                        class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                        :href="`/work/projects/tasks/${task.id}/move`"
                    >
                        Move To Project…
                    </Link>
                    <Link
                        class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                        :href="`/work/projects/tasks/${task.id}/child`"
                    >
                        Set As Child Of…
                    </Link>
                    <button
                        class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-100"
                        @click="confirmAndSubmit(`/work/projects/tasks/${task.id}/delete`, 'delete')"
                    >
                        Delete
                    </button>
                </div>
            </div>
        </div>

        <div class="max-w-full mx-auto ">
            <div class="grid grid-cols-4 gap-6">
                <!-- Основний контент: 3/4 -->
                <div class="col-span-4 lg:col-span-3">
                    <div>
                        <p
                            class="min-w-full border-collapse border border-indigo-800 text-indigo-200 p-4 rounded-lg">
                            {{task.content}}
                        </p>

                        <div
                            v-if="children.length"
                            class="overflow-auto rounded-lg shadow-lg shadow-indigo-800/40"
                            tabindex="0"
                            aria-label="Tasks list table container"
                        >
                            <p
                                class="min-w-full border-collapse border border-indigo-800 text-indigo-200 p-2 rounded-lg mt-2">
                                Sub tasks
                            </p>
                            <table
                                class="min-w-full border-collapse border border-indigo-800 text-indigo-200 rounded-lg mt-2"
                                role="table"
                            >
                                <thead class="bg-indigo-800 sticky top-0 z-10 ">
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
                                    v-for="task in children"
                                    :key="task.id"
                                    class="border-t border-indigo-700 hover:bg-indigo-900 transition-colors"
                                    tabindex="0"
                                >

                                    <td class="border border-indigo-700 p-2 text-sm font-mono">{{ task.id }}</td>
                                    <td class="border border-indigo-700 p-2 text-sm">
                                        {{ new Date(task.date).toLocaleDateString() }}
                                    </td>
                                    <td class="border border-indigo-700 p-2 text-sm">
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
                                            :href="`/work/projects/tasks/${task.id}`"
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
                    </div>
                </div>

                <!-- Бічна панель: 1/4 -->
                <div class="col-span-4 lg:col-span-1">
                    <div class="">

                        <div
                            class="overflow-auto rounded-lg shadow-lg shadow-indigo-800/40"
                            tabindex="0"
                            aria-label="Task details table"
                        >
                            <table
                                class="min-w-full border-collapse border border-indigo-800 text-indigo-200"
                                role="table"
                            >
                                <thead class="bg-indigo-800 sticky top-0 z-10">
                                <tr>
                                    <th
                                        colspan="2"
                                        class="border border-indigo-700 p-3 text-left text-sm font-semibold tracking-wide"
                                        scope="colgroup"
                                    >
                                        Task Details
                                    </th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr class="border-t border-indigo-700 hover:bg-indigo-900 transition-colors">
                                    <td class="border border-indigo-700 p-2 text-sm font-medium">Created</td>
                                    <td class="border border-indigo-700 p-2 text-sm">
                                        {{ task.date }}
                                    </td>
                                </tr>
                                <tr class="border-t border-indigo-700 hover:bg-indigo-900 transition-colors">
                                    <td class="border border-indigo-700 p-2 text-sm font-medium">Status</td>
                                    <td class="border border-indigo-700 p-2 text-sm">
                                      <span :class="['inline-block px-2 py-1 rounded text-xs font-semibold', statusBadgeClass(task.status)]">
                                        {{ formatStatus(task.status) || 'NONE' }}
                                      </span>
                                    </td>
                                </tr>

                                <tr class="border-t border-indigo-700 hover:bg-indigo-900 transition-colors">
                                    <td class="border border-indigo-700 p-2 text-sm font-medium">Type</td>
                                    <td class="border border-indigo-700 p-2 text-sm">
                                      <span :class="['inline-block px-2 py-1 rounded text-xs font-semibold', typeBadgeClass(task.type)]">
                                        {{ formatType(task.type) || 'NONE' }}
                                      </span>
                                    </td>
                                </tr>

                                <tr class="border-t border-indigo-700 hover:bg-indigo-900 transition-colors">
                                    <td class="border border-indigo-700 p-2 text-sm font-medium">Priority</td>
                                    <td class="border border-indigo-700 p-2 text-sm">
                                      <span :class="['inline-block px-2 py-1 rounded text-xs font-semibold', priorityBadgeClass(task.priority)]">
                                        {{ formatPriority(task.priority) || 'NONE' }}
                                      </span>
                                    </td>
                                </tr>
                                <tr class="border-t border-indigo-700 hover:bg-indigo-900 transition-colors">
                                    <td class="border border-indigo-700 p-2 text-sm font-medium">Progress</td>
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

                                <tr class="border-t border-indigo-700 hover:bg-indigo-900 transition-colors">
                                    <td class="border border-indigo-700 p-2 text-sm font-medium">Start Date</td>
                                    <td class="border border-indigo-700 p-2 text-sm">
                                        {{ task.start_date ? new Date(task.start_date).toLocaleDateString() : '' }}
                                    </td>
                                </tr>
                                <tr class="border-t border-indigo-700 hover:bg-indigo-900 transition-colors">
                                    <td class="border border-indigo-700 p-2 text-sm font-medium">Plan Date</td>
                                    <td class="border border-indigo-700 p-2 text-sm flex">
                                        <span class="inline-block px-2 py-1 rounded  font-semibold">
                                            {{ task.plan_date ? new Date(task.plan_date).toLocaleDateString() : '' }}
                                        </span>
                                        <Link
                                            :href="`/work/projects/tasks/${task.id}/plan`"
                                            class="bg-indigo-600 hover:bg-indigo-700 text-white font-medium px-2 py-1 rounded transition "
                                        >
                                            EDIT
                                        </Link>
                                    </td>
                                </tr>

                                <tr class="border-t border-indigo-700 hover:bg-indigo-900 transition-colors">
                                    <td class="border border-indigo-700 p-2 text-sm font-medium">Due Date</td>
                                    <td class="border border-indigo-700 p-2 text-sm">
                                        {{ task.dueDate }}
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Task Table -->
                        <div
                            class="overflow-auto rounded-lg shadow-lg shadow-indigo-800/40 mt-5"
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
                                        class="border border-indigo-700 p-3 text-left text-sm font-semibold tracking-wide select-none cursor-pointer"
                                        scope="col"
                                    >
                                        Author
                                    </th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr

                                    class="border-t border-indigo-700 hover:bg-indigo-900 transition-colors"
                                    tabindex="0"
                                >
                                    <td class="border border-indigo-700 p-2 text-sm">
                                        <a
                                            :href="`/work/projects/tasks/${task.author.id}`"
                                            class="text-indigo-300 hover:underline transition-colors"
                                        >
                                            {{ task.author.name }}
                                        </a>
                                    </td>

                                </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Task Table -->
                        <div
                            class="overflow-auto rounded-lg shadow-lg shadow-indigo-800/40 mt-5"
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
                                        class="border border-indigo-700 p-3 text-left text-sm font-semibold tracking-wide select-none cursor-pointer"
                                        scope="col"
                                    >
                                        <div class="flex items-center justify-between">
                                            <span>Executors</span>
                                            <Link
                                                :href="`/work/projects/tasks/${task.id}/assign`"
                                                class="bg-indigo-600 hover:bg-indigo-700 text-white font-medium px-3 py-1 rounded transition ml-2"
                                            >
                                                +
                                            </Link>
                                        </div>
                                    </th>
                                </tr>

                                </thead>
                                <tbody>
                                <tr
                                    v-for="executor in task.executors"
                                    :key="executor.id"
                                    class="border-t border-indigo-700 hover:bg-indigo-900 transition-colors"
                                    tabindex="0"
                                >
                                    <td class="border border-indigo-700 p-2 text-sm flex items-center justify-between">
                                        <span>{{ executor.name }}</span>
                                        <button
                                            @click="revokeExecutor(executor.id)"
                                            class="ml-2 text-red-400 hover:text-red-600"
                                            title="Revoke"
                                        >
                                            ✕
                                        </button>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </AppLayout>
</template>
