<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { Link, usePage } from '@inertiajs/inertia-vue3'
import { roleBadgeClass, statusBadgeClass } from '../../helpers.js'
import axios from 'axios'
import { computed } from 'vue'

const props = defineProps({
    user: Object,
})
const user = computed(() => usePage().props.value.auth?.user)
const me =  props.user.id === user.value.id;
const allNetworks = ['facebook', 'google']

const connectedNetworks = computed(() => {
    return props.user.networks.reduce((acc, net) => {
        acc[net.network] = net
        return acc
    }, {})
})

function unlink(network) {
    if (confirm(`Ви впевнені, що хочете відв'язати ${network}?`)) {
        axios.post(`/auth/${network}/detach`)
            .then(() => location.reload())
            .catch(err => {
                console.error(err)
                alert('Помилка при відв’язці мережі')
            })
    }
}

function confirmAction(action) {
    if (confirm(`Are you sure you want to ${action} this user?`)) {
        axios.post(`/users/${props.user.id}/${action}`)
            .then(() => location.reload())
            .catch((error) => {
                console.error(error)
                alert('Error during action')
            })
    }
}
</script>

<template>
    <AppLayout title="User Info">
        <div class="space-y-6 max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Heading -->
            <div class="flex flex-wrap justify-between items-center gap-2">
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">👤 {{ props.user.name.full }}</h1>

                <div class="flex flex-wrap gap-2">
                    <Link :href="`/users/${props.user.id}/edit`"
                          v-if="!me"
                          class="inline-flex items-center bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition">
                        ✏️ Edit
                    </Link>

                    <Link :href="`/users/${props.user.id}/role`"
                          v-if="!me"
                          class="inline-flex items-center bg-purple-600 hover:bg-purple-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition">
                        🛡️ Change Role
                    </Link>

                    <button
                        v-if="props.user.wait && !me"
                        @click="confirmAction('confirm')"
                        class="bg-green-600 hover:bg-green-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition"
                    >
                        ✅ Confirm
                    </button>

                    <button
                        v-if="props.user.active && !me"
                        @click="confirmAction('block')"
                        class="bg-red-600 hover:bg-red-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition"
                    >
                        🚫 Block
                    </button>

                    <button
                        v-if="props.user.blocked && !me"
                        @click="confirmAction('activate')"
                        class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition"
                    >
                        🔓 Activate
                    </button>
                </div>
            </div>

            <!-- Basic Info -->
            <div class="bg-white dark:bg-gray-800 shadow-sm rounded-xl p-6 space-y-4">
                <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-200">👀 User Details</h2>
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-4 text-sm text-gray-700 dark:text-gray-300">
                    <div><dt class="font-medium">Full Name</dt><dd>{{ props.user.name.full }}</dd></div>
                    <div><dt class="font-medium">Email</dt><dd>{{ props.user.email }}</dd></div>
                    <div><dt class="font-medium">Created At</dt><dd>{{ props.user.date }}</dd></div>
                    <div>
                        <dt class="font-medium">Role</dt>
                        <dd>
              <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold"
                    :class="roleBadgeClass(props.user.role)">
                {{ props.user.role }}
              </span>
                        </dd>
                    </div>
                    <div>
                        <dt class="font-medium">Status</dt>
                        <dd>
              <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold"
                    :class="statusBadgeClass(props.user.status)">
                {{ props.user.status }}
              </span>
                        </dd>
                    </div>
                </dl>
            </div>

            <!-- Networks -->
            <div class="bg-white dark:bg-gray-800 shadow-sm rounded-xl p-6">
                <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4">🌐 Connected Networks</h2>
                <table class="w-full table-auto text-sm text-left text-gray-700 dark:text-gray-200">
                    <thead class="bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-100">
                    <tr>
                        <th class="px-4 py-2">Network</th>
                        <th class="px-4 py-2">Identity</th>
                        <th class="px-4 py-2 text-right">Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="network in allNetworks" :key="network"
                        class="border-b border-gray-200 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700">
                        <td class="px-4 py-2 capitalize">{{ network }}</td>

                        <td class="px-4 py-2">
                            <span v-if="connectedNetworks[network]">
                              {{ connectedNetworks[network].identity }}
                            </span>
                            <span v-else class="italic text-gray-400 dark:text-gray-500">Not connected</span>
                        </td>

                        <td class="px-4 py-2 text-right">
                            <button
                                v-if="connectedNetworks[network]"
                                @click="unlink(network)"
                                class="bg-red-600 hover:bg-red-700 text-white px-4 py-1 rounded text-sm transition"
                            >
                                🔌 Unlink
                            </button>

                            <a
                                v-else
                                :href="`/auth/${network}`"
                                class="bg-green-600 hover:bg-green-700 text-white px-4 py-1 rounded text-sm transition"
                            >
                                🔗 Link
                            </a>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AppLayout>
</template>
