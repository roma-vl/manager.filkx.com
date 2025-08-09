<script setup>
import { useForm } from '@inertiajs/inertia-vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import SecondaryButton from '@/Components/SecondaryButton.vue'
import Breadcrumbs from '@/Components/ui/Breadcrumbs.vue'
import PageMeta from "@/Components/Seo/PageMeta.vue";

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

      <PageMeta
          :title="`Edit ${membership.name}`"
          :description="`Edit ${membership.name}`"
      />

    <Breadcrumbs
      :items="[
        { label: 'Home', href: '/' },
        { label: 'Work', href: '/work' },
        { label: 'Projects', href: '/work/projects' },
        { label: project.name, href: `/work/projects/${project.id}` },
        { label: 'Settings', href: `/work/projects/${project.id}/settings` },
        { label: 'Members', href: `/work/projects/${project.id}/settings/members` },
        { label: membership.name, href: `/work/projects/${project.id}/settings/members/${membership.id}` },
        { label: 'Edit' },
      ]"
    />

    <form class="max-w-3xl mx-auto space-y-6  p-6 " @submit.prevent="submit">
      <div class="card-body space-y-6">
        <!-- Departments Checkboxes -->
        <div>
          <label class="block mb-1 text-sm font-medium text-indigo-300">Departments</label>
          <div class="flex flex-col gap-1">
            <label
              v-for="(name, id) in departments"
              :key="id"
              class="inline-flex items-center gap-2 text-white/80"
            >
              <input v-model="form.departments" type="checkbox" :value="id" />
              {{ name }}
            </label>
          </div>
          <div v-if="form.errors.departments" class="text-red-500 text-sm">
            {{ form.errors.departments }}
          </div>
        </div>

        <!-- Roles Checkboxes -->
        <div>
          <label class="block mb-1 text-sm font-medium text-indigo-300">Roles</label>
          <div class="flex flex-col gap-1">
            <label
              v-for="(name, id) in roles"
              :key="id"
              class="inline-flex items-center gap-2 text-white/80"
            >
              <input v-model="form.roles" type="checkbox" :value="id" />
              {{ name }}
            </label>
          </div>
          <div v-if="form.errors.roles" class="text-red-500 text-sm">{{ form.errors.roles }}</div>
        </div>

        <div class="flex justify-end pt-4">
          <SecondaryButton :disabled="form.processing" type="submit">
            <span v-if="form.processing" class="animate-pulse">Saving...</span>
            <span v-else>Save</span>
          </SecondaryButton>
        </div>

        <div v-if="form.errors.message" class="text-red-500 text-sm font-medium">
          {{ form.errors.message }}
        </div>
      </div>
    </form>
  </AppLayout>
</template>

<style scoped>

</style>
