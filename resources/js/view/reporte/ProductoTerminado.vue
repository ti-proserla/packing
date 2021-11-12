<template>
    <v-container fluid>
        <v-card>
            <v-card-title>Reporte Producto Terminado</v-card-title>              
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
                desde: moment().startOf('month').format('YYYY-MM-DD'),
                hasta: moment().endOf('month').format('YYYY-MM-DD'),
                cliente_id: ''
            },
            table: [],
            header: [
                { text: 'Productor', value: 'nombre_productor'},
                { text: 'Fecha Empaque', value: 'fecha_empaque'},
                { text: 'Fecha Cosecha', value: 'fecha_cosecha'},
                { text: 'Tipo Palet', value: 'tipo_palet_id'},
                { text: 'N° Palet', value: 'numero'},
                { text: 'Calibre', value: 'nombre_calibre'},
                { text: 'Categoria', value: 'nombre_categoria'},
                { text: 'Presentación', value: 'nombre_presentacion'},
                { text: 'Cod. Fundo', value: 'codigo_fundo'},
                { text: 'Cod. Variedad', value: 'codigo_variedad'},
                { text: 'Juliano', value: 'juliano'},
                { text: 'Cod. Trazabilidad', value: 'codigo_lote'},
                { text: 'Marca Caja', value: 'nombre_marca_caja' },
                { text: 'Tipo Empaque', value: 'nombre_tipo_empaque' },
                { text: 'Marca Empaque', value: 'nombre_marca_empaque' },
                { text: 'PLU', value: 'nombre_plu' },
                { text: 'N° Cajas', value: 'numero_cajas'},
                { text: 'Operacion', value: 'operacion'},
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
            return `${url_base}/reporte/producto-terminado?excel${query}`
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
            axios.get(`${url_base}/reporte/producto-terminado`,{
                params: this.consulta
            })
            .then(response => {
                this.table=response.data;
            });
        }
    }
}
</script>