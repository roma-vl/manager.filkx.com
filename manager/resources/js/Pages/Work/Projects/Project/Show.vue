<script setup>
import { usePage } from '@inertiajs/inertia-vue3'
import ProjectTabs from '@/Components/Work/Projects/ProjectTabs.vue'
import AppLayout from '@/Layouts/AppLayout.vue'
import Breadcrumbs from '@/Components/ui/Breadcrumbs.vue'
import PageMeta from '@/Components/Seo/PageMeta.vue'

const { props } = usePage()
const project = props.value.project
</script>
<template>
  <AppLayout>
    <PageMeta :title="` ${project.name}`" :description="`Page  ${project.name}`" />
    <Breadcrumbs
      :items="[
        { label: 'Home', href: '/' },
        { label: 'Work', href: '/work' },
        { label: 'Projects', href: '/work/projects' },
        { label: project.name },
      ]"
    />

    <ProjectTabs :project-id="project.id" />

    <div class="overflow-x-auto mt-4 bg-white dark:bg-gray-800 shadow rounded p-4">
      <p class="text-gray-600">Якийсь дашборд треба зробити</p>
    </div>
  </AppLayout>
</template>

<style scoped>
  .breadcrumb > * + *::before {
    content: '>';
    margin: 0 0.5rem;
    color: #aaa;
  }
</style>
