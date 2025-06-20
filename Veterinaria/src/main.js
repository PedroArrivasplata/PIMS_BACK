import './assets/styles_css/main.css'

import { createApp } from 'vue'
import { createPinia } from 'pinia'

import App from './App.vue'
import router from './router'

// Importar los estilos globales
import './assets/styles_css/bootstrap.min.css'; 
import './assets/styles_css/iconos.css';
import './assets/styles_css/styles.css'; 
import './assets/styles_css/styles_local.css';

const app = createApp(App)

app.use(createPinia())
app.use(router)

app.mount('#app')
