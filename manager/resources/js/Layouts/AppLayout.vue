<template>
    <div class="grid grid-rows-[60px_1fr] grid-cols-[250px_1fr] grid-areas-layout h-screen font-sans">
        <header class="bg-gray-900 text-gray-200 flex items-center justify-between px-4" style="grid-area: header;">
            <h1 class="text-xl font-semibold">Task Manager</h1>
            <nav>
                <button
                    @click="$inertia.visit('/dashboard')"
                    class="text-gray-200 ml-4 text-base hover:text-teal-400 focus:outline-none"
                >
                    Dashboard
                </button>
                <button
                    @click="$inertia.visit('/projects')"
                    class="text-gray-200 ml-4 text-base hover:text-teal-400 focus:outline-none"
                >
                    Projects
                </button>
                <button
                    @click="$inertia.visit('/tasks')"
                    class="text-gray-200 ml-4 text-base hover:text-teal-400 focus:outline-none"
                >
                    Tasks
                </button>
                <button
                    @click="$inertia.visit('/profile')"
                    class="text-gray-200 ml-4 text-base hover:text-teal-400 focus:outline-none"
                >
                    Profile
                </button>
            </nav>
        </header>

        <aside
            class="bg-gray-800 text-gray-400 p-4"
            style="grid-area: sidebar;"
        >
            <p class="font-semibold mb-2">Фільтри</p>
            <!-- Тут можна додати фільтри, меню -->
        </aside>

        <main
            class="p-6 overflow-y-auto bg-gray-50"
            style="grid-area: content;"
        >
            <slot />
        </main>
    </div>
</template>

<script setup>
// Порожній, бо логіки немає
</script>

<style>
.grid-areas-layout {
    grid-template-areas:
    "header header"
    "sidebar content";
}
</style>
