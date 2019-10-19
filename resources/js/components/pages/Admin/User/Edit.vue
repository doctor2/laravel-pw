<template>
    <div class="justify-content-center">
            <div class="card">
                <div class="card-header">Edit user with name {{this.title}}</div>
                <div class="card-body">

                    <div class="alert alert-danger" v-if="has_error && !success">
                        <p v-if="error == 'The given data was invalid.'">Validation error (s), please consult the
                            message (s) below.</p>
                        <p v-else>Error, can not update at the moment. If the problem persists, please contact an
                            administrator.</p>
                    </div>

                    <form method="PATCH" @submit.prevent="update" v-if="!success">
                        <div class="form-group row">
                            <label for="name" class="col-md-4 col-form-label text-md-right">Name</label>
                            <div class="col-md-6">
                                <input id="name" type="text" class="form-control"
                                       v-bind:class="{ 'is-invalid': has_error && errors.name }"
                                       name="name" v-model="name" required autofocus>
                                <span class="help-block" v-if="has_error && errors.name">{{ errors.name }}</span>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="email"
                                   class="col-md-4 col-form-label text-md-right">E-mail</label>
                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control"
                                       v-bind:class="{ 'is-invalid': has_error && errors.email }"
                                       name="email" v-model="email" required>
                                <span class="help-block" v-if="has_error && errors.name">{{ errors.email }}</span>

                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-sm-4"></div>
                            <b-form-checkbox
                                class="col-sm-6"
                                id="banned"
                                v-model="banned"
                                name="banned"
                                value="1"
                                unchecked-value="0"
                            >
                                Ban
                            </b-form-checkbox>
                        </div>

                        <div class="form-group row">
                            <div class="col-sm-8">

                            </div>
                            <div class="col-sm-2">
                                <button type="submit" class="btn btn-primary">
                                    Save
                                </button>
                            </div>
                        </div>

                    </form>
                    <div class="alert alert-success" v-if="success">User has been updated!</div>
                </div>
            </div>

    </div>
</template>

<script>
    export default {
        data() {
            return {
                banned: '',
                title: '',
                name: '',
                email: '',
                has_error: false,
                error: '',
                errors: {},
                success: false
            }
        },
        methods: {
            update() {
                axios
                    .patch('/admin/users/edit/' + this.$route.params.id, {
                        banned: this.banned,
                        name: this.name,
                        email: this.email
                    })
                    .then( ({data}) => {
                        this.success = true;

                        setTimeout(()=> (this.$router.push({name: 'admin.users.index'})), 5000);
                    })
                    .catch(error => {
                        // console.log(JSON.stringify(error.response));

                        this.has_error = true;
                        this.error = error.response.data.message;
                        this.errors = error.response.data.errors || {};
                    });
            }
        },
        mounted() {
            let that = this;
            axios.get('/admin/users/' + this.$route.params.id)
                .then(function ({data}) {
                        that.banned = data.data.banned;
                        that.name = data.data.name;
                        that.title = data.data.name;
                        that.email = data.data.email;
                    }
                )
        },
    };
</script>
