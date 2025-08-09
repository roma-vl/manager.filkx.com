<script setup>
import { onMounted, ref, watch } from 'vue'
import axios from 'axios'
import AppLayout from '@/Layouts/AppLayout.vue'
import RolesTabs from '@/Components/Work/Projects/Project/Roles/RolesTabs.vue'
import Breadcrumbs from '@/Components/ui/Breadcrumbs.vue'
import PageMeta from "@/Components/Seo/PageMeta.vue";

const props = defineProps({
  dates: Array,
  now: String,
  result: Object,
  year: Number,
  month: Number,
  years: Array,
  next: String,
  prev: String,
})

const selectedYear = ref(props.year)
const selectedMonth = ref(props.month)
const prev = ref(props.prev)
const next = ref(props.next)
const dates = ref(props.dates)
const now = ref(props.now)
const result = ref(props.result)

function chunk(array, size) {
  const chunks = []
  for (let i = 0; i < array?.length; i += size) {
    chunks.push(array.slice(i, i + size))
  }
  return chunks
}

function formatDate(dateStr) {
  const date = new Date(dateStr)
  return date.getDate().toString().padStart(2, '0')
}

async function fetchCalendar(year, month) {
  try {
    const response = await axios.get('/work/projects/calendar', {
      params: { year, month },
      headers: { 'X-Requested-With': 'XMLHttpRequest' },
    })

    const data = response.data

    dates.value = data.dates
    now.value = data.now
    result.value = data.result

    // Оновлюємо prev і next з кореня відповіді
    prev.value = data.prev
    next.value = data.next

    selectedYear.value = year
    selectedMonth.value = month
  } catch (error) {
    console.error('Failed to load calendar data:', error)
  }
}

onMounted(() => {
  const urlParams = new URLSearchParams(window.location.search)
  const y = parseInt(urlParams.get('year'))
  const m = parseInt(urlParams.get('month'))
  if (y && m) {
    selectedYear.value = y
    selectedMonth.value = m
    fetchCalendar(y, m)
  } else {
    fetchCalendar(selectedYear.value, selectedMonth.value)
  }
})

watch([selectedYear, selectedMonth], ([newYear, newMonth], [oldYear, oldMonth]) => {
  if (newYear !== oldYear || newMonth !== oldMonth) {
    const newUrl = new URL(window.location.href)
    newUrl.searchParams.set('year', newYear)
    newUrl.searchParams.set('month', newMonth)
    window.history.pushState({}, '', newUrl.toString())

    fetchCalendar(newYear, newMonth)
  }
})

function goToPrev() {
  let y = selectedYear.value
  let m = selectedMonth.value - 1
  if (m < 1) {
    m = 12
    y -= 1
  }
  selectedYear.value = y
  selectedMonth.value = m
}

function goToNext() {
  let y = selectedYear.value
  let m = selectedMonth.value + 1
  if (m > 12) {
    m = 1
    y += 1
  }
  selectedYear.value = y
  selectedMonth.value = m
}

function goToNow() {
  const today = new Date()
  selectedYear.value = today.getFullYear()
  selectedMonth.value = today.getMonth() + 1
}
</script>

