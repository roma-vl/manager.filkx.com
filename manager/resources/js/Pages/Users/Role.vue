<script setup>
import { useForm } from '@inertiajs/inertia-vue3'
import { ref } from 'vue'
import AppLayout from "@/Layouts/AppLayout.vue"
import SelectField from '@/Components/SelectField.vue'
import InputError from "../../Components/InputError.vue";
import InputLabel from "../../Components/InputLabel.vue";
import SecondaryButton from "../../Components/SecondaryButton.vue";

const props = defineProps({
    user: Object,
    availableRoles: Object,
    flash: {
        type: Object,
        default: () => ({}),
    },
})

console.log(props, 'aaassas')
const form = useForm({
    role: props.user.roles[0] ?? '',
})

const flashError = ref(props.flash?.error ?? '')

function submit() {
    flashError.value = ''
    form.post(`/users/${props.user.id}/role`, {
        onError: (errors) => {
            flashError.value = Object.values(errors.errors ?? {}).join('; ')
        },
        preserveScroll: true,
    })
}
</script>

<template>
    <AppLayout>
        <div class="max-w-xl mx-auto mt-10 bg-white dark:bg-gray-800 p-6 rounded shadow">
            <h3 class="text-xl font-semibold mb-4 text-gray-800 dark:text-white">
                <span class="text-gray-500">Change role for:</span> {{ user.firstName }} {{ user.lastName }}
            </h3>

            <form @submit.prevent="submit" class="space-y-4">
                <div>
                    <InputLabel
                        for="role"
                        :value="'Role'"
                        class="block text-sm font-medium text-gray-700"
                    />
                    <SelectField
                        id="role"
                        name="role"
                        v-model="form.role"
                        :options="availableRoles"
                        required
                        autofocus
                    />
                    <InputError
                        class="mt-2"
                        :message="form.errors.role"
                    />
                </div>

                <div class="flex justify-end">
                    <SecondaryButton type="submit" >
                        Change role
                    </SecondaryButton>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
