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
        { name: "date", label: "Date/Time" },
        { name: "user_name", label: "Correspondent Name" },
        { name: "amount", label: "Amount", type: "number" },
        { name: "user_balance", label: "Resulting balance", type: "number" }
      ],
      sortItems: [
        { name: "date", label: "Date/Time"},
        { name: "user_name", label: "Correspondent Name"},
        { name: "amount", label: "Amount"},
        { name: "user_balance", label: "Resulting balance"}
      ]
    };
  }
};
</script>

