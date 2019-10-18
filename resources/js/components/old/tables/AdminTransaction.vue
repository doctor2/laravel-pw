<template>
  <custom-table
    :filterFields="filterFields"
    :sortItems="sortItems"
    bodyComponent="body-admin-transaction"
  >
    <slot slot="message" name="message">

    </slot>
  </custom-table>
</template>

<script>
import CustomTable from "../custom_table/Table.vue";

export default {
  components: { CustomTable },
  data() {
    return {
      filterFields: [
        { name: "date", label: "Date/Time" },
        { name: "debit_user_name", label: "Sender Name" },
        { name: "credit_user_name", label: "Recipient Name" },
        { name: "amount", label: "Amount", type: "number" }
      ],
      sortItems: [
        { name: "date", label: "Date/Time"},
        { name: "debit_user_name", label: "Sender Name"},
        { name: "credit_user_name", label: "Recipient Name"},
        { name: "amount", label: "Amount"}
      ]
    };
  }
};
</script>

