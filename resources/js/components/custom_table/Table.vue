<template>
  <div class="card-body">
    <div v-if="showSessionMessage">
      <slot name="message"></slot>
    </div>

    <filter-table :fields="filterFields"></filter-table>

    <table v-if="items.length != 0" class="table card-body-table">
      <thead>
        <sort-table :sortItems="sortItems"></sort-table>
      </thead>
      <component v-bind:is="bodyComponent" :items="items"></component>
    </table>

    <h3 v-show="showNotFound">
      <slot name="messageNotFound">Not a single transaction has been found.</slot>
      </h3>

    <pagination :meta="meta"></pagination>

  </div>
</template>

<script>
import FilterTable from "./Filter.vue";
import SortTable from "./Sort.vue";
import Pagination from "./Pagination.vue";

import BodyAdminTransaction from "../tables/bodies/AdminTransaction.vue";
import BodyAdminUser from "../tables/bodies/AdminUser.vue";
import BodyTransaction from "../tables/bodies/Transaction.vue";

export default {
  props: ["filterFields", "sortItems", "bodyComponent"],
  components: {
    FilterTable,
    SortTable,
    Pagination,
    BodyAdminTransaction,
    BodyAdminUser,
    BodyTransaction
  },
  data() {
    return {
      meta: false,
      showSessionMessage: false,
      isWaiting: true,
      items: []
    };
  },
  created() {
    this.fetch();
    this.showSessionMessage = true;
    setTimeout(() => (this.showSessionMessage = false), 5000);
  },
  computed: {
    showNotFound() {
      return !this.isWaiting && this.items.length == 0;
    }
  },
  watch: {
    "$route.query": {
      handler(query) {
        this.fetch(1, query);
      }
    }
  },
  methods: {
    fetch(page = this.$route.query.page, filters = this.$route.query) {
      axios
        .get(location.pathname, {
          params: {
            page,
            ...filters
          }
        })
        .then(this.refresh)
        .catch(error => {
          if (error.response.status == 401) {
            location.reload();
          }
        });
    },
    refresh({ data }) {
      this.meta = data;
      this.items = data.data;

      window.scrollTo(0, 0);

      this.isWaiting = false;
    }
  }
};
</script>

