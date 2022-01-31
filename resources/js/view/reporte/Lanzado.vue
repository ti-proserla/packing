<template>
    <v-container fluid>
        <v-card>
            <v-card-title>Reporte Lanzado</v-card-title>              
            <v-card-text>
                <v-row>
                    <v-col cols="12" sm=6 lg="3">
                        <v-text-field
                            @change="buscar"
                            label="Fecha Proceso:"
                            v-model="consulta.fecha_proceso"
                            type="date">
                        </v-text-field>
                    </v-col>
                    <v-col cols="12" sm=6 lg="3">
                        <v-select
                            label="Linea:"
                            v-model="consulta.linea"
                            :items="lineas"
                            :item-text="(item) => item.numero"
                            item-value="numero">
                            </v-select>
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
                fecha_proceso: moment().format('YYYY-MM-DD'),
                linea: 1
            },
            lineas: [
                {numero: 1},
                {numero: 2},
                {numero: 3},
                {numero: 4},
                {numero: 5},
                {numero: 6}
            ],
            table: [],
            header: [
                { text: 'Productor', value: 'nombre_productor'},
                { text: 'Fundo', value: 'nombre_fundo' },
                { text: 'Materia', value: 'nombre_materia' },
                { text: 'Variedad', value: 'nombre_variedad' },
                { text: 'Hora Inicio', value: 'hora_inicio' },
                { text: 'Hora Final', value: 'hora_fin' },
                { text: 'Palets', value: 'num_pallets' },
                { text: 'Jabas', value: 'num_jabas' },
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
            return `${url_base}/reporte/lanzado?excel&fecha_proceso=${this.consulta.fecha_proceso}&linea=${this.consulta.linea}`
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
            axios.get(`${url_base}/reporte/lanzado`,{
                params: this.consulta
            })
            .then(response => {
                this.table=response.data;
            });
        }
    }
}
</script>