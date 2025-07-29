<script setup>
const props = defineProps({
    items: {
        type: Array,
        required: true,
        default: () => [],
    },
})
</script>

<template>
    <nav class="mb-6" aria-label="Breadcrumb">
        <ol class="flex flex-wrap items-center space-x-2 text-sm
               text-gray-600 dark:text-gray-400">
            <li v-for="(item, index) in items" :key="index" class="flex items-center gap-2">
                <template v-if="index < items.length - 1">
                    <a
                        :href="item.href"
                        class="hover:text-indigo-600 dark:hover:text-indigo-400
                   transition-colors font-medium"
                    >
                        {{ item.label }}
                    </a>
                    <svg
                        class="w-4 h-4 text-gray-400 dark:text-gray-500"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                        viewBox="0 0 24 24"
                        aria-hidden="true"
                    >
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                    </svg>
                </template>
                <template v-else>
          <span
              class="font-semibold text-gray-900 dark:text-white"
              aria-current="page"
          >
            {{ item.label }}
          </span>
                </template>
            </li>
        </ol>
    </nav>
</template>
