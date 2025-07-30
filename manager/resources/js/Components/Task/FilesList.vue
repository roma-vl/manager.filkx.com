<script setup>
import { Link } from '@inertiajs/inertia-vue3'
import axios from 'axios'
import { ref, watch } from 'vue'

const props = defineProps({
  files: Array,
  taskId: Number,
})

const localFiles = ref([...props.files])
watch(
  () => props.files,
  newVal => {
    localFiles.value = [...newVal]
  },
)

async function deleteFile(fileId) {
  if (!confirm('Are you sure?')) return

  try {
    await axios.post(`/work/projects/tasks/${props.taskId}/files/${fileId}/delete`)
    localFiles.value = localFiles.value.filter(file => file.id !== fileId)
  } catch (error) {
    console.error('Failed to delete file:', error)
  }
}

function formatSize(size) {
  return (size / 1024).toFixed(2) + ' KB'
}

function fileUrl(file) {
  return `/storage/${file.info.path}/${file.info.name}`
}

function isImage(file) {
  return /\.(jpe?g|png|gif|webp|bmp|svg)$/i.test(file.info.name)
}

function truncateFileName(filename, maxBaseLength = 20) {
  const parts = filename.split('.')
  if (parts.length < 2) return filename

  const ext = parts.pop()
  const base = parts.join('.')

  const shortBase = base.length > maxBaseLength ? base.slice(0, maxBaseLength) + '..' : base

  return `${shortBase}.${ext}`
}
</script>

<template>
  <div class="">
    <!-- Header -->
    <div class="flex justify-between items-center">
      <span />
      <Link
        :href="`/work/projects/tasks/${taskId}/files`"
        class="inline-flex items-center px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded shadow transition"
      >
        Add File
      </Link>
    </div>

    <!-- File Grid -->
    <div v-if="localFiles?.length" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
      <div
        v-for="file in localFiles"
        :key="file.id"
        class="group h-32 relative bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-sm hover:shadow-lg overflow-hidden transition"
      >
        <!-- Image Preview -->
        <div
          v-if="isImage(file)"
          class="absolute inset-0 bg-cover bg-center opacity-80 group-hover:opacity-50 transition"
          :style="{ backgroundImage: `url(${fileUrl(file)})` }"
        />

        <!-- Top Overlay -->
        <div
          class="relative z-10 p-2 flex justify-between items-start space-x-2 bg-white/80 dark:bg-gray-900/70 backdrop-blur-sm"
        >
          <a
            :href="fileUrl(file)"
            target="_blank"
            class="text-blue-600 dark:text-blue-400 font-semibold hover:underline break-all truncate max-w-[80%] text-xs"
          >
            {{ truncateFileName(file.info.name, 20) }}
          </a>

          <button
            class="text-gray-400 hover:text-red-600 dark:hover:text-red-400 transition"
            title="Delete"
            @click.prevent="deleteFile(file.id)"
          >
            x
          </button>
        </div>

        <!-- Hover Info -->
        <div
          class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition flex flex-col justify-end text-white text-sm p-2 space-y-1"
        >
          <div>
            👤
            <Link :href="`/work/members/${file.member.id}`" class="text-blue-300 hover:underline">
              {{ file.member.name }}
            </Link>
          </div>
          <div class="flex justify-between">
            <span class="text-xs">📅 {{ new Date(file.date).toLocaleString() }}</span>
            <span class="text-xs text-gray-300 dark:text-gray-400 whitespace-nowrap">
              {{ formatSize(file.info.size) }}
            </span>
          </div>
        </div>
      </div>
    </div>

    <!-- Empty State -->
    <div v-else class="text-center text-gray-500 dark:text-gray-400 py-6">
      No files attached yet.
    </div>
  </div>
</template>

<style scoped>
  /* Можна додати плавну появу або кастомну анімацію при hover */
</style>
