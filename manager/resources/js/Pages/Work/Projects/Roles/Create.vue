<script setup>
  import AppLayout from '@/Layouts/AppLayout.vue'
  // import Breadcrumb from '@/Components/Breadcrumb.vue'
  import { useForm } from '@inertiajs/inertia-vue3'
  import Breadcrumbs from "@/Components/ui/Breadcrumbs.vue";
  import SecondaryButton from "@/Components/SecondaryButton.vue";
  import InputError from "@/Components/InputError.vue";
  import InputLabel from "@/Components/InputLabel.vue";
  import TextInput from "@/Components/TextInput.vue";

  const props = defineProps({
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
    name: '',
    permissions: [],
  })

  if (props.errors) {
    form.setError(props.errors)
  }

  function submit() {
    form.post('/work/projects/roles/create')
  }
</script>

<template>
  <AppLayout>
      <Breadcrumbs
          :items="[
        { label: 'Home', href: '/' },
        { label: 'Work', href: '/work' },
        { label: 'Projects', href: '/work/projects' },
        { label: 'Roles', href: '/work/projects/roles' },
        { label: 'Create' },
      ]"
      />

    <form @submit.prevent="submit" class="max-w-3xl mx-auto space-y-6  p-6 ">
        <div>
            <InputLabel for="name" value="Назва ролі" />
            <TextInput id="name" v-model="form.name" class="mt-1 block w-full" autofocus />
            <InputError :message="form.errors.name" class="mt-2" />
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

      <div class="flex justify-end pt-4">
          <SecondaryButton :disabled="form.processing" type="submit">
              <span v-if="form.processing" class="animate-pulse">Creating...</span>
              <span v-else>Create</span>
          </SecondaryButton>
      </div>

    </form>
  </AppLayout>
</template>

<style scoped>
</style>
