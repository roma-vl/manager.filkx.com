<script setup>
  import { computed } from 'vue'
  import { useForm } from '@inertiajs/inertia-vue3'
  import AppLayout from '@/Layouts/AppLayout.vue'

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
    <template #header>
      <h1>Assign Member — {{ project.name }}</h1>
    </template>

    <form class="card" @submit.prevent="submit">
      <div class="card-body space-y-6">
        <!-- Member Select -->
        <div>
          <label class="block font-medium">Member</label>
          <select v-model="form.member" class="form-select w-full">
            <option value="">Select member...</option>
            <template v-for="(group, index) in groupedMembers" :key="index">
              <optgroup :label="group.label">
                <option v-for="member in group.members" :key="member.id" :value="member.id">
                  {{ member.name }}
                </option>
              </optgroup>
            </template>
          </select>
          <div class="text-red-600 text-sm" v-if="form.errors.member">{{ form.errors.member }}</div>
        </div>

        <!-- Departments Checkboxes -->
        <div>
          <label class="block font-medium">Departments</label>
          <div class="flex flex-col gap-1">
            <label
              v-for="(name, id) in departments"
              :key="id"
              class="inline-flex items-center gap-2"
            >
              <input type="checkbox" :value="id" v-model="form.departments" />
              {{ name }}
            </label>
          </div>
          <div class="text-red-600 text-sm" v-if="form.errors.departments">
            {{ form.errors.departments }}
          </div>
        </div>

        <!-- Roles Checkboxes -->
        <div>
          <label class="block font-medium">Roles</label>
          <div class="flex flex-col gap-1">
            <label v-for="(name, id) in roles" :key="id" class="inline-flex items-center gap-2">
              <input type="checkbox" :value="id" v-model="form.roles" />
              {{ name }}
            </label>
          </div>
          <div class="text-red-600 text-sm" v-if="form.errors.roles">{{ form.errors.roles }}</div>
        </div>

        <!-- Submit Button -->
        <div>
          <button type="submit" class="btn btn-primary" :disabled="form.processing">Assign</button>
        </div>

        <div v-if="form.errors.message" class="text-red-700 font-medium">
          {{ form.errors.message }}
        </div>
      </div>
    </form>
  </AppLayout>
</template>
