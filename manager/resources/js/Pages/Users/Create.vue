<script setup>
import { reactive, ref } from 'vue';
import {useForm} from '@inertiajs/inertia-vue3'
import AppLayout from "../../Layouts/AppLayout.vue";
const props = defineProps({
    flash: {
        type: Object,
        default: () => ({ error: [] }),
    },
});

const form = useForm({
    email: '',
    firstName: '',
    lastName: '',
});


const errors = reactive({});

const flashError = ref(props.flash?.error?.length ? props.flash.error[0] : '');


function submit() {
    errors.email = null;
    errors.firstName = null;
    errors.lastName = null;
    flashError.value = '';

    form.post('/users/create', form, {
        onError: (errs) => {
            // Очікуємо, що backend поверне масив помилок (422)
            if (errs.errors) {
                // Тут можна розпарсити і розподілити по полях, якщо API так робить
                // Інакше просто покажемо всі помилки загалом
                flashError.value = errs.errors.join('; ');
            }
        },
    });
}
</script>


<template>
<AppLayout>     <div class="card">
    <div class="card-body">
        <form @submit.prevent="submit">
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input v-model="form.email" type="email" id="email" class="form-control" />
                <div v-if="errors.email" class="text-danger">{{ errors.email }}</div>
            </div>

            <div class="mb-3">
                <label for="firstName" class="form-label">First Name</label>
                <input v-model="form.firstName" type="text" id="firstName" class="form-control" />
                <div v-if="errors.firstName" class="text-danger">{{ errors.firstName }}</div>
            </div>

            <div class="mb-3">
                <label for="lastName" class="form-label">Last Name</label>
                <input v-model="form.lastName" type="text" id="lastName" class="form-control" />
                <div v-if="errors.lastName" class="text-danger">{{ errors.lastName }}</div>
            </div>

            <button type="submit" class="btn btn-primary">Create</button>

            <div v-if="flashError" class="alert alert-danger mt-3">{{ flashError }}</div>
        </form>
    </div>
</div>
</AppLayout>
</template>

