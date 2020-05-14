<template>
    <div>
        <Navbar></Navbar>
        <LoginHero v-if="!this.$user.username"></LoginHero>
        <Search v-else></Search>
    </div>
</template>

<script>
    import Navbar from "../navbar/Navbar";
    import LoginHero from "../login_hero/LoginHero";
    import Search from "../search/Search";

    export default {
        components: {
            Navbar,
            LoginHero,
            Search
        },
        name: "Home"
    }
</script>

<style scoped>

</style>