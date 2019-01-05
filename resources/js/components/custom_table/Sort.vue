<template>
  <tr>
    <th v-for="(item, index) in sortItems" :key="index" scope="col" @click="sortBy(index)">
      <span v-text="item.label"></span>
      <span class="arrow" :class="itemsOrder[index]? 'asc' : 'dsc'"></span>
    </th>
  </tr>
</template>
<script>
export default {
  props: ["sortItems"],
  created() {
    this.setDefaultValuesFromUrl();
  },
  data() {
    return {
      itemsOrder: []
    };
  },
  methods: {
    sortBy(index) {
      let newVal = !this.itemsOrder[index];

      this.resetAllSort();

      this.$set(this.itemsOrder, index, newVal);

      this.$router.replace({
        query: Object.assign({}, this.$route.query, {
          sort: this.sortItems[index].name,
          order: this.itemsOrder[index] ? "asc" : "dsc"
        })
      });
    },
    setDefaultValuesFromUrl() {
      for (let i of Object.keys(this.sortItems)) {
        if (this.sortItems[i].name == this.$route.query.sort) {
          this.itemsOrder.push(this.$route.query.order == "asc" ? 1 : 0);
        } else {
          this.itemsOrder.push(0);
        }
      }
    },
    resetAllSort() {
      this.itemsOrder.forEach(function(item, i, items) {
        items[i] = 0;
      });
    }
  }
};
</script>
