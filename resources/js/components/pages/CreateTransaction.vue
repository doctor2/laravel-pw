<template>
    <div class="justify-content-center">
        <div class="card">

        <div class="card-header ">Create a new transaction</div>

            <div class="card-body">
                <form method="POST" @submit.prevent="store" >
                    <div class="form-group row">
                        <label for="user_name" class="col-md-4 col-form-label text-md-right">The recipient</label>
                        <div class="col-md-6">
                            <autocomplete v-if="isLoaded" :user_name="user_name" :user_id="user_id" @selectUser="onSelectUser"></autocomplete>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="amount" class="col-md-4 col-form-label text-md-right">Amount</label>
                        <div class="col-md-6">
                            <input type="text" name="amount" id="amount" @keypress="isNumber($event)" class="form-control card-body-form_amount"
                                   v-model="amount">
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-sm-8">
                        </div>
                        <div class="col-sm-2 text-md-right">
                            <button type="submit" class="btn btn-primary">Send</button>
                        </div>
                    </div>
                </form>
                <div class="alert alert-success" v-if="success">Transaction has been created!</div>
                <div class="alert alert-danger" v-if="fail" v-text="errorMessage"></div>
            </div>
            </div>
        </div>
</template>

<script>
    import autocomplete from '../Autocomplete.vue';
    import {mapActions} from 'vuex';

    export default {
        components: {
            autocomplete
        },
        data() {
            return {
                user_name: '',
                user_id: '',
                amount: '',
                errorMessage: '',
                success: false,
                fail: false,
                isLoaded: false
            };
        },
        mounted() {
            axios.get('/transactions/create/?key=' + this.$route.query.key)
                .then(({data}) => {
                        this.user_name = data.data.user_name;
                        this.user_id = data.data.user_id;
                        this.amount = data.data.amount;
                    }
                )
                .catch(error => {
                    if (error.response.data.code == 404) {
                        this.$router.push({name: 'E404'});
                    }
                }).then(()=>{ this.isLoaded = true; });
        },
        methods: {
            ...mapActions('user', {
                setUserBalance: 'setBalance'
            }),
            onSelectUser(user){
                this.user_name =  user.name;
                this.user_id =  user.id;
            },
            store() {
                axios
                    .post("/transactions/", {
                        user_name: this.user_name,
                        user_id: this.user_id,
                        amount: this.amount,
                    })
                    .then(({data}) => {
                        this.success = true;

                        this.setUserBalance(data.data.balance);

                        setTimeout(() => {
                            this.success = false;

                            this.$router.push({name : 'transactions.index'});
                        }, 5000);
                    })
                    .catch(error => {
                        this.errorMessage = error.response.data.message;

                        this.fail = true;

                        setTimeout(() => (this.fail = false), 5000);
                    });
            },
            isNumber: function(evt) {
                evt = (evt) ? evt : window.event;
                let charCode = (evt.which) ? evt.which : evt.keyCode;
                if ((charCode > 31 && (charCode < 48 || charCode > 57)) && charCode !== 46) {
                    evt.preventDefault();
                } else {
                    return true;
                }
            }
        }
    };
</script>

