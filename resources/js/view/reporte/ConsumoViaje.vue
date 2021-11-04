<template>
    <v-container fluid>
        <v-card>
            <v-card-title>Reporte Consumo de Viaje</v-card-title>              
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
                    <v-col cols="12" sm=8 lg="4">
                        <v-select
                                v-model="consulta.cliente_id"
                                label="Productor:"
                                :items="clientes"
                                item-text="descripcion"
                                item-value="id"
                                >
                                </v-select>
                    </v-col>
                    <v-col cols="12" lg="2">
                        <v-btn color="info" @click="buscar">
                            <i class="fas fa-search"></i>
                        </v-btn>
                        <v-btn color="success" :href="excel">
                            <i class="fas fa-file-excel"></i>
                        </v-btn>
                    </v-col>
                </v-row>
                <!-- <v-row>
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
                </v-row> -->
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
                desde: moment().format('YYYY-MM-DD'),
                hasta: moment().format('YYYY-MM-DD'),
                cliente_id: ''
            },
            table: [],
            header: [
                { text: 'Productor', value: 'nombre_productor'},
                { text: 'Fundo', value: 'nombre_fundo'},
                { text: 'Viaje', value: 'viaje'},
                { text: 'Guia', value: 'guia'},
                { text: 'Placa', value: 'placa'},
                { text: 'Semana', value: 'semana'},
                { text: 'Recepción', value: 'fecha_recepcion'},
                { text: 'Hora', value: 'hora_ingreso'},
                { text: 'Lanzado', value: 'fecha_lanzado'},
                { text: 'Hora', value: 'hora_lanzado'},
                { text: 'Materia', value: 'nombre_materia'},
                { text: 'Variedad', value: 'nombre_variedad'},
                { text: 'Lote Materia', value: 'lote_materia' },
                { text: 'Número de Jabas', value: 'numero_jabas' },
                { text: 'Peso Promedio Jabas', value: 'peso_promedio_jaba' },
                { text: 'Peso Neto', value: 'peso_neto' },
                { text: 'Jabas Lanzadas', value: 'jabas_lanzadas'},
                { text: 'Peso Lanzado', value: 'peso_lanzado'},
                { text: '% Lanzado', value: '%_lanzado'},
            ],
            fecha_recepcion: moment().format('YYYY-MM-DD'),
            cliente_id: null,
            clientes: [],
            header_excel: [
                {
                    label: 'Productor',
                    field: 'nombre_productor',
                }
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
            return `${url_base}/reporte/consumo-viaje?excel${query}`
        }
    },
    methods:{
        listarClientes(){
            axios.get(url_base+'/cliente?all')
            .then(response => {
                this.clientes = response.data;
                this.clientes.push({
                    descripcion: 'Todos los Clientes',
                    id: ''
                })
            })
        },
        buscar(){
            axios.get(`${url_base}/reporte/consumo-viaje`,{
                params: this.consulta
            })
            .then(response => {
                this.table=response.data;
            });
        }
    }
}
</script>