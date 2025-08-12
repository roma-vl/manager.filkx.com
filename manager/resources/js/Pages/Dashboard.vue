<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { usePage } from '@inertiajs/inertia-vue3'
import { computed } from 'vue'
import CalendarWidget from '@/Components/Widgets/CalendarWidget.vue'
import OwnTasksWidget from '@/Components/Widgets/OwnTasksWidget.vue'
import MeTasksWidget from '@/Components/Widgets/MeTasksWidget.vue'
import PageMeta from '@/Components/Seo/PageMeta.vue'

const page = usePage()
const user = computed(() => page.props.value.auth?.user)
</script>

<template>
  <AppLayout>
    <PageMeta title="Dashboard" description="Dashboard" />
    <section
      class="text-white min-h-[70vh] p-6 sm:p-10 transition-all duration-300 ease-in-out max-w-7xl mx-auto"
    >
      <header class="mb-6">
        <h1 class="text-xl font-bold text-indigo-200">📊 Панель Керування</h1>
        <p class="text-white/80 text-sm mt-1">
          Вітаємо, <span class="font-semibold text-white">{{ user?.name || 'Користувачу' }}</span>!
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

      <CalendarWidget />

      <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-2 gap-6">
        <OwnTasksWidget />
        <MeTasksWidget />
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
