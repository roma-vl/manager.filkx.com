<script setup>
  import GroupsTabs from '@/Components/Work/Members/Groups/Tabs.vue'
  import { Head, Link } from '@inertiajs/inertia-vue3'
  import AppLayout from '@/Layouts/AppLayout.vue'
  import axios from 'axios'
  import { ref } from 'vue'
  import Breadcrumbs from '@/Components/ui/Breadcrumbs.vue'
  import PageMeta from '@/Components/Seo/PageMeta.vue'

  defineProps({
    groups: Array,
    csrf: String,
  })

  const deleting = ref(null)

  async function confirmDelete(id) {
    if (!confirm('Are you sure?')) return
    deleting.value = id

    try {
      await axios.post(`/work/members/groups/${id}/delete`)
      location.reload()
    } catch (error) {
      console.error('Delete error:', error)
      alert('Failed to delete group')
    } finally {
      deleting.value = null
    }
  }
</script>

<template>
  <AppLayout>
    <Head title="Groups" />
    <PageMeta :title="`Groups`" :description="`Page Groups`" />
    <Breadcrumbs
      :items="[
        { label: 'Home', href: '/' },
        { label: 'Work', href: '/work' },
        { label: 'Members', href: '/work/members' },
        { label: 'Groups' },
      ]"
    />

    <GroupsTabs />

    <div class="flex justify-end mb-6">
      <Link
        href="/work/members/groups/create"
        class="inline-block px-5 py-2 bg-indigo-800 hover:bg-indigo-700 text-white rounded-lg shadow-md transition"
      >
        ➕ Add Group
      </Link>
    </div>

    <div
      class="overflow-x-auto rounded-xl bg-gradient-to-br from-gray-900 to-indigo-900 shadow-md border border-indigo-800"
    >
      <table class="min-w-full divide-y divide-gray-700 text-sm text-white">
        <thead class="bg-indigo-950/50">
          <tr>
            <th class="px-6 py-3 text-left tracking-wider text-white/80">Name</th>
            <th class="px-6 py-3 text-center tracking-wider text-white/80">Members</th>
            <th class="px-6 py-3 text-right sr-only">Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-800">
          <tr v-for="group in groups" :key="group.id" class="hover:bg-indigo-800/30 transition-all">
            <td class="px-6 py-4 font-medium text-indigo-300">
              <Link
                v-if="group.members > 0"
                :href="`/work/members?group=${group.id}`"
                class="hover:underline hover:text-indigo-400 transition"
              >
                {{ group.name }}
              </Link>
              <span v-else class="text-white/70">{{ group.name }}</span>
            </td>
            <td class="px-6 py-4 text-center text-white/80">{{ group.members }}</td>
            <td class="px-6 py-4 text-right space-x-2 flex justify-end">
              <Link
                :href="`/work/members/groups/${group.id}/edit`"
                class="px-3 py-1.5 bg-indigo-700 hover:bg-indigo-600 text-white text-sm rounded-md shadow transition"
              >
                ✏️ Edit
              </Link>
              <button
                :disabled="deleting === group.id"
                class="px-3 py-1.5 bg-red-700 hover:bg-red-600 text-white text-sm rounded-md shadow transition disabled:opacity-50 disabled:cursor-not-allowed"
                @click.prevent="confirmDelete(group.id)"
              >
                <span v-if="deleting === group.id">⏳ Deleting...</span>
                <span v-else>🗑️ Delete</span>
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </AppLayout>
</template>

<style scoped>
  /* Custom scrollbar styles */
  ::-webkit-scrollbar {
    height: 8px;
    width: 8px;
  }
  ::-webkit-scrollbar-thumb {
    background-color: #4f46e5; /* indigo-600 */
    border-radius: 9999px;
  }
  ::-webkit-scrollbar-track {
    background-color: #1f2937; /* gray-800 */
  }
</style>
