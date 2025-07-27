<script lang="ts">
import { useToast } from 'vue-toastification'

const toast = useToast()

subscribe('chat:general', {
    publication(ctx) {
        toast.info(ctx.data.text, {
            timeout: 3000,
            closeOnClick: true,
            pauseOnFocusLoss: true,
            pauseOnHover: true,
        })
    },
})

subscribe(`user:${userId}`, {
    publication(ctx) {
        toast.success(`🔥 Приватне повідомлення: ${ctx.data.text}`, {
            timeout: 5000,
            closeOnClick: true,
        })
    },
})

</script>

<template>

</template>

<style scoped>

</style>
