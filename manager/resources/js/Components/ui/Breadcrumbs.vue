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
  <nav aria-label="Breadcrumb" class="mb-6">
    <ol class="flex flex-wrap gap-x-2 text-white/80 text-sm" role="list">
      <li v-for="(item, index) in items" :key="index">
        <template v-if="index < items.length - 1">
          <a :href="item.href" class="hover:text-indigo-300 transition-colors">{{ item.label }}</a>
          /
        </template>
        <template v-else>
          <span class="text-white font-semibold" aria-current="page">{{ item.label }}</span>
        </template>
      </li>
    </ol>
  </nav>
</template>

<style scoped>
  nav ol {
    list-style: none;
    padding: 0;
    margin: 0;
  }
</style>
