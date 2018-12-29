<template>
  <div class="card-body">
    <div v-if="showSessionMessage">
      <slot name="message"></slot>
    </div>
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
          <td v-text="item.debit_user_name"></td>
          <td v-text="item.credit_user_name"></td>
          <td v-html="getAmountWithLink(item)"></td>
        </tr>
      </tbody>
    </table>
    <h3 v-show="showNotFound">Not a single transaction has been found.</h3>
    <paginator :dataSet="dataSet" @changed="fetch"></paginator>
  </div>
</template>

<script>
import common from "./mixins/commonForTable";

export default {
  mixins: [common],
  data() {
    return {
      filter_fields: [
        { name: "date", label: "Date/Time", value: "" },
        { name: "debit_user_name", label: "Sender Name", value: "" },
        { name: "credit_user_name", label: "Recipient Name", value: "" },
        { name: "amount", label: "Amount", value: "", type: "number" }
      ],
      sortItems: [
        { name: "date", label: "Date/Time", sort: 0 },
        { name: "debit_user_name", label: "Sender Name", sort: "" },
        { name: "credit_user_name", label: "Recipient Name", sort: "" },
        { name: "amount", label: "Amount", sort: 0 }
      ]
    };
  },
  methods: {
    getAmountWithLink(item) {
      return `<a href="/admin/transactions/${item.transaction_key}">${
        item.amount
      }</a>`;
    }
  }
};
</script>

