<template>
    <div>
        <MainNavbar></MainNavbar>
        <div class="container">
            <div class="columns is-centered">
                <div class="column is-narrow">
                    <input class="input" type="text" v-model="input.faceitNickname" placeholder="Faceit Nickname" />
                    <a class="button is-primary" v-on:click="search()" v-bind:class="{ 'is-loading': isLoading }">Search</a>
                    <div v-if="!isInitial && !isLoading">
                        <div v-if="model.playedWith">
                            <span>You played with or against {{ input.searchedName }}</span>
                            <br>
                            <div class="card" v-for="match in model.matches">
                                <div class="card-content">
                                    <div class="content">
                                        <div class="columns is-vcentered is-vertical">
                                            <div class="column">
                                                {{ match.winner === 'team1' ? 'W' : 'L' }} {{ match.team1 }}
                                            </div>
                                            <div class="column">
                                                vs.
                                            </div>
                                            <div class="column">
                                                {{ match.winner === 'team1' ? 'L' : 'W' }} {{ match.team2 }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div v-else>
                            <span>You have not played with or against {{ input.searchedName }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import axios from 'axios';
    import MainNavbar from "../navbar/Navbar";

    export default {
        components: {
            MainNavbar
        },
        data () {
            return {
                input: {
                    faceitNickname: '',
                    searchedName: ''
                },
                isInitial: true,
                isLoading: false,
                model: {
                    playedWith: false,
                    matches: [{
                        otherTeam: '',
                        ownTeam: '',
                        team1: '',
                        team2: '',
                        url: '',
                        winner: ''
                    }],
                    success: false
                }
            }
        },
        beforeMount() {
            console.log(this.$user);
        },
        methods: {
            search() {
                console.log(this.$routing.generate('app_api_searchinrecentmatches'));

                let input = this.input;
                if (input.faceitNickname === '') {
                    return;
                }

                this.isLoading = true;
                this.isInitial = false;
                this.searchedName = input.faceitNickname;

                axios.get(this.$routing.generate('app_api_searchinrecentmatches'), {
                    params: {
                        other: input.faceitNickname
                    }
                }).then(res => {
                    this.model = res.data;
                    this.isLoading = false;
                });
            }
        },
        name: "Home"
    }
</script>

<style scoped>

</style>