<script setup>
  import AppLayout from '@/Layouts/AppLayout.vue'
  import ProjectTabs from '@/Components/Work/Projects/ProjectTabs.vue'
  import { Link, usePage, router } from '@inertiajs/inertia-vue3'
  import axios from 'axios'
  import { ref } from 'vue'
  import DepartmentsTabs from '../../../../../../Components/Work/Projects/Project/Settings/Departments/DepartmentsTabs.vue'

  const { props } = usePage()
  const project = props.value.project
  const departments = ref(props.value.departments)

  async function deleteDepartment(departmentId) {
    if (!confirm('Are you sure?')) return

    try {
      await axios.post(`/work/projects/${project.id}/settings/departments/${departmentId}/delete`, {
        token: props.value.csrf_token,
      })
      departments.value = departments.value.filter(d => d.id !== departmentId)
    } catch (error) {
      console.error('Delete failed', error)
      alert('Failed to delete department.')
    }
  }
</script>

<template>
  <AppLayout>
    <!-- Хлібні крихти -->
    <nav class="text-sm text-muted-foreground mb-6" aria-label="Breadcrumb">
      <ol class="flex flex-wrap items-center gap-1 text-gray-500">
        <li><Link href="/" class="hover:text-gray-700 transition">Home</Link><span>/</span></li>
        <li><Link href="/work" class="hover:text-gray-700 transition">Work</Link><span>/</span></li>
        <li>
          <Link href="/work/projects" class="hover:text-gray-700 transition">Projects</Link
          ><span>/</span>
        </li>
        <li>
          <Link :href="`/work/projects/${project.id}`" class="hover:text-gray-700 transition">{{
            project.name
          }}</Link
          ><span>/</span>
        </li>
        <li>
          <Link
            :href="`/work/projects/${project.id}/settings`"
            class="hover:text-gray-700 transition"
            >Settings</Link
          ><span>/</span>
        </li>
        <li class="text-gray-900 font-medium">Departments</li>
      </ol>
    </nav>

    <!-- Таби -->
    <ProjectTabs :project-id="project.id" />
    <DepartmentsTabs :project-id="project.id" />

    <!-- Кнопка додавання -->
    <div class="my-6 flex justify-end">
      <Link
        :href="`/work/projects/${project.id}/settings/departments/create`"
        class="bg-emerald-600 text-white px-4 py-2 rounded-lg shadow hover:bg-emerald-700 transition"
      >
        ➕ Add Department
      </Link>
    </div>

    <!-- Таблиця -->
    <div class="overflow-x-auto bg-white shadow-sm rounded-xl border">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th
              class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
            >
              Department
            </th>
            <th
              class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
            >
              Members
            </th>
            <th
              class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider"
            >
              Actions
            </th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr
            v-for="department in departments"
            :key="department.id"
            class="hover:bg-gray-50 transition"
          >
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ department.name }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
              {{ department.members_count }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
              <div class="flex gap-2 justify-end">
                <Link
                  :href="`/work/projects/${project.id}/settings/departments/${department.id}/edit`"
                  class="text-indigo-600 hover:text-indigo-800 transition"
                  title="Edit"
                >
                  ✏️
                </Link>
                <button
                  @click="deleteDepartment(department.id)"
                  class="text-red-600 hover:text-red-800 transition"
                  title="Delete"
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
