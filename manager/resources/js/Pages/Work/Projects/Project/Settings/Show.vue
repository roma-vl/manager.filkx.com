<script setup>
  import AppLayout from '@/Layouts/AppLayout.vue'
  import ProjectTabs from '@/Components/Work/Projects/ProjectTabs.vue'
  import { Link, usePage } from '@inertiajs/inertia-vue3'
  import DepartmentsTabs from '../../../../../Components/Work/Projects/Project/Settings/Departments/DepartmentsTabs.vue'
  import {statusBadgeClass} from "../../../../../Helpers/helpers.js";

  const { props } = usePage()
  const project = props.value.project

  function confirmAction(message, event) {
    if (!confirm(message)) {
      event.preventDefault()
    } else {
      event.target.submit()
    }
  }
</script>

<template>
  <AppLayout>
    <!-- Breadcrumb -->
    <nav class="text-sm text-muted-foreground mb-6" aria-label="Breadcrumb">
      <ol class="flex items-center space-x-2">
        <li><Link href="/" class="hover:text-primary">Home</Link><span>/</span></li>
        <li><Link href="/work" class="hover:text-primary">Work</Link><span>/</span></li>
        <li>
          <Link href="/work/projects" class="hover:text-primary">Projects</Link><span>/</span>
        </li>
        <li>
          <Link :href="`/work/projects/${project.id}`" class="hover:text-primary">{{
            project.name
          }}</Link>
          <span>/</span>
        </li>
        <li class="text-foreground font-semibold">Settings</li>
      </ol>
    </nav>

    <!-- Tabs -->
    <ProjectTabs :project-id="project.id" />

    <DepartmentsTabs :project-id="project.id" />

    <!-- Controls -->
    <div class="flex flex-wrap items-center gap-2 mb-6">
      <Link
        :href="`/work/projects/${project.id}/settings/edit`"
        class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-xl shadow"
      >
        Edit
      </Link>

      <form
        v-if="project.active"
        :action="`/work/projects/${project.id}/settings/archive`"
        method="post"
        @submit.prevent="confirmAction('Are you sure?', $event)"
      >
        <button
          type="submit"
          class="px-4 py-2 bg-yellow-600 hover:bg-yellow-700 text-white text-sm font-medium rounded-xl shadow"
        >
          Archive
        </button>
      </form>

      <form
        v-if="project.archived"
        :action="`/work/projects/${project.id}/settings/reinstate`"
        method="post"
        @submit.prevent="confirmAction('Are you sure?', $event)"
      >
        <button
          type="submit"
          class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-xl shadow"
        >
          Reinstate
        </button>
      </form>

      <form
        :action="`/work/projects/${project.id}/settings/delete`"
        method="post"
        @submit.prevent="confirmAction('Are you sure?', $event)"
      >
        <button
          type="submit"
          class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-xl shadow"
        >
          Delete
        </button>
      </form>
    </div>

    <!-- Project info -->
    <div class="bg-white rounded-2xl shadow p-6">
      <dl class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-4">
        <div>
          <dt class="text-sm font-medium text-muted-foreground">Name</dt>
          <dd class="text-base text-foreground">{{ project.name }}</dd>
        </div>
        <div>
          <dt class="text-sm font-medium text-muted-foreground">Status</dt>
          <dd>
            <span
              class="inline-block px-3 py-1 rounded-full text-xs font-semibold"
              :class="statusBadgeClass(project.status)"
            >
              {{ project.status }}
            </span>
          </dd>
        </div>
      </dl>
    </div>
  </AppLayout>
</template>
