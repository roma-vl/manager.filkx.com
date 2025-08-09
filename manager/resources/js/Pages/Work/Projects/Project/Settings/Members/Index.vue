<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import ProjectTabs from '@/Components/Work/Projects/ProjectTabs.vue'
import DepartmentsTabs from '@/Components/Work/Projects/Project/Settings/Departments/DepartmentsTabs.vue'
import { Link } from '@inertiajs/inertia-vue3'
import axios from 'axios'
import Breadcrumbs from '@/Components/ui/Breadcrumbs.vue'
import PageMeta from "@/Components/Seo/PageMeta.vue";

const props = defineProps({
  project: Object,
  memberships: Array,
})


function confirmAndRevoke(memberId) {
  if (!confirm('Are you sure?')) return

  axios.post(`/work/projects/${props.project.id}/settings/members/${memberId}/revoke`, {
    preserveScroll: true,
  })
}
</script>
<template>
  <AppLayout>

      <PageMeta
          :title="`Members for ${props.project.name}`"
          :description="`Members for ${props.project.name}`"
      />
    <Breadcrumbs
      :items="[
        { label: 'Home', href: '/' },
        { label: 'Work', href: '/work' },
        { label: 'Projects', href: '/work/projects' },
        { label: project.name, href: `/work/projects/${project.id}` },
        { label: 'Settings', href: `/work/projects/${project.id}/settings` },
        { label: 'Members' },
      ]"
    />

    <ProjectTabs :project-id="props.project.id" />
    <DepartmentsTabs :project-id="props.project.id" />

    <!-- Controls -->
    <div class="flex gap-2 mb-4 my-6 justify-end">
      <Link
        :href="`/work/projects/${props.project.id}/settings/members/assign`"
        class="btn btn-success"
      >
        Assign Member
      </Link>
      <Link href="/work/projects/roles" class="btn btn-primary">Roles</Link>
    </div>

    <!-- Table -->
    <div class="overflow-x-auto bg-white dark:bg-gray-900 shadow rounded-lg">
      <table class="min-w-full text-sm text-left border border-gray-200 dark:border-gray-700">
        <thead class="bg-gray-100 dark:bg-gray-800 dark:text-gray-200">
          <tr>
            <th class="px-4 py-2 border-b dark:border-gray-700">Name</th>
            <th class="px-4 py-2 border-b dark:border-gray-700">Departments</th>
            <th class="px-4 py-2 border-b dark:border-gray-700">Roles</th>
            <th class="px-4 py-2 border-b dark:border-gray-700 w-32" />
          </tr>
        </thead>
        <tbody class="text-gray-800 dark:text-gray-100">
          <tr
            v-for="membership in props.memberships"
            :key="membership?.member?.id"
            class="border-b border-gray-200 dark:border-gray-700"
          >
            <td class="px-4 py-2">
              <a
                :href="`/work/members/${membership.member.id}`"
                target="_blank"
                class="inline-flex items-center gap-1 text-blue-600 hover:underline dark:text-blue-400"
              >
                <span>{{ membership.member.name.full }}</span>
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                  <path
                    d="M12.293 2.293a1 1 0 011.414 0L18 6.586a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0L7 13.414a1 1 0 010-1.414l8-8z"
                  />
                </svg>
              </a>
            </td>
            <td class="px-4 py-2">
              <ul class="list-disc list-inside space-y-1">
                <li v-for="d in membership.departments" :key="d.name">{{ d.name }}</li>
              </ul>
            </td>
            <td class="px-4 py-2">
              <ul class="list-disc list-inside space-y-1">
                <li v-for="r in membership.roles" :key="r.name">{{ r.name }}</li>
              </ul>
            </td>
            <td class="px-4 py-2">
              <div class="flex gap-2">
                <Link
                  :href="`/work/projects/${project.id}/settings/members/${membership.member.id}/edit`"
                  class="btn btn-sm btn-primary"
                >
                  ✏️
                </Link>
                <button
                  class="btn btn-sm btn-danger"
                  @click="confirmAndRevoke(membership.member.id)"
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

<style scoped>
.btn {
    @apply inline-flex items-center justify-center px-4 py-2 rounded text-white text-sm font-medium;
}
.btn-success {
    @apply bg-green-600 hover:bg-green-700 dark:bg-green-700 dark:hover:bg-green-800;
}
.btn-primary {
    @apply bg-blue-600 hover:bg-blue-700 dark:bg-blue-700 dark:hover:bg-blue-800;
}
.btn-danger {
    @apply bg-red-600 hover:bg-red-700 dark:bg-red-700 dark:hover:bg-red-800;
}
.btn-sm {
    @apply px-2 py-1 text-xs;
}
</style>
