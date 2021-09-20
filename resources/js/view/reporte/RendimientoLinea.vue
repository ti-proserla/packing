<template>
    <v-container fluid>
        <v-card>
            <v-card-title>Reporte Rendimiento Linea</v-card-title>              
            <v-card-text>
                <v-row>
                    <v-col cols="12" sm=6 lg="3">
                        <v-text-field
                            label="Desde:"
                            v-model="consulta.desde"
                            type="date">
                        </v-text-field>
                    </v-col>
                    <v-col cols="12" sm=6 lg="3">
                        <v-text-field
                            label="Hasta:"
                            v-model="consulta.hasta"
                            type="date">
                        </v-text-field>
                    </v-col>
                    <v-col cols="12" sm=4 lg="2">
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
                { text: 'Hora Inicio', value: 'hora_inicio' },
                { text: 'Hora Final', value: 'hora_fin' },
                { text: 'Fecha Proceso', value: 'fecha_empaque'},
                { text: 'Linea', value: 'linea' },
                { text: 'Presentacion', value: 'nombre_presentacion' },
                { text: 'Salida (Kg)', value: 'salida' }
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
            var query="";
            Object.entries(this.consulta).forEach(([key, value]) => {
                query+=`&${key}=${value}`;
            });
            return `${url_base}/reporte/rendimiento_linea?excel${query}`
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
            axios.get(`${url_base}/reporte/rendimiento_linea`,{
                params: this.consulta
            })
            .then(response => {
                this.table=response.data;
            });
        }
    }
}
</script>