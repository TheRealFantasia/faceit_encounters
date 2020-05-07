import Vue from 'vue';
import Home from './components/home/Home';
import '../css/app.scss';
const routes = require('../../public/js/fos_js_routes.json');
import Routing from '../../vendor/friendsofsymfony/jsrouting-bundle/Resources/public/js/router.min';

Routing.setRoutingData(routes);

new Vue({
    el: '#app',
    components: {
        Home
    },
    beforeMount() {
        Vue.prototype.$routing = Routing;
        Vue.prototype.$user = JSON.parse(this.$el.attributes['data-user'].value);
    }
});
