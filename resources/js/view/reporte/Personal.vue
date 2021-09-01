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
                    <v-col>
                        <v-select
                            @change="buscar"
                                outlined
                                dense
                                v-model="codigo_labor"
                                label="Productor:"
                                :items="labores"
                                item-text="descripcion"
                                item-value="codigo"
                                >
                                </v-select>
                    </v-col>
                    <v-col cols="12" sm=4>
                        <v-btn color="info" @click="buscar">
                            Buscar
                        </v-btn>
                        <v-btn color="success" :href="excel">
                            <i class="fas fa-file-excel"></i>
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
                                <th>Labor</th>
                                <th>Primera Lectura</th>
                                <th>Ultima Lectura</th>
                                <th>Diferencia</th>
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
                                <td>{{ fila.labor }}</td>
                                <td>{{ fila.primera_lectura }}</td>
                                <td>{{ fila.ultima_lectura }}</td>
                                <td>{{ fila.diferencia }}</td>
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
            labores: [
                {codigo: '01',descripcion: 'EMPAQUE'},
                {codigo: '02',descripcion: 'PESADO'},
                {codigo: '03',descripcion: 'SELECCION'},
            ],
            codigo_labor: '01',
            fecha_produccion: moment().format('YYYY-MM-DD')
        }
    },
    mounted() {
        this.buscar();
    },
    computed:{
        excel(){
            return `${url_base}/rendimiento-personal?excel&fecha_produccion=${this.fecha_produccion}&codigo_labor=${this.codigo_labor}`
        }
    },
    methods:{
        buscar(){
            axios.get(`${url_base}/rendimiento-personal`,{ 
                params: {
                    fecha_produccion: this.fecha_produccion,
                    codigo_labor: this.codigo_labor
                }
            })
            .then(response => {
                this.datos=response.data;
            });
        }
    }
}
</script>