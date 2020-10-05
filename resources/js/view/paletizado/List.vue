<template>
    <v-container fluid>
        <v-card>
            <v-card-title>LISTA DE PALETS POR LOTES</v-card-title>
        </v-card>
        <v-btn
            dark
            fab
            bottom
            fixed="true"
            right
            color="primary"
            @click="$router.push('/paletizado/new')">
              <v-icon>+</v-icon>
        </v-btn>
        <v-row>
            <v-col sm=12 cols="12" v-for="(lote,i) in lotes" :key="i">
                <v-card>
                    <v-card-text>
                        <h5><b>Cliente:</b> {{ lote.cliente}}</h5>
                        <h5><b>Lote:</b> {{ lote.codigo }}</h5>
                        <h5><b>Materia:</b> {{ lote.nombre_materia}} - {{ lote.nombre_variedad }}</h5>
                        <h5><b>Cosecha:</b> {{ lote.fecha_cosecha }}</h5>
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
        axios.get(url_base+`/lote_ingreso/palets_salida?estado=lanzado`)
        .then(response => {
            this.lotes=response.data
        });
    },
    methods:{
        seleccionar(id){
            this.$router.push(`/paletizado/${id}`);
        }
    }
}
</script>