import Vue from 'vue';
import VueRouter from 'vue-router'

Vue.use(VueRouter)

var routes =[
    { 
        path: '/', 
        component: require('./view/Home.vue').default
    },
    {
        path: '/paletizado/new',
        component: require('./view/paletizado/New.vue').default
    },
    {
        path: '/paletizado/:id',
        component: require('./view/paletizado/Count.vue').default
    },
    {
        path: '/paletizado',
        component: require('./view/paletizado/List.vue').default
    },
    {
        path: '/ini',
        component: require('./view/paletizado/List.vue').default
    },
    {
        path: '/lote/new',
        component: require('./view/lote/New.vue').default
    }

];
var router=new VueRouter({
    mode: 'history',
    routes,
    linkActiveClass: 'active'
});
export default router;