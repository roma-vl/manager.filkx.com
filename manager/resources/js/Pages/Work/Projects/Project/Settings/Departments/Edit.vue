<script setup>
  import { useForm } from '@inertiajs/inertia-vue3'
  import AppLayout from '@/Layouts/AppLayout.vue'
  import ProjectTabs from '@/Components/Work/Projects/ProjectTabs.vue'
  import InputLabel from '@/Components/InputLabel.vue'
  import InputError from '@/Components/InputError.vue'
  import TextInput from '@/Components/TextInput.vue'
  import { Link } from '@inertiajs/inertia-vue3'
  import SecondaryButton from '@/Components/SecondaryButton.vue'
  import Breadcrumbs from "@/Components/ui/Breadcrumbs.vue";

  const props = defineProps({
    project: Object,
    department: Object,
    errors: Object,
  })

  const form = useForm({
    name: props.department.name || '',
  })

  function submit() {
    form.post(`/work/projects/${props.project.id}/settings/departments/${props.department.id}/edit`)
  }
</script>

<template>
  <AppLayout>
      <Breadcrumbs
          :items="[
        { label: 'Home', href: '/' },
        { label: 'Work', href: '/work' },
        { label: 'Projects', href: '/work/projects' },
        { label: project.name, href: `/work/projects/${project.id}` },
        { label: 'Settings', href: `/work/projects/${project.id}/settings` },
        { label: 'Departments', href: `/work/projects/${project.id}/settings/departments` },
        { label: 'Edit' },
      ]"
      />

    <!-- Tabs -->
    <ProjectTabs :project-id="project.id" />

    <!-- Form -->
    <form @submit.prevent="submit" class="">
      <div>
        <InputLabel for="name" value="Department Name" />
        <TextInput id="name" v-model="form.name" class="mt-1 block w-full" autofocus />
        <InputError :message="form.errors.name" class="mt-2" />
      </div>

      <SecondaryButton :disabled="form.processing" type="submit" class="mt-4 float-right">
        <span v-if="form.processing" class="animate-pulse">Editing...</span>
        <span v-else>Edit</span>
      </SecondaryButton>
    </form>
  </AppLayout>
</template>
