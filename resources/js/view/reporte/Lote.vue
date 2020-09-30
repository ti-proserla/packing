<template>
    <v-container fluid>
        <v-card>
            <v-card-text>
                <v-simple-table
                    fixed-header
                    height="300px">
                    <template v-slot:default>
                        <thead>
                            <tr>
                                <th>Lote</th>
                                <th colspan="2" class="text-center">Ingreso</th>
                                <th colspan="6" class="text-center">Salida</th>
                            </tr>
                            <tr>
                                <th>Código Lote</th>
                                <th>Guias</th>
                                <th>Peso Total Ingreso</th>
                                <th>palets</th>
                                <th>Nombre Producto</th>
                                <th>Peso Neto</th>
                                <th>Potes x Caja</th>
                                <th>Cajas</th>
                                <th>Peso Total Salida</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="fila in datos">
                                <td>{{ fila.codigo_lote }}</td>
                                <td>{{ fila.guias }}</td>
                                <td>{{ fila.peso_total_ingreso }}</td>
                                <td>{{ fila.palets }}</td>
                                <td>{{ fila.nombre_producto }}</td>
                                <td>{{ fila.peso_bruto }}</td>
                                <td>{{ fila.potes_x_caja }}</td>
                                <td>{{ fila.cantidad_cajas }}</td>
                                <td>{{ fila.peso_total_salida }}</td>
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
            datos: []
        }
    },
    mounted() {
        axios.get(`${url_base}/reporte/lote`)
        .then(response => {
            this.datos=response.data;
        });
    },
}
</script>