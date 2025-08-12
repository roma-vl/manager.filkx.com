<script setup>
import { useForm } from '@inertiajs/inertia-vue3'
import GroupsTabs from '@/Components/Work/Members/Groups/Tabs.vue'
import AppLayout from '../../../../Layouts/AppLayout.vue'
import InputLabel from '../../../../Components/InputLabel.vue'
import InputError from '../../../../Components/InputError.vue'
import TextInput from '../../../../Components/TextInput.vue'
import SecondaryButton from '../../../../Components/SecondaryButton.vue'
import Breadcrumbs from '@/Components/ui/Breadcrumbs.vue'
import PageMeta from '@/Components/Seo/PageMeta.vue'

const props = defineProps({
  group: Object,
})

const form = useForm({
  name: props.group.name,
})

function submit() {
  form.post(`/work/members/groups/${props.group.id}/edit`)
}
</script>

<template>
  <AppLayout>
    <PageMeta
      :title="`Edit Group ${props.group.name}`"
      :description="`Page Edit Group ${props.group.name}`"
    />
    <Breadcrumbs
      :items="[
        { label: 'Home', href: '/' },
        { label: 'Work', href: '/work' },
        { label: 'Members', href: '/work/members' },
        { label: 'Groups', href: '/work/members/groups' },
        { label: 'Edit' },
      ]"
    />

    <GroupsTabs />

    <form novalidate class="max-w-3xl mx-auto space-y-6 p-6" @submit.prevent="submit">
      <div>
        <InputLabel for="name" :value="'Name'" class="block text-sm font-medium text-gray-700" />
        <TextInput
          id="name"
          v-model="form.name"
          type="text"
          class="w-full mt-1 p-2 border-0 rounded-md focus:border-blue-500 focus:ring focus:ring-blue-200"
          required
          autocomplete="current-name"
          autofocus
        />
        <InputError class="mt-2" :message="form.errors.name" />
      </div>

      <SecondaryButton :disabled="form.processing" type="submit" class="mt-4 float-right">
        <span v-if="form.processing" class="animate-pulse">Updating...</span>
        <span v-else>Update</span>
      </SecondaryButton>
    </form>
  </AppLayout>
</template>
