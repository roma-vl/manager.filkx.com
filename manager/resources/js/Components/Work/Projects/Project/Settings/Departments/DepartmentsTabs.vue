<script setup>
  import { Link } from '@inertiajs/inertia-vue3'

  defineProps({
    projectId: {
      type: String,
      required: true,
    },
  })
</script>

<template>
  <div class="border-b border-gray-200 mb-6">
    <nav class="-mb-px flex space-x-8" aria-label="Tabs">
      <Link
        :href="`/work/projects/${projectId}/settings`"
        class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm"
        :class="{
          'border-indigo-500 text-indigo-600':
            $page.url.startsWith(`/work/projects/${projectId}/settings`) &&
            !$page.url.startsWith(`/work/projects/${projectId}/settings/departments`),
          'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': !(
            $page.url.startsWith(`/work/projects/${projectId}/settings`) &&
            !$page.url.startsWith(`/work/projects/${projectId}/settings/departments`)
          ),
        }"
      >
        Common
      </Link>

      <Link
        :href="`/work/projects/${projectId}/settings/departments`"
        class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm"
        :class="{
          'border-indigo-500 text-indigo-600': $page.url.startsWith(
            `/work/projects/${projectId}/settings/departments`
          ),
          'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300':
            !$page.url.startsWith(`/work/projects/${projectId}/settings`),
        }"
      >
        Departments
      </Link>

      <Link
        :href="`/work/projects/${projectId}/settings/members`"
        class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm"
        :class="{
          'border-indigo-500 text-indigo-600': $page.url.startsWith(
            `/work/projects/${projectId}/settings/members`
          ),
          'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300':
            !$page.url.startsWith(`/work/projects/${projectId}/settings`),
        }"
      >
        Members
      </Link>
    </nav>
  </div>
</template>
