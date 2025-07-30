<script setup>
import { useForm } from '@inertiajs/inertia-vue3'
import InputLabel from '../../../../Components/InputLabel.vue'
import InputError from '../../../../Components/InputError.vue'
import TextInput from '../../../../Components/TextInput.vue'
import AppLayout from '../../../../Layouts/AppLayout.vue'
import Breadcrumbs from '@/Components/ui/Breadcrumbs.vue'
import SecondaryButton from '@/Components/SecondaryButton.vue'
const props = defineProps({
  member: Object,
  errors: Object,
  groups: Array,
})

const form = useForm({
  id: props.member.id,
  firstName: props.member.firstName || '',
  lastName: props.member.lastName || '',
  email: props.member.email || '',
})

function submit() {
  form.post(`/work/members/${props.member.id}/edit`)
}
</script>

<template>
  <AppLayout>
    <Breadcrumbs
      :items="[
        { label: 'Home', href: '/' },
        { label: 'Work', href: '/work' },
        { label: 'Members', href: '/work/members' },
        { label: props.member.firstName + ' ' + props.member.lastName, href: `/work/members/${props.member.id}` },
        { label: 'Members' }
      ]"
    />
    <div>
      <h1 class="text-2xl font-bold mb-4">Edit member</h1>

      <form class="max-w-3xl mx-auto space-y-6  p-6 h-[500px]" @submit.prevent="submit">
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
          <SecondaryButton :disabled="form.processing" type="submit" class="mt-4 float-right">
            <span v-if="form.processing" class="animate-pulse">Updating...</span>
            <span v-else>Update</span>
          </SecondaryButton>
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
