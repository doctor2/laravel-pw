<template>
    <div class="justify-content-centermy-1 row">
        <b-table

            id="my-table"
            :busy.sync="isBusy"
            :sortBy="sortBy"
            :sortDesc="sortDesc"
            :items="myProvider"
            :fields="fields"
            :filter="filters"
            :current-page="currentPage"
        >
            <template slot="top-row" slot-scope="{ fields }">
                <td v-for="field in fields" :key="field.key" v-if="field.key != 'banned'" >
                    <input v-on:input="debounceInput({$event,field})" :placeholder="field.label">
                </td>
            </template>

            <template v-slot:cell(name)="data">
                    <router-link :to="{'name': 'admin.users.show', params: { id: data.item.id}}" :key="3" active-class="active">{{data.item.name}}</router-link>
            </template>

            <template v-slot:cell(banned)="data">
                <b v-text="data.item.banned ? 'yes' : 'no'"></b>
                <router-link :to="{'name': 'admin.users.edit', params: { id: data.item.id}}" :key="3" active-class="active">Edit</router-link>
            </template>

        </b-table>
        <b-pagination :total-rows="totalRows" :per-page="perPage" v-model="currentPage" class="pagination-sm"/>
    </div>
</template>

<script>
    import methods from "../../../mixins/apiMethods";

    export default {
        mixins: [methods],
        data() {
            return {
                sortBy: 'created_at',
                sortDesc: true,
                isBusy: false,
                items: [],
                fields: [
                    {
                        key: 'created_at',
                        label: 'Date/Time',
                        sortable: true
                    },
                    {
                        key: 'name',
                        label: 'User name',
                        sortable: true
                    },
                    {
                        key: 'email',
                        label: 'Email',
                        sortable: true
                    },
                    {
                        key: 'banned',
                        label: 'Ban',
                        sortable: true
                    }
                ],
                filters: {
                    created_at: '',
                    name: '',
                    email: '',
                },
                currentPage: 1,
                perPage: 10,
                totalRows: null,
                API_URL: '/admin/users',
            }
        },
    };
</script>
