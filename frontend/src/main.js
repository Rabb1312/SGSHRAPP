import { createApp } from "vue";
// import component App
import App from "./App.vue";

import CmpInputText from "@/components/InputText/InputText.vue";
// import CmpSelect2 from "@/components/select2/select2.vue";

//import sweet alert
import swal from "sweetalert2";
window.Swal = swal;

import moment from "moment-timezone";
moment.tz.setDefault("Asia/Jakarta");

import { VueCsvImportPlugin } from "vue-csv-import";
import Notifications from "@kyvg/vue3-notification";

import LoadingX from "vue3-loading-overlay";
import "vue3-loading-overlay/dist/vue3-loading-overlay.css";

import vSelect from "vue-select"; //import vue-select
import "vue-select/dist/vue-select.css"; //import css vue-select
import "@/assets/css/custom-vue-select.css";

// date picker
import VueDatePicker from "@vuepic/vue-datepicker";
import "@vuepic/vue-datepicker/dist/main.css";
import "@/assets/css/custom-datepicker.css";

// HANYA TAMBAHAN UNTUK STATE MANAGEMENT (TANPA ROUTER)
import { createPinia } from 'pinia'
import axios from 'axios'

// Setup axios defaults - GUNAKAN import.meta.env untuk Vite
axios.defaults.baseURL = import.meta.env.VITE_API_HOST || 'http://localhost:8000'
axios.defaults.headers.common['Accept'] = 'application/json'
axios.defaults.headers.common['Content-Type'] = 'application/json'

// Add response interceptor untuk handle unauthorized
axios.interceptors.response.use(
  (response) => response,
  (error) => {
    if (error.response?.status === 401) {
      // Token expired atau unauthorized
      localStorage.removeItem('token')
      localStorage.removeItem('uObject')
      // Tidak redirect ke /login karena kita pakai sistem routing sendiri
      window.location.reload() // Reload untuk trigger login check
    }
    return Promise.reject(error)
  }
)

//create App Vue
const app = createApp(App);

// Use Pinia for state management
const pinia = createPinia()
app.use(pinia)

// TIDAK PAKAI ROUTER - karena sudah ada sistem routing sendiri di App.vue

app.component("VueDatePicker", VueDatePicker);
app.component("CmpInputText", CmpInputText);
app.component("v-select", vSelect);
app.component("LoadingX", LoadingX);
app.use(Notifications);
app.use(VueCsvImportPlugin);

app.mount("#app");