import '../css/app.css'
import { createApp, h } from 'vue'
import { createInertiaApp } from '@inertiajs/inertia-vue3'
import axios from 'axios'
import Vue3Toastify from 'vue3-toastify'
import 'vue3-toastify/dist/index.css'

const originalFetch = window.fetch

window.fetch = (input, init = {}) => {
  init.credentials = 'include'
  return originalFetch(input, init)
}

axios.defaults.withCredentials = true
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest'
axios.defaults.headers.common['X-CSRF-TOKEN'] =
  document.querySelector('meta[name="csrf-token"]')?.content

const pages = import.meta.glob('./Pages/**/*.vue')

createInertiaApp({
  resolve: name => {
    const importPage = pages[`./Pages/${name}.vue`]
    if (!importPage) {
      throw new Error(`Unknown page ${name}`)
    }
    return importPage()
  },
  setup({ el, App, props, plugin }) {
    createApp({ render: () => h(App, props) })
      .use(plugin)
      .use(Vue3Toastify)
      .mount(el)
  },
})
