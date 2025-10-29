import { createApp } from 'vue'
import App from './App.vue'
import './assets/styles.css' // Tailwind entry file

//const app = createApp(App)
createApp(App).mount('#contact-us-app')
// Mount only if the #contact-app element exists (WordPress-safe)
//const mountEl = document.getElementById('contact-app')
//if (mountEl) app.mount(mountEl)

