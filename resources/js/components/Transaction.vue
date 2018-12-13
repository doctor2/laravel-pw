<template>
  <div class="card-body">
    <filter-table :fields="filter_fields" @changed="fetch"></filter-table>
    <table v-if="items.length != 0" class="table card-body-table">
      <thead>
        <tr>
          <th scope="col">Date/Time</th>
          <th scope="col">Correspondent Name</th>
          <th scope="col">Amount</th>
          <th scope="col">Resulting balance</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="(item, index) in items" :key="index">
          <th scope="row" v-text="item.created_at"></th>
          <td v-text="item.user_name"></td>
          <td v-text="item.amount + ' ( ' + item.transaction_type + ' )'"></td>
          <td v-html="getBalanceWithLink(item)"></td>
        </tr>
      </tbody>
    </table>
    <h3 v-show="showNotFound">Not a single transaction has been found.</h3>
    <paginator :dataSet="dataSet" @changed="fetch"></paginator>
  </div>
</template>

<script>
export default {
  data() {
    return {
      dataSet: false,
      isWaiting: true,
      items: [],
      filter_fields: [
        {
          name: "date",
          label: "Date/Time",
          value: ""
        },
        {
          name: "user_name",
          label: "Correspondent Name",
          value: ""
        },
        {
          name: "amount",
          label: "Amount",
          value: ""
        },
        {
          name: "user_balance",
          label: "Resulting balance",
          value: ""
        }
      ]
    };
  },
  created() {
    this.fetch();
  },
  computed: {
    showNotFound() {
      return !this.isWaiting && this.items.length == 0;
    }
  },
  methods: {
    fetch(params) {
      let url = this.getUrl(params);

      axios
        .get(url)
        .then(this.refresh)
        .catch(error => {
          if (error.response.status == 401) {
            location.reload();
          }
        });

      if (!this.isWaiting) {
        this.updateUrl(url);
      }
    },
    getUrl(params) {
      if (!params) {
        params = this.setDefaultParamsFromUrl();
      } else if (!params.length) {
        return location.pathname;
      }

      if (params.filter(el => el.name == "page").length) {
        return (
          location.pathname + this.addParamstoSearch("page", params[0].value)
        );
      }

      let url =
        location.pathname + "?" + params[0].name + "=" + params[0].value;

      for (let i = 1; i < params.length; i++) {
        url += "&" + params[i].name + "=" + params[i].value;
      }

      return url;
    },
    refresh({ data }) {
      this.dataSet = data;
      this.items = data.data;

      window.scrollTo(0, 0);

      this.isWaiting = false;
    },
    updateUrl(fullUrl) {
      let url = fullUrl.split("?");

      history.pushState(null, null, url[1] ? "?" + url[1] : "/");
    },
    getBalanceWithLink(item) {
      let balance = item.user_balance;
      if (item.transaction_type == "DEBIT") {
        balance += ` <a href="/transactions/create?key=${
          item.transaction_key
        }">Repeat</a>`;
      }
      return balance;
    },
    setDefaultParamsFromUrl() {
      let objSearch = this.searchToObject();
      let params = [
        {
          name: "page",
          value: objSearch.page !== undefined ? objSearch.page : 1
        }
      ];

      for (let i = 0; i < this.filter_fields.length; i++) {
        let item = this.filter_fields[i];
        if (objSearch[item.name]) {
          this.filter_fields[i].value = objSearch[item.name];
          params.push({
            name: item.name,
            value: item.value
          });
        }
      }
      return params;
    },
    searchToObject() {
      let pairs = location.search.substring(1).split("&"),
        obj = {},
        pair,
        i;

      for (i in pairs) {
        if (pairs[i] === "") continue;

        pair = pairs[i].split("=");
        obj[decodeURIComponent(pair[0])] = decodeURIComponent(pair[1]);
      }

      return obj;
    },
    addParamstoSearch(name, value) {
      let search = location.search;
      if (search.indexOf("page=") != -1) {
        search = search.replace(
          new RegExp(`${name}=(\\d+)`),
          `${name}=` + value
        );
      } else if (search.indexOf("?") != -1) {
        search += `&${name}=` + value;
      } else {
        search += `?${name}=` + value;
      }

      return search;
    }
  }
};
</script>

