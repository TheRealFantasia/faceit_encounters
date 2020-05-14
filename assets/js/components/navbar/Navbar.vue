<template>
    <nav class="navbar" role="navigation" aria-label="main navigation">
        <div class="navbar-menu">
            <div class="navbar-end">
                <a href="https://github.com/TheRealFantasia/faceit_encounters" target="_blank" class="navbar-item">
                    <span class="icon" style="margin-right: 10px"><i class="fa fa-github fa-lg"></i></span>View on GitHub
                </a>
                <div v-if="this.$user.username" class="navbar-item has-dropdown is-hoverable">
                    <a class="navbar-link">
                        <figure class="image is-32x32">
                            <img class="is-rounded" :src="this.$user.picture">
                        </figure>
                        <span style="margin-left: 10px">{{ this.$user.username }}</span>
                    </a>
                    <div class="navbar-dropdown">
                        <div class="navbar-item">
                            <a v-on:click="logout()" class="button is-primary is-fullwidth" v-bind:class="{ 'is-loading': this.isLoading }">
                                Log out
                            </a>
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