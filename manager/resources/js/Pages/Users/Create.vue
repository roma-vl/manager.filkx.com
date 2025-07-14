<script setup>
import { reactive, ref } from 'vue';
import {useForm} from '@inertiajs/inertia-vue3'
import AppLayout from "../../Layouts/AppLayout.vue";
import InputLabel from "../../Components/InputLabel.vue";
import InputError from "../../Components/InputError.vue";
import TextInput from "../../Components/TextInput.vue";
import SecondaryButton from "../../Components/SecondaryButton.vue";
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
        preserveScroll: true,
        onError: (errs) => {
            console.log(errs, 'error');

            if (errs.errors) {
                flashError.value = errs.errors.join('; ');
            }
        },
    });
}

</script>


<template>
<AppLayout>
    <div class="card">
    <div class="card-body">
        <form @submit.prevent="submit">
            <div>
                <InputLabel
                    for="email"
                    :value="'Email'"
                    class="block text-sm font-medium text-gray-700 "
                />
                <TextInput
                    id="email"
                    v-model="form.email"
                    type="text"
                    class="w-full mt-1 p-2 border-0 rounded-md focus:border-blue-500 focus:ring focus:ring-blue-200"
                    required
                    autocomplete="current-email"
                    autofocus
                />
                <InputError
                    class="mt-2"
                    :message="form.errors.email"
                />
            </div>

            <div>
                <InputLabel
                    for="firstName"
                    :value="'First Name'"
                    class="block text-sm font-medium text-gray-700"
                />
                <TextInput
                    id="firstName"
                    v-model="form.firstName"
                    type="text"
                    class="w-full mt-1 p-2 border-0 rounded-md focus:border-blue-500 focus:ring focus:ring-blue-200"
                    required
                    autocomplete="current-firstName"
                    autofocus
                />
                <InputError
                    class="mt-2"
                    :message="form.errors.firstName"
                />
            </div>

            <div>
                <InputLabel
                    for="lastName"
                    :value="'Last Name'"
                    class="block text-sm font-medium text-gray-700"
                />
                <TextInput
                    id="lastName"
                    v-model="form.lastName"
                    type="text"
                    class="w-full mt-1 p-2 border-0 rounded-md focus:border-blue-500 focus:ring focus:ring-blue-200"
                    required
                    autocomplete="current-lastName"
                    autofocus
                />
                <InputError
                    class="mt-2"
                    :message="form.errors.lastName"
                />
            </div>
            <SecondaryButton type="submit" class="mt-4 float-right">
                Create
            </SecondaryButton>

        </form>
    </div>
</div>
</AppLayout>
</template>

