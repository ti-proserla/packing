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
                            item-value="id"
                            :error-messages="error.cliente_id"
                            @change="listarOperaciones"
                            >
                            </v-select>
                    </v-col>
                    <!-- <v-col cols="12" lg="6">
                        <v-select
                            v-model="palet_salida.etapas"
                            label="Procesos:"
                            :items="procesos"
                            :item-text="proceso => `${proceso.descripcion}`"
                            :error-messages="error.etapas"
                            item-value="etapas">
                        </v-select>
                    </v-col> -->
                    <v-col cols="12" lg="3">
                        <v-select
                            v-model="palet_salida.tipo_palet_id"
                            label="Tipo de Palet:"
                            :items="tipos_palet"
                            :item-text="tipo => `${tipo.descripcion}`"
                            :error-messages="error.tipo_palet_id"
                            item-value="id">
                        </v-select>
                    </v-col>
                    <v-col cols="12" lg="2">
                        <v-select
                            v-model="palet_salida.campania_id"
                            label="Campañas:"
                            :items="campanias"
                            :item-text="campania => `${campania.id} - ${campania.nombre_materia}`"
                            :error-messages="error.campania_id"
                            item-value="id">
                        </v-select>
                    </v-col>
                    <v-col cols="12" lg="3" >
                        <v-select
                            v-model="palet_salida.parihuela_id"
                            label="Parihuela:"
                            :items="parihuelas"
                            item-value="id"
                            item-text="modelo_parihuela"
                            :error-messages="error.parihuela_id"
                        >
                        </v-select>
                    </v-col>
                    <v-col cols="12" lg="2">
                        <v-select
                            v-model="palet_salida.etiqueta_adicional"
                            label="Etiqueta Adicional:"
                            :items="[{opt: 'No'},{opt: 'Si'}]"
                            item-text="opt"
                            item-value="opt"
                            :error-messages="error.etiqueta_adicional">
                        </v-select>
                    </v-col>
                    <v-col cols="12" lg="3" >
                        <v-select
                            v-model="palet_salida.operacion_id"
                            label="Operacion:"
                            :items="operaciones"
                            item-value="id"
                            item-text="descripcion"
                            :error-messages="error.operacion_id"
                        >
                        </v-select>
                    </v-col>
                    <v-col cols=8 sm=4>
                        <v-text-field 
                            label="Cajas a Completar:" 
                            v-model="palet_salida.numero_cajas"
                            type="number"
                            outlined
                            dense
                            clearable
                        ></v-text-field>
                    </v-col>
                    <v-col cols=8 sm=4>
                        <v-text-field 
                            label="Codigo de Caja:" 
                            v-model="palet_salida.codigo_caja"
                            outlined
                            dense
                            clearable
                        ></v-text-field>
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
            error:{

            },
            lote_ingreso:[],
            palet_salida: {
                cliente_id: null,
                etapas: null,
                etiqueta_adicional: 'No'
            },
            productos: [],
            clientes: [],
            parihuelas: [],
            campanias: [],
            procesos: [
                {'etapas' : 1 , "descripcion" : "Solo Clamshell (1 Etiqueta)"},
                {'etapas' : 2 , "descripcion" : "Clamshell - Selección"},
                {'etapas' : 3 , "descripcion" : "Empaque - Selección - Pesado"},
            ],
            operaciones: [],
            tipos_palet: [],
            presentaciones: [],
            topes: [
                {cantidad: 85},
                {cantidad: 90},
                {cantidad: 92},
                {cantidad: 102},
                {cantidad: 108},
                {cantidad: 114},
                {cantidad: 115},
                {cantidad: 144},
                {cantidad: 180}
            ]
        }
    },
    mounted() {
        this.listarClientes();
        this.listarPresentaciones();
        this.listarOperaciones();
        this.listarTiposPalet();
        this.listarTiposCampanias();
        this.listarParihuelas();
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
        listarParihuelas(){
            axios.get(url_base+'/parihuela?all')
            .then(response => {
                this.parihuelas=response.data
            })
        },
        listarPresentaciones(){
            axios.get(url_base+'/presentacion?all')
            .then(response => {
                this.presentaciones=response.data
            })
        },
        listarProducto(){
            axios.get(url_base+'/producto?all')
            .then(response => {
                this.productos=response.data
            })
        },
        listarOperaciones(){
            axios.get(`${url_base}/operacion?estado=Pendiente&cliente_id=${this.palet_salida.cliente_id}`)
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
                    axios.post(url_base+'/palet_salida/lleno',t.palet_salida)
                    .then(response => {
                        var respuesta=response.data;
                        console.log(respuesta);
                        switch (respuesta.status) {
                            case "VALIDATION":
                                t.error=respuesta.data;
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