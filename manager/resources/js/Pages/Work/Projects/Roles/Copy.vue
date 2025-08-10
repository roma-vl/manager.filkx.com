<script setup>
  import AppLayout from '@/Layouts/AppLayout.vue'
  import { usePage, useForm } from '@inertiajs/inertia-vue3'
  import PageMeta from '@/Components/Seo/PageMeta.vue'
  import Breadcrumbs from '@/Components/ui/Breadcrumbs.vue'

  const { props } = usePage()
  const role = props.value.role
  const permissions = props.value.permissions

  const form = useForm({
    name: role.name || '',
    permissions: role.permissions || [],
  })

  function submit() {
    form.post(`/work/projects/roles/${role.id}/copy`)
  }
</script>

<template>
  <AppLayout>
    <PageMeta :title="`${role.name} Copy`" :description="`Page ${role.name} Copy`" />

    <Breadcrumbs
      :items="[
        { label: 'Home', href: '/' },
        { label: 'Work', href: '/work' },
        { label: 'Projects', href: '/work/projects' },
        { label: 'Roles', href: '/work/projects/roles' },
        { label: 'Copy Role' },
      ]"
    />

    <form class="max-w-xl bg-white p-6 rounded-xl shadow" @submit.prevent="submit">
      <div class="mb-4">
        <label for="name" class="block font-semibold mb-1">New Role Name</label>
        <input id="name" v-model="form.name" type="text" class="w-full border rounded px-3 py-2" />
        <p v-if="form.errors.name" class="text-red-600 text-sm mt-1">{{ form.errors.name }}</p>
      </div>

      <div class="mb-4">
        <p class="font-semibold mb-2">Permissions</p>
        <div class="grid grid-cols-2 gap-2 max-h-64 overflow-auto border rounded p-2">
          <label
            v-for="permission in permissions"
            :key="permission"
            class="inline-flex items-center space-x-2"
          >
            <input v-model="form.permissions" type="checkbox" :value="permission" />
            <span>{{ permission }}</span>
          </label>
        </div>
        <p v-if="form.errors.permissions" class="text-red-600 text-sm mt-1">
          {{ form.errors.permissions }}
        </p>
      </div>

      <button type="submit" class="btn btn-primary" :disabled="form.processing">Copy Role</button>
    </form>
  </AppLayout>
</template>

<style scoped>
  .btn {
    @apply px-4 py-2 rounded-xl font-semibold text-white shadow;
  }
  .btn-primary {
    @apply bg-blue-600 hover:bg-blue-700;
  }
</style>
