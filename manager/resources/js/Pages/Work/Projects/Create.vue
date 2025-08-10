<script setup>
  import { useForm, Link } from '@inertiajs/inertia-vue3'
  import AppLayout from '@/Layouts/AppLayout.vue'
  import InputLabel from '@/Components/InputLabel.vue'
  import InputError from '@/Components/InputError.vue'
  import TextInput from '@/Components/TextInput.vue'
  import SecondaryButton from '@/Components/SecondaryButton.vue'
  import PageMeta from '@/Components/Seo/PageMeta.vue'

  const props = defineProps({
    project: Object,
  })

  const form = useForm({
    name: '',
    sort: props.project.sort,
    status: 'active',
  })

  function submit() {
    form.post('/work/projects/create')
  }
</script>

<template>
  <AppLayout>
    <PageMeta :title="`Create Project`" :description="`Page Create Project`" />

    <!-- Breadcrumbs -->
    <nav class="mb-6 text-sm text-gray-500" aria-label="breadcrumb">
      <ol class="flex space-x-2">
        <li><Link href="/" class="hover:text-blue-600">Home</Link><span class="mx-2">/</span></li>
        <li>
          <Link href="/work" class="hover:text-blue-600">Work</Link><span class="mx-2">/</span>
        </li>
        <li>
          <Link href="/work/projects" class="hover:text-blue-600">Projects</Link
          ><span class="mx-2">/</span>
        </li>
        <li class="text-gray-700 font-semibold" aria-current="page">Create</li>
      </ol>
    </nav>

    <form novalidate @submit.prevent="submit">
      <!-- Назва -->
      <div>
        <InputLabel for="name" :value="'Name'" />
        <TextInput
          id="name"
          v-model="form.name"
          type="text"
          class="mt-1 block w-full"
          autofocus
          required
        />
        <InputError class="mt-2" :message="form.errors.name" />
      </div>

      <!-- Сортування -->
      <div class="mt-4">
        <InputLabel for="sort" :value="'Sort'" />
        <TextInput id="sort" v-model="form.sort" type="number" class="mt-1 block w-full" />
        <InputError class="mt-2" :message="form.errors.sort" />
      </div>

      <!-- Кнопка -->
      <SecondaryButton class="mt-4 float-right" type="submit" :disabled="form.processing">
        <span v-if="form.processing" class="animate-pulse">Creating...</span>
        <span v-else>Create</span>
      </SecondaryButton>
    </form>
  </AppLayout>
</template>
