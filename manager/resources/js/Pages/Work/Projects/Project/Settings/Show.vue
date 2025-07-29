<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import ProjectTabs from '@/Components/Work/Projects/ProjectTabs.vue'
import { Link, usePage } from '@inertiajs/inertia-vue3'
import DepartmentsTabs from '../../../../../Components/Work/Projects/Project/Settings/Departments/DepartmentsTabs.vue'
import { statusBadgeClass } from '../../../../../Helpers/helpers.js'
import Breadcrumbs from "@/Components/ui/Breadcrumbs.vue";

const { props } = usePage()
const project = props.value.project

function confirmAction(message, event) {
    if (!confirm(message)) {
        event.preventDefault()
    } else {
        event.target.submit()
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
        { label: 'Settings' },
      ]"
        />

        <ProjectTabs :project-id="project.id" />
        <DepartmentsTabs :project-id="project.id" />

        <!-- Controls -->
        <div class=" flex-wrap items-center gap-2 mb-6 my-6 flex justify-end">
            <Link
                :href="`/work/projects/${project.id}/settings/edit`"
                class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-xl shadow transition"
            >
                Edit
            </Link>

            <form
                v-if="project.active"
                :action="`/work/projects/${project.id}/settings/archive`"
                method="post"
                @submit.prevent="confirmAction('Are you sure?', $event)"
            >
                <button
                    type="submit"
                    class="px-4 py-2 bg-yellow-600 hover:bg-yellow-700 text-white text-sm font-medium rounded-xl shadow transition"
                >
                    Archive
                </button>
            </form>

            <form
                v-if="project.archived"
                :action="`/work/projects/${project.id}/settings/reinstate`"
                method="post"
                @submit.prevent="confirmAction('Are you sure?', $event)"
            >
                <button
                    type="submit"
                    class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-xl shadow transition"
                >
                    Reinstate
                </button>
            </form>

            <form
                :action="`/work/projects/${project.id}/settings/delete`"
                method="post"
                @submit.prevent="confirmAction('Are you sure?', $event)"
            >
                <button
                    type="submit"
                    class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-xl shadow transition"
                >
                    Delete
                </button>
            </form>
        </div>

        <!-- Project info -->
        <div class="bg-white dark:bg-zinc-900 rounded-2xl shadow p-6 border border-zinc-200 dark:border-zinc-700 transition-colors">
            <dl class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-4">
                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-zinc-400">Name</dt>
                    <dd class="text-base text-gray-800 dark:text-zinc-100">{{ project.name }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-zinc-400">Status</dt>
                    <dd>
            <span
                class="inline-block px-3 py-1 rounded-full text-xs font-semibold"
                :class="statusBadgeClass(project.status)"
            >
              {{ project.status }}
            </span>
                    </dd>
                </div>
            </dl>
        </div>
    </AppLayout>
</template>
