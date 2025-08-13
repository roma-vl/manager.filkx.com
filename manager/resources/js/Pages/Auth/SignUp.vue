<script setup>
import { useForm, usePage } from '@inertiajs/inertia-vue3'
import { computed, ref, watch } from 'vue'
import PageMeta from '@/Components/Seo/PageMeta.vue'

const page = usePage()
const flash = computed(() => page.props.value.flash || {})
const showFlash = ref(false)
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
  { immediate: true },
)

const form = useForm({
  firstName: '',
  lastName: '',
  email: '',
  password: '',
})

function submit() {
  form.post('/signup', {
    preserveScroll: true,
    onError: () => {
      form.password = ''
    },
  })
}
</script>

<template>
  <PageMeta :title="`Sign Up`" :description="`Page Sign Up`" />
  <div class="min-h-screen flex items-center justify-center bg-gray-100">
    <div class="max-w-md w-full bg-white p-8 rounded shadow">
      <h1 class="text-2xl font-bold mb-6">Sign Up</h1>
      <p class="text-gray-600 mb-6">Create your account</p>

      <div v-if="showFlash && flash.error" class="mb-4 p-3 bg-red-100 text-red-700 rounded">
        {{ flash.error }}
      </div>

      <div v-if="showFlash && flash.success" class="mb-4 p-3 bg-green-100 text-green-700 rounded">
        {{ flash.success }}
      </div>

      <form novalidate @submit.prevent="submit">
        <!--                <input type="hidden" name="_csrf_token" :value="form._csrf_token" />-->

        <label class="block mb-2 font-semibold" for="firstName">First Name</label>
        <input
          id="firstName"
          v-model="form.firstName"
          type="text"
          required
          placeholder="First Name"
          class="w-full mb-4 px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-indigo-600"
        />

        <div v-if="form.errors.firstName" class="text-red-600 text-sm mb-2">
          {{ form.errors.firstName }}
        </div>

        <label class="block mb-2 font-semibold" for="lastName">Last Name</label>
        <input
          id="lastName"
          v-model="form.lastName"
          type="text"
          required
          placeholder="Last Name"
          class="w-full mb-4 px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-indigo-600"
        />

        <div v-if="form.errors.lastName" class="text-red-600 text-sm mb-4">
          {{ form.errors.lastName }}
        </div>

        <label class="block mb-2 font-semibold" for="email">Email</label>
        <input
          id="email"
          v-model="form.email"
          type="email"
          required
          placeholder="Email"
          class="w-full mb-4 px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-indigo-600"
        />

        <div v-if="form.errors.email" class="text-red-600 text-sm mb-4">
          {{ form.errors.email }}
        </div>

        <label class="block mb-2 font-semibold" for="password">Password</label>
        <input
          id="password"
          v-model="form.password"
          type="password"
          required
          placeholder="Password"
          class="w-full mb-6 px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-indigo-600"
        />
        <div v-if="form.errors.password" class="text-red-600 text-sm mb-4">
          {{ form.errors.password }}
        </div>

        <button
          type="submit"
          :disabled="form.processing"
          class="w-full bg-green-600 hover:bg-green-700 text-white py-2 rounded font-semibold transition"
        >
          Create Account
        </button>
      </form>

        <div class="flex items-center my-6">
            <div class="flex-grow border-t border-gray-300"></div>
            <span class="mx-4 text-gray-500 font-medium">OR</span>
            <div class="flex-grow border-t border-gray-300"></div>
        </div>

        <a
            href="/login"
            class="block w-full text-center py-2 bg-green-600 text-white rounded hover:bg-green-700 transition font-semibold shadow-sm"
        >
            Login
        </a>
    </div>
  </div>
</template>
