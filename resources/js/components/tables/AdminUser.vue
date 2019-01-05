<template>
  <custom-table
    :filterFields="filterFields"
    :sortItems="sortItems"
    bodyComponent="body-admin-user"
  >
    <slot slot="message" name="message"></slot>
    <slot slot="messageNotFound" name="messageNotFound">Not a user has been found.</slot>
    
  </custom-table>
</template>

<script>
import CustomTable from "../custom_table/Table.vue";

export default {
  components: { CustomTable },
  data() {
    return {
      filterFields: [
        { name: "created_at", label: "Date/Time", value: "" },
        { name: "name", label: "User name", value: "" },
        { name: "email", label: "Email", value: "" },
        { name: "banned", label: "Ban", value: "", type: "checkbox" }
      ],
      sortItems: [
        { name: "created_at", label: "Date/Time", sort: 0 },
        { name: "name", label: "User Name", sort: 0 },
        { name: "email", label: "Email", sort: 0 },
        { name: "banned", label: "Ban", sort: 0 }
      ]
    };
  },
};
</script>

