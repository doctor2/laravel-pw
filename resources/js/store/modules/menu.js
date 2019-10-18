export default {
    namespaced: true,
    state: {
        items: [
            {
                url: '/',
                text: 'Transactions',
                forAdmin: false
            },
            {
                url: '/admin/transactions',
                text: 'Admin transactions',
                forAdmin: true
            },
            {
                url: '/admin/users',
                text: 'Admin users',
                forAdmin: true
            }
        ]
    },
    getters: {
        items(state){
            return state.items;
        }
    }
};
