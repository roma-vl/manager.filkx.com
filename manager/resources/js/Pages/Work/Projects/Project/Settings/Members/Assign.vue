<script setup>
import { computed } from 'vue'
import { useForm } from '@inertiajs/inertia-vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import SecondaryButton from '@/Components/SecondaryButton.vue'
import Breadcrumbs from '@/Components/ui/Breadcrumbs.vue'
import PageMeta from '@/Components/Seo/PageMeta.vue'

const props = defineProps({
  project: Object,
  roles: Object,
  departments: Object,
  members: Object,
})

const form = useForm({
  member: '',
  departments: [],
  roles: [],
})

const groupedMembers = computed(() => {
  const groups = {}
  for (const member of props.members) {
    if (!groups[member.group]) groups[member.group] = []
    groups[member.group].push(member)
  }
  return Object.entries(groups).map(([label, members]) => ({ label, members }))
})

const submit = () => {
  form.post(`/work/projects/${props.project.id}/settings/members/assign`, {
    preserveScroll: true,
  })
}
</script>

<template>
  <AppLayout>
    <PageMeta
      :title="`Assign Member for -  ${project.name}`"
      :description="`Assign Member for -  ${project.name}`"
    />
    <Breadcrumbs
      :items="[
        { label: 'Home', href: '/' },
        { label: 'Work', href: '/work' },
        { label: 'Projects', href: '/work/projects' },
        { label: project.name, href: `/work/projects/${project.id}` },
        { label: 'Settings', href: `/work/projects/${project.id}/settings` },
        { label: 'Members', href: `/work/projects/${project.id}/settings/members` },
        { label: 'Assign' },
      ]"
    />

    <form class="max-w-3xl mx-auto space-y-6 p-6" @submit.prevent="submit">
      <div>
        <label class="block mb-1 text-sm font-medium text-indigo-300">Member</label>
        <select
          v-model="form.member"
          class="p-2 w-full rounded-md border border-indigo-800 bg-gray-950 text-sm text-indigo-100 shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-600 transition-colors"
        >
          <option class="bg-gray-950 text-indigo-100" value="">Select member...</option>
          <template v-for="(group, index) in groupedMembers" :key="index">
            <optgroup :label="group.label">
              <option
                v-for="member in group.members"
                :key="member.id"
                class="bg-gray-950 text-indigo-100"
                :value="member.id"
              >
                {{ member.name }}
              </option>
            </optgroup>
          </template>
        </select>
        <p v-if="form.errors.member" class="text-red-500 text-sm mt-1">{{ form.errors.member }}</p>
      </div>

      <div>
        <label class="block mb-1 text-sm font-medium text-indigo-300">Departments</label>
        <div class="space-y-2">
          <label
            v-for="(name, id) in departments"
            :key="id"
            class="flex items-center gap-2 text-gray-800 dark:text-gray-100"
          >
            <input
              v-model="form.departments"
              type="checkbox"
              :value="id"
              class="rounded text-indigo-600 dark:bg-gray-700"
            />
            {{ name }}
          </label>
        </div>
        <p v-if="form.errors.departments" class="text-red-500 text-sm mt-1">
          {{ form.errors.departments }}
        </p>
      </div>

      <div>
        <label class="block mb-1 text-sm font-medium text-indigo-300">Roles</label>
        <div class="space-y-2">
          <label
            v-for="(name, id) in roles"
            :key="id"
            class="flex items-center gap-2 text-gray-800 dark:text-gray-100"
          >
            <input
              v-model="form.roles"
              type="checkbox"
              :value="id"
              class="rounded text-indigo-600 dark:bg-gray-700"
            />
            {{ name }}
          </label>
        </div>
        <p v-if="form.errors.roles" class="text-red-500 text-sm mt-1">{{ form.errors.roles }}</p>
      </div>

      <div class="flex justify-end pt-4">
        <SecondaryButton :disabled="form.processing" type="submit">
          <span v-if="form.processing" class="animate-pulse">Assigning...</span>
          <span v-else>Assign</span>
        </SecondaryButton>
      </div>

      <div v-if="form.errors.message" class="text-red-600 font-semibold mt-2">
        {{ form.errors.message }}
      </div>
    </form>
  </AppLayout>
</template>
