<template>
  <div class="filter_table">
    <div v-for="(field, index) in fields" class="form-group" :key="index">
      <label v-text="field.label" :for="field.name"></label>
      
      <input
        v-if="field.type  == 'number'"
        v-int
        type="text"
        :value="field.value"
        @input="change(index, $event.target)"
      >
      
      <input
        v-else-if="field.type  == 'checkbox'"
        type="checkbox"
        :checked="field.value"
        @click="change(index, $event.target)"
      >
      
      <input v-else type="text" :value="field.value" @input="change(index, $event.target)">
    </div>
  </div>
</template>
<script>
import debounce from "debounce";

export default {
  props: ["fields"],
  data() {
    return {
      data: {}
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
        this.data[fieldName] = el.checked ? 1 : "";
      } else {
        this.data[fieldName] = el.value;
      }

      let result = {};
      for (let key of Object.keys(this.data)) {
        if (!!this.data[key]) {
          result[key] = this.data[key];
        }
      }

      this.$router.push({ path: location.path, query: result });
    },
    setDefaultValuesFromUrl() {
      for (let i = 0; i < this.fields.length; i++) {
        if (this.$route.query[this.fields[i].name]) {
          this.fields[i].value = this.$route.query[this.fields[i].name];
        }
        this.data[this.fields[i].name] = this.fields[i].value;
      }
    }
  }
};
</script>
