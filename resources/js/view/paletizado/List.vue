<template>
    <v-container fluid>
        <v-card>
            <v-card-title>LISTA DE PALETS</v-card-title>
        </v-card>
        <!-- <v-btn
            fab
            bottom
            :fixed="true"
            left
            color="primary"
            @click="$router.push('/paletizado/new')">
              <v-icon>+</v-icon>
        </v-btn> -->
        
        <v-row>
            <v-col cols="12">
                <v-data-table
                    class="table-lineal"
                    :headers="header"
                    :items="lotes"
                    hide-default-footer
                    >
                </v-data-table>
            </v-col>
            <v-col sm=4 cols="12" v-for="(lote,i) in lotes" :key="i">
                <v-card>
                    <v-card-text>
                        <p class="mb-0"><b class="detalles">Tipo:</b> {{ lote.tipo_palet_id}}</p>
                        <p class="mb-0"><b class="detalles">Número:</b> {{ lote.numero}}</p>
                        <p class="mb-0"><b class="detalles">Cliente:</b> {{ lote.cliente}}</p>
                        <p class="mb-0"><b class="detalles">Cajas:</b> {{ lote.cajas_contadas }}</p>
                        <p class="mb-0"><b class="detalles">Estado:</b> {{ lote.estado }}</p>
                        <p class="mb-0"><b class="detalles">Parihuela:</b> {{ lote.parihuela }}</p>
                        <p class="mb-0"><b class="detalles">Etiq. Adicional:</b> {{ lote.etiqueta_adicional }}</p>
                        <div v-if="lote.detalles!=null">
                            <p class="mb-0" v-for="detalle in lote.detalles.split(',')"><b>{{detalle }}</b></p>
                        </div>
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
        <v-speed-dial
                v-model="fab"
                bottom
                fixed
                right
                :direction="'top'"
                open-on-hover
                transition='slide-y-reverse-transition'
            >
            <template v-slot:activator>
                <v-btn
                v-model="fab"
                color="blue darken-2"
                dark
                fab
                >
                    <i v-if="fab" class="fas fa-ellipsis-h"></i>
                    <i v-else class="fas fa-ellipsis-v"></i>
                </v-btn>
            </template>
            <v-btn
                fab
                dark
                color="red"
                @click="$router.push('/paletizado/remonte')">
                <i class="far fa-object-ungroup"></i>
            </v-btn>
            <v-btn
                fab
                dark
                color="primary"
                @click="$router.push('/paletizado/new')">
                <i class="fas fa-plus"></i>
            </v-btn>
        </v-speed-dial>
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
            header: [],
            fab: false
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