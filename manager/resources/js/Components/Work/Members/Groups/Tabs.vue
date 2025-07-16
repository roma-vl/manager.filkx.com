<!-- resources/js/Components/Work/Members/Groups/Tabs.vue -->
<template>
    <div class="tabs ">
        <Link
            href="/work/members"
            class="tab"
            :class="{
                active: $page.url.startsWith('/work/members') && !$page.url.startsWith('/work/members/groups')
            }"
        >
            Members
        </Link>
        <Link
            href="/work/members/groups"
            class="tab"
            :class="{ active: $page.url.startsWith('/work/members/groups') }"
        >
            Groups
        </Link>
    </div>
</template>


<script setup>
import { Link } from '@inertiajs/inertia-vue3'
</script>

<style scoped>
.tab {
    padding: 0.5rem 1rem;
    text-decoration: none;
}
.active {
    font-weight: bold;
    border-bottom: 2px solid #3490dc;
}
</style>
