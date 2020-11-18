<template>
    <v-container fluid>
        <v-card>
            <v-card-text>
                <v-row>
                    <v-col cols="12" sm=4>
                        <v-text-field
                            outlined
                            dense
                            label="Fecha Producción"
                            v-model="fecha_produccion"
                            type="date">
                        </v-text-field>
                    </v-col>
                    <v-col cols="12" sm=4>
                        <v-btn color="info" @click="buscar">
                            Buscar
                        </v-btn>
                    </v-col>
                </v-row>
                <v-simple-table
                    fixed-header
                    height="300px">
                    <template v-slot:default>
                        <thead>
                            <tr>
                                <th colspan="3">Lote</th>
                                <th colspan="2" class="text-center">Ingreso</th>
                                <th colspan="6" class="text-center">Salida</th>
                            </tr>
                            <tr>
                                <th>Código Lote</th>
                                <th>Materia</th>
                                <th>Variedad</th>
                                <th>Guias</th>
                                <th>Peso Total (Kg)</th>
                                <th>palets</th>
                                <th>Nombre Producto</th>
                                <th>Peso Neto (Gr)</th>
                                <th>Potes x Caja</th>
                                <th>Cajas</th>
                                <th>Peso Total (Kg)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-if="datos.length==0">
                                <td colspan="11" class="text-center"> Sin Registros</td>
                            </tr>
                            <tr v-for="fila in datos">
                                <td>{{ fila.codigo_lote }}</td>
                                <td>{{ fila.nombre_materia }}</td>
                                <td>{{ fila.nombre_variedad }}</td>
                                <td><pre>{{ fila.guias }}</pre></td>
                                <td>{{ decimal(fila.peso_total_ingreso) }}</td>
                                <td>{{ fila.palets }}</td>
                                <td>{{ fila.nombre_producto }}</td>
                                <td>{{ fila.peso_neto }}</td>
                                <td>{{ fila.potes_x_caja }}</td>
                                <td>{{ fila.cantidad_cajas }}</td>
                                <td>{{ decimal(fila.peso_total_salida) }}</td>
                            </tr>
                        </tbody>
                    </template>
                </v-simple-table>
            </v-card-text>
        </v-card>
    </v-container>
</template>
<style >
table{ border: 1px solid #dddddd; }
table th + th { border-left:1px solid #dddddd; }
table td + td { border-left:1px solid #dddddd; }
</style>
<script>
export default {
    data() {
        return {
            datos: [],
            fecha_produccion: moment().format('YYYY-MM-DD')
        }
    },
    mounted() {
        this.buscar();
    },
    methods:{
        buscar(){
            axios.get(`${url_base}/reporte/lote`,{ 
                params: {
                    fecha_produccion: this.fecha_produccion
                }
            })
            .then(response => {
                this.datos=response.data;
            });
        },
        decimal(num){
            return Number(num).toFixed(2);
        }
    }
}
</script>