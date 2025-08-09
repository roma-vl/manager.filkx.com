<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { usePage } from '@inertiajs/inertia-vue3'
import { Link } from '@inertiajs/inertia-vue3'
import RolesTabs from '@/Components/Work/Projects/Project/Roles/RolesTabs.vue'
import Breadcrumbs from '@/Components/ui/Breadcrumbs.vue'
import PageMeta from "@/Components/Seo/PageMeta.vue";

const { props } = usePage()
const roles = props.value.roles
const permissions = props.value.permissions
</script>

<template>
  <AppLayout>
      <PageMeta
          :title="`Roles`"
          :description="`Page Roles`"
      />
    <Breadcrumbs
      :items="[
        { label: 'Home', href: '/' },
        { label: 'Work', href: '/work' },
        { label: 'Projects', href: '/work/projects' },
        { label: 'Roles' },
      ]"
    />

    <RolesTabs />

    <div class="flex gap-2 mb-4 my-6 justify-end">
      <Link
        :href="`/work/projects/roles/create`"
        class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-xl shadow"
      >
        ➕ Add Role
      </Link>
    </div>

    <!-- Таблиця ролей -->
    <div class="bg-white dark:bg-gray-900 shadow-sm rounded-2xl overflow-x-auto p-4">
      <table class="min-w-full text-sm text-left border-collapse">
        <thead>
          <tr>
            <th class="py-2 px-4 text-gray-700 dark:text-gray-300 font-semibold">Permission</th>
            <template v-for="role in roles" :key="role.id">
              <th class="py-2 px-4 text-center text-gray-700 dark:text-gray-300 font-semibold">
                <Link
                  :href="`/work/projects/roles/${role.id}`"
                  class="hover:underline text-blue-600 dark:text-blue-400"
                >
                  {{ role.name }}
                </Link>
                ({{ role.memberships_count }})
              </th>
            </template>
          </tr>
        </thead>
        <tbody>
          <tr
            v-for="permission in permissions"
            :key="permission"
            class="border-t border-gray-200 dark:border-gray-700"
          >
            <td class="py-2 px-4 text-gray-800 dark:text-gray-200">{{ permission }}</td>
            <template v-for="role in roles" :key="role.id">
              <td class="py-2 px-4 text-center">
                <span
                  v-if="role.permissions.includes(permission)"
                  class="text-green-600 dark:text-green-400"
                >
                  ✔️
                </span>
              </td>
            </template>
          </tr>
        </tbody>
      </table>
    </div>
  </AppLayout>
</template>
