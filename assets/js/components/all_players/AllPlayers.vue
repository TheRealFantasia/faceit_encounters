<template>
    <RecycleScroller
        style="height: 100%"
        :items="users"
        :item-size="32"
        key-field="id"
        v-slot="{ item }"
    >
        <span>{{ item.nickname }}</span>
    </RecycleScroller>
</template>

<script>
    export default {
        name: "AllPlayers",
        data() {
            return {
                users: [
                    {
                        id: 1,
                        nickname: 'Test1'
                    },
                    {
                        id: 2,
                        nickname: 'Test2'
                    }
                ]
            }
        }
    }
</script>

<style scoped>

</style>