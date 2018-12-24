export default {
    data() {
        return {
            dataSet: false,
            showSessionMessage: false,
            isWaiting: true,
            items: [],
            sortKey: "",
        }
    },
    created() {
        this.fetch();
        this.showSessionMessage = true;
        setTimeout(() => this.showSessionMessage = false, 5000);
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
                    location.pathname +
                    this.addParamstoSearch("page", /page=\d+/, params[0].value)
                );
            } else if (params.filter(el => el.name == "sort").length) {
                let search = location.search;
                search = this.addParamstoSearch(
                    "sort",
                    /sort=\w+/,
                    params.filter(el => el.name == "sort")[0].value,
                    search
                );
                search = this.addParamstoSearch(
                    "order",
                    /order=\w+/,
                    params.filter(el => el.name == "order")[0].value,
                    search
                );

                return location.pathname + search;
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

            history.pushState(null, null, url[1] ? "?" + url[1] : location.pathname);
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
                    params.push({ name: item.name, value: item.value });
                }
            }
            if (objSearch["sort"] && objSearch["order"]) {
                params.push({ name: "sort", value: objSearch["sort"] });
                params.push({ name: "order", value: objSearch["order"] });
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
        addParamstoSearch(name, pattern, value, search = location.search) {
            if (search.indexOf(`${name}=`) != -1) {
                search = search.replace(pattern, `${name}=` + value);
            } else if (search.indexOf("?") != -1) {
                search += `&${name}=` + value;
            } else {
                search += `?${name}=` + value;
            }

            return search;
        },
        sortBy(index) {
            let newVal = !this.sortItems[index].sort;

            this.sortItems.forEach(function (item, i, items) {
                items[i].sort = 0;
            });

            this.sortItems[index].sort = newVal;

            this.fetch([
                {
                    name: "sort",
                    value: this.sortItems[index].name
                },
                {
                    name: "order",
                    value: this.sortItems[index].sort ? "asc" : "dsc"
                }
            ]);
        }
    }
}