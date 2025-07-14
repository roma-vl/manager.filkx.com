<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import {Link, usePage} from '@inertiajs/inertia-vue3'
import {roleBadgeClass, statusBadgeClass} from "../../helpers.js";
import axios from "axios";
import { computed } from 'vue'


const props = defineProps({
    user: Object,
})
const user = computed(() => usePage().props.value.auth?.user)
console.log(user, 'user')
function confirmAction(action) {
    if (confirm(`Are you sure you want to ${action} this user?`)) {
        axios.post(`/users/${props.user.id}/${action}`)
            .then(() => {
                location.reload()
            })
            .catch((error) => {
                console.error(error)
                alert('Error during action')
            })
    }
}

</script>

<template>
    <AppLayout title="User Info">
        <div class="space-y-4">
            <div class="flex justify-between items-center">
                <h1 class="text-2xl font-bold">User: {{ props.user.name.full }}</h1>
                <Link
                    :href="`/users/${user.id}/edit`"
                    class="bg-blue-600 p-2 rounded text-ellipsis text-white">Edit</Link>

                <Link
                    :href="`/users/${user.id}/role`"
                    class="bg-blue-600 p-2 rounded text-ellipsis text-white">Change Role</Link>

                <div class="flex gap-2">
                    <button
                        @click="confirmAction('confirm')"
                        class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded"
                        v-if="props.user.wait"
                    >
                        ✅ Confirm
                    </button>

                    <button
                        @click="confirmAction('block')"
                        class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded"
                        v-if="props.user.active"
                    >
                        🚫 Block
                    </button>

                    <button
                        @click="confirmAction('activate')"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded"
                        v-if="props.user.blocked"
                    >
                        🔓 Activate
                    </button>
                </div>
            </div>

            <div class="bg-white shadow rounded p-6">
                <table class="table-auto w-full">
                    <tbody>
                    <tr><th class="text-left w-32">Name:</th><td>{{ props.user.name.full }}</td></tr>
                    <tr><th>Email:</th><td>{{ props.user.email }}</td></tr>
                    <tr><th>Created:</th><td>{{ props.user.date }}</td></tr>
                    <tr><th>Role:</th><td>
                        <span
                            class="inline-block px-3 py-1 rounded-full text-sm font-semibold"
                            :class="roleBadgeClass(props.user.role)"
                        >
                  {{ props.user.role }}
                </span>
                    </td></tr>
                    <tr><th>Status:</th><td>
                        <span
                            class="inline-block px-3 py-1 rounded-full text-sm font-semibold"
                            :class="statusBadgeClass(props.user.status)"
                        >
                  {{ props.user.status }}
                </span>
                    </td></tr>
                    </tbody>
                </table>
            </div>

            <div v-if="props.user.networks.length" class="bg-white shadow rounded p-6">
                <h2 class="font-bold text-lg mb-2">Networks</h2>
                <table class="table-auto w-full">
                    <thead>
                    <tr>
                        <th class="text-left">Network</th>
                        <th class="text-left">Identity</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="network in props.user.networks" :key="network.identity">
                        <td>{{ network.network }}</td>
                        <td>{{ network.identity }}</td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AppLayout>
</template>
