<script setup>
const props = defineProps({
  action: Object,
})

function taskPriority(priority) {
  const map = {
    1: 'Low',
    2: 'Normal',
    3: 'High',
  }
  return map[priority] ?? `Unknown (${priority})`
}

function taskStatus(status) {
  const map = {
    new: 'New',
    in_progress: 'In Progress',
    completed: 'Completed',
    failed: 'Failed',
  }
  return map[status] ?? status
}

function taskType(type) {
  const map = {
    none: 'Task',
    bug: 'Bug',
    feature: 'Feature',
  }
  return map[type] ?? type
}

</script>

<template>
  <div class="space-y-1 text-sm">
    <div>
      <a :href="`/work/members/${props.action.actor_id}`" class="text-blue-600 dark:text-blue-400 hover:underline">
        {{ action.actor_name }}
      </a>
    </div>

    <div v-if="action.set_project_id && action.set_name">
      created in
      <a :href="`/work/projects/${action.set_project_id}`" class="underline text-blue-600 dark:text-blue-400">
        {{ action.set_project_name }}
      </a>
      as a {{ taskType(action.set_type) }}
      with priority {{ taskPriority(action.set_priority) }}
    </div>

    <div v-else-if="action.set_name">
      renamed to <strong>&laquo;{{ action.set_name }}&raquo;</strong>
    </div>

    <div v-if="action.set_content && !action.set_project_id">
      edited content
    </div>

    <div v-if="action.set_executor_id">
      assigned
      <a :href="`/work/members/${action.set_executor_id}`" class="underline text-blue-600 dark:text-blue-400">
        {{ action.set_executor_name }}
      </a>
    </div>

    <div v-if="action.set_revoked_executor_id">
      revoked
      <a :href="`/work/members/${action.set_revoked_executor_id}`" class="underline text-blue-600 dark:text-blue-400">
        {{ action.set_revoked_executor_name }}
      </a>
    </div>

    <div v-if="action.set_file_id">
      added file
    </div>

    <div v-if="action.set_removed_file_id">
      removed file
    </div>



    <div v-if="action.set_removed_parent">
      removed parent
    </div>

    <div v-if="action.set_plan">
      planned to {{ new Date(action.set_plan).toLocaleDateString() }}
    </div>

    <div v-if="action.set_removed_plan">
      removed plan date
    </div>

    <div v-if="action.set_priority && !action.set_project_id">
      changed priority to {{ taskPriority(action.set_priority) }}
    </div>

    <div v-if="action.set_progress">
      changed progress to {{ action.set_progress }}%
    </div>

    <div v-if="action.set_status">
      changed status to {{ taskStatus(action.set_status) }}
    </div>

    <div v-if="action.set_type && !action.set_project_id">
      changed type to {{ taskType(action.set_type) }}
    </div>

    <div v-if="action.set_parent_id">
      set as child of
      <a :href="`/work/projects/tasks/${action.set_parent_id}`" class="underline text-blue-600 dark:text-blue-400">
        #{{ action.set_parent_id }}
      </a>
    </div>
  </div>
</template>
