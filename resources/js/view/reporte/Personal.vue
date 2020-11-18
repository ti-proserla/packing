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
                                <th>Código</th>
                                <th>Nombres</th>
                                <th>Apellidos</th>
                                <th>Conteo</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-if="datos.length==0">
                                <td colspan="4" class="text-center"> Sin Registros</td>
                            </tr>
                            <tr v-for="fila in datos">
                                <td>{{ fila.codigo_operador }}</td>
                                <td>{{ fila.nom_operador }}</td>
                                <td>{{ fila.ape_operador }}</td>
                                <td>{{ fila.conteo }}</td>
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
            axios.get(`${url_base}/rendimiento-personal`,{ 
                params: {
                    fecha_produccion: this.fecha_produccion
                }
            })
            .then(response => {
                this.datos=response.data;
            });
        }
    }
}
</script>