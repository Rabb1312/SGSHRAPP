// src/router/index.js
import { createRouter, createWebHistory } from 'vue-router'

// Minimal router - hanya untuk compatibility
// Sistem routing utama tetap di App.vue
const routes = [
  {
    path: '/:pathMatch(.*)*',
    redirect: '/'
  }
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

export default router