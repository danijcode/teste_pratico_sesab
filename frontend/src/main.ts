import { createApp } from 'vue'
import App from './App.vue'
import router from './router'
import vuetify from './plugins/vuetify'
import axios from 'axios'
import 'vuetify/styles'
import '@mdi/font/css/materialdesignicons.css'

axios.defaults.baseURL = import.meta.env.VITE_API_URL ?? 'http://localhost:8000'

createApp(App)
  .use(router)
  .use(vuetify)
  .mount('#app')
