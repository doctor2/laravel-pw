<template>
<div>
  <vue-bootstrap-typeahead
    :data="userNames"
    v-model="nameSearch"
    ref="typeahead"
    :serializer="u => u.name"
    placeholder="Type an user name..."
    @hit="selectedUser = $event"
  />
  <input type="hidden" name="user_name" :value="selectedUser.name">
  <input type="hidden" name="user_id" :value="selectedUser.id">

</div>
</template>

<script>
import VueBootstrapTypeahead from 'vue-bootstrap-typeahead'

const API_URL = location.origin +'/users?name=:query'

export default {
  props:['user_name', 'user_id'],
  data() {
    return {
      userNames: [],
      nameSearch: '',
      selectedUser: this.defaultUser()
    }
  },
  mounted(){
      this.selectedUser = {name:this.user_name, id: this.user_id};
      this.$refs.typeahead.$data.inputValue = this.user_name;
  },
  components: {
        VueBootstrapTypeahead
    },
  methods: {
    async getNames(query) {
      const res = await fetch(API_URL.replace(':query', query))
      const users = await res.json()

      this.userNames = users
    },
    defaultUser(){
      return {name: null, id: null};
    }
  },

  watch: {
    nameSearch: _.debounce(function(name) { 
      this.getNames(name);  
      if(this.nameSearch != this.selectedUser.name){
        this.selectedUser = this.defaultUser();
      }
    }, 500)
  }
}

</script>