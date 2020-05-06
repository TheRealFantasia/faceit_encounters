import Vue from 'vue';
import Home from './components/home/Home';
import '../css/app.scss';

new Vue({
    el: '#app',
    components: {
        Home
    },
    beforeMount() {
        Vue.prototype.$user = JSON.parse(this.$el.attributes['data-user'].value);
    }
});
