<template>
    <nav class="navbar" role="navigation" aria-label="main navigation">
        <div class="navbar-brand">
            <a class="navbar-item" href="http://127.0.0.1:8000">
                <h1>Faceit Encounters</h1>
            </a>
        </div>
        <div class="navbar-menu">
            <div class="navbar-end">
                <div class="navbar-item">
                    <div v-if="!this.$user.username" class="buttons">
                        <a v-on:click="login()" class="button is-primary">
                            Log in
                        </a>
                    </div>
                    <div v-else>
                        <div class="navbar-item has-dropdown is-hoverable">
                            <a class="navbar-link">
                                <figure class="image is-32x32">
                                    <img class="is-rounded" :src="this.$user.picture">
                                </figure>
                                <strong style="margin-left: 10px">{{ this.$user.username }}</strong>
                            </a>
                            <div class="navbar-dropdown has-shadow">
                                <div class="navbar-item">
                                    <a v-on:click="logout()" class="button is-primary is-fullwidth" v-bind:class="{ 'is-loading': this.isLoading }">
                                        Log out
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>
</template>

<script>
    import axios from 'axios';
    export default {
        name: "Navbar",
        data() {
            return {
                isLoading: false
            }
        },
        methods: {
            login() {
                window.location.href = this.$routing.generate('connect_faceit_start');
            },
            logout() {
                this.isLoading = true;
                axios.get(this.$routing.generate('app_logout')).then(res => {
                    this.isLoading = false;
                    if (res.status === 200) {
                        location.reload();
                    }
                });
            }
        }
    }
</script>

<style scoped>

</style>