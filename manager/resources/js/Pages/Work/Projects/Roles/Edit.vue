<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { useForm } from '@inertiajs/inertia-vue3'
import PageMeta from '@/Components/Seo/PageMeta.vue'
import Breadcrumbs from '@/Components/ui/Breadcrumbs.vue'

const props = defineProps({
  role: Object,
  permissions: Array,
  errors: Object,
})

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
    <PageMeta :title="`${role.name} Edit`" :description="`Page ${role.name} Edit`" />

    <Breadcrumbs
      :items="[
        { label: 'Home', href: '/' },
        { label: 'Work', href: '/work' },
        { label: 'Projects', href: '/work/projects' },
        { label: 'Roles', href: '/work/projects/roles' },
        { label: 'Edit' },
      ]"
    />

    <form @submit.prevent="submit">
      <div class="card p-6">
        <div class="mb-4">
          <label for="name" class="block font-semibold mb-1">Назва ролі</label>
          <input
            id="name"
            v-model="form.name"
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
                v-model="form.permissions"
                type="checkbox"
                :value="permission"
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
