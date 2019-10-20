<template>
    <div class="justify-content-center">
            <div class="card">
                <div class="card-header">Edit user with name {{title}}</div>
                <div class="card-body">

                    <div class="alert alert-danger" v-if="has_error && !success">
                        <p v-if="error == 'update_validation_error'">Validation error (s), please consult the
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
                            <div class="col-sm-2 text-md-right">
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
                success: false,
                API_URL: '/admin/users/' + this.$route.params.id
            }
        },
        methods: {
            update() {
                axios
                    .patch(this.API_URL, {
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
            axios.get(this.API_URL)
                .then(({data}) => {
                    // console.log(JSON.stringify(data));

                    this.banned = data.data.banned;
                        this.name = data.data.name;
                        this.title = data.data.name;
                        this.email = data.data.email;
                    }
                )
                .catch(error => {
                    if(error.response.data.code == 404){
                        this.$router.push({name: 'E404'});
                    }
                });
        },
    };
</script>
