<template>
    <div class="container">
        <div class="columns is-centered">
            <div class="column is-narrow">
                <h1 class="title">
                    Faceit Encounters
                </h1>
                <input class="input" type="text" v-model="input.faceitNickname" placeholder="Faceit Nickname" />
                <a class="button is-primary" v-on:click="search()">Search</a>
                <div v-if="isLoading">
                    <span>Loading...</span>
                </div>
                <div v-if="!isInitial && !isLoading">
                    <div v-if="model.playedWith">
                        <span>You played with or against {{ input.searchedName }}</span>
                        <br>
                        <ul>
                            <li v-for="match in model.matches">
                                {{ match.team1 }} vs. {{ match.team2 }}
                            </li>
                        </ul>
                    </div>
                    <div v-else>
                        <span>You have not played with or against {{ input.searchedName }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import axios from 'axios';
    export default {
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
                let input = this.input;
                if (input.faceitNickname === '') {
                    return;
                }

                this.isLoading = true;
                this.isInitial = false;
                this.searchedName = input.faceitNickname;

                axios.get('http://localhost:8000/api/search', {
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