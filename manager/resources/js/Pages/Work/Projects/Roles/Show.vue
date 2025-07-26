<script setup>
  import AppLayout from '@/Layouts/AppLayout.vue'
  import { Link, usePage } from '@inertiajs/inertia-vue3'

  const { props } = usePage()

  const role = props.value.role
</script>

<template>
  <AppLayout>
    <!-- Хлібні крихти -->
    <nav class="text-sm text-muted-foreground mb-6" aria-label="Breadcrumb">
      <ol class="flex items-center space-x-2">
        <li><Link href="/" class="hover:text-primary">Home</Link><span>/</span></li>
        <li><Link href="/work" class="hover:text-primary">Work</Link><span>/</span></li>
        <li>
          <Link href="/work/projects" class="hover:text-primary">Projects</Link><span>/</span>
        </li>
        <li>
          <Link href="/work/projects/roles" class="hover:text-primary">Roles</Link><span>/</span>
        </li>
        <li class="text-foreground font-semibold">{{ role.name }}</li>
      </ol>
    </nav>

    <!-- Дії -->
    <div class="flex gap-3 mb-4">
      <Link :href="`/work/projects/roles/${role.id}/edit`" class="btn btn-primary"> Edit </Link>
      <form
        :action="`/work/projects/roles/${role.id}/delete`"
        method="post"
        @submit.prevent="confirmDelete"
        class="inline"
      >
        <button type="submit" class="btn btn-danger">Delete</button>
      </form>
      <Link :href="`/work/projects/roles/${role.id}/copy`" class="btn btn-primary"> Copy </Link>
    </div>

    <!-- Інформація про роль -->
    <div class="box bg-white dark:bg-gray-900 rounded-xl shadow p-6">
      <table class="table-auto w-full border-collapse border border-gray-200 dark:border-gray-700">
        <tbody>
          <tr class="border-b border-gray-200 dark:border-gray-700">
            <th class="text-left py-2 px-4 font-semibold text-gray-700 dark:text-gray-300 w-32">
              Name
            </th>
            <td class="py-2 px-4 text-gray-900 dark:text-gray-100">{{ role.name }}</td>
          </tr>
          <tr>
            <th
              class="text-left py-2 px-4 font-semibold text-gray-700 dark:text-gray-300 align-top"
            >
              Permissions
            </th>
            <td class="py-2 px-4 text-gray-900 dark:text-gray-100">
              <ul class="list-disc list-inside space-y-1">
                <li v-for="permission in role.permissions" :key="permission">
                  {{ permission }}
                </li>
              </ul>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </AppLayout>
</template>

<script>
  function confirmDelete(event) {
    if (!confirm('Are you sure?')) {
      event.preventDefault()
    } else {
      event.target.submit()
    }
  }
</script>

<style scoped>
  .btn {
    @apply px-4 py-2 rounded-xl font-semibold text-white shadow;
  }
  .btn-primary {
    @apply bg-blue-600 hover:bg-blue-700;
  }
  .btn-danger {
    @apply bg-red-600 hover:bg-red-700;
  }
  .box {
    @apply overflow-hidden;
  }
</style>
