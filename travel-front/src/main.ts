import { createApp } from 'vue'
import App from './App.vue'
import router from './router'
import './assets/css/main.css'
import { createPinia } from 'pinia'

// Bootstrap Icons
import 'bootstrap-icons/font/bootstrap-icons.css'

// Font Awesome
import { library } from '@fortawesome/fontawesome-svg-core'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'

// Solid Icons
import {
  faUserSecret,
  faThumbsUp,
  faHouse,
  faPhone,
  faSearch,
} from '@fortawesome/free-solid-svg-icons'

// Regular Icons
import { faCircleUser } from '@fortawesome/free-regular-svg-icons'

// Brands Icons
import { faFacebook } from '@fortawesome/free-brands-svg-icons'

// Add icons to library
library.add(
  faUserSecret,
  faThumbsUp,
  faHouse,
  faPhone,
  faSearch,
  faCircleUser,
  faFacebook,
)

const pinia = createPinia()

const app = createApp(App)

app.component('font-awesome-icon', FontAwesomeIcon)

app.use(router)
app.use(pinia)

app.mount('#app')