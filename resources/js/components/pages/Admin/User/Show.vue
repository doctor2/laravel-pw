<template>
        <div class="card card-thin">
            <table class="table " >
                <thead class="thead-light">
                <tr>
                    <th scope="col" colspan="2">User page</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <th scope="row">Name</th>
                    <td>{{user.name}}</td>
                </tr>
                <tr>
                    <th scope="row">Email</th>
                    <td>{{user.email}}</td>
                </tr>
                <tr>
                    <th scope="row">Date created</th>
                    <td>{{user.created_at}}</td>
                </tr>
                <tr>
                    <th scope="row">Ban</th>
                    <td>{{user.hasBan}}</td>
                </tr>
                </tbody>
            </table>
            <div class="card-body">
                <router-link :to="{'name': 'admin.users.index'}" :key="3" active-class="active"><- Back</router-link>
            </div>
        </div>
</template>

<script>
    export default {
        data() {
            return {
                user: {}
            };
        },
        mounted() {
            let that = this;
            axios.get('/admin/users/'+ this.$route.params.id)
                .then(function ({data}) {
                        that.user = data.data;
                    }
                )
        },
    };
</script>
