import './bootstrap';

import 'admin-lte/plugins/bootstrap/js/bootstrap.bundle.min.js';
import 'admin-lte/dist/js/adminlte.min.js';

import { createApp } from 'vue/dist/vue.esm-bundler.js';
import { createRouter, createWebHistory } from 'vue-router';
import routes from './routes.js';

const app = createApp({})


const router = createRouter({
    routes: routes,
    history: createWebHistory(),
})


app.use(router).mount('#app');
