<template>
    <div class="row justify-content-center">
        <div class="col-md-12">

            <div class="card-header card-thin">Create a new transaction</div>
            <div class="card-body card-thin">
                <form method="POST" @submit.prevent="update" class="card-body-form">
                    <div class="form-group">
                        <label for="user_name">The recipient</label>
                        <autocomplete v-if="isLoaded" :user_name="this.user_name"
                                      :user_id="this.user_id"></autocomplete>
                    </div>

                    <div class="form-group">
                        <label for="amount">Amount</label>
                        <input type="text" name="amount" id="amount" v-int class="card-body-form_amount"
                               v-model="amount">
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">Send</button>
                    </div>
                </form>
                <div class="alert alert-success" v-if="success">Transaction has been updated!</div>
                <div class="alert alert-danger" v-if="fail" v-text="error"></div>
            </div>
        </div>
    </div>
</template>

<script>
    import autocomplete from '../Autocomplete.vue';

    export default {
        components: {
            autocomplete
        },
        data() {
            return {
                user_name: '',
                user_id: '',
                amount: '',
                has_error: false,
                error: '',
                errors: {},
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
            update() {
                axios
                    .post("/transactions/", {
                        user_name: this.user_name,
                        user_id: this.user_id,
                        amount: this.amount,
                    })
                    .then(({data}) => {
                        this.success = true;

                        this.amount = 0;
                        this.user_name = '';
                        this.user_id = '';

                        setTimeout(() => (this.success = false), 5000);
                    })
                    .catch(error => {
                        this.error = error.response.data.message;

                        this.fail = true;

                        setTimeout(() => (this.fail = false), 5000);
                    });
                this.editing = false;
            }
        }
    };
</script>

