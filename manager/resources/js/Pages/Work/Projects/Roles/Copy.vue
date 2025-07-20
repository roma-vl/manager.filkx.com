<script setup>
  import AppLayout from '@/Layouts/AppLayout.vue'
  import { usePage, Link, useForm } from '@inertiajs/inertia-vue3'

  const { props } = usePage()
  const role = props.value.role
  const permissions = props.value.permissions
  const errors = props.value.errors || {}

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
        <li class="text-foreground font-semibold">Copy Role</li>
      </ol>
    </nav>

    <form @submit.prevent="submit" class="max-w-xl bg-white p-6 rounded-xl shadow">
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
            <input type="checkbox" :value="permission" v-model="form.permissions" />
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
