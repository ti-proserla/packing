<template>
    <v-container fluid>
        <v-card>
            <v-card-title>Reetiquetar Palet</v-card-title>              
            <v-card-text>
                <v-row>
                    <v-col>
                        <v-select
                            v-model="palet_salida.campania_id"
                            label="Campañas:"
                            :items="campanias"
                            :item-text="campania => `${campania.id} - ${campania.nombre_materia}`"
                            item-value="id">
                        </v-select>
                    </v-col>
                    <v-col cols=12 lg=6>
                        <v-select
                            @change="listarPaletsSaldos"
                            v-model="palet_salida.cliente_id"
                            label="Cliente:"
                            :items="clientes"
                            :item-text="cliente => `${cliente.descripcion}`"
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
                        >
                        </v-select>
                    </v-col>
                    <v-col cols="12" lg="2">
                        <v-select
                            v-model="palet_salida.etiqueta_adicional"
                            label="Etiqueta Adicional:"
                            :items="[{opt: 'No'},{opt: 'Si'}]"
                            item-text="opt"
                            item-value="opt">
                        </v-select>
                    </v-col>
                    <v-col cols="12">
                        <v-simple-table>
                            <template v-slot:default>
                                <thead>
                                    <tr>
                                        <th class="text-left"></th>
                                        <th class="text-left">Código</th>
                                        <th class="text-left">Materia</th>
                                        <th class="text-left">Variedad</th>
                                        <th class="text-left">Calibre</th>
                                        <th class="text-left">Categoria</th>
                                        <th class="text-left">Presentación</th>
                                        <th class="text-left">Conteo</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="palet in palets">
                                        <td>
                                            <v-checkbox 
                                                v-model="palet_salida.palets_id"
                                                :value="palet.id"
                                            >
                                            </v-checkbox>
                                        </td>
                                        <td>{{`${palet.tipo_palet_id} - ${palet.numero}`}}</td>
                                        <td>{{palet.materia}}</td>
                                        <td>{{palet.variedad}}</td>
                                        <td>{{palet.calibre}}</td>
                                        <td>{{palet.categoria}}</td>
                                        <td>{{palet.presentacion}}</td>
                                        <td>{{palet.cajas_contadas}}</td>
                                    </tr>
                                </tbody>
                            </template>
                        </v-simple-table>
                        
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
                etapas: null,
                palets_id: [],
                etiqueta_adicional: 'No',
            },
            palets_id: [],
            productos: [],
            clientes: [],
            campanias: [],
            parihuelas: [],
            procesos: [
                {'etapas' : 1 , "descripcion" : "Solo Empaque"},
                {'etapas' : 3 , "descripcion" : "Empaque - Selección - Pesado"},
            ],
            operaciones: [],
            tipos_palet: [],
            palets: []
        }
    },
    mounted() {
        this.listarClientes();
        this.listarOperaciones();
        this.listarTiposPalet();
        this.listarTiposCampanias();
        this.listarPaletsSaldos();
        this.listarParihuelas();
    },
    methods: {
        listarPaletsSaldos(){
            axios.get(url_base+`/palet_salida?tipo=SAL&cliente_id=`+this.palet_salida.cliente_id)
            .then(response => {
                this.palets=response.data;
            });
        },
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
        listarParihuelas(){
            axios.get(url_base+'/parihuela?all')
            .then(response => {
                this.parihuelas=response.data
            })
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
                title: "¿Desea crear Remonte?",
                buttons: ['Cancelar',"Crear"],
            })
            .then((res) => {
                if (res) {
                    axios.post(url_base+'/palet_salida/remonte',t.palet_salida
                    )
                    .then(response => {
                        var respuesta=response.data;
                        // console.log(respuesta);
                        switch (respuesta.status) {
                            case "VALIDATION":
                                break;
                            case "OK":
                                swal("Palet Remontado TER-"+respuesta.data.numero, { icon: "success", timer: 4000, buttons: false });
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