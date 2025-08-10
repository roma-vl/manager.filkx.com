<script setup>
  import { reactive, ref } from 'vue'
  import { useRouter } from 'vue-router'
  import { Inertia } from '@inertiajs/inertia'
  import AppLayout from '@/Layouts/AppLayout.vue'
  import Breadcrumbs from '@/Components/ui/Breadcrumbs.vue'
  import PageMeta from '@/Components/Seo/PageMeta.vue'

  const props = defineProps({
    task: Object,
    types: Array,
    priorities: Array,
  })

  const router = useRouter()
  const error = ref(null)

  const form = reactive({
    name: props.task.name,
    content: props.task.content ?? '',
  })

  function submit() {
    error.value = null

    Inertia.post(
      `/work/projects/tasks/${props.task.id}/edit`,
      {
        name: form.name,
        content: form.content,
      },
      {
        onSuccess: () => {
          router.push({
            name: 'work.projects.project.tasks',
            params: { project_id: props.project.id },
          })
        },
        onError: errors => {
          error.value = errors.error || 'Сталася помилка'
        },
      }
    )
  }
</script>

<template>
  <AppLayout>
    <PageMeta :title="`Edit ${task.name}`" :description="`Page Edit ${task.name}`" />

    <Breadcrumbs
      :items="[
        { label: 'Home', href: '/' },
        { label: 'Work', href: '/work' },
        { label: 'Tasks', href: '/work/projects/tasks' },
        { label: task.name, href: `/work/projects/tasks/${task.id}` },
        { label: 'Edit' },
      ]"
    />

    <form
      class="max-w-3xl mx-auto space-y-6 bg-gradient-to-br from-indigo-950 via-gray-900 to-[#0e0f11] p-6 rounded-lg shadow-lg shadow-indigo-900/40 text-indigo-200"
      @submit.prevent="submit"
    >
      <div>
        <label class="block mb-1 text-sm font-medium text-indigo-300"
          >Назви задач (назви можуть бути декілька):</label
        >
        <!--                <div v-for="(name, index) in form.names" :key="index" class="flex items-center space-x-2 mb-2">-->
        <input
          v-model="form.name"
          type="text"
          required
          aria-label="Назва задачі"
          class="p-2 flex-1 rounded-md border border-indigo-800 bg-gray-950 text-sm text-indigo-100 placeholder-indigo-400 shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-600 transition-colors"
          placeholder="Назва задачі"
        />
      </div>

      <div>
        <label class="block mb-1 text-sm font-medium text-indigo-300">Опис</label>
        <textarea
          v-model="form.content"
          rows="6"
          aria-label="Опис задачі"
          class="p-2 w-full rounded-md border border-indigo-800 bg-gray-950 text-sm text-indigo-100 placeholder-indigo-400 shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-600 transition-colors"
          placeholder="Опис задачі..."
        />
      </div>

      <div>
        <button
          type="submit"
          class="w-full text-sm font-semibold px-4 py-2 rounded-md bg-indigo-700 hover:bg-indigo-600 text-white shadow-md transition-colors focus:outline-none focus:ring-2 focus:ring-indigo-400"
        >
          Оновити задачу
        </button>
        <p v-if="error" class="mt-2 text-sm text-red-400">{{ error }}</p>
      </div>
    </form>
  </AppLayout>
</template>

<style scoped>
  .error {
    color: red;
  }
</style>
