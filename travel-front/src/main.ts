import { createApp } from 'vue'
import App from './App.vue'
import router from './router'
import './assets/css/main.css'
import { createPinia } from 'pinia'


import 'bootstrap-icons/font/bootstrap-icons.css'

/* Import Font Awesome core */
import { library } from '@fortawesome/fontawesome-svg-core'

/* Import Font Awesome component */
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'

/* Import Solid Icons */
import { faUserSecret, faThumbsUp, faHouse, faPhone } from '@fortawesome/free-solid-svg-icons'

/* Import Regular Icons */
import { faCircleUser } from '@fortawesome/free-regular-svg-icons'

/* Import Brands Icons */
import { faFacebook } from '@fortawesome/free-brands-svg-icons'

/* Add all imported icons to the library */
library.add(
  faUserSecret,
  faThumbsUp,
  faHouse,
  faPhone,
  faCircleUser,
  faFacebook
)

const pinia = createPinia()
const app = createApp(App)

app.component('font-awesome-icon', FontAwesomeIcon)
app.use(router)

app.use(pinia)
app.mount('#app')