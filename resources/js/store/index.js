import Vue from 'vue';
import Vuex from 'vuex';

Vue.use(Vuex);

import menu from './modules/menu';


export const store = new Vuex.Store({
    modules: {
        menu,
    },
});
