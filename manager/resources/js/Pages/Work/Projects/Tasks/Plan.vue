<script setup>
import { reactive, ref } from 'vue'
import { useRouter } from 'vue-router'
import { Inertia } from '@inertiajs/inertia'
import AppLayout from "../../../../Layouts/AppLayout.vue"
import Breadcrumbs from "../../../../Components/ui/Breadcrumbs.vue"
import Datepicker from '@vuepic/vue-datepicker'
import '@vuepic/vue-datepicker/dist/main.css'

const props = defineProps({
    task: Object,
})

const router = useRouter()
const error = ref(null)

const form = reactive({
    plan_date: props.task.plan_date ? new Date(props.task.plan_date) : null,
})

function submit() {
    error.value = null

    Inertia.post(
        `/work/projects/tasks/${props.task.id}/plan`,
        {
            plan_date: form.plan_date?.toISOString(), // 💡 передаємо ISO-формат для Symfony
        },
        {
            onSuccess: () => {
                router.push({ name: 'work.projects.project.tasks', params: { project_id: props.project?.id } })
            },
            onError: (errors) => {
                error.value = errors.error || 'Сталася помилка'
            },
        }
    )
}
</script>


<template>
    <AppLayout>
        <Breadcrumbs :items="[
          { label: 'Home', href: '/' },
          { label: 'Work', href: '/work' },
          { label: 'Tasks', href: '/work/projects/tasks' },
          { label: task.name, href: `/work/projects/tasks/${task.id}` },
          { label: 'Edit Plan' }
        ]" />

        <form
            @submit.prevent="submit"
            class="max-w-3xl mx-auto space-y-6 bg-gradient-to-br from-indigo-950 via-gray-900 to-[#0e0f11] p-6 rounded-lg shadow-lg shadow-indigo-900/40 text-indigo-200"
        >
            <div>
                <label class="block mb-1 text-sm font-medium text-indigo-300">Plan date & time:</label>
                <Datepicker
                    v-model="form.plan_date"
                    :enable-time-picker="true"
                    :is-24="true"
                    :teleport="true"
                    :format="'yyyy-MM-dd HH:mm'"
                    input-class-name="p-2 w-full rounded-md border border-indigo-800 bg-gray-950 text-sm text-indigo-100 placeholder-indigo-400 shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-600 transition-colors"
                    placeholder="Виберіть дату та час"
                />
            </div>

            <div>
                <button
                    type="submit"
                    class="w-full text-sm font-semibold px-4 py-2 rounded-md bg-indigo-700 hover:bg-indigo-600 text-white shadow-md transition-colors focus:outline-none focus:ring-2 focus:ring-indigo-400"
                >
                    Set plan
                </button>
                <p v-if="error" class="mt-2 text-sm text-red-400">{{ error }}</p>
            </div>
        </form>
    </AppLayout>
</template>




<style scoped>
.error {
    color: red;
}
</style>
