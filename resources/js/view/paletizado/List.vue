<template>
    <v-container fluid>
        <v-card>
            <v-card-title>LISTA DE PALETS POR LOTES</v-card-title>
        </v-card>
        <v-btn
            fab
            bottom
            :fixed="true"
            right
            color="primary"
            @click="$router.push('/paletizado/new')">
              <v-icon>+</v-icon>
        </v-btn>
        <v-row>
            <v-col sm=12 cols="12" v-for="(lote,i) in lotes" :key="i">
                <v-card>
                    <v-card-text>
                        <v-row>
                            <v-col cols="12" lg="8">
                                <h4><b class="detalles">Cliente:</b> {{ lote.cliente}}</h4>
                                <h4><b class="detalles">Lote:</b> {{ lote.codigo }}</h4>
                                <h4><b class="detalles">Materia:</b> {{ lote.materia}} - {{ lote.variedad }}</h4>
                                <h4><b class="detalles">Cosecha:</b> {{ lote.fecha_cosecha }}</h4>
                            </v-col>
                            <v-col cols="12" lg="4" class="text-right">
                                <v-btn color="primary" @click="finalizar(lote.id)">
                                    Finalizar
                                </v-btn>
                            </v-col>
                        </v-row>
                        <v-row>
                            <v-col v-for="(palet,index) in lote.palets_salida" :key="index" cols=6 sm=3>
                                <v-card 
                                    class="text-center" 
                                    @click="seleccionar(palet.id)"
                                    :disabled="palet.estado=='Cerrado'"
                                    >
                                    <v-card-text>
                                        {{ palet.producto }}
                                    </v-card-text>
                                    <v-card-actions>
                                        Palet {{palet.numero }}
                                    </v-card-actions>
                                </v-card>
                            </v-col>
                        </v-row>
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
            lotes: []
        }
    },
    methods: {
        nuevo(){
            this.$router.push('/acopio/lote/new');
        }
    },
    mounted() {
        this.listar();
    },
    methods:{
        listar(){
            axios.get(url_base+`/lote_ingreso/palets_salida?estado=Pendiente`)
            .then(response => {
                this.lotes=response.data
            });
        },
        seleccionar(id){
            this.$router.push(`/paletizado/${id}`);
        },
        finalizar(id){
            swal({ title: "¿Desea Finalizar?", buttons: ['Cancelar',"Finalizar"]})
            .then((res) => {
                if (res) {
                    axios.post(url_base+`/lote_ingreso/${ id }?_method=patch`,{
                        estado: 'Finalizado'
                    }).then(response => {
                        var res=response.data;
                        switch (res.status) {
                            case 'OK':
                                this.listar();
                                break;
                            default:
                                break;
                        }
                    });
                }
            });
        }
    }
}
</script>