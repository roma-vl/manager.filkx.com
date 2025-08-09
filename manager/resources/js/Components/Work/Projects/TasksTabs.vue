<script setup>
import { Link, usePage } from '@inertiajs/inertia-vue3'
import { computed } from 'vue'

const page = usePage()
const currentUrl = computed(() => page.url.value)
const isExactPath = path => currentUrl.value === path
</script>

<template>
  <div class="border-b border-gray-200 dark:border-gray-700 mb-6">
    <nav class="-mb-px flex space-x-4" aria-label="Tasks Tabs">
      <Link
        href="/work/projects/tasks"
        class="whitespace-nowrap py-4 px-4 border-b-2 font-medium text-sm transition-colors"
        :class="{
          'border-indigo-500 text-indigo-600 dark:text-indigo-400':
            isExactPath('/work/projects/tasks'),
          'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-white dark:hover:border-gray-500':
            !isExactPath('/work/projects/tasks'),
        }"
      >
        Усі задачі
      </Link>

      <Link
        href="/work/projects/tasks/me"
        class="whitespace-nowrap py-4 px-4 border-b-2 font-medium text-sm transition-colors"
        :class="{
          'border-indigo-500 text-indigo-600 dark:text-indigo-400':
            isExactPath('/work/projects/tasks/me'),
          'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-white dark:hover:border-gray-500':
            !isExactPath('/work/projects/tasks/me'),
        }"
      >
        Мої задачі
      </Link>

      <Link
        href="/work/projects/tasks/own"
        class="whitespace-nowrap py-4 px-4 border-b-2 font-medium text-sm transition-colors"
        :class="{
          'border-indigo-500 text-indigo-600 dark:text-indigo-400': isExactPath(
            '/work/projects/tasks/own'
          ),
          'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-white dark:hover:border-gray-500':
            !isExactPath('/work/projects/tasks/own'),
        }"
      >
        Створені мною
      </Link>
    </nav>
  </div>
</template>
