<template>
    <v-container class="frio" fluid>
        <v-row>
            <v-col cols="12">
                <v-card>
                    <v-card-title>PALETS FUERA DE CAMARA DE FRIO</v-card-title>
                </v-card>

            </v-col>
            <v-col sm=3 cols="12" v-for="(palet,i) in palets" :key="i">
                <v-card>
                    <v-card-text>
                        <p class="mb-0"><b class="detalles">Número:</b> {{ palet.numero }}</p>
                        <p class="mb-0"><b class="detalles">Cliente:</b> {{ palet.cliente}}</p>
                        <p class="mb-0"><b class="detalles">Cajas:</b> {{ palet.cajas_contadas }}</p>
                        <div class="text-center">
                            <v-btn @click="seleccionar(palet.id)"
                                color="primary">Pasar a Frio</v-btn>
                        </div>
                    </v-card-text>
                </v-card>
            </v-col>
        </v-row>
        <v-dialog 
            max-width="400"
            v-model="open_mover">
            <v-card>
                <v-card-title class="headline">Mover a Cámara de Frio</v-card-title>
                <v-card-text>
                    <v-row>
                        <v-col cols=12>
                            <v-select
                                outlined
                                dense
                                v-model="camara_seleccionada"
                                label="Seleccione Cámara:"
                                :items="camaras"
                                item-text="camara"
                                item-value="camara"
                                hide-details="auto"
                                >
                                </v-select>
                        </v-col>
                        <v-col cols="12" class="text-center">
                            <v-btn
                                @click="mover()"
                                color="primary">
                                Mover
                            </v-btn>
                        </v-col>
                    </v-row>
                </v-card-text>
            </v-card>
        </v-dialog>
    </v-container>
</template>
<style>
    .frio b.detalles{
        width: 70px;
        display: inline-block;
    }
</style>
<script>
export default {
    data() {
        return {
            palets: [],
            palet_seleccionado: 0,
            camara_seleccionada: 0,
            open_mover: false,
            camaras: [
                {camara: 1},
                {camara: 2},
                {camara: 3},
                {camara: 4},
                {camara: 5},
                {camara: 6},
                {camara: 7},
                {camara: 8},
            ],
        }
    },
    methods: {
        nuevo(){
            this.$router.push('/acopio/palet/new');
        }
    },
    mounted() {
        this.listar();
    },
    methods:{
        listar(){
            axios.get(url_base+`/palet_salida?estado=Cerrado`)
            .then(response => {
                this.palets=response.data
            });
        },
        seleccionar(id){
            this.palet_seleccionado=id;
            this.open_mover=true;
        },
        mover(){
            swal({
                title: "Desea mover Palet",
                buttons: ['Cancelar',"Mover"],
            })
            .then((res) => {
                if (res) {
                    axios.post(url_base+`/palet_salida/${ this.palet_seleccionado }?_method=patch`,{
                        estado: 'Frio',
                        camara: this.camara_seleccionada
                    })
                    .then(response => {
                        var res=response.data;
                        switch (res.status) {
                            case 'OK':
                                swal(`Palet en Cámara ${this.camara_seleccionada}`, {
                                    icon: "success",
                                    timer: 2000,
                                    buttons: false
                                });
                                this.listar();
                                this.camara_seleccionada=null;
                                this.open_mover=false;
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