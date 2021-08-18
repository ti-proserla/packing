<template>
    <v-container fluid>
        <v-card>
            <v-card-title>Reporte Acopio</v-card-title>              
            <v-card-text>
                <v-row>
                    <v-col cols="12" sm=4>
                        <v-text-field
                            outlined
                            dense
                            label="Fecha Recepción"
                            v-model="fecha_recepcion"
                            type="date">
                        </v-text-field>
                    </v-col>
                    <v-col cols="12" sm=4>
                        <v-select
                                outlined
                                dense
                                v-model="cliente_id"
                                label="Cliente:"
                                :items="clientes"
                                item-text="descripcion"
                                item-value="id"
                                >
                                </v-select>
                    </v-col>
                    <v-col cols="12" sm=4>
                        <v-btn color="info" @click="buscar">
                            Buscar
                        </v-btn>
                    </v-col>
                </v-row>
                <v-simple-table
                    class="table-lineal">
                    <template v-slot:default>
                        <thead>
                            <tr>
                                <th>Cliente</th>
                                <th>Fundo</th>
                                <th>Viaje</th>
                                <th>Guia</th>
                                <th>Semana</th>
                                <th>Fecha Recepcion</th>
                                <th>Hora Ingreso</th>
                                <!-- <th>Fecha Proceso</th> -->
                                <th>Materia</th>
                                <th>Variedad</th>
                                <!-- <th>Tipo</th> -->
                                <th>Lote Materia</th>
                                <th>Número Jabas</th>
                                <th>Peso Promedio Jaba</th>
                                <th>Peso Guia</th>
                                <th>Peso Neto</th>
                                <th>Peso Neto Proceso</th>
                                <th>Descarte Granos</th>
                                <th>Descarte Racimos</th>
                                <th>Total Descarte</th>
                                <th>Descarte Porcentaje</th>
                                <th>Cantidad Jabas Descarte</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(row,i) in table" :key="i">
                                <td>{{row.cliente}}</td>
                                <td>{{row.fundo}}</td>
                                <td>{{row.viaje}}</td>
                                <td>{{row.guia}}</td>
                                <td>{{row.semana}}</td>
                                <td>{{row.fecha_recepcion}}</td>
                                <td>{{row.hora_ingreso}}</td>
                                <!-- <td>{{row.fecha_proceso}}</td> -->
                                <td>{{row.materia}}</td>
                                <td>{{row.variedad}}</td>
                                <!-- <td>{{row.tipo}}</td> -->
                                <td>{{row.lote_materia}}</td>
                                <td>{{row.numero_jabas}}</td>
                                <td>{{row.peso_promedio_jaba}}</td>
                                <td>{{row.peso_guia}}</td>
                                <td>{{row.peso_neto}}</td>
                                <td>{{row.peso_neto_proceso}}</td>
                                <td>{{row.descarte_granos}}</td>
                                <td>{{row.descarte_racimos}}</td>
                                <td>{{row.total_descarte}}</td>
                                <td>{{row.descarte_porcentaje}}</td>
                                <td>{{row.cantidad_jabas_descarte}}</td>
                            </tr>
                        </tbody>
                    </template>
                </v-simple-table>
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
            table: [],
            fecha_recepcion: moment().format('YYYY-MM-DD'),
            cliente_id: null,
            clientes: []
        }
    },
    mounted() {
        this.listarClientes();
        this.buscar();
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
                params: {
                    fecha_recepcion: this.fecha_recepcion,
                    cliente_id: this.cliente_id
                }
            })
            .then(response => {
                this.table=response.data;
            });
        }
    }
}
</script>