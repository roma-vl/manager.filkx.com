<script setup>
import GroupsTabs from '@/Components/Work/Members/Groups/Tabs.vue'
import { Head, Link } from '@inertiajs/inertia-vue3'
import AppLayout from '../../../../Layouts/AppLayout.vue'
import axios from 'axios'
import { ref } from 'vue'

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
        // Можеш тут емісувати подію або оновлювати список груп, або перезавантажувати сторінку
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

        <nav class="text-sm text-gray-500 mb-6" aria-label="Breadcrumb">
            <ol class="list-reset flex space-x-2">
                <li>
                    <Link href="/" class="hover:text-blue-600">Home</Link>
                    <span class="mx-2">/</span>
                </li>
                <li>
                    <Link href="/work" class="hover:text-blue-600">Work</Link>
                    <span class="mx-2">/</span>
                </li>
                <li>
                    <Link href="/work/members" class="hover:text-blue-600">Members</Link>
                    <span class="mx-2">/</span>
                </li>
                <li class="text-gray-700 font-semibold">Groups</li>
            </ol>
        </nav>

        <GroupsTabs />

        <div class="flex justify-end mb-6">
            <Link
                href="/work/members/groups/create"
                class="inline-block px-5 py-2 bg-green-600 hover:bg-green-700 text-white rounded-md shadow transition"
            >
                Add Group
            </Link>
        </div>

        <div class="overflow-x-auto bg-white rounded-lg shadow border border-gray-200">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                <tr>
                    <th
                        scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                    >
                        Name
                    </th>
                    <th
                        scope="col"
                        class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider"
                    >
                        Members
                    </th>
                    <th scope="col" class="relative px-6 py-3">
                        <span class="sr-only">Actions</span>
                    </th>
                </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                <tr
                    v-for="group in groups"
                    :key="group.id"
                    class="hover:bg-gray-50 transition"
                >
                    <td class="px-6 py-4 whitespace-nowrap text-blue-600 font-medium">
                        <Link
                            v-if="group.members > 0"
                            :href="`/work/members?group=${group.id}`"
                            class="hover:underline"
                        >
                            {{ group.name }}
                        </Link>
                        <span v-else class="text-gray-900">
                            {{ group.name }}
                        </span>
                    </td>

                    <td class="px-6 py-4 text-center text-gray-700 font-semibold">
                        {{ group.members }}
                    </td>
                    <td
                        class="px-6 py-4 whitespace-nowrap text-right flex space-x-2 justify-end"
                    >
                        <Link
                            :href="`/work/members/groups/${group.id}/edit`"
                            class="inline-flex items-center px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded shadow transition"
                            title="Edit Group"
                        >
                            <i class="fa fa-pencil mr-1"></i> Edit
                        </Link>

                        <button
                            @click.prevent="confirmDelete(group.id)"
                            :disabled="deleting === group.id"
                            class="inline-flex items-center px-3 py-1.5 bg-red-600 hover:bg-red-700 disabled:opacity-50 disabled:cursor-not-allowed text-white text-sm font-medium rounded shadow transition"
                            title="Delete Group"
                        >
                            <i class="fa fa-trash mr-1"></i>
                            <span v-if="deleting === group.id">Deleting...</span>
                            <span v-else>Delete</span>
                        </button>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </AppLayout>
</template>
