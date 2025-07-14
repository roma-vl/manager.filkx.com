<script setup>
  import AppLayout from '../Layouts/AppLayout.vue'

  const props = defineProps({
    message: String,
  })
</script>

<template>
  <AppLayout>
    <div class="bg-white shadow-lg rounded-xl p-6 border border-gray-200">
      <h1 class="text-2xl font-bold text-gray-800 mb-4">📊 Dashboard</h1>
      <div class="bg-green-500 text-white text-2xl p-4">Tailwind is Alive!</div>

      <div class="mt-6 grid grid-cols-2 gap-4">
        <div class="bg-green-100 text-green-800 p-4 rounded-lg text-center">✅ Task Completed</div>
        <div class="bg-blue-100 text-blue-800 p-4 rounded-lg text-center">🔧 In Progress</div>
      </div>

      <div class="mt-6">
        <button
          class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded transition"
          @click="$inertia.visit('/tasks')"
        >
          Перейти до задач
        </button>
      </div>
    </div>
  </AppLayout>
</template>
