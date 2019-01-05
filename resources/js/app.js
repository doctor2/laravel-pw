
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');


window.Vue = require('vue');

import VueRouter from 'vue-router'
window.Vue.use(VueRouter)

import onlyInt from 'vue-input-only-number';
window.Vue.use(onlyInt);
//Vue.use(onlyFloat); // v-float>
/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

const AdminTransactions = require('./components/tables/AdminTransaction.vue');
const AdminUsers = require('./components/tables/AdminUser.vue');
const Transactions = require('./components/tables/Transaction.vue');
Vue.component('transaction', Transactions);
Vue.component('admin-transaction', AdminTransactions);
Vue.component('admin-user', AdminUsers);
Vue.component('admin-transaction-edit', require('./components/AdminTransactionEdit.vue'));
Vue.component('autocomplete', require('./components/Autocomplete.vue'));
    
// const files = require.context('./', true, /\.vue$/i)
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key)))

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */


const routes = [
    {
        path: '/admin/transactions',
        name: 'admin.transactions.index',
        component: AdminTransactions
    },
    {
        path: '/admin/users',
        name: 'admin.users.index',
        component: AdminUsers
    },
    {
        path: '/',
        name: 'transactions.index',
        component: Transactions
    },
]


const router = new VueRouter({
    mode: 'history',
    routes
})

const app = new Vue({
    el: '#app',
    router
});
