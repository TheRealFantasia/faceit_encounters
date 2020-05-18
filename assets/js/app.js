import Vue from 'vue';
import Home from './components/home/Home';
import '../css/app.scss';
const routes = require('../../public/js/fos_js_routes.json');
import Routing from '../../vendor/friendsofsymfony/jsrouting-bundle/Resources/public/js/router.min';
import { RecycleScroller } from 'vue-virtual-scroller';
import 'vue-virtual-scroller/dist/vue-virtual-scroller.css';

Vue.component('RecycleScroller', RecycleScroller);
Routing.setRoutingData(routes);

new Vue({
    el: '#app',
    components: {
        Home
    },
    beforeMount() {
        Vue.prototype.$routing = Routing;
        Vue.prototype.$user = JSON.parse(this.$el.attributes['data-user'].value);
    },
    mounted() {
        this.$el.setAttribute('data-user', null);
    }
});
