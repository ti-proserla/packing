<template>
    <v-container fluid>
        <v-card>
            <v-card-title>Reporte Acopio</v-card-title>              
            <v-card-text>
                <v-row>
                    <v-col cols="12" sm=3>
                        <v-text-field
                            label="Desde:"
                            v-model="consulta.desde"
                            type="date">
                        </v-text-field>
                    </v-col>
                    <v-col cols="12" sm=3>
                        <v-text-field
                            label="Hasta:"
                            v-model="consulta.hasta"
                            type="date">
                        </v-text-field>
                    </v-col>
                    <v-col cols="12" sm=4>
                        <v-select
                            @change="buscar"
                                outlined
                                dense
                                v-model="consulta.cliente_id"
                                label="Productor:"
                                :items="clientes"
                                item-text="descripcion"
                                item-value="id"
                                >
                                </v-select>
                    </v-col>
                    <v-col cols="12" sm=2>
                        <v-btn color="info" @click="buscar">
                            <i class="fas fa-search"></i>
                        </v-btn>
                        <v-btn color="success" :href="excel">
                            <i class="fas fa-file-excel"></i>
                        </v-btn>
                    </v-col>
                </v-row>
                <v-data-table
                    class="table-lineal"
                    :headers="header"
                    :items="table"
                    hide-default-footer
                    >
                </v-data-table>
            </v-card-text>
        </v-card>
    </v-container>
</template>
<style>
    .table-lineal td{
        white-space: nowrap
    }
</style>
<script>
export default {
    data() {
        return {
            consulta:{
                desde: moment().startOf('month').format('YYYY-MM-DD'),
                hasta: moment().endOf('month').format('YYYY-MM-DD')
            },
            table: [],
            header: [
                { text: 'Productor', value: 'nombre_productor'},
                { text: 'Fundo', value: 'nombre_fundo' },
                { text: 'Lugar Producción', value: 'lugar_produccion' },
                { text: 'N° Viaje', value: 'viaje' },
                { text: 'N° Guia', value: 'guia' },
                { text: 'Código Lugar Producción', value: 'cod_lugar_produccion' },
                { text: 'Semana', value: 'semana' },
                { text: 'Fecha Recepción', value: 'fecha_recepcion' },
                { text: 'Hr. Ingreso Camión', value: 'hora_ingreso' },
                { text: 'Fecha Proceso', value: 'fecha_proceso' },
                { text: 'Materia', value: 'nombre_materia' },
                { text: 'Variedad', value: 'nombre_variedad' },
                { text: 'Lote Materia', value: 'lote_materia' },
                { text: 'Número de Jabas', value: 'numero_jabas' },
                { text: 'Peso Prom. Jaba (Kg)', value: 'peso_promedio_jaba' },
                { text: 'Peso Guia Campo (Kg)', value: 'peso_guia' },
                { text: 'Peso Ingreso Planta (Kg)', value: 'peso_neto_proceso' },
                { text: 'Peso Ingreso Proceso (Kg)', value: 'peso_neto_proceso' },
            ],
            fecha_recepcion: moment().format('YYYY-MM-DD'),
            cliente_id: null,
            clientes: [],
            header_excel: [
                {
                    label: 'Productor',
                    field: 'nombre_productor',
                },
                // 'Productor': 'nombre_productor'
            ]
        }
    },
    mounted() {
        this.listarClientes();
        this.buscar();
    },
    computed:{
        excel(){
            return `${url_base}/reporte/acopio?excel&desde=${this.consulta.desde}&hasta=${this.consulta.hasta}&cliente_id=${this.consulta.cliente_id}`
        }
    },
    methods:{
        listarClientes(){
            axios.get(url_base+'/cliente?all')
            .then(response => {
                this.clientes = response.data;
            })
        },
        buscar(){
            axios.get(`${url_base}/reporte/acopio`,{
                params: this.consulta
            })
            .then(response => {
                this.table=response.data;
            });
        }
    }
}
</script>