<template>
  <AppLayout>
    <div class="max-w-7xl mx-auto">
        <PageMeta
            :title="`Calendar`"
            :description="`Page Calendar`"
        />
      <Breadcrumbs
        :items="[
          { label: 'Home', href: '/' },
          { label: 'Work', href: '/work' },
          { label: 'Projects' },
        ]"
      />
      <RolesTabs />
      <div class="flex flex-wrap gap-3 items-center mb-6">
        <select
          v-model="selectedYear"
          class="border border-gray-300 rounded-md py-2 px-3 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-200"
          aria-label="Вибір року"
        >
          <option v-for="y in years" :key="y" :value="y">{{ y }}</option>
        </select>

        <select
          v-model="selectedMonth"
          class="border border-gray-300 rounded-md py-2 px-3 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-200"
          aria-label="Вибір місяця"
        >
          <option v-for="m in 12" :key="m" :value="m">{{ m }}</option>
        </select>

        <button
          class="btn btn-outline border border-blue-600 text-blue-600 hover:bg-blue-600 hover:text-white transition rounded-md px-4 py-2"
          @click="fetchCalendar(selectedYear, selectedMonth)"
        >
          Показати
        </button>

        <button
          class="btn btn-outline border border-gray-500 text-gray-700 hover:bg-gray-700 hover:text-white transition rounded-md px-4 py-2 dark:border-gray-400 dark:text-gray-300 dark:hover:bg-gray-600"
          @click="goToNow"
        >
          Зараз
        </button>

        <div class="ml-auto flex gap-2">
          <button
            class="btn btn-outline border border-gray-400 text-gray-600 hover:bg-gray-200 transition rounded-md px-3 py-1 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700"
            aria-label="Попередній місяць"
            @click="goToPrev"
          >
            ⬅
          </button>
          <button
            class="btn btn-outline border border-gray-400 text-gray-600 hover:bg-gray-200 transition rounded-md px-3 py-1 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700"
            aria-label="Наступний місяць"
            @click="goToNext"
          >
            ➡
          </button>
        </div>
      </div>

      <div class="overflow-auto rounded-lg border border-gray-300 dark:border-gray-700 shadow-sm">
        <table
          class="w-full table-fixed border-collapse text-center text-gray-900 dark:text-gray-100"
        >
          <tbody>
            <tr v-for="(week, i) in chunk(dates, 7)" :key="i" class="align-top">
              <td
                v-for="date in week"
                :key="date"
                :class="{
                  'bg-gray-100 dark:bg-gray-800': date === now,
                  'text-gray-400 dark:text-gray-500': date.slice(5, 7) !== result.month.slice(5, 7),
                  'border-r border-b border-gray-300 dark:border-gray-700': true,
                }"
                class="p-3 align-top min-w-[110px] max-w-[140px] h-36 md:h-40 lg:h-44 overflow-y-auto"
              >
                <div class="flex justify-between items-center mb-2">
                  <span
                    class="font-semibold text-md"
                    :class="{ 'text-blue-600 dark:text-blue-400': date === now }"
                  >
                    {{ formatDate(date) }}
                  </span>
                </div>

                <div class="flex flex-col gap-1">
                  <template v-for="item in result.items" :key="item.id">
                    <div
                      v-if="
                        [item.date, item.plan_date, item.start_date, item.end_date].includes(date)
                      "
                      class="bg-white dark:bg-gray-900 rounded-md p-3 shadow hover:shadow-lg transition cursor-pointer border border-gray-200 dark:border-gray-700"
                    >
                      <a
                        :href="`/work/projects/tasks/${item.id}`"
                        class="font-semibold text-gray-900 dark:text-white hover:text-blue-600 dark:hover:text-blue-400"
                      >
                        {{ item.name }}
                      </a>

                      <div class="flex flex-wrap gap-2 mt-2 text-xs">
                        <template v-if="item.date === date">
                          <span
                            class="inline-flex items-center gap-1 bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200 px-2 py-0.5 rounded-full"
                          >
                            📌 Створено
                          </span>
                        </template>

                        <template v-if="item.plan_date === date">
                          <span
                            class="inline-flex items-center gap-1 bg-indigo-100 dark:bg-indigo-900 text-indigo-800 dark:text-indigo-200 px-2 py-0.5 rounded-full"
                          >
                            🗓️ Дата завершення
                          </span>
                        </template>

                        <template v-if="item.start_date === date">
                          <span
                            class="inline-flex items-center gap-1 bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200 px-2 py-0.5 rounded-full"
                          >
                            ▶️ Початок
                          </span>
                        </template>

                        <template v-if="item.end_date === date">
                          <span
                            class="inline-flex items-center gap-1 bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200 px-2 py-0.5 rounded-full"
                          >
                            ⏹️ Завершення
                          </span>
                        </template>
                      </div>
                    </div>
                  </template>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </AppLayout>
</template>

<style scoped>
  /* додатково можна плавно сховати scrollbar для краси */
  td::-webkit-scrollbar {
    width: 6px;
  }
  td::-webkit-scrollbar-track {
    background: transparent;
  }
  td::-webkit-scrollbar-thumb {
    background-color: rgba(100, 100, 100, 0.3);
    border-radius: 3px;
  }
</style>
