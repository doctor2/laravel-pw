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
  }
};
</script>

