<template>
    <div class="card" style="margin-top: 20px">
        <div class="card-content">
            <div class="content">
                <div class="columns is-flex is-vcentered has-text-centered">
                    <div class="column">
                        <div class="columns is-vcentered">
                            <div class="column is-8">
                                {{ this.match['team1'] }}
                            </div>
                            <div class="column is-2" v-if="match.userTeam === 'team1'">
                                <figure class="image is-32x32" :title="user.username">
                                    <img v-if="user.picture" class="is-rounded" :src="user.picture" alt="own profile image">
                                    <img v-else class="is-rounded" :src="defaultImage" alt="default image">
                                </figure>
                            </div>
                            <div class="column is-2" v-if="match.searchedTeam === 'team1'">
                                <figure class="image is-32x32" :title="otherName">
                                    <img v-if="searchedUser.picture" class="is-rounded" :src="searchedUser.picture" alt="other profile image">
                                    <img v-else class="is-rounded" :src="defaultImage" alt="default image">
                                </figure>
                            </div>
                        </div>
                    </div>
                    <div class="column">
                        <div class="container">
                            <span :class="this.match.winner === 'team1' ? 'winner' : 'loser'">
                                {{ this.match.winner === 'team1' ? 'W' : 'L' }}
                            </span>
                            <span style="margin: 0 10px 0 10px">
                                vs.
                            </span>
                            <span :class="this.match.winner !== 'team1' ? 'winner' : 'loser'">
                                {{ this.match.winner !== 'team1' ? 'W' : 'L' }}
                            </span>
                            <br>
                            <a class="button is-primary is-small" style="margin-top: 15px" :href="match.url" target="_blank">Go to match page</a>
                        </div>
                    </div>
                    <div class="column">
                        <div class="columns is-vcentered">
                            <div class="column is-2" v-if="match.searchedTeam === 'team2'" :class="{'is-offset-2': match.userTeam === 'team1'}">
                                <figure class="image is-32x32" :title="otherName">
                                    <img v-if="searchedUser.picture" class="is-rounded" :src="searchedUser.picture" alt="other profile image">
                                    <img v-else class="is-rounded" :src="defaultImage" alt="default image">
                                </figure>
                            </div>
                            <div class="column is-2" v-if="match.userTeam === 'team2'" :class="{'is-offset-2': match.searchedTeam === 'team1'}">
                                <figure class="image is-32x32" :title="user.username">
                                    <img v-if="user.picture" class="is-rounded" :src="user.picture" alt="own profile image">
                                    <img v-else class="is-rounded" :src="defaultImage" alt="default image">
                                </figure>
                            </div>
                            <div class="column is-8" v-bind:class="{'is-offset-4': (match.userTeam === 'team1') && (match.searchedTeam === 'team1')}">
                                {{ match.team2 }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: "MatchCard",
        props: {
            match: {
                type: Object,
                required: true
            },
            searchedUser: {
                type: Object,
                required: true
            }
        },
        data() {
            return {
                user: this.$user,
                defaultImage: require('../../../images/default_player.jpg').default
            }
        }
    }
</script>

<style scoped>

</style>