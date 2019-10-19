<template>
    <div>
        <header>
            <nav class="navbar navbar-expand-md navbar-light navbar-laravel">
                <div class="container">
                    <a class="navbar-brand" href="/" >
                        {{ this.appName}}
                    </a>

                    <div class="collapse navbar-collapse" id="navbarSupportedContent">

                        <!-- Right Side Of Navbar -->
                        <ul class="navbar-nav ml-auto">
                            <!-- Authentication Links -->
                            <template v-if="!$auth.check()">
                                <li class="nav-item">
                                    <router-link :to="{ name: 'login' }" class="nav-link">Login</router-link>
                                </li>
                                <li class="nav-item">
                                    <router-link :to="{ name: 'register' }" class="nav-link" >Register</router-link>
                                </li>
                            </template>
                            <template v-else>
                                <li class="nav-item dropdown">
                                    <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        {{ this.getUserName }} <span class="caret"></span>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                        <a href="#" @click.prevent="$auth.logout()" class="nav-link">Logout</a>
                                    </div>
                                </li>

                                <li class="nav-item">
                                    <span class="nav-link">Balance: {{ this.getUserBalance }}</span>
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
                                         :key="index"
                                         :to="item.url"
                                         tag="li"
                                         class="list-group-item"
                                         active-class="active"

                            >
                                <a>{{ item.text }}</a>
                            </router-link>
                            <template v-if="this.getIsAdmin">
                                <router-link v-for="(item, index) in menuList"
                                             v-if="item.forAdmin"
                                             :key="index"
                                             :to="item.url"
                                             tag="li"
                                             class="list-group-item"
                                             active-class="active"
                                >
                                    <a>{{ item.text }}</a>
                                </router-link>
                            </template>


<!--                                                        <router-link :to="{'name': 'transactions.index'}"-->
<!--                                                                     :key="2"-->
<!--                                                                     tag="li"-->
<!--                                                                     class="list-group-item"-->
<!--                                                                     active-class="active"-->
<!--                                                        >-->
<!--                                                            Transactions-->
<!--                                                        </router-link>-->
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

    export default {
        data(){
            return {
                appName: window.config.appName
            }
        },
        computed: {
            getIsAdmin(){
                return !!window.user ?  window.user.isAdmin : true;
            },
            getUserName(){
                return window.user.name || '';
            },
            getUserBalance(){
                return (window.user.currentBalance || 0) + ' PW';
            },
            ...mapGetters('menu', {
                menuList: 'items'
            }),
        },
        methods: {

        }
    }
</script>

<style>
    .menu{
        border-right: 1px solid #ddd;
    }

    .list-group-item{
        transition: background 0.3s, color 0.3s;
    }

    .list-group-item a{
        text-decoration: none;
    }

    .list-group-item.active a{
        color: inherit;
    }

    .slide-enter{

    }

    .slide-enter-active{
        animation: slideIn 0.5s;
    }

    .slide-enter-to{

    }

    .slide-leave{

    }

    .slide-leave-active{
        animation: slideOut 0.5s;
    }

    .slide-leave-to{

    }

    @keyframes slideIn{
        from{transform: rotateY(90deg);}
        to{transform: rotateY(0deg);}
    }

    @keyframes slideOut{
        from{transform: rotateY(0deg);}
        to{transform: rotateY(90deg);}
    }
</style>
