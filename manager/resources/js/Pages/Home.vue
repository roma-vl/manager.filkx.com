<script setup>
import AppLayout from '../Layouts/AppLayout.vue'

import { usePage } from '@inertiajs/inertia-vue3'
import {computed} from 'vue'

const page = usePage()

const user = computed(() => page.props.value.auth?.user)

</script>

<template>
  <AppLayout>
    <h1>Привіт з Inertia + Vue 3 + Symfony </h1>
    <p>{{ user.name }}</p>
  </AppLayout>
</template>

<style></style>
