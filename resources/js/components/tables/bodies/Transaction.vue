<template>
  <tbody>
    <tr v-for="(item, index) in items" :key="index">
      <td v-text="item.created_at"></td>
      <td v-text="item.user_name"></td>
      <td v-text="item.amount + ' ( ' + item.transaction_type + ' )'"></td>
      <td v-html="getBalanceWithLink(item)"></td>
    </tr>
  </tbody>
</template>

<script>
export default {
  props: ["items"],
  methods: {
    getBalanceWithLink(item) {
      let balance = item.user_balance;
      if (item.transaction_type == "DEBIT") {
        balance += ` <a href="/transactions/create?key=${item.id}">Repeat</a>`;
      }
      return balance;
    }
  }
};
</script>

