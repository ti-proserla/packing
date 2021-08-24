import Vue from 'vue';
import VueRouter from 'vue-router'

Vue.use(VueRouter)

var routes =[
    { 
        path: '/', 
        component: require('./view/Home.vue').default
    },
    { 
        path: '/lanzado', 
        component: require('./view/lanzado/index.vue').default
    },
    {
        path: '/acopio/new',
        component: require('./view/acopio/New.vue').default
    },
    {
        path: '/materia',
        name: 'administracion.materia',
        component: require('./view/materia.vue').default,
    },
    {
        path: '/variedad',
        component: require('./view/variedad.vue').default
    },
    {
        path: '/calibre',
        component: require('./view/calibre.vue').default
    },
    {
        path: '/tipo',
        component: require('./view/tipo.vue').default
    },
    {
        path: '/fundo',
        component: require('./view/fundo.vue').default
    },
    {
        path: '/impresora',
        component: require('./view/impresora.vue').default
    },
    {
        path: '/cliente',
        component: require('./view/cliente.vue').default
    },
    {
        path: '/producto',
        component: require('./view/producto.vue').default
    },
    {
        path: '/despacho',
        component: require('./view/despacho.vue').default
    },
    {
        path: '/despacho/new',
        component: require('./view/despacho.new.vue').default
    },
    {
        path: '/despacho/:id',
        component: require('./view/despacho.count.vue').default
    },
    {
        path: '/produccion',
        component: require('./view/produccion.vue').default
    },
    {
        path: '/produccion/new',
        component: require('./view/produccion.new.vue').default
    },
    // {
    //     path: '/produccion/:id',
    //     component: require('./view/produccion.count.vue').default
    // },
    //Paletizado
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
        path: '/frio',
        component: require('./view/paletizado/ListFrio.vue').default
    },
    {
        path: '/ini',
        component: require('./view/paletizado/List.vue').default
    },
    //ACOPIO
    {
        path: '/acopio/lote',
        component: require('./view/lote/Acopio.vue').default
    },
    {
        path: '/acopio/lote/new',
        component: require('./view/lote/New.vue').default
    },
    {
        path: '/acopio/sublote',
        component: require('./view/lote/sub-lote.vue').default
    },
    //Reportes
    {
        path: '/reporte/acopio',
        component: require('./view/reporte/Acopio.vue').default
    },
    {
        path: '/reporte/linea',
        component: require('./view/reporte/Linea.vue').default
    },
    {
        path: '/reporte/lote',
        component: require('./view/reporte/Lote.vue').default
    },
    {
        path: '/reporte/personal',
        component: require('./view/reporte/Personal.vue').default
    },
    {
        path: '/dispensador',
        component: require('./view/Dispensador.vue').default
    },
    {
        path: '/etiqueta.caja',
        component: require('./view/etiqueta.caja.vue').default
    },
    {
        path: '/reporte/avance-lote',
        component: require('./view/reporte/AvanceLote.vue').default
    },
];
var router=new VueRouter({
    mode: 'history',
    routes,
    linkActiveClass: 'active'
});
export default router;