<script setup>
  import { computed, reactive, ref, watchEffect } from 'vue'
  import { useForm, usePage } from '@inertiajs/inertia-vue3'
  import AppLayout from '../../Layouts/AppLayout.vue'

  const page = usePage()
  const user = computed(() => page.props.value.auth?.user)

  const props = defineProps({
    user: Object,
  })

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
    if (props.user) {
      form.first = props.user.first_name
      form.last = props.user.last_name
      form.email = props.user.email
    }
  })

  function startEdit(field) {
    editing[field] = true
  }

  function cancelEdit(field) {
    if (field === 'name') {
      form.first = props.user.first_name
      form.last = props.user.last_name
    } else {
      form[field] = props.user[field]
    }
    editing[field] = false
  }

  function saveField(field) {
    if (field === 'name') {
      console.log(form, 'form')
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

  const roleBadgeClass = role => {
    switch (role.toLowerCase()) {
      case 'admin':
        return 'bg-red-600 text-white'
      case 'moderator':
        return 'bg-yellow-500 text-black'
      case 'user':
        return 'bg-green-600 text-white'
      default:
        return 'bg-gray-400 text-white'
    }
  }

  const statusBadgeClass = status => {
    switch (status.toLowerCase()) {
      case 'active':
        return 'bg-green-500 text-white'
      case 'inactive':
        return 'bg-gray-400 text-white'
      case 'banned':
        return 'bg-red-700 text-white'
      default:
        return 'bg-blue-500 text-white'
    }
  }
</script>

<template>
  <AppLayout>
    <div class="space-y-6">
      <div class="bg-white p-6 rounded shadow" v-if="props.user">
        <h2 class="text-xl font-bold mb-4">Profile</h2>
        <table class="table-auto w-full border">
          <tbody>
            <tr>
              <th class="text-left p-2 border">ID</th>
              <td class="p-2 border">{{ props.user.id }}</td>
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
                  <button @click="saveField('name')" class="text-green-600 hover:text-green-800">
                    💾
                  </button>
                  <button @click="cancelEdit('name')" class="text-gray-600 hover:text-gray-800">
                    ✖
                  </button>
                </template>
                <template v-else>
                  <span class="flex-1">{{ props.user.first_name }} {{ props.user.last_name }}</span>
                  <button @click="startEdit('name')" class="text-blue-600 hover:text-blue-800">
                    ✏️
                  </button>
                </template>
              </td>
            </tr>
            <tr>
              <th class="text-left p-2 border">Email</th>
              <td class="p-2 border flex items-center gap-2">
                <template v-if="editing.email">
                  <input v-model="props.user.email" class="border p-1 rounded w-full" />
                  <p v-if="form.errors.email" class="text-red-600 text-sm">
                    {{ form.errors.email }}
                  </p>
                  <button @click="saveField('email')" class="text-green-600 hover:text-green-800">
                    💾
                  </button>
                  <button @click="cancelEdit('email')" class="text-gray-600 hover:text-gray-800">
                    ✖
                  </button>
                </template>
                <template v-else>
                  <span class="flex-1">{{ user.email }}</span>
                  <button @click="startEdit('email')" class="text-blue-600 hover:text-blue-800">
                    ✏️
                  </button>
                </template>
              </td>
            </tr>

            <tr>
              <th class="text-left p-2 border">Created</th>
              <td class="p-2 border">{{ props.user.created_at }}</td>
            </tr>
            <tr>
              <th class="text-left p-2 border">Role</th>
              <td class="p-2 border">
                <span
                  class="inline-block px-3 py-1 rounded-full text-sm font-semibold"
                  :class="roleBadgeClass(props.user.roles[0])"
                >
                  {{ props.user.roles[0] }}
                </span>
              </td>
            </tr>
            <tr>
              <th class="text-left p-2 border">Status</th>
              <td class="p-2 border">
                <span
                  class="inline-block px-3 py-1 rounded-full text-sm font-semibold"
                  :class="statusBadgeClass(props.user.status)"
                >
                  {{ props.user.status }}
                </span>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <div class="bg-white p-6 rounded shadow">
        <h2 class="text-xl font-bold mb-4">Networks</h2>
        <div v-if="props.user?.networks?.length">
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
