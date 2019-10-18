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

            <template v-slot:cell(amount)="data">
                    <router-link :to="{'name': 'admin.transactions.edit', params: { id: data.item.id}}" :key="3" active-class="active">{{data.item.amount}}</router-link>
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
                        key: 'debit_user_name',
                        label: 'Sender Name',
                        sortable: true
                    },
                    {
                        key: 'credit_user_name',
                        label: 'Recipient Name',
                        sortable: true

                    },
                    {
                        key: 'amount',
                        label: 'Amount',
                        sortable: true
                    }
                ],
                filters: {
                    created_at: '',
                    debit_user_name: '',
                    credit_user_name: '',
                    amount: ''
                },
                currentPage: 1,
                perPage: 10,
                totalRows: null,
                API_URL: '/admin',
            }
        },
    };
</script>
