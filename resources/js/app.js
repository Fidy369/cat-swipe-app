// resources/js/app.js
import { createApp } from 'vue'
import App from './components/App.vue'
import Vue3TouchEvents from 'vue3-touch-events'

createApp(App)
  .use(Vue3TouchEvents)
  .mount('#app')
