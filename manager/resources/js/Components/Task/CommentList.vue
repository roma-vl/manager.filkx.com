<script setup>
  import { useForm } from '@inertiajs/inertia-vue3'
  import { ref } from 'vue'
  import MarkdownRenderer from '@/Components/ui/MarkdownRenderer.vue'

  const props = defineProps({
    comments: Array,
    taskId: [String, Number],
  })

  console.log(props.comments, 'sad')
  const form = useForm({ text: '' })

  const comments = ref([...(props.comments || [])])
  const editingId = ref(null)
  const editForm = useForm({ text: '' })

  function submitComment() {
    form.post(`/work/projects/tasks/${props.taskId}/comments`, {
      preserveScroll: true,
      onSuccess: () => {
        comments.value.unshift({
          id: Math.random().toString(36).substr(2, 9),
          text: form.text,
          author_name: 'You',
          date: new Date().toISOString(),
        })
        form.reset()
      },
    })
  }

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
        const idx = comments.value.findIndex(c => c.id === commentId)
        if (idx !== -1) comments.value[idx].text = editForm.text
        editingId.value = null
      },
      onError: errors => {
        console.error(errors)
      },
    })
  }

  function deleteComment(id) {
    if (confirm('Are you sure you want to delete this comment?')) {
      form.post(`/work/projects/tasks/${props.taskId}/comments/${id}/delete`, {
        preserveScroll: true,
        onSuccess: () => {
          comments.value = comments.value.filter(comment => comment.id !== id)
        },
      })
    }
  }

  function formatDate(dateStr) {
    const date = new Date(dateStr)
    return date.toLocaleString('uk-UA', { hour12: false })
  }
</script>

<template>
  <div
    class="dark:bg-[#0e0f11] bg-white dark:text-white text-gray-900 mt-6 rounded-2xl shadow-xl transition-all duration-300 ease-in-out max-w-4xl mx-auto p-4"
  >
    <h2 class="text-xl font-bold mb-4">Коментарі</h2>

    <div v-if="comments.length === 0" class="text-gray-400 dark:text-gray-500 mb-4">
      Ще немає коментарів.
    </div>

    <div
      v-for="comment in comments"
      :key="comment.id"
      class="mb-4 p-4 rounded-xl shadow-sm border dark:border-gray-700 bg-gray-100 dark:bg-[#1a1b1f] relative"
    >
      <div class="flex justify-between items-start">
        <div class="flex-1">
          <!-- Якщо редагується -->
          <div v-if="editingId === comment.id">
            <textarea
              v-model="editForm.text"
              class="w-full rounded-xl p-2 text-sm dark:bg-indigo-950 bg-white text-gray-900 dark:text-white border focus:ring focus:ring-indigo-500"
              rows="4"
              required
            />
            <div class="mt-2 flex space-x-2">
              <button
                @click="saveEdit(comment.id)"
                :disabled="editForm.processing"
                class="bg-green-600 text-white px-4 py-1 rounded-xl hover:bg-green-700 transition"
              >
                Змінити
              </button>
              <button
                @click="cancelEdit"
                class="bg-gray-300 text-gray-800 px-4 py-1 rounded-xl hover:bg-gray-400 transition"
              >
                Скасувати
              </button>
            </div>
          </div>
          <!-- Інакше відображаємо текст -->
          <div v-else>
            <p class="text-sm text-gray-800 dark:text-gray-200 whitespace-pre-wrap">
              <MarkdownRenderer :content="comment.text" />
            </p>
            <div class="text-xs text-gray-500 mt-2">
              Автор: <span class="font-semibold">{{ comment.author_name }}</span> •
              {{ formatDate(comment.date) }}
            </div>
          </div>
        </div>

        <!-- Кнопки -->
        <div class="flex flex-col space-y-1 ml-4">
          <button
            v-if="editingId !== comment.id"
            @click="startEditComment(comment)"
            class="text-xs text-indigo-600 hover:text-indigo-400 transition"
          >
            ✎ Редагувати
          </button>
          <button
            @click="deleteComment(comment.id)"
            class="text-xs text-red-500 hover:text-red-300 transition"
          >
            ✖ Видалити
          </button>
        </div>
      </div>
    </div>

    <form @submit.prevent="submitComment" class="mt-6">
      <textarea
        v-model="form.text"
        placeholder="Написати коментар..."
        class="w-full border dark:border-gray-700 rounded-xl p-3 text-sm dark:bg-indigo-950 bg-white text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 transition"
        rows="4"
        required
      ></textarea>

      <button
        type="submit"
        :disabled="form.processing"
        class="mt-3 px-5 py-2 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 transition font-semibold"
      >
        Додати коментар
      </button>
    </form>
  </div>
</template>
