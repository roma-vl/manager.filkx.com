<script setup>
import { computed, reactive, watchEffect } from 'vue'
import { useForm, usePage } from '@inertiajs/inertia-vue3'
import AppLayout from '../../Layouts/AppLayout.vue'
import {roleBadgeClass, statusBadgeClass} from '@/Helpers/helpers.js'

const page = usePage()
const user = computed(() => page.props.value.auth?.user)

const editing = reactive({
  name: false,
  email: false,
})

const form = useForm({
  first: '',
  last: '',
  email: '',
})

watchEffect(() => {
  if (user.value) {
    form.first = user.value.first_name
    form.last = user.value.last_name
    form.email = user.value.email
  }
})

function startEdit(field) {
  editing[field] = true
}

function cancelEdit(field) {
  if (field === 'name') {
    form.first = user.value.first_name
    form.last = user.value.last_name
  } else {
    form[field] = user[field]
  }
  editing[field] = false
}

function saveField(field) {
  if (field === 'name') {
    form.post('/profile/name', {
      preserveScroll: true,
      onSuccess: () => {
        editing.name = false
      },
    })
  } else if (field === 'email') {
    form.post('/profile/email', {
      preserveScroll: true,
      onSuccess: () => {
        editing.email = false
      },
    })
  }
}
</script>

<template>
  <AppLayout>
    <div class="space-y-6">
      <div v-if="user" class="bg-white p-6 rounded shadow">
        <h2 class="text-xl font-bold mb-4">Profile</h2>
        <table class="table-auto w-full border">
          <tbody>
            <tr>
              <th class="text-left p-2 border">ID</th>
              <td class="p-2 border">{{ user.id }}</td>
            </tr>
            <tr>
              <th class="text-left p-2 border">Name</th>
              <td class="p-2 border flex items-center gap-2">
                <template v-if="editing.name">
                  <input
                    v-model="form.first"
                    class="border p-1 rounded w-full"
                    placeholder="First Name"
                  />
                  <p v-if="form.errors.first" class="text-red-600 text-sm">
                    {{ form.errors.first }}
                  </p>
                  <input
                    v-model="form.last"
                    class="border p-1 rounded w-full"
                    placeholder="Last Name"
                  />
                  <p v-if="form.errors.last" class="text-red-600 text-sm">{{ form.errors.last }}</p>
                  <button class="text-green-600 hover:text-green-800" @click="saveField('name')">
                    💾
                  </button>
                  <button class="text-gray-600 hover:text-gray-800" @click="cancelEdit('name')">
                    ✖
                  </button>
                </template>
                <template v-else>
                  <span class="flex-1">{{ user.first_name }} {{ user.last_name }}</span>
                  <button class="text-blue-600 hover:text-blue-800" @click="startEdit('name')">
                    ✏️
                  </button>
                </template>
              </td>
            </tr>
            <tr>
              <th class="text-left p-2 border">Email</th>
              <td class="p-2 border flex items-center gap-2">
                <template v-if="editing.email">
                  <input v-model="user.email" class="border p-1 rounded w-full" />
                  <p v-if="form.errors.email" class="text-red-600 text-sm">
                    {{ form.errors.email }}
                  </p>
                  <button class="text-green-600 hover:text-green-800" @click="saveField('email')">
                    💾
                  </button>
                  <button class="text-gray-600 hover:text-gray-800" @click="cancelEdit('email')">
                    ✖
                  </button>
                </template>
                <template v-else>
                  <span class="flex-1">{{ user.email }}</span>
                  <button class="text-blue-600 hover:text-blue-800" @click="startEdit('email')">
                    ✏️
                  </button>
                </template>
              </td>
            </tr>

            <tr>
              <th class="text-left p-2 border">Created</th>
              <td class="p-2 border">{{ user.created_at }}</td>
            </tr>
            <tr>
              <th class="text-left p-2 border">Role</th>
              <td class="p-2 border">
                <span
                  class="inline-block px-3 py-1 rounded-full text-sm font-semibold"
                  :class="roleBadgeClass(user.roles[0])"
                >
                  {{ user.roles[0] }}
                </span>
              </td>
            </tr>
            <tr>
              <th class="text-left p-2 border">Status</th>
              <td class="p-2 border">
                <span
                  class="inline-block px-3 py-1 rounded-full text-sm font-semibold"
                  :class="statusBadgeClass(user.status)"
                >
                  {{ user.status }}
                </span>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <div class="bg-white p-6 rounded shadow">
        <h2 class="text-xl font-bold mb-4">Networks</h2>
        <div v-if="user?.networks?.length">
          <table class="table-auto w-full border mb-4">
            <thead>
              <tr>
                <th class="text-left p-2 border">Network</th>
                <th class="text-left p-2 border">Identity</th>
                <th class="text-left p-2 border">Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="network in user.networks" :key="network.identity">
                <td class="p-2 border">{{ network.network }}</td>
                <td class="p-2 border">{{ network.identity }}</td>
                <td class="p-2 border">
                  <form
                    :action="`/profile/oauth/${network.network}/${network.identity}`"
                    method="post"
                    @submit.prevent="detach(network)"
                  >
                    <input type="hidden" name="_method" value="DELETE" />
                    <input type="hidden" name="token" value="delete" />
                    <button
                      type="submit"
                      class="btn btn-sm bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded"
                    >
                      Delete
                    </button>
                  </form>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        <div>
          <a
            href="/profile/oauth/facebook"
            class="inline-block bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded"
          >
            Attach Facebook
          </a>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
