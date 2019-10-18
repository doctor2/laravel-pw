
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

import './bootstrap';

import Vue from 'vue';
import axios from 'axios';
import VueAxios from 'vue-axios';
import VueAuth from '@websanova/vue-auth';
import VueRouter from 'vue-router'
import App from './components/App.vue';

import auth from './auth'
import {router} from './routes.js';
import {store} from './store';

// Set Vue router
Vue.router = router
Vue.use(VueRouter)

// Set Vue authentication
Vue.use(VueAxios, axios);
axios.defaults.baseURL = `/api`;
Vue.use(VueAuth, auth);

import BootstrapVue from 'bootstrap-vue'
Vue.use(BootstrapVue)

import onlyInt from 'vue-input-only-number';
Vue.use(onlyInt);


const app = new Vue({
    el: '#app',
    store,
    router,
    render: h => h(App)
});
