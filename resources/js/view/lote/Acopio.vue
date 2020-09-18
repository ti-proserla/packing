<template>
    <v-container fluid>
        <v-card>
            <v-card-title>LOTES DE INGRESO</v-card-title>              
            <v-card-text>
                <v-btn color="primary" @click="nuevo">
                    Nuevo Lote
                </v-btn>
                <v-row>
                    <v-col sm=4 cols="12" v-for="(lote,i) in lotes_ingreso" :key="i">
                        <v-card>
                            <v-card-text>
                                <h5><b>Cliente:</b> {{ lote.descripcion}}</h5>
                                <h5><b>Lote:</b> {{ lote.codigo }}</h5>
                                <h5><b>Materia:</b> {{ lote.nombre_materia}} - {{ lote.nombre_variedad }}</h5>
                                <h5><b>Cosecha:</b> {{ lote.fecha_cosecha }}</h5>
                                <v-btn outlined="true" @click="redirect(lote.id)">
                                    Detalles
                                </v-btn>
                            </v-card-text>
                        </v-card>
                    </v-col>
                </v-row>
            </v-card-text>
        </v-card>
    </v-container>
</template>
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