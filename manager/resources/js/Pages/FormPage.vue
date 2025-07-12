
<template>
    <form @submit.prevent="submit">
        <input v-model="form.email" type="text" placeholder="Email" />
        <p v-if="errors?.email" style="color: red">{{ errors.email }}</p>
        <button type="submit">Надіслати</button>
    </form>
</template>

<script setup>
import { Inertia } from '@inertiajs/inertia'
import { ref } from 'vue'

const props = defineProps({ errors: Object })
const form = ref({ email: '' })

function submit() {
    Inertia.post('/submit', form.value)
}
</script>
