<script setup>
  import { ref, onMounted } from 'vue'
  import axios from 'axios'

  const dates = ref([])
  const now = ref('')
  const items = ref([])

  onMounted(async () => {
    const { data } = await axios.get('/api/widgets/work/projects/calendar')
    dates.value = data.dates
    now.value = data.now
    items.value = data.items
  })

  function iconsForItem(item, date) {
    const icons = []

    if (item.date === date) icons.push({ icon: '➕', title: 'Create' })
    if (item.plan_date === date) icons.push({ icon: '📅', title: 'Plan' })
    if (item.start_date === date) icons.push({ icon: '▶️', title: 'Start' })
    if (item.end_date === date) icons.push({ icon: '⏹️', title: 'End' })

    return icons
  }
</script>

<template>
  <div
    class="dark:bg-[#0e0f11] bg-gradient-to-br from-gray-900 to-indigo-900 text-white mt-6 rounded-2xl shadow-md transition-all duration-300 ease-in-out max-w-7xl mx-auto"
  >
    <div class="px-6 py-4 border-b dark:border-gray-700 flex items-center justify-between">
      <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-200">📆 Task Calendar</h2>
      <span class="text-sm text-gray-500 dark:text-gray-400"> *</span>
    </div>

    <div class="overflow-x-auto">
      <table
        class="min-w-full table-fixed text-sm text-gray-700 dark:text-gray-300 border-collapse"
      >
        <thead>
          <tr>
            <th
              v-for="date in dates"
              :key="date"
              class="text-center px-2 py-2 border-b font-medium whitespace-nowrap w-[150px]"
            >
              <span
                :class="{
                  'text-red-600 font-bold': date === now,
                  'text-gray-600 dark:text-gray-400': date !== now,
                }"
              >
                {{ new Date(date).getDate() }}
              </span>
            </th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td v-for="date in dates" :key="date" class="align-top px-2 py-2">
              <div class="space-y-2">
                <div
                  v-for="item in items"
                  :key="`task-${item.id}-${date}`"
                  class="min-w-[110px] max-w-[140px]"
                >
                  <div
                    v-if="iconsForItem(item, date).length"
                    class="border border-gray-300 dark:border-gray-700 rounded-lg p-2 bg-gray-50 dark:bg-gray-800 shadow-sm hover:shadow-md transition"
                  >
                    <div
                      class="flex items-center justify-between text-xs text-gray-500 dark:text-gray-400 mb-1"
                    >
                      <a class="hover:underline" :href="`/work/projects/tasks/${item.id}`">
                        #{{ item.id }}
                      </a>
                      <span class="flex gap-1">
                        <span
                          v-for="icon in iconsForItem(item, date)"
                          :key="icon.title"
                          :title="icon.title"
                        >
                          {{ icon.icon }}
                        </span>
                      </span>
                    </div>
                    <div class="text-sm font-medium text-gray-800 dark:text-gray-100 truncate">
                      <a class="hover:underline" :href="`/work/projects/tasks/${item.id}`">
                        {{ item.name }}
                      </a>
                    </div>
                  </div>
                </div>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>
