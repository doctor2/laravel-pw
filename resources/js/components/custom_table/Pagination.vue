<template>
    <nav v-if="(meta.total/meta.per_page) > 1">
        <ul class="pagination">
            <li class="page-item" :class="{ 'disabled': meta.current_page === 1 }">
                <a class="page-link" href="#" @click.prevent="switched(meta.current_page - 1)">
                    <span>&laquo;</span>
                </a>
            </li>
            <li class="page-item" v-for="x in meta.last_page" :class="{ 'active': meta.current_page === x }">
                <a class="page-link" href="#" @click.prevent="switched(x)">{{ x }}</a>
            </li>
            <li class="page-item" :class="{ 'disabled': meta.current_page === meta.last_page }">
                <a class="page-link" href="#" @click.prevent="switched(meta.current_page + 1)">
                    <span>&raquo;</span>
                </a>
            </li>
        </ul>
    </nav>
</template>

<script>
    export default {
        props: [
            'meta'
        ],
        methods: {
            switched (page) {
                if (this.pageIsOutOfBounds(page)) {
                    return;
                }

                this.$router.replace({
                    query: Object.assign({}, this.$route.query, { page: page })
                })
            },
            pageIsOutOfBounds (page) {
                return page <= 0 || page > this.meta.last_page
            }
        }
    }
</script>
