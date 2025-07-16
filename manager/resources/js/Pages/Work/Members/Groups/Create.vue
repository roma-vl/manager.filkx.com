<script setup>
import { Head, useForm, Link } from '@inertiajs/inertia-vue3'
import GroupsTabs from '@/Components/Work/Members/Groups/Tabs.vue'
import AppLayout from '../../../../Layouts/AppLayout.vue'
import InputLabel from "../../../../Components/InputLabel.vue";
import InputError from "../../../../Components/InputError.vue";
import TextInput from "../../../../Components/TextInput.vue";
import SecondaryButton from "../../../../Components/SecondaryButton.vue";

const form = useForm({
    name: '',
})

function submit() {
    form.post('/work/members/groups/create')
}
</script>

<template>
    <AppLayout>
        <Head title="Create Group" />

        <!-- Breadcrumbs -->
        <nav class="mb-6 text-sm text-gray-500" aria-label="breadcrumb">
            <ol class="flex space-x-2">
                <li>
                    <Link href="/" class="hover:text-blue-600">Home</Link>
                    <span class="mx-2 select-none">/</span>
                </li>
                <li>
                    <Link href="/work" class="hover:text-blue-600">Work</Link>
                    <span class="mx-2 select-none">/</span>
                </li>
                <li>
                    <Link href="/work/members" class="hover:text-blue-600">Members</Link>
                    <span class="mx-2 select-none">/</span>
                </li>
                <li>
                    <Link href="/work/members/groups" class="hover:text-blue-600">Groups</Link>
                    <span class="mx-2 select-none">/</span>
                </li>
                <li class="text-gray-700 font-semibold" aria-current="page">
                    Create
                </li>
            </ol>
        </nav>

        <GroupsTabs />

            <form @submit.prevent="submit" novalidate>
                <div>
                    <InputLabel
                        for="name"
                        :value="'Name'"
                        class="block text-sm font-medium text-gray-700"
                    />
                    <TextInput
                        id="name"
                        v-model="form.name"
                        type="text"
                        class="w-full mt-1 p-2 border-0 rounded-md focus:border-blue-500 focus:ring focus:ring-blue-200"
                        required
                        autocomplete="current-name"
                        autofocus
                    />
                    <InputError class="mt-2" :message="form.errors.name" />
                </div>

                <SecondaryButton
                    :disabled="form.processing"
                    type="submit" class="mt-4 float-right">
                    <span v-if="form.processing" class="animate-pulse">Creating...</span>
                    <span v-else>Create</span>
                </SecondaryButton>
            </form>
    </AppLayout>
</template>
