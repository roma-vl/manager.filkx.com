<script setup>
  import { Link, usePage } from '@inertiajs/inertia-vue3'
  import { computed } from 'vue'

  const page = usePage()
  const currentUrl = computed(() => page.url.value)
  const isActive = path => computed(() => currentUrl.value.startsWith(path))
</script>

<template>
  <div class="border-b border-gray-200 dark:border-gray-700 mb-6">
    <nav class="-mb-px flex space-x-4" aria-label="Проєкт">
      <Link
        href="/work/projects"
        class="whitespace-nowrap py-4 px-4 border-b-2 font-medium text-sm"
        :class="{
          'border-indigo-500 text-indigo-600 dark:text-indigo-400':
            isActive('/work/projects').value &&
            !isActive('/work/projects/roles').value &&
            !isActive('/work/projects/tasks').value &&
            !isActive('/work/projects/actions').value &&
            !isActive('/work/projects/calendar').value,
          'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-200 dark:hover:border-gray-500':
            !(
              isActive('/work/projects').value &&
              !isActive('/work/projects/roles').value &&
              !isActive('/work/projects/tasks').value &&
              !isActive('/work/projects/actions').value &&
              !isActive('/work/projects/calendar').value
            ),
        }"
      >
        Проєкт
      </Link>

      <Link
        href="/work/projects/tasks"
        class="whitespace-nowrap py-4 px-4 border-b-2 font-medium text-sm"
        :class="{
          'border-indigo-500 text-indigo-600 dark:text-indigo-400':
            isActive('/work/projects/tasks').value,
          'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-200 dark:hover:border-gray-500':
            !isActive('/work/projects/tasks').value,
        }"
      >
        Задачі
      </Link>

      <Link
        href="/work/projects/calendar"
        class="whitespace-nowrap py-4 px-4 border-b-2 font-medium text-sm"
        :class="{
          'border-indigo-500 text-indigo-600 dark:text-indigo-400':
            isActive('/work/projects/calendar').value,
          'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-200 dark:hover:border-gray-500':
            !isActive('/work/projects/calendar').value,
        }"
      >
        Календар
      </Link>

        <Link
            href="/work/projects/actions"
            class="whitespace-nowrap py-4 px-4 border-b-2 font-medium text-sm"
            :class="{
          'border-indigo-500 text-indigo-600 dark:text-indigo-400':
            isActive('/work/projects/actions').value,
          'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-200 dark:hover:border-gray-500':
            !isActive('/work/projects/actions').value,
        }"
        >
            Дії
        </Link>

      <Link
        href="/work/projects/roles"
        class="whitespace-nowrap py-4 px-4 border-b-2 font-medium text-sm"
        :class="{
          'border-indigo-500 text-indigo-600 dark:text-indigo-400':
            isActive('/work/projects/roles').value,
          'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-200 dark:hover:border-gray-500':
            !isActive('/work/projects/roles').value,
        }"
      >
        Ролі
      </Link>
    </nav>
  </div>
</template>
