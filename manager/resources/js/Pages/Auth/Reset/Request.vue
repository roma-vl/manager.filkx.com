<script setup>
import { useForm } from '@inertiajs/inertia-vue3'
import PageMeta from "@/Components/Seo/PageMeta.vue";

const form = useForm({
  email: '',
})

function submit() {
  form.post('/reset', {
    preserveScroll: true,
  })
}
</script>

<template>

    <PageMeta
        :title="`Password Reset`"
        :description="`Page Password Reset`"
    />
  <div class="max-w-md mx-auto mt-10 bg-white p-6 rounded shadow">
    <h1 class="text-xl font-bold mb-4">Password Reset</h1>

    <form @submit.prevent="submit">
      <div class="mb-4">
        <input
          v-model="form.email"
          type="email"
          placeholder="Email"
          class="w-full px-3 py-2 border rounded"
        />
        <p v-if="form.errors.email" class="text-red-600 text-sm">{{ form.errors.email }}</p>
      </div>

      <button
        type="submit"
        class="w-full bg-green-600 text-white py-2 rounded hover:bg-green-700"
        :disabled="form.processing"
      >
        Reset Password
      </button>
    </form>
  </div>
</template>
