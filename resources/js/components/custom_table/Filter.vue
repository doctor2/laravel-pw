<template>
  <div class="filter_table">
    <div v-for="(field, index) in fields" class="form-group" :key="index">
      <label v-text="field.label" :for="field.name"></label>
      
      <input
        v-if="field.type  == 'number'"
        v-int
        type="text"
        :value="values[field.name]"
        @input="change(index, $event.target)"
      >
      
      <input
        v-else-if="field.type  == 'checkbox'"
        type="checkbox"
        :checked="values[field.name]"
        @click="change(index, $event.target)"
      >
      
      <input v-else type="text" :value="values[field.name]" @input="change(index, $event.target)">
    </div>
  </div>
</template>
<script>
import debounce from "debounce";

export default {
  props: ["fields"],
  data() {
    return {
      values: {}
    };
  },
  created() {
    this.change = debounce(this.change, 500);

    this.setDefaultValuesFromUrl();
  },
  methods: {
    change(index, el) {
      let fieldName = this.fields[index].name;
      if (el.type == "checkbox") {
        this.values[fieldName] = el.checked ? 1 : "";
      } else {
        this.values[fieldName] = el.value;
      }

      let result = {};
      for (let key of Object.keys(this.values)) {
        if (!!this.values[key]) {
          result[key] = this.values[key];
        }
      }

      this.$router.push({ path: location.path, query: result });
    },
    setDefaultValuesFromUrl() {
      for (let i = 0; i < this.fields.length; i++) {
        let item = this.fields[i];

        if (this.$route.query[item.name]) {
          this.values[item.name] = this.$route.query[item.name];
        } else {
          this.values[item.name] = "";
        }
      }
    }
  }
};
</script>
