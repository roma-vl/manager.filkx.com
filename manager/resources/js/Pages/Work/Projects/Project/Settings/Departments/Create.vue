<script setup>
  import { useForm } from '@inertiajs/inertia-vue3'
  import AppLayout from '@/Layouts/AppLayout.vue'
  import ProjectTabs from '@/Components/Work/Projects/ProjectTabs.vue'
  import InputLabel from '@/Components/InputLabel.vue'
  import InputError from '@/Components/InputError.vue'
  import TextInput from '@/Components/TextInput.vue'
  import { Link } from '@inertiajs/inertia-vue3'
  import SecondaryButton from '../../../../../../Components/SecondaryButton.vue'

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
    <!-- Breadcrumbs -->
    <nav class="text-sm text-muted-foreground mb-6" aria-label="Breadcrumb">
      <ol class="flex items-center space-x-2">
        <li><Link href="/" class="hover:text-primary">Home</Link><span>/</span></li>
        <li><Link href="/work" class="hover:text-primary">Work</Link><span>/</span></li>
        <li>
          <Link href="/work/projects" class="hover:text-primary">Projects</Link><span>/</span>
        </li>
        <li>
          <Link :href="`/work/projects/${project.id}`" class="hover:text-primary">{{
            project.name
          }}</Link
          ><span>/</span>
        </li>
        <li>
          <Link :href="`/work/projects/${project.id}/settings`" class="hover:text-primary"
            >Settings</Link
          ><span>/</span>
        </li>
        <li>
          <Link
            :href="`/work/projects/${project.id}/settings/departments`"
            class="hover:text-primary"
            >Departments</Link
          ><span>/</span>
        </li>
        <li class="text-foreground font-semibold">Create</li>
      </ol>
    </nav>

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
        <span v-if="form.processing" class="animate-pulse">Creating...</span>
        <span v-else>Create</span>
      </SecondaryButton>
    </form>
  </AppLayout>
</template>
