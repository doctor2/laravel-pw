<template>
  <custom-table
    :filterFields="filterFields"
    :sortItems="sortItems"
    bodyComponent="body-transaction"
  >
    <slot slot="message" name="message"></slot>
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
        { name: "user_name", label: "Correspondent Name", value: "" },
        { name: "amount", label: "Amount", value: "" , type: "number"},
        { name: "user_balance", label: "Resulting balance", value: "" , type: "number"}
      ],
      sortItems: [
        { name: "date", label: "Date/Time", sort: 0 },
        { name: "user_name", label: "Correspondent Name", sort: 0 },
        { name: "amount", label: "Amount", sort: 0 },
        { name: "user_balance", label: "Resulting balance", sort: 0 }
      ]
    };
  },
};
</script>

