<script setup>
import { useForm } from '@inertiajs/inertia-vue3'
import { ref } from 'vue'
import AppLayout from "../../Layouts/AppLayout.vue";

const props = defineProps({
    user: Object,
    flash: Object,
})

const form = useForm({
    email: props.user.email,
    firstName: props.user.firstName,
    lastName: props.user.lastName,
})

const flashError = ref(props.flash?.error ?? '')

function submit() {
    flashError.value = ''
    form.post(`/users/${props.user.id}/edit`, {
        onError: (errors) => {
            if (errors.errors) {
                flashError.value = Object.values(errors.errors).join('; ')
            }
        },
        preserveScroll: true,
    })
}
</script>

<template>
<AppLayout>
    <div class="card">
        <div class="card-body">
            <form @submit.prevent="submit">
                <div class="mb-3">
                    <label for="email">Email</label>
                    <input v-model="form.email" type="email" id="email" class="form-control" />
                    <div v-if="form.errors.email" class="text-danger">{{ form.errors.email }}</div>
                </div>

                <div class="mb-3">
                    <label for="firstName">First Name</label>
                    <input v-model="form.firstName" type="text" id="firstName" class="form-control" />
                    <div v-if="form.errors.firstName" class="text-danger">{{ form.errors.firstName }}</div>
                </div>

                <div class="mb-3">
                    <label for="lastName">Last Name</label>
                    <input v-model="form.lastName" type="text" id="lastName" class="form-control" />
                    <div v-if="form.errors.lastName" class="text-danger">{{ form.errors.lastName }}</div>
                </div>

                <div v-if="flashError" class="alert alert-danger mt-2">{{ flashError }}</div>

                <button type="submit" class="btn btn-primary">Save</button>
            </form>
        </div>
    </div>
</AppLayout>
</template>
