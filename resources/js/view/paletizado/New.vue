<template>
    <v-container fluid>
        <v-card>
            <v-card-title>Nuevo Palet</v-card-title>              
            <v-card-text>
                <v-row>
                    <v-col cols=12 lg=6>
                        <v-select
                            v-model="palet_salida.cliente_id"
                            label="Cliente:"
                            :items="clientes"
                            :item-text="cliente => `${cliente.descripcion}`"
                            item-value="id">
                            </v-select>
                    </v-col>
                    <v-col cols="12" lg="6">
                        <v-select
                            v-model="palet_salida.etapas"
                            label="Procesos:"
                            :items="procesos"
                            :item-text="proceso => `${proceso.descripcion}`"
                            item-value="etapas">
                        </v-select>
                    </v-col>
                    <v-col cols="12" lg="4">
                        <v-select
                            v-model="palet_salida.tipo_palet_id"
                            label="Tipo de Palet:"
                            :items="tipos_palet"
                            :item-text="tipo => `${tipo.descripcion}`"
                            item-value="id">
                        </v-select>
                    </v-col>
                    <v-col>
                        <v-select
                            v-model="palet_salida.campania_id"
                            label="Campañas:"
                            :items="campanias"
                            :item-text="campania => `${campania.id} - ${campania.nombre_materia}`"
                            item-value="id">
                        </v-select>
                    </v-col>
                </v-row>
                <v-btn color=primary @click="crear()">
                    Crear
                </v-btn>
            </v-card-text>
        </v-card>
    </v-container>
</template>
<script>
export default {
    data() {
        return {
            lote_ingreso:[],
            palet_salida: {
                cliente_id: null,
                etapas: null
            },
            productos: [],
            clientes: [],
            campanias: [],
            procesos: [
                {'etapas' : 1 , "descripcion" : "Solo Empaque"},
                {'etapas' : 3 , "descripcion" : "Empaque - Selección - Pesado"},
            ],
            operaciones: [],
            tipos_palet: []
        }
    },
    mounted() {
        this.listarClientes();
        this.listarOperaciones();
        this.listarTiposPalet();
        this.listarTiposCampanias();
    },
    methods: {
        listarTiposPalet(){
            axios.get(url_base+`/tipo-palet`)
            .then(response => {
                this.tipos_palet=response.data
            });
        },
        listarTiposCampanias(){
            axios.get(url_base+`/campania?all&estado=Abierto`)
            .then(response => {
                this.campanias=response.data
            });
        },
        listarLoteIngreso(){
            axios.get(url_base+`/lote_ingreso?estado=Pendiente`)
            .then(response => {
                this.lote_ingreso=response.data
            });
        },
        listarClientes(){
            axios.get(url_base+'/cliente?all')
            .then(response => {
                this.clientes=response.data
            })
        },
        listarProducto(){
            axios.get(url_base+'/producto?all')
            .then(response => {
                this.productos=response.data
            })
        },
        listarOperaciones(){
            axios.get(url_base+'/operacion?estado=Pendiente')
            .then(response => {
                this.operaciones=response.data
            })
        },
        crear(){
            var t=this;
            swal({
                title: "¿Desea crear Palet?",
                buttons: ['Cancelar',"Crear"],
            })
            .then((res) => {
                if (res) {
                    axios.post(url_base+'/palet_salida',t.palet_salida)
                    .then(response => {
                        var respuesta=response.data;
                        console.log(respuesta);
                        switch (respuesta.status) {
                            case "VALIDATION":
                                break;
                            case "OK":
                                swal("Palet Creado", { icon: "success", timer: 2000, buttons: false });
                                t.$router.push('/paletizado/'+respuesta.data.id);
                                break;
                            default:
                                // t.lote_error={};
                                break;
                        }
                    });
                }
            });
        }
    },
}

</script>