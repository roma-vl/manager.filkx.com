<template>
  <div class="border-b border-gray-200 mb-6">
    <nav class="-mb-px flex space-x-4" aria-label="Tabs">
      <Link
        :href="`/work/projects`"
        class="whitespace-nowrap py-4 px-4 border-b-2 font-medium text-sm"
        :class="{
          'border-indigo-500 text-indigo-600': isProjectTabActive,
          'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300':
            !isProjectTabActive,
        }"
      >
        Project
      </Link>

      <Link
        :href="`/work/projects/roles`"
        class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm"
        :class="{
          'border-indigo-500 text-indigo-600': isRolesTabActive,
          'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300':
            !isRolesTabActive,
        }"
      >
        Roles
      </Link>
    </nav>
  </div>
</template>

<script setup>
  import { Link } from '@inertiajs/inertia-vue3'
  import { computed } from 'vue'
  import { usePage } from '@inertiajs/inertia-vue3'

  const page = usePage()

  const isProjectTabActive = computed(
    () =>
      page.url.value.startsWith('/work/projects') &&
      !page.url.value.startsWith('/work/projects/roles')
  )

  const isRolesTabActive = computed(() => page.url.value.startsWith('/work/projects/roles'))
</script>
