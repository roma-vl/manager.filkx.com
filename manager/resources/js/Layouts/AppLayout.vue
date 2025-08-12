<script setup>
import { ref, computed, watch, onMounted, onBeforeUnmount } from 'vue'
import { Link, usePage } from '@inertiajs/inertia-vue3'
import SearchBar from '../Components/SearchBar.vue'
import NavItem from '../Components/NavItem.vue'
import UserDropdown2 from '../Components/UserDropdown2.vue'
import DarkModeToggle from '../Components/DarkModeToggle.vue'

const page = usePage()
const sidebarOpen = ref(window.innerWidth >= 1024)
const flash = computed(() => page.props.value.flash || {})
const roles = page.props.value.auth.roles
const canManageUsers = roles?.includes('ROLE_MANAGE_USERS')
const showFlash = ref(false)
import { useCentrifugo } from '@/services/useCentrifugo.js'
import { toast } from 'vue3-toastify'

const { init, subscribe, unsubscribe, disconnect } = useCentrifugo()

const privateMessages = ref([])
const userId = page.props.value.auth.user.id
onMounted(async () => {
  await init()

  subscribe('chat:general', {
    publication(ctx) {
      toast.info(ctx.data.text)
    },
  })

  subscribe(`user:${userId}`, {
    publication(ctx) {
      privateMessages.value.push(ctx.data)
      toast.info(ctx.data.text)
    },
  })
})

onBeforeUnmount(() => {
  unsubscribe('chat:general')
  unsubscribe(`user:${userId}`)
  disconnect()
})

watch(
  flash,
  newVal => {
    if (newVal.error || newVal.success) {
      showFlash.value = true
      setTimeout(() => (showFlash.value = false), 5000)
    }
  },
  {},
)
onMounted(() => {
  if (flash.value.success || flash.value.error) {
    showFlash.value = true
    setTimeout(() => (showFlash.value = false), 5000)
  }
})

const toggleSidebar = () => {
  sidebarOpen.value = !sidebarOpen.value
}

watch(
  () => page.url,
  () => {
    if (window.innerWidth < 1024) {
      sidebarOpen.value = false
    }
  },
)
</script>

<template>
  <div
    class="grid grid-rows-[auto_1fr] grid-cols-[auto_1fr] h-screen font-sans bg-[#f4f6f8] dark:bg-[#0e0f11] text-gray-900 dark:text-gray-100"
  >
    <aside
      class="bg-gradient-to-br from-indigo-900 to-indigo-700 text-white w-72 transition-all duration-500 ease-in-out col-start-1 row-span-2 dark:from-gray-900 dark:to-gray-800 z-20"
      :class="{ '-ml-72': !sidebarOpen, 'shadow-2xl': sidebarOpen }"
    >
      <div
        class="p-5 flex items-center justify-between border-b border-indigo-600 h-16 select-none"
      >
        <Link
          :href="'/'"
          class="font-extrabold text-xl tracking-widest text-white hover:text-indigo-300"
        >
          Filkx Task
        </Link>
        <button
          class="text-indigo-300 hover:text-white focus:outline-none lg:hidden"
          aria-label="Toggle sidebar"
          @click="toggleSidebar"
        >
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

      <nav class="p-4 space-y-6 overflow-y-auto h-[calc(100vh-64px)] custom-scroll">
        <div>
          <NavItem icon="dashboard" to="/dashboard" :active="$page.url.startsWith('/dashboard')">
            Dashboard
          </NavItem>
        </div>
        <div>
          <h3 class="px-3 mb-2 text-xs font-semibold uppercase tracking-wide text-indigo-200">
            Projects & Tasks
          </h3>
          <NavItem icon="folder" to="/work/projects" :active="$page.url === '/work/projects'">
            Projects
          </NavItem>
          <NavItem
            icon="check-circle"
            to="/work/projects/tasks"
            :active="$page.url.startsWith('/work/projects/tasks')"
          >
            Tasks
          </NavItem>
          <NavItem
            icon="users"
            to="/work/members/groups"
            :active="$page.url.startsWith('/work/members')"
          >
            Team
          </NavItem>
        </div>
        <div v-if="canManageUsers">
          <h3 class="px-3 mb-2 text-xs font-semibold uppercase tracking-wide text-indigo-200">
            Control Users
          </h3>
          <NavItem icon="folder" to="/users" :active="$page.url.startsWith('/users')">
            Users
          </NavItem>
        </div>
        <div>
          <h3 class="px-3 mb-2 text-xs font-semibold uppercase tracking-wide text-indigo-200">
            Settings
          </h3>
          <NavItem icon="cog" to="/settings" :active="$page.url.startsWith('/settings')">
            Settings
          </NavItem>
        </div>
      </nav>
    </aside>

    <!-- Header -->
    <header class="bg-white dark:bg-gray-900 shadow-md z-10 col-start-2 row-start-1">
      <div class="flex items-center justify-between h-16 px-6">
        <div class="flex items-center">
          <button
            class="text-gray-500 dark:text-gray-300 mr-4 focus:outline-none lg:hidden"
            aria-label="Toggle sidebar"
            @click="toggleSidebar"
          >
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
          <h2 class="text-lg font-semibold">{{ $page.props.title || 'Dashboard' }}</h2>
        </div>
        <div class="flex items-center space-x-4">
          <button class="text-gray-400 hover:text-gray-600 dark:hover:text-white">
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
          <DarkModeToggle />
          <UserDropdown2 />
        </div>
      </div>
    </header>

    <!-- Main -->
    <main class="col-start-2 row-start-2 overflow-y-auto p-6">
      <div class="max-w-7xl mx-auto">
        <Transition name="fade" mode="out-in">
          <div
            v-if="showFlash && flash.error"
            class="mb-4 p-3 rounded bg-red-500/10 text-red-700 dark:text-red-300"
          >
            {{ flash.error }}
          </div>
        </Transition>
        <Transition name="fade" mode="out-in">
          <div
            v-if="showFlash && flash.success"
            class="mb-4 p-3 rounded bg-green-500/10 text-green-700 dark:text-green-300"
          >
            {{ flash.success }}
          </div>
        </Transition>
        <div
          class="mx-auto p-3 rounded-lg bg-white text-gray-800 shadow-md shadow-gray-200/50 dark:bg-gradient-to-br dark:from-indigo-900 dark:via-gray-900 dark:to-[#0e0f11] dark:text-indigo-200 dark:shadow-indigo-900/40 transition-all duration-300 ease-in-out min-h-[400px]"
          role="main"
        >
          <slot />
        </div>
      </div>
    </main>

    <!-- Overlay -->
    <Transition name="fade">
      <div
        v-if="sidebarOpen"
        class="fixed inset-0 z-10 bg-black/50 lg:hidden"
        @click="sidebarOpen = false"
      />
    </Transition>
  </div>
</template>

<style>
  .fade-enter-active,
  .fade-leave-active {
    transition: opacity 0.2s ease;
  }
  .fade-enter-from,
  .fade-leave-to {
    opacity: 0;
  }
  .custom-scroll::-webkit-scrollbar {
    width: 8px;
  }
  .custom-scroll::-webkit-scrollbar-track {
    background: transparent;
  }
  .custom-scroll::-webkit-scrollbar-thumb {
    background: rgba(255, 255, 255, 0.2);
    border-radius: 4px;
  }
  .custom-scroll::-webkit-scrollbar-thumb:hover {
    background: rgba(255, 255, 255, 0.3);
  }
</style>
