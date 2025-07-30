<script setup>
import { ref } from 'vue'
import axios from 'axios'
import { Head } from '@inertiajs/inertia-vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import GroupsTabs from '@/Components/Work/Members/Groups/Tabs.vue'
import Breadcrumbs from '@/Components/ui/Breadcrumbs.vue'
import InputError from '@/Components/InputError.vue'
import SelectInput from '@/Components/SelectInput.vue'
import SecondaryButton from '@/Components/SecondaryButton.vue'

const props = defineProps({
  member: Object,
  groups: Object, // { id: name }
})

const group = ref(props.member.group_id || '')
const errors = ref({})
const loading = ref(false)

async function submit() {
  loading.value = true
  errors.value = {}

  try {
    await axios.post(`/work/members/${props.member.id}/move`, {
      group: group.value,
    })

    window.location.href = `/work/members/${props.member.id}`
  } catch (error) {
    if (error.response?.status === 422) {
      errors.value = error.response.data.errors || {}
    } else {
      alert('Unexpected error occurred.')
      console.error(error)
    }
  } finally {
    loading.value = false
  }
}
</script>

<template>
  <AppLayout>
    <Head title="Move Member" />
    <Breadcrumbs
      :items="[
        { label: 'Home', href: '/' },
        { label: 'Work', href: '/work' },
        { label: 'Members', href: '/work/members' },
        { label: member.name, href: `/work/members/${member.id}` },
        { label: 'Move' }
      ]"
    />

    <GroupsTabs :active="'move'" :member-id="member.id" />

    <div class=" rounded shadow mt-5">
      <form class="max-w-3xl mx-auto space-y-6  p-6 h-[500px]" @submit.prevent="submit">
        <div>
          <SelectInput
            v-model="group"
            name="group"
            label="Група"
            :options="groups"
            :error="errors?.group"
          />

          <InputError :message="errors.group" class="mt-2" />
        </div>

        <SecondaryButton type="submit" class="mt-4 float-right" :disabled="loading">
          {{ loading ? 'Moving...' : 'Move' }}
        </SecondaryButton>
      </form>
    </div>
  </AppLayout>
</template>

<style scoped>
  .form-select {
    @apply border border-gray-300 rounded px-3 py-2 w-full;
  }
  .btn-primary {
    @apply bg-blue-600 text-white hover:bg-blue-700 px-4 py-2 rounded;
  }
</style>
