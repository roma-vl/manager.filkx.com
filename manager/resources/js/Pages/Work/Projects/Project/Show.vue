<script setup>
  import { ref, computed } from 'vue'
  import { Link, router, usePage } from '@inertiajs/inertia-vue3'
  import ProjectTabs from '@/Components/Work/Projects/ProjectTabs.vue'
  import AppLayout from '../../../../Layouts/AppLayout.vue'
  import Breadcrumbs from "@/Components/ui/Breadcrumbs.vue";

  const { props } = usePage()
  const project = props.value.project
  const csrf = props.value.csrf ?? {
    delete: '',
    archive: '',
    reinstate: '',
  }

  const tab = computed(() => {
    if (window.location.pathname.includes('/settings')) {
      return 'settings'
    }
    return 'overview'
  })

  const statusLabel = status => {
    switch (status) {
      case 'active':
        return '🟢 Active'
      case 'archived':
        return '🔴 Archived'
      default:
        return '⚪ Unknown'
    }
  }

  const submit = action => {
    const token = csrf[action]
    const url = route(`work.projects.project.settings.${action}`, { project_id: project.id })

    router.post(
      url,
      { token },
      {
        preserveScroll: true,
      }
    )
  }
</script>
<template>
  <AppLayout>
      <Breadcrumbs
          :items="[
        { label: 'Home', href: '/' },
        { label: 'Work', href: '/work' },
        { label: 'Projects', href: '/work/projects' },
        { label: project.name, },
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
