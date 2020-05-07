<template>
    <nav class="navbar" role="navigation" aria-label="main navigation">
        <div class="navbar-menu">
            <div class="navbar-end">
                <div class="navbar-item">
                    <div v-if="!this.$user.username" class="buttons">
                        <a v-on:click="login()" class="button is-primary">
                            Log in
                        </a>
                    </div>
                    <div v-else>
                        <a v-on:click="logout()" class="button is-primary" v-bind:class="{ 'is-loading': this.isLoading }">
                            Log out
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </nav>
</template>

<script>
    import axios from 'axios';
    export default {
        name: "MainNavbar",
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