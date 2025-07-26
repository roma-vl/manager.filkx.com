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
        :href="`/work/projects/${projectId}`"
        class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm "
        :class="{
          'border-indigo-500 text-indigo-600':
            $page.url.startsWith(`/work/projects/${projectId}`) &&
            !$page.url.startsWith(`/work/projects/${projectId}/tasks`) &&
            !$page.url.startsWith(`/work/projects/${projectId}/calendar`) &&
            !$page.url.startsWith(`/work/projects/${projectId}/actions`) &&
            !$page.url.startsWith(`/work/projects/${projectId}/settings`),
          'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': !(
            $page.url.startsWith(`/work/projects/${projectId}`) &&
            !$page.url.startsWith(`/work/projects/${projectId}/tasks`) &&
           !$page.url.startsWith(`/work/projects/${projectId}/calendar`) &&
            !$page.url.startsWith(`/work/projects/${projectId}/actions`) &&
            !$page.url.startsWith(`/work/projects/${projectId}/settings`)
          ),
        }"
      >
        Overview
      </Link>
      <Link
        :href="`/work/projects/${projectId}/tasks`"
        class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm"
        :class="{
          'border-indigo-500 text-indigo-600': $page.url.startsWith(
            `/work/projects/${projectId}/tasks`
          ),
          'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300':
            !$page.url.startsWith(`/work/projects/${projectId}/tasks`),
        }"
      >
        Tasks
      </Link>

      <Link
        :href="`/work/projects/${projectId}/calendar`"
        class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm"
        :class="{
          'border-indigo-500 text-indigo-600': $page.url.startsWith(
            `/work/projects/${projectId}/calendar`
          ),
          'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300':
            !$page.url.startsWith(`/work/projects/${projectId}/calendar`),
        }"
      >
        calendar
      </Link>

      <Link
        :href="`/work/projects/${projectId}/settings`"
        class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm"
        :class="{
          'border-indigo-500 text-indigo-600': $page.url.startsWith(
            `/work/projects/${projectId}/settings`
          ),
          'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300':
            !$page.url.startsWith(`/work/projects/${projectId}/settings`),
        }"
      >
        Settings
      </Link>
      <Link
        :href="`/work/projects/${projectId}/actions`"
        class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm"
        :class="{
          'border-indigo-500 text-indigo-600': $page.url.startsWith(
            `/work/projects/${projectId}/actions`
          ),
          'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300':
            !$page.url.startsWith(`/work/projects/${projectId}/actions`),
        }"
      >
          actions
      </Link>
    </nav>
  </div>
</template>
