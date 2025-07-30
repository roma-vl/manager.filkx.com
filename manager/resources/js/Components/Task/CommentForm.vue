<script setup>
import { useForm } from '@inertiajs/inertia-vue3'

const props = defineProps({
  taskId: {
    type: [String, Number],
    required: true,
  },
})

const emit = defineEmits(['comment:added'])

const form = useForm({ text: '' })

function submitComment() {
  form.post(`/work/projects/tasks/${props.taskId}/comments`, {
    preserveScroll: true,
    onSuccess: () => {
      emit('comment:added')
      form.reset()
    },
  })
}
</script>

<template>
  <form class="mt-6" @submit.prevent="submitComment">
    <textarea
      v-model="form.text"
      placeholder="Написати коментар..."
      class="w-full border dark:border-gray-700 rounded-xl p-3 text-sm dark:bg-indigo-950 bg-white text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 transition"
      rows="4"
      required
    />

    <button
      type="submit"
      :disabled="form.processing"
      class="mt-3 px-5 py-2 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 font-semibold transition"
    >
      Додати коментар
    </button>
  </form>
</template>
