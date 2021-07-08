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
                <v-simple-table>
                    <template v-slot:default>
                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>Cliente</th>
                                <th>Codigo</th>
                                <th>Linea</th>
                                <th>Cantidad</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(row,i) in table" :key="i">
                                <td>{{row.fecha_produccion}}</td>
                                <td>{{row.cliente}}</td>
                                <td>{{row.codigo}}</td>
                                <td>{{row.linea}}</td>
                                <td>{{row.cantidad}}</td>
                            </tr>
                        </tbody>
                    </template>
                </v-simple-table>
            </v-card-text>
        </v-card>
    </v-container>
</template>
<script>
export default {
    data() {
        return {
            table: [],
            fecha_produccion: moment().format('YYYY-MM-DD')
        }
    },
    mounted() {
        this.buscar();
    },
    methods:{
        buscar(){
            axios.get(`${url_base}/cantidad-por-linea`,{
                params: {
                    fecha_produccion: this.fecha_produccion
                }
            })
            .then(response => {
                this.table=response.data;
            });
        }
    }
}
</script>