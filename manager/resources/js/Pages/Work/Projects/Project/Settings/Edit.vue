<script setup>
  import { useForm } from '@inertiajs/inertia-vue3'
  import InputLabel from '@/Components/InputLabel.vue'
  import InputError from '@/Components/InputError.vue'
  import TextInput from '@/Components/TextInput.vue'
  import AppLayout from '@/Layouts/AppLayout.vue'
  import ProjectTabs from '@/Components/Work/Projects/ProjectTabs.vue'
  import SecondaryButton from '@/Components/SecondaryButton.vue'
  import { Link } from '@inertiajs/inertia-vue3'

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
          }}</Link>
          <span>/</span>
        </li>
        <li class="text-foreground font-semibold">Edit</li>
      </ol>
    </nav>

    <ProjectTabs :project-id="project.id" />

    <form @submit.prevent="submit" class="">
      <div>
        <InputLabel for="name" value="Назва проєкту" />
        <TextInput id="name" v-model="form.name" class="mt-1 block w-full" />
        <InputError :message="form.errors.name" class="mt-2" />
      </div>

      <div>
        <InputLabel for="sort" value="Сортування" />
        <TextInput id="sort" type="number" v-model="form.sort" class="mt-1 block w-full" />
        <InputError :message="form.errors.sort" class="mt-2" />
      </div>

      <SecondaryButton :disabled="form.processing" type="submit" class="mt-4 float-right">
        <span v-if="form.processing" class="animate-pulse">Creating...</span>
        <span v-else>Create</span>
      </SecondaryButton>
    </form>
  </AppLayout>
</template>
