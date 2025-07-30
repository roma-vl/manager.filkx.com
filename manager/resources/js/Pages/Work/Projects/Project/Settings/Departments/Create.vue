<script setup>
import { useForm } from '@inertiajs/inertia-vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import ProjectTabs from '@/Components/Work/Projects/ProjectTabs.vue'
import InputLabel from '@/Components/InputLabel.vue'
import InputError from '@/Components/InputError.vue'
import TextInput from '@/Components/TextInput.vue'
import SecondaryButton from '../../../../../../Components/SecondaryButton.vue'
import Breadcrumbs from '@/Components/ui/Breadcrumbs.vue'

const props = defineProps({
  project: Object,
  errors: Object,
})

const form = useForm({
  name: '',
})

function submit() {
  form.post(`/work/projects/${props.project.id}/settings/departments/create`)
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
        { label: 'Create' },
      ]"
    />

    <!-- Tabs -->
    <ProjectTabs :project-id="project.id" />

    <!-- Form -->
    <form class="  " @submit.prevent="submit">
      <div>
        <InputLabel for="name" value="Department Name" />
        <TextInput id="name" v-model="form.name" class="mt-1 block w-full" autofocus />
        <InputError :message="form.errors.name" class="mt-2" />
      </div>

      <SecondaryButton :disabled="form.processing" type="submit" class="mt-4 float-right">
        <span v-if="form.processing" class="animate-pulse">Creating...</span>
        <span v-else>Create</span>
      </SecondaryButton>
    </form>
  </AppLayout>
</template>
