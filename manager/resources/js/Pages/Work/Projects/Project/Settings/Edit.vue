<script setup>
  import { useForm } from '@inertiajs/inertia-vue3'
  import InputLabel from '@/Components/InputLabel.vue'
  import InputError from '@/Components/InputError.vue'
  import TextInput from '@/Components/TextInput.vue'
  import AppLayout from '@/Layouts/AppLayout.vue'
  import ProjectTabs from '@/Components/Work/Projects/ProjectTabs.vue'
  import SecondaryButton from '@/Components/SecondaryButton.vue'
  import PageMeta from '@/Components/Seo/PageMeta.vue'
  import Breadcrumbs from '@/Components/ui/Breadcrumbs.vue'

  const props = defineProps({
    project: Object,
  })

  const form = useForm({
    name: props.project.name || '',
    sort: props.project.sort || '',
  })

  function submit() {
    form.post(`/work/projects/${props.project.id}/settings/edit`)
  }
</script>

<template>
  <AppLayout>
    <PageMeta
      :title="`Settings for ${props.project.name}`"
      :description="`Settings for ${props.project.name}`"
    />

    <Breadcrumbs
      :items="[
        { label: 'Home', href: '/' },
        { label: 'Work', href: '/work' },
        { label: 'Projects', href: '/work/projects' },
        { label: project.name, href: `/work/projects/${project.id}` },
        { label: 'Settings', href: `/work/projects/${project.id}/settings` },
        { label: 'Edit' },
      ]"
    />

    <ProjectTabs :project-id="project.id" />

    <form class="" @submit.prevent="submit">
      <div>
        <InputLabel for="name" value="Назва проєкту" />
        <TextInput id="name" v-model="form.name" class="mt-1 block w-full" />
        <InputError :message="form.errors.name" class="mt-2" />
      </div>

      <div>
        <InputLabel for="sort" value="Сортування" />
        <TextInput id="sort" v-model="form.sort" type="number" class="mt-1 block w-full" />
        <InputError :message="form.errors.sort" class="mt-2" />
      </div>

      <SecondaryButton :disabled="form.processing" type="submit" class="mt-4 float-right">
        <span v-if="form.processing" class="animate-pulse">Editing...</span>
        <span v-else>Edit</span>
      </SecondaryButton>
    </form>
  </AppLayout>
</template>
