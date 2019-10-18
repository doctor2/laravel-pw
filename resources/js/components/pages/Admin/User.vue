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
                <td v-for="field in fields" :key="field.key">
                    <input v-on:input="debounceInput({$event,field})" :placeholder="field.label">
                </td>
            </template>

        </b-table>
        <b-pagination :total-rows="totalRows" :per-page="perPage" v-model="currentPage" class="my-0 pagination-sm"/>
    </div>
</template>

<script>
    import methods from "../../mixins/apiMethods";

    export default {
        mixins: [methods],
        data() {
            return {
                sortBy: 'created_at',
                sortDesc: false,
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
