<template>
    <div>
        <header>
            <nav class="navbar navbar-expand-md navbar-light navbar-laravel">
                <div class="container">

                    <router-link :to="{ name: 'transactions.index' }" class="navbar-brand">{{ appName}}</router-link>

                    <div class="collapse navbar-collapse" id="navbarSupportedContent">

                        <!-- Right Side Of Navbar -->
                        <ul class="navbar-nav ml-auto">
                            <!-- Authentication Links -->
                            <template v-if="!$auth.check()">
                                <li class="nav-item">
                                    <router-link :to="{ name: 'login' }" class="nav-link">Login</router-link>
                                </li>
                                <li class="nav-item">
                                    <router-link :to="{ name: 'register' }" class="nav-link">Register</router-link>
                                </li>
                            </template>
                            <template v-else>
                                <li class="nav-item dropdown">
                                    <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button"
                                       data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        {{ userName }} <span class="caret"></span>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                        <a href="#" @click.prevent="$auth.logout()" class="nav-link">Logout</a>
                                    </div>
                                </li>

                                <li class="nav-item">
                                    <span class="nav-link">Balance: {{ userBalance }}  PW </span>
                                </li>
                            </template>

                        </ul>
                    </div>
                </div>
            </nav>
        </header>
        <section>
            <div class="container">
                <div class="row">
                    <div class="col col-sm-3 menu">
                        <ul class="list-group">
                            <router-link v-for="(item, index) in menuList"
                                         v-if="!item.forAdmin"
                                         :exact="item.url == '/'"
                                         :key="index"
                                         :to="item.url"
                                         tag="li"
                                         class="list-group-item"
                                         active-class="active"
                            >
                                <a>{{ item.text }}</a>
                            </router-link>
                            <template v-if="isLoaded">
                                <router-link v-for="(item, index) in menuList"
                                             v-if="item.forAdmin &&  isAdmin()"
                                             :key="index"
                                             :to="item.url"
                                             tag="li"
                                             class="list-group-item"
                                             active-class="active"
                                >
                                    <a>{{ item.text }}</a>
                                </router-link>
                            </template>
                        </ul>
                    </div>
                    <div class="col col-sm-9">
                        <transition name="slide" mode="out-in">
                            <router-view></router-view>
                        </transition>
                    </div>
                </div>
            </div>
        </section>
    </div>
</template>

<script>
    import {mapGetters} from 'vuex';
    import {mapActions} from 'vuex';

    export default {
        data() {
            return {
                appName: window.config.appName
            }
        },
        computed: {
            isLoaded() {
                if(!!this.$auth.watch.data){
                    this.setUserData();
                }
                return !!this.$auth.watch.data;
            },
            ...mapGetters('menu', {
                menuList: 'items'
            }),
            ...mapGetters('user', {
                userName: 'name',
                userBalance: 'balance'
            }),
        },
        methods: {
            isAdmin() {
                return this.$auth.watch.data.isAdmin;
            },
            ...mapActions('user', {
                setUserName: 'setName',
                setUserBalance: 'setBalance'
            }),
            setUserData(){
                this.setUserName(this.$auth.watch.data.name);

                this.setUserBalance(this.$auth.watch.data.currentBalance);
            }
        }
    }
</script>

<style>
    .menu {
        border-right: 1px solid #ddd;
    }

    .list-group-item {
        transition: background 0.3s, color 0.3s;
    }

    .list-group-item a {
        text-decoration: none;
    }

    .list-group-item.active a {
        color: inherit;
    }

    .slide-enter {

    }

    .slide-enter-active {
        animation: slideIn 0.5s;
    }

    .slide-enter-to {

    }

    .slide-leave {

    }

    .slide-leave-active {
        animation: slideOut 0.5s;
    }

    .slide-leave-to {

    }

    @keyframes slideIn {
        from {
            transform: rotateY(90deg);
        }
        to {
            transform: rotateY(0deg);
        }
    }

    @keyframes slideOut {
        from {
            transform: rotateY(0deg);
        }
        to {
            transform: rotateY(90deg);
        }
    }
</style>
