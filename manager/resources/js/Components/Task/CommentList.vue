<script setup>
import { ref, computed } from 'vue'
import { useForm } from '@inertiajs/inertia-vue3'
import MarkdownRenderer from '@/Components/ui/MarkdownRenderer.vue'
import {formatUtcDate} from '@/Helpers/helpers.js'

const props = defineProps({
  comments: {
    type: [Array, Object],
    required: true,
  },
  taskId: [String, Number],
})

const normalizedComments = computed(() => {
  if (Array.isArray(props.comments)) return props.comments
  else if (props.comments && typeof props.comments === 'object') return [props.comments]
  else return []
})

const editingId = ref(null)
const editForm = useForm({ text: '' })

function startEditComment(comment) {
  editingId.value = comment.id
  editForm.text = comment.text_raw
}

function cancelEdit() {
  editingId.value = null
  editForm.reset()
}

function saveEdit(commentId) {
  editForm.put(`/work/projects/tasks/${props.taskId}/comments/${commentId}/edit`, {
    preserveScroll: true,
    onSuccess: () => {
      const idx = normalizedComments.value.findIndex(c => c.id === commentId)
      if (idx !== -1) normalizedComments.value[idx].text = editForm.text
      editingId.value = null
    },
  })
}

function deleteComment(id) {
  if (confirm('Видалити коментар?')) {
    editForm.post(`/work/projects/tasks/${props.taskId}/comments/${id}/delete`, {
      preserveScroll: true,
      onSuccess: () => {
        const index = normalizedComments.value.findIndex(comment => comment.id === id)
        if (index !== -1) normalizedComments.value.splice(index, 1)
      },
    })
  }
}

</script>

<template>
  <div class="  rounded-2xl shadow-xl">
    <div
      v-for="comment in normalizedComments"
      :key="comment.id"
      class="mb-4 p-4 rounded-xl  bg-gray-100 dark:bg-[#1a1b1f]"
    >
      <div class="flex justify-between items-start">
        <div class="flex-1">
          <div v-if="editingId === comment.id">
            <textarea
              v-model="editForm.text"
              class="w-full rounded-xl p-2 text-sm bg-white dark:bg-indigo-950 text-gray-900 dark:text-white border focus:ring focus:ring-indigo-500"
              rows="4"
            />
            <div class="mt-2 flex space-x-2">
              <button class="bg-green-600 text-white px-4 py-1 rounded-xl" @click="saveEdit(comment.id)">Змінити</button>
              <button class="bg-gray-300 text-gray-800 px-4 py-1 rounded-xl" @click="cancelEdit">Скасувати</button>
            </div>
          </div>
          <div v-else>
            <p class="text-sm whitespace-pre-wrap">
              <MarkdownRenderer :content="comment.text" />
            </p>
            <div class="text-xs text-gray-500 mt-2">
              Автор: <span class="font-semibold">{{ comment.author_name }}</span> • {{ formatUtcDate(comment.date.date) }}
            </div>
          </div>
        </div>

        <div class="flex flex-col space-y-1 ml-4">
          <button v-if="editingId !== comment.id" class="text-xs text-indigo-600" @click="startEditComment(comment)">✎ Редагувати</button>
          <button class="text-xs text-red-500" @click="deleteComment(comment.id)">✖ Видалити</button>
        </div>
      </div>
    </div>
  </div>
</template>
