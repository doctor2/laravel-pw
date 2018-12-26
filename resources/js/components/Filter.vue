<template>
  <div class="filter_table">
    <div v-for="(field, index) in fields" class="form-group" :key="index">
      <label v-text="field.label" :for="field.name"></label>

      <input v-if="field.type  == 'number'" v-int type="text" :name="field.name" :value="data.value" @input="change(index, $event.target)">
     
      <input v-else-if="field.type  == 'checkbox'"  type="checkbox" :name="field.name" :checked="data.value" @click="change(index, $event.target)">
     
      <input v-else  type="text" :name="field.name" :value="data.value" @input="change(index, $event.target)">

    </div>
  </div>
</template>
<script>
import debounce from "debounce";

export default {
  props: ["fields"],
  data() {
    return {
      data: []
    };
  },
  created() {
    this.change = debounce(this.change, 500);

    for (let i = 0; i < this.fields.length; i++) {
      this.data.push({
        name: this.fields[i].name,
        value: this.fields[i].value
      });
    }
  },
  methods: {
    change(index, el) {

      if (el.type == "checkbox") {
        this.data[index].value = el.checked ? 1 : '';
      } else {
        this.data[index].value = el.value;
      }

      this.$emit("changed", this.data.filter((item) => !!item.value));
    }
  }
};
</script>
