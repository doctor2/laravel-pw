<template>
  <div class="card-body">
    <filter-table :fields="filter_fields" @changed="fetch"></filter-table>
    <table v-if="items.length != 0" class="table card-body-table">
      <thead>
        <tr>
          <th v-for="(item, index) in sortItems" :key="index" scope="col" @click="sortBy(index)">
            <span v-text="item.label"></span>
            <span class="arrow" :class="item.sort? 'asc' : 'dsc'"></span>
          </th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="(item, index) in items" :key="index">
          <td v-text="item.created_at"></td>
          <td v-text="item.user_name"></td>
          <td v-text="item.amount + ' ( ' + item.transaction_type + ' )'"></td>
          <td v-html="getBalanceWithLink(item)"></td>
        </tr>
      </tbody>
    </table>
    <h3 v-show="showNotFound">Not a single transaction has been found.</h3>
    <paginator :dataSet="dataSet" @changed="fetch"></paginator>
  </div>
</template>

<script>
import common from './mixins/commonForTable';

export default {
  mixins: [common],
  data() {
    return {
      filter_fields: [
        { name: "date", label: "Date/Time", value: "" },
        { name: "user_name", label: "Correspondent Name", value: "" },
        { name: "amount", label: "Amount", value: "" },
        { name: "user_balance", label: "Resulting balance", value: "" }
      ],
      sortItems: [
        { name: "date", label: "Date/Time", sort: 0 },
        { name: "user_name", label: "Correspondent Name", sort: 0 },
        { name: "amount", label: "Amount", sort: 0 },
        { name: "user_balance", label: "Resulting balance", sort: 0 }
      ]
    };
  },
  methods: {
    getBalanceWithLink(item) {
      let balance = item.user_balance;
      if (item.transaction_type == "DEBIT") {
        balance += ` <a href="/transactions/create?key=${
          item.transaction_key
        }">Repeat</a>`;
      }
      return balance;
    },
  }
};
</script>

