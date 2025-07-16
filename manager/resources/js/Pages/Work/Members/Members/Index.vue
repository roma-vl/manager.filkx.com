<script setup>
import { ref, watch, computed } from 'vue'
import { Head, Link } from '@inertiajs/inertia-vue3'
import GroupsTabs from '@/Components/Work/Members/Groups/Tabs.vue'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({
    members: Array,
    filters: Object,
    groups: Array,
    statuses: Array,
    sort: String,
    direction: String,
    pagination: Object,
})

const name = ref(props.filters.name || '')
const email = ref(props.filters.email || '')
const group = ref(props.filters.group || '')
const status = ref(props.filters.status || '')
const sort = ref(props.sort || 'name')
const direction = ref(props.direction || 'asc')

function toggleSort(field) {
    if (sort.value === field) {
        direction.value = direction.value === 'asc' ? 'desc' : 'asc'
    } else {
        sort.value = field
        direction.value = 'asc'
    }
    submitFilters()
}

function submitFilters(page = 1) {
    const query = new URLSearchParams({
        name: name.value,
        email: email.value,
        group: group.value,
        status: status.value,
        sort: sort.value,
        direction: direction.value,
        page: page,
    }).toString()

    window.location.href = `/work/members?${query}`
}

function resetFilters() {
    name.value = ''
    email.value = ''
    group.value = ''
    status.value = ''
    submitFilters()
}

function paginationLink(page) {
    const query = new URLSearchParams({
        name: name.value,
        email: email.value,
        group: group.value,
        status: status.value,
        sort: sort.value,
        direction: direction.value,
        page,
    }).toString()

    return `/work/members?${query}`
}

const paginationRange = computed(() => {
    const current = props.pagination.currentPage
    const last = props.pagination.lastPage
    const delta = 2
    const range = []

    for (let i = Math.max(1, current - delta); i <= Math.min(last, current + delta); i++) {
        range.push(i)
    }

    const result = []
    if (range[0] > 1) {
        result.push(1)
        if (range[0] > 2) result.push('...')
    }

    result.push(...range)

    if (range[range.length - 1] < last) {
        if (range[range.length - 1] < last - 1) result.push('...')
        result.push(last)
    }

    return result
})
</script>

<template>
    <AppLayout>
        <Head title="Members" />

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

                <li class="text-gray-700 font-semibold">Members</li>
            </ol>
        </nav>

        <GroupsTabs />

        <!-- Filters -->
        <form @submit.prevent="submitFilters()" class="bg-white p-4 rounded shadow mb-6 grid grid-cols-1 md:grid-cols-5 gap-4 mt-6">
            <input v-model="name" placeholder="Name" class="form-input" />
            <input v-model="email" placeholder="Email" class="form-input" />
            <select v-model="group" class="form-select">
                <option value="">All Groups</option>
                <option v-for="g in groups" :key="g.id" :value="g.id">{{ g.name }}</option>
            </select>
            <select v-model="status" class="form-select">
                <option value="">All Statuses</option>
                <option v-for="s in statuses" :key="s.id" :value="s.id">{{ s.name }}</option>
            </select>
            <div class="flex gap-2">
                <button type="submit" class="btn btn-primary w-full">Filter</button>
                <button type="button" @click="resetFilters" class="btn btn-outline w-full">Reset</button>
            </div>
        </form>

        <!-- Members Table -->
        <div class="overflow-x-auto bg-white rounded-lg shadow border border-gray-200">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50 text-left text-xs font-semibold text-gray-500">
                <tr>
                    <th @click="toggleSort('name')" class="px-6 py-3 cursor-pointer">Name <span v-if="sort === 'name'">{{ direction === 'asc' ? '↑' : '↓' }}</span></th>
                    <th @click="toggleSort('email')" class="px-6 py-3 cursor-pointer">Email <span v-if="sort === 'email'">{{ direction === 'asc' ? '↑' : '↓' }}</span></th>
                    <th class="px-6 py-3">Group</th>
                    <th class="px-6 py-3 text-center">Projects</th>
                    <th class="px-6 py-3">Status</th>
                </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-sm">
                <tr v-for="member in members" :key="member.id" class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4 text-blue-600 font-medium">
                        <Link :href="`/work/members/${member.id}`">{{ member.name }}</Link>
                    </td>
                    <td class="px-6 py-4">{{ member.email }}</td>
                    <td class="px-6 py-4">{{ member.group }}</td>
                    <td class="px-6 py-4 text-center">{{ member.memberships_count }}</td>
                    <td class="px-6 py-4">{{ member.status }}</td>
                </tr>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div v-if="pagination.lastPage > 1" class="mt-6 flex justify-center space-x-1">
            <Link v-if="pagination.currentPage > 1" :href="paginationLink(pagination.currentPage - 1)" class="px-3 py-1 border rounded hover:bg-gray-100">← Prev</Link>

            <template v-for="page in paginationRange" :key="page">
                <span v-if="page === '...'" class="px-3 py-1 text-gray-500">…</span>
                <Link
                    v-else
                    :href="paginationLink(page)"
                    class="px-3 py-1 border rounded hover:bg-gray-100"
                    :class="{ 'bg-blue-600 text-white': page === pagination.currentPage }"
                >
                    {{ page }}
                </Link>
            </template>

            <Link v-if="pagination.currentPage < pagination.lastPage" :href="paginationLink(pagination.currentPage + 1)" class="px-3 py-1 border rounded hover:bg-gray-100">Next →</Link>
        </div>
    </AppLayout>
</template>

<style scoped>
.form-input,
.form-select {
    @apply border border-gray-300 rounded px-2 py-1 w-full;
}
.btn {
    @apply px-3 py-1 rounded;
}
.btn-primary {
    @apply bg-blue-600 text-white hover:bg-blue-700;
}
.btn-outline {
    @apply border border-gray-400 text-gray-700 hover:bg-gray-100;
}
</style>
