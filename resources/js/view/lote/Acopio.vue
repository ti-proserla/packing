<template>
    <v-container fluid>
        <v-card>
            <v-card-title>
                Acopio de Lotes
            </v-card-title>
        </v-card>
        <v-btn
            dark
            fab
            bottom
            fixed="true"
            right
            color="primary"
            @click="nuevo">
              <v-icon>+</v-icon>
        </v-btn>
        <v-row>
            <v-col sm=4 cols="12" v-for="(lote,i) in lotes_ingreso" :key="i">
                <v-card>
                    <v-card-text>
                        <h4><b class="detalles">Cliente:</b> {{ lote.descripcion}}</h4>
                        <h4><b class="detalles">Lote:</b> {{ lote.codigo }}</h4>
                        <h4><b class="detalles">Materia:</b> {{ lote.nombre_materia}} - {{ lote.nombre_variedad }}</h4>
                        <h4><b class="detalles">Cosecha:</b> {{ lote.fecha_cosecha }}</h4>
                        <div class="text-center my-3">
                            <v-btn outlined="true" color="info" @click="redirect(lote.id)">
                                Detalles
                            </v-btn>
                        </div>
                    </v-card-text>
                </v-card>
            </v-col>
        </v-row>
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
        redirect(id){
            this.$router.push('/acopio/lote/'+id);
        },
        listarLotes(){
            axios.get(url_base+'/lote_ingreso?estado=REGISTRADO')
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