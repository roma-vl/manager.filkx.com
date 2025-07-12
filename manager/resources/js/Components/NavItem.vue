<template>
    <Link :href="to" class="flex items-center p-3 rounded-lg transition-colors"
          :class="active ? 'bg-indigo-700 text-white' : 'text-indigo-200 hover:bg-indigo-700 hover:text-white'">
    <span class="mr-3">
      <slot name="icon">
        <component :is="iconComponent" class="h-5 w-5" />
      </slot>
    </span>
        <span class="truncate"><slot /></span>
    </Link>
</template>

<script setup>
import { computed } from 'vue'
import {Link} from "@inertiajs/inertia-vue3";


const props = defineProps({
    to: {
        type: String,
        required: true
    },
    icon: {
        type: String,
        default: 'folder'
    },
    active: {
        type: Boolean,
        default: false
    }
})

const iconComponent = computed(() => {
    const icons = {
        dashboard: 'DashboardIcon',
        folder: 'FolderIcon',
        'check-circle': 'CheckCircleIcon',
        users: 'UsersIcon',
        cog: 'CogIcon'
    }
    return icons[props.icon] || 'FolderIcon'
})
</script>
