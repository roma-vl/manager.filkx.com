<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import ProjectTabs from '@/Components/Work/Projects/ProjectTabs.vue'
import { Link, usePage } from '@inertiajs/inertia-vue3'
import axios from 'axios'
import { ref } from 'vue'
import DepartmentsTabs from '@/Components/Work/Projects/Project/Settings/Departments/DepartmentsTabs.vue'
import Breadcrumbs from "@/Components/ui/Breadcrumbs.vue";

const { props } = usePage()
const project = props.value.project
const departments = ref(props.value.departments)

async function deleteDepartment(departmentId) {
    if (!confirm('Are you sure?')) return

    try {
        await axios.post(`/work/projects/${project.id}/settings/departments/${departmentId}/delete`, {
            token: props.value.csrf_token,
        })
        departments.value = departments.value.filter(d => d.id !== departmentId)
    } catch (error) {
        console.error('Delete failed', error)
        alert('Failed to delete department.')
    }
}
</script>

<template>
    <AppLayout>
        <Breadcrumbs
            :items="[
        { label: 'Home', href: '/' },
        { label: 'Work', href: '/work' },
        { label: 'Projects', href: '/work/projects' },
        { label: project.name, href: `/work/projects/${project.id}` },
        { label: 'Settings', href: `/work/projects/${project.id}/settings` },
        { label: 'Departments' },
      ]"
        />

        <ProjectTabs :project-id="project.id" />
        <DepartmentsTabs :project-id="project.id" />

        <div class="my-6 flex justify-end">
            <Link
                :href="`/work/projects/${project.id}/settings/departments/create`"
                class="bg-emerald-600 text-white px-4 py-2 rounded-lg shadow hover:bg-emerald-700 dark:hover:bg-emerald-500 transition"
            >
                ➕ Add Department
            </Link>
        </div>

        <!-- Таблиця -->
        <div class="overflow-x-auto bg-white dark:bg-gray-900 shadow-sm rounded-xl border border-gray-200 dark:border-gray-700">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-800">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                        Department
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                        Members
                    </th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                        Actions
                    </th>
                </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                <tr
                    v-for="department in departments"
                    :key="department.id"
                    class="hover:bg-gray-50 dark:hover:bg-gray-800 transition"
                >
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                        {{ department.name }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-400">
                        {{ department.members_count }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <div class="flex gap-2 justify-end">
                            <Link
                                :href="`/work/projects/${project.id}/settings/departments/${department.id}/edit`"
                                class="text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-300 transition"
                                title="Edit"
                            >
                                ✏️
                            </Link>
                            <button
                                @click="deleteDepartment(department.id)"
                                class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 transition"
                                title="Delete"
                            >
                                ❌
                            </button>
                        </div>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </AppLayout>
</template>
