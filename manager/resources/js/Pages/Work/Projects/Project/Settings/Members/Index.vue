<script setup>
  import AppLayout from '@/Layouts/AppLayout.vue'
  import ProjectTabs from '@/Components/Work/Projects/ProjectTabs.vue'
  import DepartmentsTabs from '@/Components/Work/Projects/Project/Settings/Departments/DepartmentsTabs.vue'
  import { Link } from '@inertiajs/inertia-vue3'
  import axios from 'axios'

  const props = defineProps({
    project: Object,
    memberships: Array,
  })

  console.log(props.memberships.value, 'asdasd')

  function confirmAndRevoke(memberId) {
    if (!confirm('Are you sure?')) return

    axios.post(`/work/projects/${props.project.id}/settings/members/${memberId}/revoke`, {
      preserveScroll: true,
    })
  }
</script>

<template>
  <AppLayout>
    <!-- Хлібні крихти -->
    <nav class="text-sm text-muted-foreground mb-6" aria-label="Breadcrumb">
      <ol class="flex flex-wrap items-center gap-1 text-gray-500">
        <li><Link href="/" class="hover:text-gray-700">Home</Link><span>/</span></li>
        <li><Link href="/work" class="hover:text-gray-700">Work</Link><span>/</span></li>
        <li>
          <Link href="/work/projects" class="hover:text-gray-700">Projects</Link><span>/</span>
        </li>
        <li>
          <Link :href="`/work/projects/${props.project.id}`" class="hover:text-gray-700">{{
            props.project.name
          }}</Link
          ><span>/</span>
        </li>
        <li>
          <Link :href="`/work/projects/${props.project.id}/settings`" class="hover:text-gray-700"
            >Settings</Link
          ><span>/</span>
        </li>
        <li class="text-gray-900 font-medium">Members</li>
      </ol>
    </nav>

    <!-- Таби -->
    <ProjectTabs :project-id="props.project.id" />
    <DepartmentsTabs :project-id="props.project.id" />

    <!-- Кнопки -->
    <div class="flex gap-2 mb-4">
      <Link
        :href="`/work/projects/${props.project.id}/settings/members/assign`"
        class="btn btn-success"
        >Assign Member</Link
      >
      <Link href="/work/projects/roles" class="btn btn-primary">Roles</Link>
    </div>

    <!-- Таблиця -->
    <div class="overflow-x-auto bg-white shadow rounded-lg">
      <table class="min-w-full text-sm text-left border">
        <thead class="bg-gray-100">
          <tr>
            <th class="px-4 py-2 border-b">Name</th>
            <th class="px-4 py-2 border-b">Departments</th>
            <th class="px-4 py-2 border-b">Roles</th>
            <th class="px-4 py-2 border-b w-32"></th>
          </tr>
        </thead>
        <tbody>
          <tr
            v-for="membership in props.memberships"
            :key="membership?.member?.id"
            class="border-b"
          >
            <td class="px-4 py-2">
              <a
                :href="`/work/members/${membership.member.id}`"
                target="_blank"
                class="inline-flex items-center gap-1 text-blue-600 hover:underline"
              >
                <span>{{ membership.member.name.full }}</span>
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                  <path
                    d="M12.293 2.293a1 1 0 011.414 0L18 6.586a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0L7 13.414a1 1 0 010-1.414l8-8z"
                  />
                </svg>
              </a>
            </td>
            <td class="px-4 py-2">
              <ul class="list-disc list-inside space-y-1">
                <li v-for="d in membership.departments" :key="d.name">{{ d.name }}</li>
              </ul>
            </td>
            <td class="px-4 py-2">
              <ul class="list-disc list-inside space-y-1">
                <li v-for="r in membership.roles" :key="r.name">{{ r.name }}</li>
              </ul>
            </td>
            <td class="px-4 py-2">
              <div class="flex gap-2">
                <Link
                  :href="`/work/projects/${project.id}/settings/members/${membership.member.id}/edit`"
                  class="btn btn-sm btn-primary"
                >
                  ✏️
                </Link>
                <button
                  @click="confirmAndRevoke(membership.member.id)"
                  class="btn btn-sm btn-danger"
                >
                  ❌
                </button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </AppLayout>
</template>

<style scoped>
  .btn {
    @apply inline-flex items-center justify-center px-4 py-2 rounded text-white text-sm font-medium;
  }
  .btn-success {
    @apply bg-green-600 hover:bg-green-700;
  }
  .btn-primary {
    @apply bg-blue-600 hover:bg-blue-700;
  }
  .btn-danger {
    @apply bg-red-600 hover:bg-red-700;
  }
  .btn-sm {
    @apply px-2 py-1 text-xs;
  }
</style>
