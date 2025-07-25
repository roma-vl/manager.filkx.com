<script setup>
  import { ref } from 'vue'
  import axios from 'axios'
  import { Link } from '@inertiajs/inertia-vue3'
  import AppLayout from '@/Layouts/AppLayout.vue'

  const props = defineProps({
    task: Object,
  })

  const isUploading = ref(false)
  const errors = ref(null)

  const uploadFiles = async e => {
    const formData = new FormData()
    for (const file of e.target.files) {
      formData.append('files[]', file) // ключ "files" без []
    }

    isUploading.value = true
    errors.value = null

    try {
      await axios.post(`/work/projects/tasks/${props.task.id}/files`, formData, {
        headers: {
          'Content-Type': 'multipart/form-data',
        },
      })
    } catch (error) {
      errors.value = error.response?.data?.error || 'Upload failed'
    } finally {
      isUploading.value = false
    }
  }
</script>

<template>
  <AppLayout>
    <div class="max-w-xl mx-auto bg-white dark:bg-gray-900 p-6 rounded-lg shadow">
      <h1 class="text-2xl font-bold mb-4 text-gray-800 dark:text-gray-100">
        Upload Files for: {{ task.name }}
      </h1>

      <input
        type="file"
        multiple
        @change="uploadFiles"
        class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
      />

      <div v-if="isUploading" class="mt-4 text-blue-500">Uploading...</div>
      <div v-if="errors" class="mt-4 text-red-500">{{ errors }}</div>

      <Link
        :href="`/work/projects/tasks/${task.id}`"
        class="inline-block mt-6 text-sm text-blue-600 hover:underline"
      >
        ← Back to Task
      </Link>
    </div>
  </AppLayout>
</template>
