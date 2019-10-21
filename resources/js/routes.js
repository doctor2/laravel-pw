import Vue from 'vue';
import VueRouter from 'vue-router';

Vue.use(VueRouter);

import E404 from './components/pages/E404';


import Transactions from './components/pages/Transaction.vue';
import CreateTransaction from './components/pages/CreateTransaction.vue';
import AdminTransactions from './components/pages/Admin/Transaction/Index.vue';
import AdminTransactionsEdit from './components/pages/Admin/Transaction/Edit.vue';
import AdminUsersShow from './components/pages/Admin/User/Show.vue';
import AdminUsersEdit from './components/pages/Admin/User/Edit.vue';
import AdminUsers from './components/pages/Admin/User/Index.vue';
import Register from './components/pages/Register.vue'
import Login from './components/pages/Login.vue'


const routes = [
    {
        path: '',
        redirect: {name: 'transactions.index'},
    },
    {
        path: '/',
        name: 'transactions.index',
        component: Transactions,
        meta: {
            auth: true
        }
    },
    {
        path: '/transactions/create',
        name: 'transactions.create',
        component: CreateTransaction,
        meta: {
            auth: true
        }
    },
    {
        path: '/register',
        name: 'register',
        component: Register,
        meta: {
            auth: false
        }
    },
    {
        path: '/login',
        name: 'login',
        component: Login,
        meta: {
            auth: false
        }
    },
    {
        path: '/admin/transactions',
        name: 'admin.transactions.index',
        component: AdminTransactions,
        meta: {
            auth: true
            // auth: {isAdmin: true, redirect: {name: 'login'}, forbiddenRedirect: '/403'}
        }
    },
    {
        path: '/admin/transactions/:id/edit/',
        name: 'admin.transactions.edit',
        component: AdminTransactionsEdit,
        meta: {
            auth: true
            // auth: {isAdmin: true, redirect: {name: 'login'}, forbiddenRedirect: '/403'}
        }
    },
    {
        path: '/admin/users/edit/:id',
        name: 'admin.users.edit',
        component: AdminUsersEdit,
        meta: {
            auth: true
        }
    },
    {
        path: '/admin/users/:id',
        name: 'admin.users.show',
        component: AdminUsersShow,
        meta: {
            auth: true
        }
    },
    {
        path: '/admin/users',
        name: 'admin.users.index',
        component: AdminUsers,
        meta: {
            auth: true
        }
    },
    {
        path: '*',
        name: 'E404',
        component: E404
    }
];

export const router = new VueRouter({
    routes,
    mode: 'history'
});
