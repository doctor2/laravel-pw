<template>
  <tr>
    <th v-for="(item, index) in sortItems" :key="index" scope="col" @click="sortBy(index)">
      <span v-text="item.label"></span>
      <span class="arrow" :class="item.sort? 'asc' : 'dsc'"></span>
    </th>
  </tr>
</template>
<script>
export default {
  props: ["sortItems"],
  created() {
    this.setDefaultValuesFromUrl();
  },
  methods: {
    sortBy(index) {
      let newVal = !this.sortItems[index].sort;

      this.resetAllSort();

      this.sortItems[index].sort = newVal;

      this.$router.replace({
        query: Object.assign({}, this.$route.query, {
          sort: this.sortItems[index].name,
          order: this.sortItems[index].sort ? "asc" : "dsc"
        })
      });
    },
    setDefaultValuesFromUrl() {
      for (let i of Object.keys(this.sortItems)) {
        if (this.sortItems[i].name == this.$route.query.sort) {
          this.sortItems[i].sort = this.$route.query.order == "asc" ? 1 : 0;
        }
      }
    },
    resetAllSort(){
       this.sortItems.forEach(function(item, i, items) {
        items[i].sort = 0;
      });
    }
  }
};
</script>
