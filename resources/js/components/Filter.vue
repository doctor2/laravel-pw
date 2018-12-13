<template>
  <div class="filter_table">
    <div v-for="(field, index) in fields" class="form-group" :key="index">
      <label v-text="field.label" :for="field.name"></label>
      <input :id="field.name" type="text" :name="field.name" :value="field.value" @input="change">
    </div>
  </div>
</template>
<script>
import debounce from 'debounce'

export default {
  props: ["fields"],
  created(){
        this.change = debounce(this.change, 500)
  },
  methods: {
    change(item) {
      let values = [];
      for (let i = 0; i < this.fields.length; i++) {
        let el = document.getElementById(this.fields[i].name);
        if (el.value) {
          values.push({
            name: el.name,
            value: el.value
          });
        }
      }

      this.$emit("changed", values);
    }
  }
};
</script>
