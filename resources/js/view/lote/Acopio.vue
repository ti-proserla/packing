<template>
    <v-container fluid>
        <v-card>
            <v-card-title>
                Acopio de Lotes
            </v-card-title>
            <v-card-text>
                <v-simple-table dense>
                    <template v-slot:default>
                        <thead>
                            <tr>
                                <th>Cliente</th>
                                <th>Lote</th>
                                <th>Materia</th>
                                <th>Variedad</th>
                                <th>Cosecha</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr cols="12" v-for="(lote,i) in lotes_ingreso" :key="i">
                                <td>{{ lote.descripcion}}</td>
                                <td>{{ lote.codigo}}</td>
                                <td>{{ lote.nombre_materia}}</td>
                                <td>{{ lote.nombre_variedad }}</td>
                                <td>{{ lote.fecha_cosecha }}</td>
                            </tr>
                        </tbody>
                    </template>
                </v-simple-table>
            </v-card-text>
        </v-card>
        <v-btn
            dark
            fab
            bottom
            :fixed="true"
            right
            color="primary"
            @click="nuevo">
              <v-icon>+</v-icon>
        </v-btn>
    </v-container>
</template>
<style>
    b.detalles{
        width: 100px;
        display: inline-block;
    }
</style>
<script>
export default {
    data() {
        return {
            lotes_ingreso: []
        }
    },
    mounted() {
        this.listarLotes()
    },
    methods: {
        listarLotes(){
            axios.get(url_base+'/lote_ingreso?estado=Pendiente')
            .then(response => {
                this.lotes_ingreso=response.data
            })
        },
        nuevo(){
            this.$router.push('/acopio/lote/new');
        }
    },
}
</script>