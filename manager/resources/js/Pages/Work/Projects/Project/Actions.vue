<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import ActionRow from "@/Components/ActionRow.vue";
import RolesTabs from "@/Components/Work/Projects/Project/Roles/RolesTabs.vue";
import {Head} from "@inertiajs/inertia-vue3";
import Breadcrumbs from "@/Components/ui/Breadcrumbs.vue";
import ProjectTabs from "@/Components/Work/Projects/ProjectTabs.vue";


const props = defineProps({
    project: Object,
    pagination: Object
});
</script>

<template>
    <AppLayout :title="`Actions for ${project.name}`">
        <Head title="Projects" />

        <Breadcrumbs
            :items="[
          { label: 'Home', href: '/' },
          { label: 'Work', href: '/work' },
             { label: 'Projects', href: '/work/projects'  },
          { label: 'Actions' },
        ]"
        />
        <ProjectTabs :project-id="project.id" />

        <div class="overflow-x-auto mt-4 bg-white dark:bg-gray-800 shadow rounded p-4">
            <table class="min-w-full text-sm text-gray-900 dark:text-gray-200">
                <thead>
                <tr class="text-left border-b dark:border-gray-600">
                    <th class="py-2 px-3">Date</th>
                    <th class="py-2 px-3">Task</th>
                    <th class="py-2 px-3">Action</th>
                </tr>
                </thead>
                <tbody>
                <tr
                    v-for="action in pagination.items"
                    :key="action.id"
                    class="border-b hover:bg-gray-100 dark:hover:bg-gray-700"
                >
                    <td class="py-2 px-3 whitespace-nowrap">
                        {{ new Date(action.date).toLocaleString() }}
                    </td>
                    <td class="py-2 px-3">
                        <a
                            v-if="action.task_id"
                            :href="`/work/projects/tasks/${action.task_id}`"
                            class="text-blue-600 dark:text-blue-400 hover:underline"
                        >
                            {{ action.task_name }}
                        </a>
                    </td>
                    <td class="py-2 px-3">
                        <ActionRow :action="action" />
                    </td>
                </tr>
                </tbody>
            </table>
        </div>

        <div class="mt-4 flex justify-between items-center">
            <div>
                Page {{ pagination.pagination.current_page }} of {{ pagination.pagination.total_pages }}
            </div>
            <div>
                <button
                    v-if="pagination.pagination.current_page > 1"
                    @click="$inertia.get(window.location.pathname + '?page=' + (pagination.pagination.current_page - 1))"
                    class="btn"
                >
                    Prev
                </button>
                <button
                    v-if="pagination.pagination.current_page < pagination.pagination.total_pages"
                    @click="$inertia.get(window.location.pathname + '?page=' + (pagination.pagination.current_page + 1))"
                    class="btn ml-2"
                >
                    Next
                </button>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
.btn {
    @apply bg-blue-600 text-white py-1 px-3 rounded hover:bg-blue-700 transition;
}
</style>
