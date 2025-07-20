<script setup>
  import { ref, computed } from 'vue'
  import { Link, router, usePage } from '@inertiajs/inertia-vue3'
  import ProjectTabs from '@/Components/Work/Projects/ProjectTabs.vue'
  import AppLayout from '../../../../Layouts/AppLayout.vue'

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
    <nav class="text-sm text-gray-500 mb-6" aria-label="Breadcrumb">
      <ol class="list-reset flex space-x-2">
        <li><Link href="/" class="hover:text-blue-600">Home</Link><span class="mx-2">/</span></li>
        <li>
          <Link href="/work" class="hover:text-blue-600">Work</Link><span class="mx-2">/</span>
        </li>
        <li>
          <Link href="/work/projects" class="hover:text-blue-600">Projects</Link
          ><span class="mx-2">/</span>
        </li>
        <li class="text-gray-700 font-semibold">{{ project.name }}</li>
      </ol>
    </nav>

    <ProjectTabs :project-id="project.id" />

    <div v-if="tab === 'settings'" class="mt-6">
      <div class="flex gap-2 mb-4">
        <Link
          :href="route('work.projects.project.settings.edit', { project_id: project.id })"
          class="btn btn-primary"
        >
          Edit
        </Link>

        <form
          v-if="project.active"
          :action="route('work.projects.project.settings.archive', { project_id: project.id })"
          method="post"
          @submit.prevent="submit('archive')"
        >
          <input type="hidden" name="token" :value="csrf.archive" />
          <button class="btn btn-danger">Archive</button>
        </form>

        <form
          v-if="project.archived"
          :action="route('work.projects.project.settings.reinstate', { project_id: project.id })"
          method="post"
          @submit.prevent="submit('reinstate')"
        >
          <input type="hidden" name="token" :value="csrf.reinstate" />
          <button class="btn btn-success">Reinstate</button>
        </form>

        <form
          :action="route('work.projects.project.settings.delete', { project_id: project.id })"
          method="post"
          @submit.prevent="submit('delete')"
        >
          <input type="hidden" name="token" :value="csrf.delete" />
          <button class="btn btn-danger">Delete</button>
        </form>
      </div>

      <div class="box p-4 bg-white rounded shadow">
        <table class="table w-full">
          <tbody>
            <tr>
              <th class="text-left w-1/4">Name</th>
              <td>{{ project.name }}</td>
            </tr>
            <tr>
              <th class="text-left">Status</th>
              <td>{{ statusLabel(project.status) }}</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <div v-else class="mt-6">
      <p class="text-gray-600">This is the overview tab. (add content later)</p>
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
