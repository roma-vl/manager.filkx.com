<script setup>
import AppLayout from '../Layouts/AppLayout.vue'

import { usePage} from '@inertiajs/inertia-vue3'
import {computed} from 'vue'
import PageMeta from "@/Components/Seo/PageMeta.vue";

const page = usePage()

const user = computed(() => page.props.value.auth?.user)

</script>

<template>
  <AppLayout>
      <PageMeta
          title="Dashboard"
          description="Dashboard"
      />
    <h1>Привіт з Inertia + Vue 3 + Symfony </h1>
    <p>{{ user.name }}</p>
  </AppLayout>
</template>

<style></style>
