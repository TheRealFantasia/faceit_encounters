<template>
    <section class="hero is-fullheight-with-navbar">
        <div class="hero-body">
            <div class="container has-text-centered">
                <h1 class="title">
                    Faceit Encounters
                </h1>
                <h2 class="subtitle">
                    Find out if you have recently encountered a specific player on Faceit
                </h2>
                <a v-on:click="login()" class="button is-primary">
                    Log in
                </a>
            </div>
        </div>
    </section>
</template>

<script>
    export default {
        name: "LoginHero",
        methods: {
            login() {
                window.location.href = this.$routing.generate('connect_faceit_start');
            },
        }
    }
</script>

<style scoped>

</style>