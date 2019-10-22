import { store } from '../../store'

export default {
    namespaced: true,
    state: {
        name: '',
        balance: 0
    },
    getters: {
        name(state){
            return state.name;
        },
        balance(state){
            return state.balance;
        }
    },
    mutations: {
        setName(state, name){
            state.name = name;
        },
        setBalance(state, balance){
            state.balance = balance;
        },
    },
    actions: {
        setName(state, name){
            store.commit('user/setName', name);
        },
        setBalance(state, balance){
            store.commit('user/setBalance', balance);
        },
    }
};
