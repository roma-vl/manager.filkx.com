<script setup>
import { useForm } from '@inertiajs/inertia-vue3'
import { ref } from 'vue'
import AppLayout from "../../Layouts/AppLayout.vue";

const props = defineProps({
    user: Object,
    availableRoles: Object, // приклад: { ROLE_ADMIN: 'Admin', ROLE_USER: 'User' }
    flash: {
        type: Object,
        default: () => ({}),
    },
})

// ❗ Якщо ролей може бути лише одна — беремо першу з масиву
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
        <div class="card">
            <div class="card-body">
                <h3 class="text-lg font-semibold mb-4">Change role for {{ user.firstName }} {{ user.lastName }}</h3>

                <form @submit.prevent="submit">
                    <div class="mb-4">
                        <label for="role" class="block font-medium mb-1">Role</label>
                        <select
                            id="role"
                            v-model="form.role"
                            class="form-select w-full px-3 py-2 border rounded focus:outline-none focus:ring"
                        >
                            <option v-for="(label, value) in availableRoles" :key="value" :value="value">
                                {{ label }}
                            </option>
                        </select>
                        <div v-if="form.errors.role" class="text-danger text-sm mt-1">{{ form.errors.role }}</div>
                    </div>

                    <div v-if="flashError" class="alert alert-danger mt-2">{{ flashError }}</div>

                    <button type="submit" class="btn btn-primary">Save</button>
                </form>
            </div>
        </div>
    </AppLayout>
</template>

