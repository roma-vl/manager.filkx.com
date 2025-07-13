<template>
  <div class="grid grid-rows-[auto_1fr] grid-cols-[auto_1fr] h-screen font-sans bg-gray-50">
    <!-- Sidebar -->
    <aside
      class="bg-indigo-800 text-white w-64 transition-all duration-300 ease-in-out col-start-1 row-span-2"
      :class="{ '-ml-64': !sidebarOpen, 'shadow-xl': sidebarOpen }"
    >
      <div class="p-4 flex items-center justify-between border-b border-indigo-700 h-16">
        <h1 class="text-xl font-bold truncate">TaskFlow Pro</h1>
        <button @click="toggleSidebar" class="text-indigo-200 hover:text-white focus:outline-none">
          <svg
            xmlns="http://www.w3.org/2000/svg"
            class="h-6 w-6"
            fill="none"
            viewBox="0 0 24 24"
            stroke="currentColor"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M4 6h16M4 12h16M4 18h16"
            />
          </svg>
        </button>
      </div>

      <nav class="p-4 space-y-2">
        <NavItem icon="dashboard" to="/dashboard" :active="$page.url.startsWith('/dashboard')">
          Dashboard
        </NavItem>
        <NavItem icon="folder" to="/projects" :active="$page.url.startsWith('/projects')">
          Projects
        </NavItem>
        <NavItem icon="check-circle" to="/tasks" :active="$page.url.startsWith('/tasks')">
          Tasks
        </NavItem>
        <NavItem icon="users" to="/team" :active="$page.url.startsWith('/team')"> Team </NavItem>
        <NavItem icon="cog" to="/settings" :active="$page.url.startsWith('/settings')">
          Settings
        </NavItem>
      </nav>

      <div class="absolute bottom-0 w-full p-4 border-t border-indigo-700">
        <UserDropdown />
      </div>
    </aside>

    <!-- Header -->
    <header class="bg-white shadow-sm z-10 col-start-2 row-start-1">
      <div class="flex items-center justify-between h-16 px-6">
        <div class="flex items-center">
          <button @click="toggleSidebar" class="text-gray-500 mr-4 focus:outline-none lg:hidden">
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-6 w-6"
              fill="none"
              viewBox="0 0 24 24"
              stroke="currentColor"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M4 6h16M4 12h16M4 18h16"
              />
            </svg>
          </button>
          <h2 class="text-lg font-medium text-gray-900">{{ $page.props.title || 'Dashboard' }}</h2>
        </div>

        <div class="flex items-center space-x-4">
          <button class="p-1 text-gray-400 hover:text-gray-500 focus:outline-none">
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-6 w-6"
              fill="none"
              viewBox="0 0 24 24"
              stroke="currentColor"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"
              />
            </svg>
          </button>
          <SearchBar />
          <UserDropdown2 />
        </div>
      </div>
    </header>

    <!-- Main Content -->
    <main class="col-start-2 row-start-2 overflow-y-auto p-6 bg-gray-50">
      <Transition name="fade" mode="out-in">
        <div class="max-w-7xl mx-auto">
          <div v-if="showFlash && flash.error" class="mb-4 p-3 bg-red-100 text-red-700 rounded">
            {{ flash.error }}
          </div>

          <div
            v-if="showFlash && flash.success"
            class="mb-4 p-3 bg-green-100 text-green-700 rounded"
          >
            {{ flash.success }}
          </div>
          <slot />
        </div>
      </Transition>
    </main>

    <!-- Mobile sidebar backdrop -->
    <Transition name="fade">
      <div
        v-if="sidebarOpen"
        @click="sidebarOpen = false"
        class="fixed inset-0 z-20 bg-black bg-opacity-50 lg:hidden"
      ></div>
    </Transition>
  </div>
</template>

<script setup>
  import { ref, computed, watch } from 'vue'
  import { usePage } from '@inertiajs/inertia-vue3'
  import SearchBar from '../Components/SearchBar.vue'
  import UserDropdown from '../Components/UserDropdown.vue'
  import NavItem from '../Components/NavItem.vue'
  import UserDropdown2 from '../Components/UserDropdown2.vue'
  const page = usePage()
  const sidebarOpen = ref(window.innerWidth >= 1024)
  const flash = computed(() => page.props.value.flash || {})
  const showFlash = ref(false)
  // Показуємо flash-повідомлення на 5 секунд
  watch(
    flash,
    newVal => {
      if (newVal.error || newVal.success) {
        showFlash.value = true
        setTimeout(() => {
          showFlash.value = false
        }, 5000)
      }
    },
    {}
  )
  const toggleSidebar = () => {
    sidebarOpen.value = !sidebarOpen.value
  }

  // Close sidebar on mobile when route changes

  watch(
    () => page.url,
    () => {
      if (window.innerWidth < 1024) {
        sidebarOpen.value = false
      }
    }
  )
</script>

<style>
  .fade-enter-active,
  .fade-leave-active {
    transition: opacity 0.15s ease;
  }

  .fade-enter-from,
  .fade-leave-to {
    opacity: 0;
  }

  /* Smooth scrolling */
  html {
    scroll-behavior: smooth;
  }

  /* Custom scrollbar */
  ::-webkit-scrollbar {
    width: 8px;
    height: 8px;
  }

  ::-webkit-scrollbar-track {
    background: #f1f1f1;
  }

  ::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 4px;
  }

  ::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
  }
</style>

<style>
  .grid-areas-layout {
    grid-template-areas:
      'header header'
      'sidebar content';
  }
</style>
