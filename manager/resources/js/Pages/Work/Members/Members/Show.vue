<script setup>
import { Link } from '@inertiajs/inertia-vue3'
import Tabs from '@/Components/Work/Members/Groups/Tabs.vue'
import AppLayout from '@/Layouts/AppLayout.vue'
import { CheckCircleIcon, BanIcon } from 'lucide-vue-next'
import axios from 'axios'
import { statusBadgeClass } from '@/Helpers/helpers.js'
import Breadcrumbs from '@/Components/ui/Breadcrumbs.vue'
import PageMeta from '@/Components/Seo/PageMeta.vue'

const props = defineProps({
  member: Object,
  departments: Array,
  currentUserId: String,
})

async function archive() {
  if (!confirm('Are you sure you want to archive this member?')) return

  try {
    await axios.post(`/work/members/${props.member.id}/archive`)
    window.location.reload()
  } catch (e) {
    alert('Failed to archive member.')
    console.error(e)
  }
}

async function reinstate() {
  if (!confirm('Are you sure you want to reinstate this member?')) return

  try {
    await axios.post(`/work/members/${props.member.id}/reinstate`)
    window.location.reload()
  } catch (e) {
    alert('Failed to reinstate member.')
    console.error(e)
  }
}
</script>
<template>
  <AppLayout>
    <PageMeta :title="`Member Profile`" :description="`Page Member Profile`" />

    <Breadcrumbs
      :items="[
        { label: 'Home', href: '/' },
        { label: 'Work', href: '/work' },
        { label: 'Members', href: '/work/members' },
        { label: member.name.full },
      ]"
    />

    <Tabs />

    <div class="flex flex-wrap justify-between items-center my-6 gap-4">
      <h1 class="text-3xl font-bold text-gray-800 dark:text-gray-100">
        {{ member.name.full }}
      </h1>

      <div class="flex gap-2 flex-wrap">
        <Link
          :href="`/work/members/${member.id}/edit`"
          class="inline-flex items-center gap-1 px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg shadow transition"
        >
          <!-- svg -->
          Edit
        </Link>

        <Link
          :href="`/work/members/${member.id}/move`"
          class="inline-flex items-center gap-1 px-4 py-2 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg shadow transition"
        >
          <!-- svg -->
          Move
        </Link>

        <Link
          :href="`/users/${member.id}`"
          class="inline-flex items-center gap-1 px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-200 border border-gray-300 dark:border-gray-600 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg transition"
        >
          <!-- svg -->
          View User
        </Link>

        <template v-if="member.active && member.id !== currentUserId">
          <button
            class="inline-flex items-center gap-1 px-4 py-2 text-sm font-medium text-white bg-red-600 hover:bg-red-700 rounded-lg shadow transition"
            @click="archive"
          >
            <BanIcon class="w-4 h-4" />
            Archive
          </button>
        </template>

        <template v-if="member.archived">
          <button
            class="inline-flex items-center gap-1 px-4 py-2 text-sm font-medium text-white bg-green-600 hover:bg-green-700 rounded-lg shadow transition"
            @click="reinstate"
          >
            <CheckCircleIcon class="w-4 h-4" />
            Reinstate
          </button>
        </template>
      </div>
    </div>

    <!-- Profile Info -->
    <div
      class="bg-white dark:bg-gray-900 shadow rounded-xl p-6 mb-8 border border-gray-200 dark:border-gray-700"
    >
      <dl class="grid grid-cols-1 sm:grid-cols-2 gap-6">
        <div>
          <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Name</dt>
          <dd class="mt-1 text-lg text-gray-900 dark:text-gray-100">{{ member.name.full }}</dd>
        </div>

        <div>
          <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Email</dt>
          <dd class="mt-1 text-lg text-gray-900 dark:text-gray-100">{{ member.email.value }}</dd>
        </div>

        <div>
          <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Group</dt>
          <dd class="mt-1 text-lg text-gray-900 dark:text-gray-100">{{ member.group.name }}</dd>
        </div>

        <div>
          <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Status</dt>
          <dd class="mt-1">
            <span
              class="inline-block px-3 py-1 rounded-full text-xs font-semibold"
              :class="statusBadgeClass(member.status.label)"
            >
              {{ member.status.label }}
            </span>
          </dd>
        </div>
      </dl>
    </div>

    <!-- Departments -->
    <div
      v-if="departments?.length"
      class="bg-white dark:bg-gray-900 shadow rounded-xl p-6 border border-gray-200 dark:border-gray-700"
    >
      <h2 class="text-lg font-semibold text-gray-700 dark:text-gray-200 mb-4">Departments</h2>
      <div class="overflow-x-auto">
        <table class="min-w-full text-sm border border-gray-200 dark:border-gray-700 rounded-lg">
          <thead>
            <tr class="bg-gray-50 dark:bg-gray-800 text-left text-gray-600 dark:text-gray-300">
              <th class="px-4 py-2 border-b dark:border-gray-700">Project</th>
              <th class="px-4 py-2 border-b dark:border-gray-700">Department</th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="dept in departments"
              :key="dept.project_id + '-' + dept.department_name"
              class="hover:bg-gray-50 dark:hover:bg-gray-800"
            >
              <td class="px-4 py-2 border-b dark:border-gray-700">
                <Link
                  :href="`/work/projects/${dept.project_id}`"
                  class="text-blue-600 dark:text-blue-400 hover:underline"
                >
                  {{ dept.project_name }}
                </Link>
              </td>
              <td class="px-4 py-2 border-b dark:border-gray-700">{{ dept.department_name }}</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </AppLayout>
</template>
