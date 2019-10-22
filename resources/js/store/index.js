import Vue from 'vue';
import Vuex from 'vuex';

Vue.use(Vuex);

import menu from './modules/menu';
import user from './modules/user';


export const store = new Vuex.Store({
    modules: {
        menu,
        user,
    },
});
