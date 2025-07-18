<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import ProjectTabs from '@/Components/Work/Projects/ProjectTabs.vue'
import { usePage } from '@inertiajs/inertia-vue3'
import { Link } from '@inertiajs/inertia-vue3'
import RolesTabs from "../../../../Components/Work/Projects/Project/Roles/RolesTabs.vue";

const { props } = usePage()
const project = props.value.project
const roles = props.value.roles
const permissions = props.value.permissions
</script>

<template>
    <AppLayout>
        <!-- Хлібні крихти -->
        <nav class="text-sm text-muted-foreground mb-6" aria-label="Breadcrumb">
            <ol class="flex items-center space-x-2">
                <li><Link href="/" class="hover:text-primary">Home</Link><span>/</span></li>
                <li><Link href="/work" class="hover:text-primary">Work</Link><span>/</span></li>
                <li><Link href="/work/projects" class="hover:text-primary">Projects</Link><span>/</span></li>
                <li class="text-foreground font-semibold">Roles</li>
            </ol>
        </nav>

        <!-- Таби проєкту -->
        <RolesTabs />

        <!-- Кнопка додавання -->
        <div class="mb-4">
            <Link
                :href="`/work/projects/roles/create`"
                class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-xl shadow"
            >
                ➕ Add Role
            </Link>
        </div>

        <!-- Таблиця ролей -->
        <div class="bg-white dark:bg-gray-900 shadow-sm rounded-2xl overflow-x-auto p-4">
            <table class="min-w-full text-sm text-left border-collapse">
                <thead>
                <tr>
                    <th class="py-2 px-4 text-gray-700 dark:text-gray-300 font-semibold">Permission</th>
                    <template v-for="role in roles" :key="role.id">
                        <th class="py-2 px-4 text-center text-gray-700 dark:text-gray-300 font-semibold">
                            <Link
                                :href="`/work/projects/roles/${role.id}`"
                                class="hover:underline text-blue-600 dark:text-blue-400"
                            >
                                {{ role.name }}
                            </Link>
                            ({{ role.memberships_count }})
                        </th>
                    </template>
                </tr>
                </thead>
                <tbody>
                <tr v-for="permission in permissions" :key="permission" class="border-t border-gray-200 dark:border-gray-700">
                    <td class="py-2 px-4 text-gray-800 dark:text-gray-200">{{ permission }}</td>
                    <template v-for="role in roles" :key="role.id">
                        <td class="py-2 px-4 text-center">
                                <span
                                    v-if="role.permissions.includes(permission)"
                                    class="text-green-600 dark:text-green-400"
                                >
                                    ✔️
                                </span>
                        </td>
                    </template>
                </tr>
                </tbody>
            </table>
        </div>
    </AppLayout>
</template>
