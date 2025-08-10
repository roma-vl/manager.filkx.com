<script setup>
  import { useForm } from '@inertiajs/inertia-vue3'
  import InputLabel from '../../../../Components/InputLabel.vue'
  import InputError from '../../../../Components/InputError.vue'
  import TextInput from '../../../../Components/TextInput.vue'
  import SelectInput from '../../../../Components/SelectInput.vue'
  import AppLayout from '../../../../Layouts/AppLayout.vue'
  import Breadcrumbs from '@/Components/ui/Breadcrumbs.vue'
  import PageMeta from '@/Components/Seo/PageMeta.vue'
  const props = defineProps({
    user: Object,
    errors: Object,
    groups: Array,
  })
  const form = useForm({
    id: props.user.id,
    group: '',
    firstName: props.user.firstName || '',
    lastName: props.user.lastName || '',
    email: props.user.email || '',
  })

  function submit() {
    form.post(`/work/members/create/${props.user.id}`)
  }
</script>

<template>
  <AppLayout>
    <PageMeta :title="`Create Member`" :description="`Page Create Member`" />
    <div>
      <h1 class="text-2xl font-bold mb-4">Додати учасника</h1>
      <Breadcrumbs
        :items="[
          { label: 'Home', href: '/' },
          { label: 'Work', href: '/work' },
          { label: 'Members', href: '/work/members' },
          { label: 'Create' },
        ]"
      />
      <form class="max-w-3xl mx-auto space-y-6 p-6 h-[500px]" @submit.prevent="submit">
        <div>
          <SelectInput
            v-model="form.group"
            name="group"
            label="Група"
            :options="groups"
            :error="form.errors?.group"
          />

          <InputError :message="form.errors.group" class="mt-2" />
        </div>

        <div>
          <InputLabel for="firstName" value="Ім’я" />
          <TextInput id="firstName" v-model="form.firstName" class="mt-1 block w-full" />
          <InputError :message="form.errors.firstName" class="mt-2" />
        </div>

        <div>
          <InputLabel for="lastName" value="Прізвище" />
          <TextInput id="lastName" v-model="form.lastName" class="mt-1 block w-full" />
          <InputError :message="form.errors.lastName" class="mt-2" />
        </div>

        <div>
          <InputLabel for="email" value="Email" />
          <TextInput id="email" v-model="form.email" class="mt-1 block w-full" />
          <InputError :message="form.errors.email" class="mt-2" />
        </div>

        <div>
          <button type="submit" class="btn btn-primary">Створити</button>
        </div>
      </form>
    </div>
  </AppLayout>
</template>

<style scoped>
  .btn-primary {
    background-color: #2563eb;
    color: white;
    padding: 0.5rem 1rem;
    border-radius: 0.375rem;
  }
</style>
