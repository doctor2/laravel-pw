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
                <span v-text="data.item.amount + ' - ' + data.item.transaction_type + ''">
                </span>
            </template>

            <template v-slot:cell(user_balance)="data">
                {{data.item.user_balance}}
                <template v-if="data.item.transaction_type == 'DEBIT'">
                    <router-link :to="{'name': 'transactions.create', params: { key: data.item.id}}" :key="3" active-class="active">Repeat</router-link>
                </template>
            </template>

        </b-table>
        <b-pagination v-if="!isFirstRequest" :total-rows="totalRows" :per-page="perPage" v-model="currentPage" class="my-0 pagination-sm"/>
    </div>
</template>

<script>
    import methods from "../mixins/apiMethods";

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
                        key: 'user_name',
                        label: 'Correspondent Name',
                        sortable: true
                    },
                    {
                        key: 'amount',
                        label: 'Amount',
                        sortable: true

                    },
                    {
                        key: 'user_balance',
                        label: 'Resulting balance',
                        sortable: true
                    }
                ],
                filters: {
                    created_at: '',
                    amount: '',
                    user_name: '',
                    user_balance: ''
                },
                currentPage: 1,
                perPage: 10,
                totalRows: null,
            }
        },
        methods:{
        }
    };
</script>

