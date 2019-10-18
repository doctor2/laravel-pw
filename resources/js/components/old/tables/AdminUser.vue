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
        { name: "created_at", label: "Date/Time" },
        { name: "name", label: "User name" },
        { name: "email", label: "Email" },
        { name: "banned", label: "Ban", type: "checkbox" }
      ],
      sortItems: [
        { name: "created_at", label: "Date/Time"},
        { name: "name", label: "User Name"},
        { name: "email", label: "Email"},
        { name: "banned", label: "Ban"}
      ]
    };
  },
};
</script>

