<template>
  <div class="relative ml-4">
    <button @click="open = !open" class="flex items-center space-x-2 focus:outline-none group">
<!--      <div class="relative">-->
<!--        <img-->
<!--          class="h-8 w-8 rounded-full object-cover"-->
<!--          :src="$page.props.auth.user.avatar || '/images/default-avatar.png'"-->
<!--          alt="User avatar"-->
<!--        />-->
<!--        <span-->
<!--          class="absolute bottom-0 right-0 block h-2 w-2 rounded-full bg-green-400 ring-2 ring-white"-->
<!--        ></span>-->
<!--      </div>-->
      <span class="hidden md:inline-flex items-center">
        <span class="text-sm font-medium text-gray-700 group-hover:text-indigo-600">
          {{ $page.props.auth.user.name }}
        </span>
        <ChevronDownIcon class="ml-1 h-4 w-4 text-gray-500 group-hover:text-indigo-600" />
      </span>
    </button>

    <Transition
      enter-active-class="transition ease-out duration-100"
      enter-from-class="transform opacity-0 scale-95"
      enter-to-class="transform opacity-100 scale-100"
      leave-active-class="transition ease-in duration-75"
      leave-from-class="transform opacity-100 scale-100"
      leave-to-class="transform opacity-0 scale-95"
    >
      <div
        v-show="open"
        @click.away="open = false"
        class="absolute right-0 mt-2 w-56 origin-top-right rounded-md bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none z-50"
      >
        <div class="py-1">
          <div class="px-4 py-2 border-b">
            <p class="text-sm font-medium text-gray-900 truncate">
              {{ $page.props.auth.user.name }}
            </p>
            <p class="text-xs text-gray-500 truncate">{{ $page.props.auth.user.email }}</p>
          </div>

          <Link
            href="/profile"
            class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900 w-full text-left"
          >
            <UserIcon class="h-5 w-5 mr-3 text-gray-400" />
            Profile
          </Link>

          <Link
            href="/settings"
            class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900 w-full text-left"
          >
            <CogIcon class="h-5 w-5 mr-3 text-gray-400" />
            Settings
          </Link>

          <div class="border-t border-gray-100"></div>

          <Link
            href="/logout"
            class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900 w-full text-left"
          >
            <LogoutIcon class="h-5 w-5 mr-3 text-gray-400" />
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
  import {
    ChevronDownIcon,
    UserIcon,
    Cog6ToothIcon as CogIcon,
    ArrowLeftOnRectangleIcon as LogoutIcon,
  } from '@heroicons/vue/24/outline'

  const open = ref(false)
</script>
