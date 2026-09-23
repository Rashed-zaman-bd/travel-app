import { createApp } from 'vue'
import App from './App.vue'
import router from './router'
import './assets/css/main.css'
import { createPinia } from 'pinia'
import i18n from './i18n'
import 'swiper/css'
import 'swiper/css/effect-coverflow'
import 'swiper/css/pagination'

// Bootstrap Icons
import 'bootstrap-icons/font/bootstrap-icons.css'

// Font Awesome
import { library } from '@fortawesome/fontawesome-svg-core'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'

import {
  faUserSecret,
  faThumbsUp,
  faHouse,
  faPhone,
  faSearch,
} from '@fortawesome/free-solid-svg-icons'
import { faCircleUser } from '@fortawesome/free-regular-svg-icons'
import { faFacebook } from '@fortawesome/free-brands-svg-icons'

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
app.use(i18n)

app.mount('#app')