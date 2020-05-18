<template>
    <div>
        <Navbar/>
        <LoginHero v-if="!this.$user.username"/>
        <Search v-else/>
    </div>
</template>

<script>
    import Navbar from "../navbar/Navbar";
    import LoginHero from "../login_hero/LoginHero";
    import Search from "../search/Search";
    import AllPlayers from "../all_players/AllPlayers";

    export default {
        components: {
            Navbar,
            LoginHero,
            Search,
            AllPlayers
        },
        name: "Home"
    }
</script>

<style scoped>

</style>