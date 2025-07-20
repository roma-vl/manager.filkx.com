<script setup>
  import { ref } from 'vue'
  import axios from 'axios'
  import { Head, Link } from '@inertiajs/inertia-vue3'
  import AppLayout from '@/Layouts/AppLayout.vue'
  import GroupsTabs from '@/Components/Work/Members/Groups/Tabs.vue'

  const props = defineProps({
    member: Object,
    groups: Object, // { id: name }
  })

  const group = ref(props.member.group_id || '')
  const errors = ref({})
  const loading = ref(false)

  async function submit() {
    loading.value = true
    errors.value = {}

    try {
      await axios.post(`/work/members/${props.member.id}/move`, {
        group: group.value,
      })

      window.location.href = `/work/members/${props.member.id}`
    } catch (error) {
      if (error.response?.status === 422) {
        errors.value = error.response.data.errors || {}
      } else {
        alert('Unexpected error occurred.')
        console.error(error)
      }
    } finally {
      loading.value = false
    }
  }
</script>

<template>
  <AppLayout>
    <Head title="Move Member" />

    <!-- Breadcrumbs -->
    <nav class="text-sm text-gray-500 mb-4" aria-label="Breadcrumb">
      <ol class="flex space-x-2">
        <li><Link href="/" class="hover:text-blue-600">Home</Link><span class="mx-2">/</span></li>
        <li>
          <Link href="/work" class="hover:text-blue-600">Work</Link><span class="mx-2">/</span>
        </li>
        <li>
          <Link href="/work/members" class="hover:text-blue-600">Members</Link
          ><span class="mx-2">/</span>
        </li>
        <li>
          <Link :href="`/work/members/${member.id}`" class="hover:text-blue-600">{{
            member.name
          }}</Link
          ><span class="mx-2">/</span>
        </li>
        <li class="text-gray-700 font-semibold">Move</li>
      </ol>
    </nav>

    <!-- Tabs -->
    <GroupsTabs :active="'move'" :member-id="member.id" />

    <!-- Form Card -->
    <div class="bg-white p-6 rounded shadow">
      <form @submit.prevent="submit" class="space-y-4">
        <div>
          <label class="block text-sm font-medium text-gray-700">Group</label>
          <select v-model="group" class="form-select w-full">
            <option value="" disabled>Select group</option>
            <option v-for="(name, id) in groups" :key="id" :value="id">
              {{ name }}
            </option>
          </select>
          <div v-if="errors.group" class="text-red-500 text-sm mt-1">{{ errors.group }}</div>
        </div>

        <button type="submit" class="btn btn-primary" :disabled="loading">
          {{ loading ? 'Moving...' : 'Move' }}
        </button>
      </form>
    </div>
  </AppLayout>
</template>

<style scoped>
  .form-select {
    @apply border border-gray-300 rounded px-3 py-2 w-full;
  }
  .btn-primary {
    @apply bg-blue-600 text-white hover:bg-blue-700 px-4 py-2 rounded;
  }
</style>
