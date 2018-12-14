<template>
  <div class="filter_table">
    <div v-for="(field, index) in fields" class="form-group" :key="index">
      <label v-text="field.label" :for="field.name"></label>
      
      <input v-if="field.type  == 'number'" v-int :id="field.name" type="text" :name="field.name" :value="field.value" @input="change">
     
      <input v-else-if="field.type  == 'checkbox'" v-bind:id="field.name" type="checkbox" :name="field.name" :checked="field.value" @click="change">
     
      <input v-else :id="field.name" type="text" :name="field.name" :value="field.value" @input="change">
      
    </div>
  </div>
</template>
<script>
import debounce from "debounce";

export default {
  props: ["fields"],
  created() {
    this.change = debounce(this.change, 500);
  },
  methods: {
    change() {
      let values = [];
      for (let i = 0; i < this.fields.length; i++) {
        let el = document.getElementById(this.fields[i].name);

        if (el.type == "checkbox") {
          if(el.checked){
            values.push({ name: el.name, value: 1 });
          }
        } else if (el.value) {
          values.push({ name: el.name, value: el.value });
        }
      }
      
      this.$emit("changed", values);
    }
  }
};
</script>
