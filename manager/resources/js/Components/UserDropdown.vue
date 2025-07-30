<template>
  <div class="relative">
    <button class="flex items-center space-x-2 focus:outline-none" @click="open = !open">
      <img class="h-8 w-8 rounded-full" :src="$page.props.auth.user.avatar" alt="User avatar" />
      <span class="text-sm font-medium text-white truncate">{{ $page.props.auth.user.name }}</span>
      <svg
        xmlns="http://www.w3.org/2000/svg"
        class="h-4 w-4 text-indigo-200"
        viewBox="0 0 20 20"
        fill="currentColor"
      >
        <path
          fill-rule="evenodd"
          d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
          clip-rule="evenodd"
        />
      </svg>
    </button>

    <Transition name="dropdown">
      <div
        v-show="open"
        class="absolute bottom-full left-0 w-48 mb-2 bg-white rounded-md shadow-lg z-50"
        @click="open = false"
      >
        <div class="py-1">
          <Link href="/profile" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
            Your Profile
          </Link>
          <Link href="/settings" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
            Settings
          </Link>
          <Link
            href="/logout"
            method="post"
            as="button"
            class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
          >
            Sign out
          </Link>
        </div>
      </div>
    </Transition>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { Link } from '@inertiajs/inertia-vue3'

const open = ref(false)
</script>

<style>
  .dropdown-enter-active,
  .dropdown-leave-active {
    transition: all 0.15s ease;
    transform-origin: top center;
  }

  .dropdown-enter-from,
  .dropdown-leave-to {
    opacity: 0;
    transform: scale(0.95) translateY(-10px);
  }
</style>
