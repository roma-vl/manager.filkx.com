<script setup>
  import axios from 'axios'

  import { useForm, usePage } from '@inertiajs/inertia-vue3'
  import { computed, ref, watch } from 'vue'

  const page = usePage()
  const flash = computed(() => page.props.value.flash || {})
  const showFlash = ref(false)
  const user = computed(() => page.props.value.auth?.user)
  const permissions = computed(() => page.props.value.auth?.permissions || [])
  const props = defineProps({
    lastUsername: String,
    csrfToken: String,
  })

  console.log(user.value)
  console.log(page, 'page')
  console.log(flash.value)
  console.log(props.error)

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
    { immediate: true }
  )

  const form = useForm({
    _csrf_token: props.csrfToken,
    email: props.lastUsername || '',
    password: '',
    _remember_me: false,
  })
  function submit() {
    form.post('/login', {
      preserveScroll: true,
      onError: () => {
        // Очищаємо пароль при помилці
        form._password = ''
      },
    })
  }
</script>

<template>
  <div class="min-h-screen flex items-center justify-center bg-gray-100">
    <div class="max-w-md w-full bg-white p-8 rounded shadow">
      <h1 class="text-2xl font-bold mb-6">Log In</h1>

      <div v-if="showFlash && flash.error" class="mb-4 p-3 bg-red-100 text-red-700 rounded">
        {{ flash.error }}
      </div>

      <div v-if="showFlash && flash.success" class="mb-4 p-3 bg-green-100 text-green-700 rounded">
        {{ flash.success }}
      </div>

      <form @submit.prevent="submit" novalidate>
        <!--        <input type="hidden" name="_csrf_token" :value="form._csrf_token" />-->

        <label class="block mb-2 font-semibold" for="email">Email</label>
        <input
          id="email"
          type="email"
          v-model="form.email"
          required
          class="w-full mb-4 px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-indigo-600"
          placeholder="Email"
        />

        <label class="block mb-2 font-semibold" for="password">Password</label>
        <input
          id="password"
          type="password"
          v-model="form.password"
          required
          class="w-full mb-4 px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-indigo-600"
          placeholder="Password"
        />

        <div class="mb-4 flex items-center">
          <input id="remember_me" type="checkbox" v-model="form._remember_me" class="mr-2" />
          <label for="remember_me" class="text-sm text-gray-700">Remember me</label>
        </div>

        <button
          type="submit"
          :disabled="form.processing"
          class="w-full bg-indigo-600 hover:bg-indigo-700 text-white py-2 rounded font-semibold transition"
        >
          Login
        </button>
      </form>

      <div class="mt-4 flex justify-between text-sm">
        <a href="/reset" class="text-indigo-600 hover:underline">Forgot password?</a>
        <a href="/signup" class="text-indigo-600 hover:underline">Register Now!</a>
      </div>

      <div class="mt-6 space-y-2">
        <a
          href="/oauth/facebook"
          class="block text-center py-2 bg-blue-600 text-white rounded hover:bg-blue-700"
        >
          Login with Facebook
        </a>
        <a
          href="/oauth/google"
          class="block text-center py-2 bg-red-600 text-white rounded hover:bg-red-700"
        >
          Login with Google
        </a>
      </div>
    </div>
  </div>
</template>
