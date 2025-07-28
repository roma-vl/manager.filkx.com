<script setup>
  import AppLayout from '../Layouts/AppLayout.vue'

  import { usePage } from '@inertiajs/inertia-vue3'
  import { computed } from '../../../assets/vendor/@vue/reactivity/reactivity.index.js'

  const page = usePage()

  const user = computed(() => page.props.value.auth?.user)

</script>

<template>
  <AppLayout>
    <h1>Привіт з Inertia + Vue 3 + Symfony</h1>
  </AppLayout>
</template>

<style></style>
