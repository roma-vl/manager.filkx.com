<script setup>
import { computed } from 'vue'
import { usePage } from '@inertiajs/inertia-vue3'
import AppLayout from "../../Layouts/AppLayout.vue";

const page = usePage()
const user = computed(() => page.props.value.auth?.user)
console.log( usePage())
</script>

<template>
<AppLayout>
    <div class="space-y-6">
        <div class="bg-white p-6 rounded shadow">
            <h2 class="text-xl font-bold mb-4">Profile</h2>
            <table class="table-auto w-full border">
                <tbody>
                <tr>
                    <th class="text-left p-2 border">ID</th>
                    <td class="p-2 border">{{ user.id }}</td>
                </tr>
                <tr>
                    <th class="text-left p-2 border">Name</th>
                    <td class="p-2 border">{{ user.name }}</td>
                </tr>
                <tr>
                    <th class="text-left p-2 border">Email</th>
                    <td class="p-2 border">{{ user.email || '' }}</td>
                </tr>
                <tr>
                    <th class="text-left p-2 border">Created</th>
                    <td class="p-2 border">{{ user.created_at }}</td>
                </tr>
                <tr>
                    <th class="text-left p-2 border">Role</th>
                    <td class="p-2 border">{{ user.roles[0] }}</td>
                </tr>
                <tr>
                    <th class="text-left p-2 border">Status</th>
                    <td class="p-2 border">{{ user.status }}</td>
                </tr>
                </tbody>
            </table>
        </div>

        <div class="bg-white p-6 rounded shadow">
            <h2 class="text-xl font-bold mb-4">Networks</h2>
            <div v-if="user.networks?.length">
                <table class="table-auto w-full border mb-4">
                    <thead>
                    <tr>
                        <th class="text-left p-2 border">Network</th>
                        <th class="text-left p-2 border">Identity</th>
                        <th class="text-left p-2 border">Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="network in user.networks" :key="network.identity">
                        <td class="p-2 border">{{ network.network }}</td>
                        <td class="p-2 border">{{ network.identity }}</td>
                        <td class="p-2 border">
                            <form
                                :action="`/profile/oauth/${network.network}/${network.identity}`"
                                method="post"
                                @submit.prevent="detach(network)"
                            >
                                <input type="hidden" name="_method" value="DELETE" />
                                <input type="hidden" name="token" value="delete" />
                                <button
                                    type="submit"
                                    class="btn btn-sm bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded"
                                >
                                    Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <div>
                <a
                    href="/profile/oauth/facebook"
                    class="inline-block bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded"
                >
                    Attach Facebook
                </a>
            </div>
        </div>
    </div>
</AppLayout>
</template>
