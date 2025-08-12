<script setup>
import { useForm, Link } from '@inertiajs/inertia-vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import { onBeforeUnmount, onMounted, ref } from 'vue'
import Breadcrumbs from '@/Components/ui/Breadcrumbs.vue'
import axios from 'axios'
import ChangeTypeDropdown from './Partials/ChangeTypeDropdown.vue'
import ChangeStatusDropdown from './Partials/ChangeStatusDropdown.vue'
import ChangePriorityDropdown from './Partials/ChangePriorityDropdown.vue'
import ChangeProgressBar from './Partials/ChangeProgressBar.vue'
import SubTasksTable from '@/Components/Task/SubTasksTable.vue'
import MarkdownRenderer from '@/Components/ui/MarkdownRenderer.vue'
import FilesList from '@/Components/Task/FilesList.vue'
import CommentList from '@/Components/Task/CommentList.vue'
import ActionRow from '@/Components/ActionRow.vue'
import CommentForm from '@/Components/Task/CommentForm.vue'
import PageMeta from '@/Components/Seo/PageMeta.vue'

const props = defineProps({
  task: Object,
  project: Object,
  member: Object,
  children: Array,
  statuses: Array,
  types: Array,
  priorities: Array,
  progress: Array,
  feed: Object,
  meta: Object,
})

const openDropdown = ref(false)
const form = useForm({})
function toggleDropdown() {
  openDropdown.value = !openDropdown.value
}

function closeDropdown(event) {
  if (!event.target.closest('.dropdown-container')) {
    openDropdown.value = false
  }
}

onMounted(() => {
  document.addEventListener('click', closeDropdown)
})

onBeforeUnmount(() => {
  document.removeEventListener('click', closeDropdown)
})

function confirmAndSubmit(url) {
  if (!window.confirm('Are you sure?')) return
  axios.post(url)
}

function revokeExecutor(memberId) {
  if (!confirm('Are you sure?')) return

  form.post(`/work/projects/tasks/${props.task.id}/revoke/${memberId}`, {
    preserveScroll: true,
  })
}

function formatDate(date) {
  return new Date(date).toLocaleString()
}

function reloadComments() {
  window.location.reload()
}
</script>

