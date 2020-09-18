<template>
    <v-container fluid>
        <v-card>
            <v-card-title>LISTA DE PALETS POR LOTES</v-card-title>              
            <v-card-text>
                <v-btn color="primary" @click="$router.push('/paletizado/new')">Nuevo Palet Salida</v-btn>
                <v-row>
                    <v-col sm=12 cols="12" v-for="(lote,i) in lotes" :key="i">
                        <v-card>
                            <v-card-text>
                                <h5><b>Cliente:</b> {{ lote.cliente}}</h5>
                                <h5><b>Lote:</b> {{ lote.codigo }}</h5>
                                <h5><b>Materia:</b> {{ lote.nombre_materia}} - {{ lote.nombre_variedad }}</h5>
                                <h5><b>Cosecha:</b> {{ lote.fecha_cosecha }}</h5>
                                <div v-for="palet in lote.palets_salida" class="col-sm-3">
                                    <button class="btn btn-primary">{{ palet.producto }}</button>
                                </div>
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
}
</script>