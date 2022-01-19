<template>
    <v-container fluid>
        <v-card>
            <v-card-title>LISTA DE PALETS</v-card-title>
            <v-card-text>
                <v-row>
                    <v-col cols=12 sm=6>
                            <v-select
                                outlined
                                dense
                                v-model="consulta.cliente_id"
                                label="Cliente:"
                                :items="clientes"
                                item-text="descripcion"
                                item-value="id"
                                >
                                </v-select>
                        </v-col>
                    <!-- <v-col cols="12" lg="4">
                        <v-select
                            v-model="consulta.tipo_palet_id"
                            label="Tipo de Palet:"
                            :items="tipos_palet"
                            :item-text="tipo => `${tipo.descripcion}`"
                            item-value="id">
                        </v-select>
                    </v-col> -->
                    <v-col cols="12" lg="4">
                        <v-btn @click="listar()"
                        color="primary"
                        >Actualizar</v-btn>
                    </v-col>
                </v-row>
            </v-card-text>
        </v-card>
        <v-row>
            <v-col sm=4 cols="12" v-for="(lote,i) in lotes.data" :key="i">
                <v-card>
                    <v-card-text>
                        <p class="mb-0"><b class="detalles">Tipo:</b> {{ lote.tipo_palet_id}}</p>
                        <p class="mb-0"><b class="detalles">Número:</b> {{ lote.numero}}</p>
                        <p class="mb-0"><b class="detalles">Cliente:</b> {{ lote.cliente}}</p>
                        <p class="mb-0"><b class="detalles">Cajas:</b> {{ lote.cajas_contadas }}</p>
                        <p class="mb-0"><b class="detalles">Estado:</b> {{ lote.estado }}</p>
                        <p class="mb-0"><b class="detalles">Parihuela:</b> {{ lote.parihuela }}</p>
                        <p class="mb-0"><b class="detalles">Etiq. Adicional:</b> {{ lote.etiqueta_adicional }}</p>
                        <p class="mb-0"><b class="detalles">Operación:</b> {{ lote.operacion }}</p>
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
            <v-col cols="12">
                <v-pagination 
                    v-model="lotes.current_page" 
                    :length="lotes.last_page" 
                    circle 
                    @input="listar">
                </v-pagination>
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
                @click="$router.push('/paletizado/newLleno')">
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
            clientes: [],
            tipos_palet:[],
            lotes: {
                current_page: 1,
                last_page: 1,
                data: []
            },
            printer_select: null,
            zpl: '',
            header: [],
            fab: false,
            consulta: {
                cliente_id: 1,
                tipo_palet_id: 'TER',
                estado: 'Pendiente,Cerrado'
            }
        }
    },
    methods: {
        nuevo(){
            this.$router.push('/acopio/lote/new');
        }
    },
    mounted() {
        this.listarClientes();
        this.listar();
        var t = this;
        BrowserPrint.getDefaultDevice("printer", function(device){
            t.printer_select=device;
        });
        this.listarTiposPalet();
    },
    methods:{
        listarClientes(){
            axios.get(url_base+'/cliente?all')
            .then(response => {
                this.clientes=response.data
            });
        },
        listarTiposPalet(){
            axios.get(url_base+`/tipo-palet`)
            .then(response => {
                this.tipos_palet=response.data
            });
        },
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
        listar(n=this.lotes.current_page){
            this.consulta.page=n;
            axios.get(url_base+`/palet_salida`,{
                params: this.consulta
            })
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