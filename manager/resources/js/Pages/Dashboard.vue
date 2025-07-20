<script setup>
  import AppLayout from '../Layouts/AppLayout.vue'
  import { usePage } from '@inertiajs/inertia-vue3'
  import { computed } from 'vue'

  const page = usePage()
  const user = computed(() => page.props.value.auth?.user)

  const props = defineProps({
    message: String,
  })
</script>

<template>
  <AppLayout>
    <section
      class="dark:bg-[#0e0f11] bg-gradient-to-br from-gray-900 to-indigo-900 text-white min-h-[70vh] p-6 sm:p-10 rounded-2xl shadow-md transition-all duration-300 ease-in-out max-w-7xl mx-auto"
    >
      <header class="mb-6">
        <h1 class="text-3xl font-bold text-indigo-200">📊 Панель Керування</h1>
        <p class="text-white/80 text-sm mt-1">
          Вітаємо, <span class="font-semibold text-white">{{ user?.name || 'Користувачу' }}</span
          >!
        </p>
      </header>

      <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-6">
        <div
          class="bg-gradient-to-tr from-gray-800 to-indigo-800 p-5 rounded-xl shadow-md hover:shadow-xl transition-all duration-300 border border-indigo-700"
        >
          <h2 class="text-lg font-semibold text-white/90 mb-2">✅ Завдань виконано</h2>
          <p class="text-white/70 text-sm">24 завершених задачі цього тижня</p>
        </div>

        <div
          class="bg-gradient-to-tr from-gray-800 to-indigo-800 p-5 rounded-xl shadow-md hover:shadow-xl transition-all duration-300 border border-indigo-700"
        >
          <h2 class="text-lg font-semibold text-white/90 mb-2">🔧 В процесі</h2>
          <p class="text-white/70 text-sm">12 задач активні зараз</p>
        </div>

        <div
          class="bg-gradient-to-tr from-gray-800 to-indigo-800 p-5 rounded-xl shadow-md hover:shadow-xl transition-all duration-300 border border-indigo-700"
        >
          <h2 class="text-lg font-semibold text-white/90 mb-2">🚧 Очікують</h2>
          <p class="text-white/70 text-sm">5 задач ще не стартували</p>
        </div>
      </div>

      <div class="mt-8">
        <button
          @click="$inertia.visit('/tasks')"
          class="bg-indigo-700 hover:bg-indigo-600 text-white px-6 py-2 rounded-xl shadow-sm transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-indigo-500"
        >
          Перейти до задач
        </button>
      </div>
    </section>
  </AppLayout>
</template>

<style scoped>
  /* 🖱️ Кастомний скрол для естетики */
  ::-webkit-scrollbar {
    width: 8px;
  }
  ::-webkit-scrollbar-track {
    background: #1f2937; /* gray-800 */
  }
  ::-webkit-scrollbar-thumb {
    background-color: #4f46e5; /* indigo-600 */
    border-radius: 4px;
  }
</style>
