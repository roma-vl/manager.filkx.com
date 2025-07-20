<script setup>
  import { Head, Link } from '@inertiajs/inertia-vue3'
  import Tabs from '@/Components/Work/Members/Groups/Tabs.vue'
  import AppLayout from '@/Layouts/AppLayout.vue'
  import { CheckCircleIcon, BanIcon } from 'lucide-vue-next'
  import { statusBadgeClass } from '../../../../helpers.js'
  import axios from 'axios'

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
    <Head title="Member Profile" />

    <Tabs />

    <div class="flex flex-wrap justify-between items-center my-6 gap-4">
      <h1 class="text-3xl font-bold text-gray-800">{{ member.name.full }}</h1>

      <div class="flex gap-2 flex-wrap">
        <Link
          :href="`/work/members/${member.id}/edit`"
          class="inline-flex items-center gap-1 px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg shadow transition"
        >
          <svg
            class="w-4 h-4"
            fill="none"
            stroke="currentColor"
            stroke-width="2"
            viewBox="0 0 24 24"
          >
            <path
              d="M15.232 5.232l3.536 3.536m-2.036-2.036L8.464 17.464a5 5 0 01-2.121 1.414l-3.122.781.781-3.122a5 5 0 011.414-2.121l8.536-8.536z"
            />
          </svg>
          Edit
        </Link>

        <Link
          :href="`/work/members/${member.id}/move`"
          class="inline-flex items-center gap-1 px-4 py-2 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg shadow transition"
        >
          <svg
            class="w-4 h-4"
            fill="none"
            stroke="currentColor"
            stroke-width="2"
            viewBox="0 0 24 24"
          >
            <path d="M14 5l7 7m0 0l-7 7m7-7H3" />
          </svg>
          Move
        </Link>

        <Link
          :href="`/users/${member.id}`"
          class="inline-flex items-center gap-1 px-4 py-2 text-sm font-medium text-gray-700 border border-gray-300 hover:bg-gray-100 rounded-lg transition"
        >
          <svg
            class="w-4 h-4"
            fill="none"
            stroke="currentColor"
            stroke-width="2"
            viewBox="0 0 24 24"
          >
            <path d="M5.121 17.804A9 9 0 1116.88 6.196 9 9 0 015.12 17.804z" />
            <path d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
          </svg>
          View User
        </Link>

        <template v-if="member.active && member.id !== currentUserId">
          <button
            @click="archive"
            class="inline-flex items-center gap-1 px-4 py-2 text-sm font-medium text-white bg-red-600 hover:bg-red-700 rounded-lg shadow transition"
          >
            <BanIcon class="w-4 h-4" />
            Archive
          </button>
        </template>

        <template v-if="member.archived">
          <button
            @click="reinstate"
            class="inline-flex items-center gap-1 px-4 py-2 text-sm font-medium text-white bg-green-600 hover:bg-green-700 rounded-lg shadow transition"
          >
            <CheckCircleIcon class="w-4 h-4" />
            Reinstate
          </button>
        </template>
      </div>
    </div>

    <!-- Profile Info -->
    <div class="bg-white shadow rounded-xl p-6 mb-8 border border-gray-200">
      <dl class="grid grid-cols-1 sm:grid-cols-2 gap-6">
        <div>
          <dt class="text-sm font-medium text-gray-500">Name</dt>
          <dd class="mt-1 text-lg text-gray-900">{{ member.name.full }}</dd>
        </div>

        <div>
          <dt class="text-sm font-medium text-gray-500">Email</dt>
          <dd class="mt-1 text-lg text-gray-900">{{ member.email.value }}</dd>
        </div>

        <div>
          <dt class="text-sm font-medium text-gray-500">Group</dt>
          <dd class="mt-1 text-lg text-gray-900">{{ member.group.name }}</dd>
        </div>

        <div>
          <dt class="text-sm font-medium text-gray-500">Status</dt>
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
    <div v-if="departments?.length" class="bg-white shadow rounded-xl p-6 border border-gray-200">
      <h2 class="text-lg font-semibold text-gray-700 mb-4">Departments</h2>
      <div class="overflow-x-auto">
        <table class="min-w-full text-sm border border-gray-200 rounded-lg">
          <thead>
            <tr class="bg-gray-50 text-left text-gray-600">
              <th class="px-4 py-2 border-b">Project</th>
              <th class="px-4 py-2 border-b">Department</th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="dept in departments"
              :key="dept.project_id + '-' + dept.department_name"
              class="hover:bg-gray-50"
            >
              <td class="px-4 py-2 border-b">
                <Link
                  :href="`/work/projects/${dept.project_id}`"
                  class="text-blue-600 hover:underline"
                >
                  {{ dept.project_name }}
                </Link>
              </td>
              <td class="px-4 py-2 border-b">{{ dept.department_name }}</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </AppLayout>
</template>
