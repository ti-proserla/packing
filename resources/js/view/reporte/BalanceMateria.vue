<template>
    <v-container fluid>
        <v-card>
            <v-card-title>Balance de Materia</v-card-title>              
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
                cliente_id: 1
            },
            table: [],
            header: [
                // fecha_cosecha,
                //         codigo,
                //         nombre_productor,
                //         nombre_materia,
                //         nombre_variedad,
                //         nombre_fundo,
                //         viajes,
                //         semana,
                //         numero_jabas,
                //         peso_promedio_jaba,
                //         peso_neto,
                //         descarte,
                { text: 'Fecha Cosecha', value: 'fecha_cosecha'},
                { text: 'Código', value: 'codigo'},
                { text: 'Productor', value: 'nombre_productor'},
                { text: 'Materia', value: 'nombre_materia'},
                { text: 'Variedad', value: 'nombre_variedad'},
                { text: 'Fundo', value: 'nombre_fundo'},
                { text: 'viajes', value: 'viajes'},
                { text: 'Semana', value: 'semana'},
                { text: 'Número Jabas', value: 'numero_jabas'},
                { text: 'Peso P. Jaba', value: 'peso_promedio_jaba'},
                { text: 'Peso Neto', value: 'peso_neto'},
                { text: 'Descarte', value: 'descarte'},

                { text: 'descarte_%', value: 'descarte_%' },
                { text: 'produccion_kg', value: 'produccion_kg' },
                { text: 'produccion_%', value: 'produccion_%' },
                { text: 'merma_%', value: 'merma_%' },
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
            return `${url_base}/reporte/balance-materia?excel${query}`
        }
    },
    methods:{
        listarClientes(){
            axios.get(url_base+'/cliente?all')
            .then(response => {
                this.clientes = response.data;
                // this.clientes.push({
                //     descripcion: 'Todos los Clientes',
                //     id: ''
                // })
            })
        },
        buscar(){
            axios.get(`${url_base}/reporte/balance-materia`,{
                params: this.consulta
            })
            .then(response => {
                this.table=response.data;
            });
        }
    }
}
</script>