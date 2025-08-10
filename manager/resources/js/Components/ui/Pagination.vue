<script setup>
  import { Link } from '@inertiajs/inertia-vue3'
  import { computed, reactive } from 'vue'

  const props = defineProps({
    pagination: {
      type: Object,
      required: true,
    },
    linkBuilder: {
      type: Function,
      required: true,
    },
  })
  const pagination = reactive({ ...props.pagination })
  const paginationRange = computed(() => {
    const current = props.pagination.currentPage
    const last = props.pagination.lastPage
    const delta = 2
    const range = []

    for (let i = Math.max(1, current - delta); i <= Math.min(last, current + delta); i++) {
      range.push(i)
    }

    const result = []
    if (range[0] > 1) {
      result.push(1)
      if (range[0] > 2) result.push('...')
    }

    result.push(...range)

    if (range[range.length - 1] < last) {
      if (range[range.length - 1] < last - 1) result.push('...')
      result.push(last)
    }

    return result
  })
</script>

<template>
  <div v-if="pagination.lastPage > 1" class="mt-4 flex justify-center items-center space-x-1">
    <span
      v-if="pagination.currentPage === 1"
      class="px-3 py-1 border rounded text-gray-400 cursor-not-allowed select-none"
    >
      ← Prev
    </span>
    <Link
      v-else
      :href="linkBuilder(pagination.currentPage - 1)"
      class="px-3 py-1 border rounded hover:bg-gray-100"
    >
      ← Prev
    </Link>

    <template v-for="page in paginationRange" :key="page">
      <span v-if="page === '...'" class="px-3 py-1 text-gray-500">...</span>
      <Link
        v-else
        :href="linkBuilder(page)"
        class="px-3 py-1 border rounded hover:bg-gray-100"
        :class="{ 'bg-blue-600 text-white': page === pagination.currentPage }"
      >
        {{ page }}
      </Link>
    </template>

    <span
      v-if="pagination.currentPage === pagination.lastPage"
      class="px-3 py-1 border rounded text-gray-400 cursor-not-allowed select-none"
    >
      Next →
    </span>
    <Link
      v-else
      :href="linkBuilder(pagination.currentPage + 1)"
      class="px-3 py-1 border rounded hover:bg-gray-100"
    >
      Next →
    </Link>
  </div>
</template>
