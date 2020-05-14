<template>
    <section class="hero is-fullheight-with-navbar">
        <div class="hero-body">
            <div class="container has-text-centered">
                <h1 class="title">
                    Faceit Encounters
                </h1>
                <div class="columns">
                    <div class="column is-half is-offset-one-quarter">
                        <label class="subtitle" for="nickname">Enter the Faceit nickname you want to look up</label>
                        <input id="nickname" style="margin-top: 20px" class="input" type="text" v-model="input.faceitNickname" placeholder="Faceit Nickname" :class="{ 'is-danger': isNicknameEmpty }" />
                        <a class="button is-primary" style="margin-top: 20px" v-on:click="search()" v-bind:class="{ 'is-loading': isLoading }">Search</a>
                    </div>
                </div>
                <div class="columns" v-if="!isInitial && !isLoading">
                    <div class="column is-10 is-offset-1">
                        <div class="container">
                            <div v-if="model.success">
                                <div v-if="model.playedWith">
                                    <div v-for="match in model.matches">
                                        <MatchCard :match="match" :other-image="model.otherPicture" :other-name="input.searchedName"></MatchCard>
                                    </div>
                                </div>
                                <div v-else>
                                    <span>You have not encountered {{ input.searchedName }}</span>
                                </div>
                            </div>
                            <div v-else>
                                <span>{{ model.message }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>

<script>
    import axios from "axios";
    import MatchCard from "../match_card/MatchCard";

    export default {
        name: "Search",
        components: {
            MatchCard
        },
        data () {
            return {
                user: this.$user,
                input: {
                    faceitNickname: '',
                    searchedName: ''
                },
                isInitial: true,
                isLoading: false,
                isNicknameEmpty: false,
                model: {
                    otherPicture: '',
                    playedWith: false,
                    matches: [{
                        otherTeam: '',
                        ownTeam: '',
                        team1: '',
                        team2: '',
                        url: '',
                        winner: ''
                    }],
                    message: '',
                    success: false
                }
            }
        },
        methods: {
            search() {
                let input = this.input;
                if (input.faceitNickname === '') {
                    this.isNicknameEmpty = true;
                    return;
                }
                this.isNicknameEmpty = false;

                this.isLoading = true;
                this.isInitial = false;
                input.searchedName = input.faceitNickname;

                axios.get(this.$routing.generate('app_api_searchinrecentmatches'), {
                    params: {
                        other: input.faceitNickname
                    }
                }).then(res => {
                    this.model = res.data;
                    this.isLoading = false;
                });
            }
        }
    }
</script>

<style scoped>

</style>