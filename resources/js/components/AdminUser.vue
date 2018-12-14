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
          <td v-html="getNameWithLink(item)"></td>
          <td v-text="item.email"></td>
          <td v-text="item.banned?'yes': 'no'"></td>
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
        { name: "created_at", label: "Date/Time", value: "" },
        { name: "name", label: "User name", value: "" },
        { name: "email", label: "Email", value: "" },
        { name: "banned", label: "Ban", value: "", type: 'checkbox' }
      ],
      sortItems: [
        { name: "created_at", label: "Date/Time", sort: 0 },
        { name: "name", label: "User Name", sort: 0 },
        { name: "email", label: "Email", sort: 0 },
        { name: "banned", label: "Ban", sort: 0 }
      ]
    };
  },
  methods: {
    getNameWithLink(item) {
      return `<a href="/admin/users/${item.id}">${item.name}</a>`;
    }
  }
};
</script>

