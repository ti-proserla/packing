<template>
    <v-container fluid>
        <v-card>
            <v-card-title>LISTA DE PALETS</v-card-title>
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
            <v-col sm=4 cols="12" v-for="(lote,i) in lotes" :key="i">
                <v-card>
                    <v-card-text>
                        <p class="mb-0"><b class="detalles">Cliente:</b> {{ lote.cliente}}</p>
                        <p class="mb-0"><b class="detalles">Cajas:</b> {{ lote.cajas_contadas }}</p>
                        <p class="mb-0"><b class="detalles">Estado:</b> {{ lote.estado }}</p>
                        <div class="text-center">
                            <v-btn :to="`/paletizado/${lote.id}`"
                                color="primary">Ver</v-btn>
                            <v-btn @click="print(lote.id)">
                                IMPRIMIR
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
            lotes: [],
            printer_select: null,
            zpl: '',
        }
    },
    methods: {
        nuevo(){
            this.$router.push('/acopio/lote/new');
        }
    },
    mounted() {
        this.listar();
        var t = this;
        BrowserPrint.getDefaultDevice("printer", function(device){
            t.printer_select=device;
        });
    },
    methods:{
        print(id){
            axios.get(url_base+`/print/zpl/palet_salida?palet_id=`+id)
            .then(response => {
                var zplPrint=response.data;
                console.log(zplPrint);
                this.printer_select.send(zplPrint, undefined, function(errorMessage){
                    alert("Error: " + errorMessage);	
                });
            });
        },
        listar(){
            axios.get(url_base+`/palet_salida?estado=Pendiente,Cerrado`)
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