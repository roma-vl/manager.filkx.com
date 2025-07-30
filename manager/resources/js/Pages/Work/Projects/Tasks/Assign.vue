<script setup>
import { reactive, ref } from 'vue'
import { useRouter } from 'vue-router'
import { Inertia } from '@inertiajs/inertia'
import AppLayout from '../../../../Layouts/AppLayout.vue'
import Breadcrumbs from '../../../../Components/ui/Breadcrumbs.vue'

const props = defineProps({
  task: Object,
  project: Object,
  members: Array,
  selectedMembers: Array,
})

const router = useRouter()
const error = ref(null)

// В реактивній формі зберігаємо вибраних виконавців (масив рядків)
const form = reactive({
  members: props.selectedMembers ? [...props.selectedMembers] : [],
})

function submit() {
  error.value = null
  if (!form.members.length) {
    error.value = 'Виберіть принаймні одного виконавця'
    return
  }

  Inertia.post(
    `/work/projects/tasks/${props.task.id}/assign`,
    { members: form.members },
    {
      onSuccess: () => {
        router.push({ name: 'work.tasks.show', params: { id: props.task.id } })
      },
      onError: errors => {
        error.value = errors.error || 'Сталася помилка'
      },
    },
  )
}
</script>

<template>
  <AppLayout>
    <Breadcrumbs
      :items="[
        { label: 'Home', href: '/' },
        { label: 'Work', href: '/work' },
        { label: 'Tasks', href: '/work/projects/tasks' },
        { label: task.name, href: `/work/projects/tasks/${task.id}` },
        { label: 'Assign Executors' },
      ]"
    />

    <form
      class="max-w-3xl mx-auto space-y-6 bg-gradient-to-br from-indigo-950 via-gray-900 to-[#0e0f11] p-6 rounded-lg shadow-lg shadow-indigo-900/40 text-indigo-200"
      @submit.prevent="submit"
    >
      <h2 class="text-lg font-semibold mb-4">Assign Executors to Task</h2>

      <div
        class="space-y-2 max-h-64 overflow-y-auto border border-indigo-700 rounded p-3 bg-gray-900"
      >
        <div v-for="member in members" :key="member.id" class="flex items-center gap-3">
          <input
            :id="member.id"
            v-model="form.members"
            type="checkbox"
            :value="member.id"
            class="accent-indigo-600"
          />
          <label :for="member.id" class="cursor-pointer select-none">
            {{ member.name }}
          </label>
        </div>
      </div>

      <div>
        <button
          type="submit"
          class="w-full text-sm font-semibold px-4 py-2 rounded-md bg-indigo-700 hover:bg-indigo-600 text-white shadow-md transition-colors focus:outline-none focus:ring-2 focus:ring-indigo-400"
        >
          Assign Selected
        </button>
        <p v-if="error" class="mt-2 text-sm text-red-400">{{ error }}</p>
      </div>
    </form>
  </AppLayout>
</template>

<style scoped></style>
