<template>
    <div>
        <table class="table">
            <thead class="thead-light">
            <tr>
                <th scope="col" colspan="2">Transaction page</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <th scope="row">Sender name</th>
                <td v-text="item.debit_user_name"></td>
            </tr>
            <tr>
                <th scope="row">Sender balance</th>
                <td v-text="item.debit_user_balance"></td>
            </tr>
            <tr>
                <th scope="row">Recipient name</th>
                <td v-text="item.credit_user_name"></td>
            </tr>
            <tr>
                <th scope="row">Recipient balance</th>
                <td v-text="item.credit_user_balance"></td>
            </tr>
            <tr v-if="editing">
                <th scope="row">Amount</th>
                <td>
                    <form @submit.prevent="update">
                        <div class="form-group">
                            <input
                                type="text"
                                class="card-body-form_amount"
                                v-int
                                name="amount"
                                v-model="item.amount"
                            >
                        </div>
                        <button class="btn btn-xs btn-primary">Update</button>
                        <button class="btn btn-xs btn-link" @click="editing=false" type="button">Cancel</button>
                    </form>
                </td>
            </tr>
            <tr v-else>
                <th scope="row">Amount</th>
                <td>
                    <span v-text="item.amount"></span>
                    <button @click="editing=true" class="btn btn-primary">Edit</button>
                </td>
            </tr>
            <tr>
                <th scope="row">Date created</th>
                <td v-text="item.created_at"></td>
            </tr>
            </tbody>
        </table>
        <div class="alert alert-success" v-if="updateSuccess">Transaction has been updated!</div>
        <div class="alert alert-danger" v-if="updateFail" v-text="errorMessage"></div>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                updateSuccess: false,
                updateFail: false,
                errorMessage: "",
                editing: false,
                item: {}
            };
        },
        mounted() {
            axios.get('/admin/transactions/edit/'+ this.$route.params.id)
                .then(({data}) => {
                        this.item = data.data;
                    }
                )
        },
        methods: {
            update() {
                axios
                    .patch("/admin/transactions/edit/" + this.item.id, {
                        amount: this.item.amount
                    })
                    .then(({data}) => {
                        this.item = data.data;

                        this.updateSuccess = true;

                        setTimeout(() => (this.updateSuccess = false), 5000);
                    })
                    .catch(error => {
                        // console.log(JSON.stringify(error.response));
                        this.errorMessage = error.response.data.message;

                        this.updateFail = true;

                        setTimeout(() => (this.updateFail = false), 5000);
                    });
                this.editing = false;
            }
        }
    };
</script>

