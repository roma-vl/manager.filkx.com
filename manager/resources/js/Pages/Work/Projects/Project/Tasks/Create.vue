
<script setup>
import { reactive, ref } from 'vue';
import { useRouter } from 'vue-router'; // або Inertia router
import { Inertia } from '@inertiajs/inertia';
import AppLayout from "../../../../../Layouts/AppLayout.vue";
import Breadcrumbs from "../../../../../Components/ui/Breadcrumbs.vue";
import ProjectTabs from "../../../../../Components/Work/Projects/ProjectTabs.vue";

const props = defineProps({
    project: Object,
    defaults: Object,
    types: Array,
    priorities: Array,
});

const router = useRouter();
const error = ref(null);

const form = reactive({
    names: props.defaults.names.length ? [...props.defaults.names] : [''],
    content: props.defaults.content ?? '',
    parent: props.defaults.parent ?? null,
    plan: props.defaults.plan ?? '',
    type: props.defaults.type,
    priority: props.defaults.priority,
});

function addName() {
    form.names.push('');
}

function removeName(index) {
    form.names.splice(index, 1);
}

function submit() {
    error.value = null;

    // Валідація мінімальна: перевірка, що є хоч одна назва
    if (!form.names.some(name => name.trim().length > 0)) {
        error.value = 'Мінімум одна назва потрібна';
        return;
    }

    // Відправляємо POST через Inertia
    Inertia.post(
        `/work/projects/${props.project.id}/tasks/create`,
        {
            names: form.names,
            content: form.content,
            parent: form.parent,
            plan: form.plan,
            type: form.type,
            priority: form.priority,
        },
        {
            onSuccess: () => {
                router.push({ name: 'work.projects.project.tasks', params: { project_id: props.project.id } });
            },
            onError: (errors) => {
                error.value = errors.error || 'Сталася помилка';
            },
        }
    );
}
</script>

<template>
    <AppLayout>
        <Breadcrumbs :items="[
              { label: 'Home', href: '/' },
              { label: 'Work', href: '/work' },
              { label: project.name, href: `/work/projects/${project.id}` },
              { label: 'Tasks', href: `/work/projects/${project.id}/tasks` },
              { label: 'Create' }
            ]" />

        <ProjectTabs :project-id="project.id" />
        <form @submit.prevent="submit" class="max-w-3xl mx-auto space-y-6 bg-gradient-to-br from-indigo-950 via-gray-900 to-[#0e0f11] p-6 rounded-lg shadow-lg shadow-indigo-900/40 text-indigo-200">

            <div>
                <label class="block mb-1 text-sm font-medium text-indigo-300">Назви задач (назви можуть бути декілька):</label>
                <div v-for="(name, index) in form.names" :key="index" class="flex items-center space-x-2 mb-2">
                    <input
                        v-model="form.names[index]"
                        type="text"
                        required
                        aria-label="Назва задачі"
                        class=" p-2 flex-1 rounded-md border border-indigo-800 bg-gray-950 text-sm text-indigo-100 placeholder-indigo-400 shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-600 transition-colors"
                        placeholder="Назва задачі"
                    />
                    <button
                        type="button"
                        @click="removeName(index)"
                        aria-label="Видалити назву"
                        class="text-xs text-red-400 hover:text-red-300 transition-colors"
                    >
                        ✕
                    </button>
                </div>
                <button
                    type="button"
                    @click="addName"
                    class="text-sm px-3 py-1.5 rounded-md bg-indigo-800 hover:bg-indigo-700 text-white transition-colors"
                >
                    ➕ Додати назву
                </button>
            </div>

            <div>
                <label class="block mb-1 text-sm font-medium text-indigo-300">Опис</label>
                <textarea
                    v-model="form.content"
                    rows="6"
                    aria-label="Опис задачі"
                    class=" p-2 w-full rounded-md border border-indigo-800 bg-gray-950 text-sm text-indigo-100 placeholder-indigo-400 shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-600 transition-colors"
                    placeholder="Опис задачі..."
                />
            </div>

            <div>
                <label class="block mb-1 text-sm font-medium text-indigo-300">Батьківська задача (ID)</label>
                <input
                    v-model.number="form.parent"
                    type="number"
                    min="0"
                    aria-label="ID батьківської задачі"
                    class=" p-2 w-full rounded-md border border-indigo-800 bg-gray-950 text-sm text-indigo-100 placeholder-indigo-400 shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-600 transition-colors"
                    placeholder="0"
                />
            </div>

            <div>
                <label class="block mb-1 text-sm font-medium text-indigo-300">Планова дата</label>
                <input
                    v-model="form.plan"
                    type="date"
                    aria-label="Планова дата"
                    class=" p-2 w-full rounded-md border border-indigo-800 bg-gray-950 text-sm text-indigo-100 shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-600 transition-colors"
                />
            </div>

            <div>
                <label class="block mb-1 text-sm font-medium text-indigo-300">Тип</label>
                <select
                    v-model="form.type"
                    aria-label="Тип задачі"
                    class=" p-2 w-full rounded-md border border-indigo-800 bg-gray-950 text-sm text-indigo-100 shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-600 transition-colors"
                >
                    <option
                        v-for="t in types"
                        :key="t.value"
                        :value="t.value"
                        class="bg-gray-950 text-indigo-100"
                    >
                        {{ t.label }}
                    </option>
                </select>
            </div>

            <div>
                <label class="block mb-1 text-sm font-medium text-indigo-300">Пріоритет</label>
                <select
                    v-model.number="form.priority"
                    aria-label="Пріоритет задачі"
                    class=" p-2 w-full rounded-md border border-indigo-800 bg-gray-950 text-sm text-indigo-100 shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-600 transition-colors"
                >
                    <option
                        v-for="p in priorities"
                        :key="p.value"
                        :value="p.value"
                        class="bg-gray-950 text-indigo-100"
                    >
                        {{ p.label }}
                    </option>
                </select>
            </div>

            <div>
                <button
                    type="submit"
                    class="w-full text-sm font-semibold px-4 py-2 rounded-md bg-indigo-700 hover:bg-indigo-600 text-white shadow-md transition-colors focus:outline-none focus:ring-2 focus:ring-indigo-400"
                >
                    🚀 Створити задачу
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
