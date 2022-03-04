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
        path: '/campania',
        component: require('./view/campania.vue').default
    },
    {
        path: '/zpl',
        component: require('./view/zpl.vue').default
    },
    {
        path: '/variedad',
        component: require('./view/variedad.vue').default
    },
    {
        path: '/presentacion',
        component: require('./view/presentacion.vue').default
    },
    {
        path: '/marca.caja',
        component: require('./view/marca.caja.vue').default
    },
    {
        path: '/tipo.empaque',
        component: require('./view/tipo.empaque.vue').default
    },
    {
        path: '/marca.empaque',
        component: require('./view/marca.empaque.vue').default
    },
    {
        path: '/plu',
        component: require('./view/plu.vue').default
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
        path: '/camara.ingreso',
        component: require('./view/camara.ingreso.vue').default
    },
    {
        path: '/camara.semaforizacion',
        component: require('./view/camara.semaforizacion.vue').default
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
        path: '/operacion',
        component: require('./view/operacion.vue').default
    },
    {
        path: '/operacion/new',
        component: require('./view/operacion.new.vue').default
    },
    {
        path: '/operacion/:id',
        component: require('./view/operacion.count.vue').default
    },
    {
        path: '/produccion',
        component: require('./view/produccion.vue').default
    },
    {
        path: '/produccion/new',
        component: require('./view/produccion.new.vue').default
    },
    {
        path: '/presentacion.linea',
        component: require('./view/presentacion.linea.vue').default
    },
    //Paletizado
    {
        path: '/paletizado/new',
        component: require('./view/paletizado/New.vue').default
    },
    {
        path: '/paletizado/newLleno',
        component: require('./view/paletizado/NewLleno.vue').default
    },
    {
        path: '/paletizado/remonte',
        component: require('./view/paletizado/Remonte.vue').default
    },
    {
        path: '/paletizado/reetiquetar',
        component: require('./view/paletizado/Reetiquetar.vue').default
    },
    {
        path: '/palet',
        component: require('./view/paletizado/Palet.vue').default
    },
    {
        path: '/palet/:id',
        component: require('./view/paletizado/Palet.vue').default
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
        path: '/descarte',
        component: require('./view/descarte.vue').default
    },
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
        path: '/reporte/lanzado',
        component: require('./view/reporte/Lanzado.vue').default
    },
    {
        path: '/reporte/rendimiento-linea',
        component: require('./view/reporte/RendimientoLinea.vue').default
    },
    {
        path: '/reporte/balance-materia',
        component: require('./view/reporte/BalanceMateria.vue').default
    },
    {
        path: '/reporte/producto-terminado',
        component: require('./view/reporte/ProductoTerminado.vue').default
    },
    {
        path: '/reporte/producto-terminado-linea',
        component: require('./view/reporte/ProductoTerminadoLinea.vue').default
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
        path: '/reporte/bono-personal',
        component: require('./view/reporte/BonoPersonal.vue').default
    },
    {
        path: '/reporte/consolidado-bonos',
        component: require('./view/reporte/ConsolidadoBonos.vue').default
    },
    {
        path: '/reporte/consumo-viaje',
        component: require('./view/reporte/ConsumoViaje.vue').default
    },
    {
        path: '/dashboard/consumo-viaje',
        component: require('./view/dashboard/ConsumoViaje.vue').default
    },
    {
        path: '/dashboard/aforo',
        component: require('./view/dashboard/Aforo.vue').default
    },
    {
        path: '/dashboard/rendimiento-linea',
        component: require('./view/dashboard/RendimientoLinea.vue').default
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