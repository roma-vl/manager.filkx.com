<script setup>
  import AppLayout from '@/Layouts/AppLayout.vue'
  import { useForm } from '@inertiajs/inertia-vue3'

  const props = defineProps({
    role: Object,
    permissions: Array,
    errors: Object,
  })

  const breadcrumb = [
    { title: 'Home', url: '/' },
    { title: 'Work', url: '/work' },
    { title: 'Projects', url: '/work/projects' },
    { title: 'Roles', url: '/work/projects/roles' },
    { title: 'Create', url: null },
  ]

  const form = useForm({
    name: props.role.name,
    permissions: props.role.permissions,
  })

  if (props.errors) {
    form.setError(props.errors)
  }

  function submit() {
    form.post(`/work/projects/roles/${props.role.id}/edit`)
  }
</script>

<template>
  <AppLayout>
    <ol class="breadcrumb mb-4">
      <li class="breadcrumb-item"><a href="/home">Home</a></li>
      <li class="breadcrumb-item"><a href="/work">Work</a></li>
      <li class="breadcrumb-item"><a href="/work/projects">Projects</a></li>
      <li class="breadcrumb-item"><a href="/work/projects/roles">Roles</a></li>
      <li class="breadcrumb-item active">Create</li>
    </ol>

    <form @submit.prevent="submit">
      <div class="card p-6">
        <div class="mb-4">
          <label for="name" class="block font-semibold mb-1">Назва ролі</label>
          <input
            v-model="form.name"
            id="name"
            type="text"
            class="input"
            :class="{ 'border-red-500': form.errors.name }"
          />
          <p v-if="form.errors.name" class="text-red-500 text-sm mt-1">
            {{ form.errors.name }}
          </p>
        </div>

        <div class="mb-4">
          <label class="block font-semibold mb-2">Права доступу</label>
          <div class="grid grid-cols-2 gap-2">
            <div
              v-for="permission in permissions"
              :key="permission"
              class="flex items-center space-x-2"
            >
              <input
                type="checkbox"
                :value="permission"
                v-model="form.permissions"
                class="checkbox"
              />
              <span>{{ permission }}</span>
            </div>
          </div>
          <p v-if="form.errors.permissions" class="text-red-500 text-sm mt-1">
            {{ form.errors.permissions }}
          </p>
        </div>

        <button type="submit" class="btn btn-primary">Створити</button>
      </div>
    </form>
  </AppLayout>
</template>

<style scoped>
  .input {
    @apply border border-gray-300 rounded px-3 py-2 w-full;
  }
  .checkbox {
    @apply h-4 w-4 text-indigo-600;
  }
  .btn {
    @apply bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700;
  }
  .card {
    @apply bg-white rounded shadow;
  }
</style>
