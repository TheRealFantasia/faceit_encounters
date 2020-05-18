<template>
    <nav class="navbar" role="navigation" aria-label="main navigation">
        <div class="navbar-brand">
            <a role="button" class="navbar-burger burger" v-on:click="toggleMenu()">
                <span aria-hidden="true"></span>
                <span aria-hidden="true"></span>
                <span aria-hidden="true"></span>
            </a>
        </div>
        <div class="navbar-menu" :class="{ 'is-active': menuActive }">
            <div class="navbar-end">
                <a class="navbar-item" v-on:click="donate()">
                    <span class="icon" style="margin-right: 10px"><i class="fa fa-heart fa-lg"></i></span>Donate
                </a>
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
        <form ref="donateForm" action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top" class="is-hidden">
            <input type="hidden" name="cmd" value="_s-xclick" />
            <input type="hidden" name="hosted_button_id" value="D68BCC3EEUP72" />
        </form>
    </nav>
</template>

<script>
    import axios from 'axios';

    export default {
        name: "Navbar",
        data() {
            return {
                isLoading: false,
                menuActive: false
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
            },
            donate() {
                this.$refs['donateForm'].submit();
            },
            toggleMenu() {
                this.menuActive = !this.menuActive;
            }
        }
    }
</script>

<style scoped>

</style>