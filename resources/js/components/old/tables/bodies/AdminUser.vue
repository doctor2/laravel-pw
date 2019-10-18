<template>
  <tbody>
    <tr v-for="(item, index) in items" :key="index">
      <td v-text="item.created_at"></td>
      <td v-html="getNameWithLink(item)"></td>
      <td v-text="item.email"></td>
      <td v-html="getBanWithLink(item)"></td>
    </tr>
  </tbody>
</template>

<script>
export default {
  props: ["items"],
  methods: {
    getNameWithLink(item) {
      return `<a href="/admin/users/${item.id}">${item.name}</a>`;
    },
    getBanWithLink(item) {
      return (
        (item.banned ? "yes" : "no") +
        `  <a href="/admin/users/edit/${item.id}">Edit</a>`
      );
    }
  }
};
</script>

