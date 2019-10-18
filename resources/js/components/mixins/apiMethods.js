export default {
    data() {
        return {
            currentPage: 1,
            perPage: 10,
            totalRows: null,
            API_URL: '/',
            isFirstRequest: true
        }
    },
    methods: {
        async fetchData(url) {
            let that = this;

            return await axios.get(url)
                .then(function ({data}) {
                        that.currentPage = data.data.current_page;
                        that.perPage = data.data.per_page;
                        that.totalRows = data.data.total;


                        return data.data.data;
                    }
                )
        },
        myProvider(ctx) {
            let url = '';
            if (this.isFirstRequest) {
                url = this.getFirstUrl(ctx);

                this.isFirstRequest = false;
            } else {
                url = this.getUrl(ctx);
            }
            return this.fetchData(url).catch(error => {
                console.error(error)
            });
        },
        getFirstUrl: (function (ctx) {

            if (parseInt(this.$route.query.page) > 0 || parseInt(this.$route.query.limit) > 0) {

                return this.processUrlFromQuery();
            }

            let url = this.API_URL + `?page=${this.currentPage}&limit=${this.perPage}`;
            if (!!ctx && !!ctx.sortBy) {
                url += '&sort=' + ctx.sortBy + '&order=' + (ctx.sortDesc ? 'desc' : 'asc');
            }

            return url;
        }),
        getUrl: (function (ctx) {

            let order = '',
                sort = '';
            if (!!ctx && !!ctx.sortBy) {
                order = ctx.sortDesc ? 'desc' : 'asc';
                sort = ctx.sortBy;
            }

            let cleanFilter = {};
            for (let key in this.filters) {
                if (this.filters.hasOwnProperty(key) && !!this.filters[key]) {
                    cleanFilter[key] = this.filters[key];
                    this.resetCurrentPage(key);
                }
            }

            this.$router.push({
                path: location.path, query: {
                    page: this.currentPage,
                    limit: this.perPage,
                    order: order,
                    sort: sort,
                    ...cleanFilter
                }
            });

            return this.processUrlFromQuery();
        }),
        resetCurrentPage: function (key) {
            if (this.$route.query[key] != this.filters[key]) {
                this.currentPage = 1;
            }
        },
        processUrlFromQuery: function () {
            let url = this.API_URL + '?';

            for (let key in this.$route.query) {
                if (this.$route.query.hasOwnProperty(key) && this.$route.query[key]) {
                    url += '&' + key + '=' + this.$route.query[key];
                }
            }
            url = url.replace('?&', '?');

            return url;
        },
        debounceInput: _.debounce(function (e) {
            this.filters[e.field.key] = e.$event.target.value;
        }, 500)
    },
}
