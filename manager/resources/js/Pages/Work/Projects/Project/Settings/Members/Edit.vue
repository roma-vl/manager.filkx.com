<script setup>
  import { useForm } from '@inertiajs/inertia-vue3'
  import AppLayout from '@/Layouts/AppLayout.vue'

  const props = defineProps({
    project: Object,
    membership: Object,
    roles: Object,
    departments: Object,
  })


  const form = useForm({
    departments: props.membership?.departments ?? [],
    roles: props.membership?.roles ?? [],
  })

  const submit = () => {
    form.post(`/work/projects/${props.project.id}/settings/members/${props.membership.id}/edit`, {
      preserveScroll: true,
    })
  }
</script>

<template>
  <AppLayout>
    <template #header>
      <h1>Edit Member — {{ project.name }}</h1>
    </template>

    <form class="card" @submit.prevent="submit">
      <div class="card-body space-y-6">
        <!-- Departments Checkboxes -->
        <div>
          <label class="block font-medium text-white/80">Departments</label>
          <div class="flex flex-col gap-1">
            <label
              v-for="(name, id) in departments"
              :key="id"
              class="inline-flex items-center gap-2 text-white/80"
            >
              <input type="checkbox" :value="id" v-model="form.departments" />
              {{ name }}
            </label>
          </div>
          <div class="text-red-500 text-sm" v-if="form.errors.departments">
            {{ form.errors.departments }}
          </div>
        </div>

        <!-- Roles Checkboxes -->
        <div>
          <label class="block font-medium text-white/80">Roles</label>
          <div class="flex flex-col gap-1">
            <label
              v-for="(name, id) in roles"
              :key="id"
              class="inline-flex items-center gap-2 text-white/80"
            >
              <input type="checkbox" :value="id" v-model="form.roles" />
              {{ name }}
            </label>
          </div>
          <div class="text-red-500 text-sm" v-if="form.errors.roles">{{ form.errors.roles }}</div>
        </div>

        <!-- Submit Button -->
        <div>
          <button
            type="submit"
            class="px-4 py-2 bg-indigo-800 hover:bg-indigo-700 text-white rounded transition-all shadow-md"
            :disabled="form.processing"
          >
            Save
          </button>
        </div>

        <div v-if="form.errors.message" class="text-red-500 text-sm font-medium">
          {{ form.errors.message }}
        </div>
      </div>
    </form>
  </AppLayout>
</template>

<style scoped>
  .card {
    @apply dark:bg-[#0e0f11] bg-gradient-to-br from-gray-900 to-indigo-900 text-indigo-200 rounded p-4 max-w-4xl mx-auto shadow-md transition-all;
    scrollbar-width: thin;
    scrollbar-color: #4f46e5 #1f2937;
  }

  .card-body {
    @apply space-y-4;
  }

  input[type='checkbox'] {
    accent-color: #4f46e5;
  }
</style>