<template>
  <AppLayout>
    <PageMeta
      :title="`${task.name + ' - ' + project.name}`"
      :description="`Page ${task.name + ' - ' + project.name}`"
    />
    <Breadcrumbs
      :items="[
        { label: 'Home', href: '/' },
        { label: 'Work', href: '/work' },
        { label: 'Projects', href: '/work/projects' },
        { label: project.name, href: `/work/projects/${project.id}` },
        { label: 'Tasks', href: `/work/projects/${project.id}/tasks` },
        { label: task.name },
      ]"
    />

    <!-- Заголовок -->
    <h1 class="mt-0 mb-3 text-xl">
      <template v-if="task.parent">
        <Link :href="`/work/projects/tasks/${task.parent.id}`">{{ task.parent.name }}</Link> /
      </template>
      {{ task.name }}
    </h1>

    <!-- Controls -->
    <div class="flex flex-wrap items-center gap-2 mb-6">
      <template v-if="task.hasExecutor && task.hasExecutor(member.id)">
        <template v-if="task.isNew">
          <button
            class="bg-green-600 hover:bg-green-700 text-white font-medium px-2 py-1 rounded transition"
            @click="confirmAndSubmit(`/work/projects/tasks/start/${task.id}`, 'start')"
          >
            Start
          </button>
        </template>
      </template>
      <template v-else>
        <button
          class="bg-green-600 hover:bg-green-700 text-white font-medium px-2 py-1 rounded transition"
          @click="confirmAndSubmit(`/work/projects/tasks/take/${task.id}`, 'take')"
        >
          Take
        </button>

        <template v-if="task.isNew">
          <button
            class="bg-green-600 hover:bg-green-700 text-white font-medium px-2 py-1 rounded transition"
            @click="
              confirmAndSubmit(`/work/projects/tasks/take_and_start/${task.id}`, 'take-and-start')
            "
          >
            Take & Start
          </button>
        </template>
      </template>

      <Link
        :href="`/work/projects/tasks/${task.id}/assign`"
        class="bg-indigo-600 hover:bg-indigo-700 text-white font-medium px-2 py-1 rounded transition"
      >
        Assign Executors
      </Link>

      <Link
        :href="`/work/projects/${project.id}/tasks/create?parent=${task.id}`"
        class="bg-indigo-600 hover:bg-indigo-700 text-white font-medium px-2 py-1 rounded transition"
      >
        Add SubTasks
      </Link>

      <Link
        :href="`/work/projects/tasks/${task.id}/files`"
        class="inline-flex items-center px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded shadow transition"
      >
        Add File
      </Link>

      <Link
        :href="`/work/projects/tasks/${task.id}/edit`"
        class="bg-indigo-600 hover:bg-indigo-700 text-white font-medium px-2 py-1 rounded transition"
      >
        Edit
      </Link>

      <!-- Dropdown -->
      <div class="relative dropdown-container">
        <button
          class="bg-indigo-600 hover:bg-indigo-700 text-white font-medium px-2 py-1 rounded transition flex items-center gap-1"
          @click.stop="toggleDropdown"
        >
          More
          <svg
            class="w-4 h-4"
            fill="none"
            stroke="currentColor"
            stroke-width="2"
            viewBox="0 0 24 24"
          >
            <path d="M19 9l-7 7-7-7" />
          </svg>
        </button>

        <div
          v-if="openDropdown"
          class="absolute z-50 mt-2 w-48 bg-white border border-gray-200 rounded shadow-lg"
        >
          <Link
            class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
            :href="`/work/projects/tasks/${task.id}/move`"
          >
            Move To Project…
          </Link>
          <Link
            class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
            :href="`/work/projects/tasks/${task.id}/child`"
          >
            Set As Child Of…
          </Link>
          <button
            class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-100"
            @click="confirmAndSubmit(`/work/projects/tasks/${task.id}/delete`, 'delete')"
          >
            Delete
          </button>
        </div>
      </div>
    </div>

    <div class="max-w-full mx-auto">
      <div class="grid grid-cols-4 gap-6">
        <!-- Основний контент: 3/4 -->
        <div class="col-span-4 lg:col-span-3">
          <div>
            <p
              class="min-w-full border-collapse border border-indigo-800 text-indigo-200 p-4 rounded-lg"
            >
              <MarkdownRenderer :content="task.content" />
            </p>
            <div
              v-if="children.length"
              class="overflow-auto rounded-lg shadow-lg shadow-indigo-800/40"
              tabindex="0"
              aria-label="Tasks list table container"
            >
              <SubTasksTable :children="children" :project-id="project.id" />
            </div>

            <div class="rounded-lg shadow-lg shadow-indigo-800/40 mt-5">
              <div
                class="rounded-t-xl p-3 text-left text-sm font-semibold tracking-wide select-none bg-indigo-800 sticky top-0 text-white"
              >
                Attachments
              </div>
              <div class="space-y-4 pt-2 p-2">
                <FilesList :files="task.files" :task-id="task.id" />
              </div>
            </div>

            <div class="rounded-lg shadow-lg shadow-indigo-800/40 mt-5">
              <div
                class="rounded-t-xl p-3 text-left text-sm font-semibold tracking-wide select-none bg-indigo-800 sticky top-0 text-white"
              >
                Comments
              </div>
              <div class="space-y-4 pt-5 p-2">
                <CommentForm :task-id="task.id" @comment:added="reloadComments" />
              </div>
            </div>

            <div class="rounded-lg shadow-lg shadow-indigo-800/40 mt-5">
              <div
                class="text-white rounded-t-xl p-3 text-left text-sm font-semibold tracking-wide select-none bg-indigo-800 sticky top-0"
              >
                History
              </div>

              <div class="space-y-4 pt-5 p-2">
                <div
                  v-for="(entry, index) in feed"
                  :key="index"
                  class="rounded-xl p-4 shadow bg-white dark:bg-gray-900"
                >
                  <div class="text-sm text-gray-500 dark:text-gray-400">
                    {{ formatDate(entry.date) }}
                  </div>

                  <ActionRow v-if="entry.action" :action="entry.action" />

                  <CommentList v-if="entry.comment" :comments="entry.comment" :task-id="task.id" />
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Бічна панель: 1/4 -->
        <div class="col-span-4 lg:col-span-1">
          <div class="">
            <div
              class="rounded-lg shadow-lg shadow-indigo-800/40"
              tabindex="0"
              aria-label="Task details table"
            >
              <table class="min-w-full text-indigo-200" role="table">
                <thead class="bg-indigo-800 sticky top-0">
                  <tr>
                    <th
                      colspan="2"
                      class="rounded-t-xl border-indigo-700 p-3 text-left text-sm font-semibold tracking-wide"
                      scope="colgroup"
                    >
                      Task Details
                    </th>
                  </tr>
                </thead>
                <tbody>
                  <tr class="hover:bg-indigo-900 transition-colors">
                    <td class="p-2 text-sm font-medium">Created</td>
                    <td class="p-2 text-sm">
                      {{ task.date }}
                    </td>
                  </tr>
                  <tr class="hover:bg-indigo-900 transition-colors">
                    <td class="p-2 text-sm font-medium">Status</td>
                    <td class="p-2 text-sm">
                      <ChangeStatusDropdown
                        :task-id="task.id"
                        :current-status="task.status"
                        :statuses="meta.statuses"
                      />
                    </td>
                  </tr>

                  <tr class="hover:bg-indigo-900 transition-colors">
                    <td class="p-2 text-sm font-medium">Type</td>
                    <td class="p-2 text-sm">
                      <ChangeTypeDropdown
                        :task-id="task.id"
                        :current-type="task.type"
                        :types="meta.types"
                      />
                    </td>
                  </tr>

                  <tr class="hover:bg-indigo-900 transition-colors">
                    <td class="p-2 text-sm font-medium">Priority</td>
                    <td class="p-2 text-sm">
                      <ChangePriorityDropdown
                        :task-id="task.id"
                        :current-priority="task.priority"
                        :priorities="meta.priorities"
                      />
                    </td>
                  </tr>
                  <tr class="hover:bg-indigo-900 transition-colors">
                    <td class="p-2 text-sm font-medium">Progress</td>
                    <td class="p-2 text-sm text-center">
                      <ChangeProgressBar
                        :task-id="task.id"
                        :current-progress="task.progress"
                        :progress="meta.progress"
                      />
                    </td>
                  </tr>

                  <tr class="hover:bg-indigo-900 transition-colors">
                    <td class="p-2 text-sm font-medium">Start Date</td>
                    <td class="p-2 text-sm">
                      {{ task.start_date ? new Date(task.start_date).toLocaleDateString() : '' }}
                    </td>
                  </tr>
                  <tr class="hover:bg-indigo-900 transition-colors">
                    <td class="p-2 text-sm font-medium">Plan Date</td>
                    <td class="p-2 text-sm flex">
                      <span class="inline-block px-2 py-1 rounded font-semibold">
                        {{ task.plan_date ? new Date(task.plan_date).toLocaleDateString() : '' }}
                      </span>
                      <Link
                        :href="`/work/projects/tasks/${task.id}/plan`"
                        class="bg-indigo-600 hover:bg-indigo-700 text-white font-medium px-2 py-1 rounded transition"
                      >
                        EDIT
                      </Link>
                    </td>
                  </tr>

                  <tr class="hover:bg-indigo-900 transition-colors">
                    <td class="p-2 text-sm font-medium">Due Date</td>
                    <td class="p-2 text-sm">
                      {{ task.dueDate }}
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>

            <!-- Task Table -->
            <div
              class="rounded-lg shadow-lg shadow-indigo-800/40 mt-5"
              tabindex="0"
              aria-label="Tasks list table container"
            >
              <table class="min-w-full text-indigo-200" role="table">
                <thead class="bg-indigo-800 sticky top-0">
                  <tr>
                    <th
                      class="rounded-t-xl p-3 text-left text-sm font-semibold tracking-wide select-none cursor-pointer"
                      scope="col"
                    >
                      Author
                    </th>
                  </tr>
                </thead>
                <tbody>
                  <tr class="hover:bg-indigo-900 transition-colors" tabindex="0">
                    <td class="p-2 text-sm">
                      {{ task.author.name }}
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>

            <!-- Task Table -->
            <div
              class="rounded-lg shadow-lg shadow-indigo-800/40 mt-5"
              tabindex="0"
              aria-label="Tasks list table container"
            >
              <table class="min-w-full border-indigo-800 text-indigo-200" role="table">
                <thead class="bg-indigo-800 sticky top-0">
                  <tr>
                    <th
                      class="rounded-t-xl p-3 text-left text-sm font-semibold tracking-wide select-none cursor-pointer"
                      scope="col"
                    >
                      <div class="flex items-center justify-between">
                        <span>Executors</span>
                        <Link
                          :href="`/work/projects/tasks/${task.id}/assign`"
                          class="bg-indigo-600 hover:bg-indigo-700 text-white font-medium px-2 rounded transition ml-2"
                        >
                          +
                        </Link>
                      </div>
                    </th>
                  </tr>
                </thead>
                <tbody>
                  <tr
                    v-for="executor in task.executors"
                    :key="executor.id"
                    class="hover:bg-indigo-900 transition-colors"
                    tabindex="0"
                  >
                    <td class="p-2 text-sm flex items-center justify-between">
                      <span>{{ executor.name }}</span>
                      <button
                        class="ml-2 text-red-400 hover:text-red-600 bg-indigo-800 hover:bg-indigo-700 font-medium px-1 rounded transition"
                        title="Revoke"
                        @click="revokeExecutor(executor.id)"
                      >
                        ✕
                      </button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
