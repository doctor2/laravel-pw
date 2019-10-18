<template>
  <tbody>
    <tr v-for="(item, index) in items" :key="index">
      <td v-text="item.created_at"></td>
      <td v-text="item.debit_user_name"></td>
      <td v-text="item.credit_user_name"></td>
      <td v-html="getAmountWithLink(item)"></td>
    </tr>
  </tbody>
</template>

<script>

export default {
  props:['items'],
  methods: {
    getAmountWithLink(item) {
      return `<a href="/admin/transactions/${item.id}">${
        item.amount
      }</a>`;
    }
  }
};
</script>

