<script setup>
import { useForm, usePage } from '@inertiajs/inertia-vue3'

const page = usePage()
const props = defineProps({
    token: String
})

const form = useForm({
    password: '',
})

function submit() {
    form.post(`/reset/${props.token}`)
}


console.log(page, 'page')
</script>

<template>
    <div class="max-w-md mx-auto mt-10 bg-white p-6 rounded shadow">
        <h1 class="text-xl font-bold mb-4">Set New Password</h1>

        <form @submit.prevent="submit">
            <div class="mb-4">
                <input
                    v-model="form.password"
                    type="password"
                    placeholder="New Password"
                    class="w-full px-3 py-2 border rounded"
                />
                <p v-if="form.errors.password" class="text-red-600 text-sm">{{ form.errors.password }}</p>
            </div>

            <button
                type="submit"
                class="w-full bg-green-600 text-white py-2 rounded hover:bg-green-700"
                :disabled="form.processing"
            >
                Set Password
            </button>
        </form>
    </div>
</template>
