<template>
    <v-container fluid>
        <v-dialog 
            max-width="800"
            v-model="open_palets" 
            persistent>
            <v-card>
                <v-card-title class="headline">
                    <v-btn 
                        icon
                        color="black"
                        @click="open_palets=false">
                        <i class="fas fa-times"></i>
                    </v-btn> 
                        Registro de Palets
                </v-card-title>
                <v-card-text>
                    <v-row>
                        <v-col cols="12">
                            <b>Viaje: </b>{{ sub_lote_seleccionado.viaje }} <br>
                            <b>Cliente: </b>{{ sub_lote_seleccionado.cliente }} <br>
                            <b>Fecha Recepción: </b>{{ sub_lote_seleccionado.fecha_recepcion }}
                        </v-col>
                    </v-row>
                        <v-row v-if="sub_lote_seleccionado.estado=='Pendiente'">
                            <v-col cols="12" sm="3">
                                <v-text-field 
                                    label="N° de Jabas:" 
                                    v-model="num_jabas"
                                    clearable
                                    type="number"
                                    :error-messages="palets_error.num_jabas"
                                ></v-text-field>
                            </v-col>
                            <v-col cols="12" sm="3">
                                <v-text-field 
                                    label="Peso Total:" 
                                    v-model="peso"
                                    clearable
                                    type="number"
                                    :error-messages="palets_error.peso"
                                ></v-text-field>
                            </v-col>
                            <v-col cols="12" sm="3">
                                <v-text-field 
                                    label="Peso Parihuela (Kg):" 
                                    v-model="peso_palet"
                                    clearable
                                    type="number"
                                    :error-messages="palets_error.peso_palet"
                                ></v-text-field>
                            </v-col>
                            <v-col cols="12" sm="3">
                                <v-text-field 
                                    label="Peso Jaba (Kg):" 
                                    v-model="peso_jaba"
                                    clearable
                                    type="text"
                                    :error-messages="palets_error.peso_jaba"
                                ></v-text-field>
                            </v-col>
                            <v-col cols="12" sm="6">
                                <v-btn 
                                    color="primary"
                                    block 
                                    @click="add()">
                                    Agregar
                                </v-btn>
                            </v-col>
                            <v-col cols="12" sm="6">
                                <v-checkbox
                                v-model="printAdd"
                                label="Imprimir al Agregar"
                                ></v-checkbox>
                            </v-col>
                        </v-row>
                        <v-row v-else>
                            <v-col cols=12>
                                <v-btn 
                                    color="primary"
                                    block 
                                    @click="print(sub_lote_seleccionado.id)">
                                    Imprimir Todo
                                </v-btn>
                            </v-col>
                        </v-row>
                        <div class="text-center">
                            <v-simple-table dense>
                                <template v-slot:default>
                                    <thead>
                                        <tr>
                                            <th>N°</th>
                                            <th>N° Jabas</th>
                                            <th>Peso Total</th>
                                            <th>Peso Palet</th>
                                            <th>Peso Jaba</th>
                                            <th>Imprimir</th>
                                            <th v-if="sub_lote_seleccionado.estado=='Pendiente'">Editar</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(palet,index) in sub_lote_seleccionado.palets.slice().reverse()">
                                            <td>{{ sub_lote_seleccionado.palets.length - index}}</td>
                                            <td v-if="palet.id!=index_edit"> 
                                                {{ palet.num_jabas }}
                                            </td>
                                            <td v-else>
                                                <v-text-field 
                                                    v-model="palet.num_jabas"
                                                ></v-text-field>
                                            </td>
                                            <td v-if="palet.id!=index_edit"> 
                                                {{ palet.peso }}
                                            </td>
                                            <td v-else>
                                                <v-text-field 
                                                    v-model="palet.peso"
                                                ></v-text-field>
                                            </td>
                                            <td v-if="palet.id!=index_edit"> 
                                                {{ palet.peso_palet }}
                                            </td>
                                            <td v-else>
                                                <v-text-field 
                                                    v-model="palet.peso_palet"
                                                ></v-text-field>
                                            </td>
                                            <td v-if="palet.id!=index_edit"> 
                                                {{ palet.peso_jaba }}
                                            </td>
                                            <td v-else>
                                                <v-text-field 
                                                    v-model="palet.peso_jaba"
                                                ></v-text-field>
                                            </td>
                                            <td>
                                                <v-btn
                                                    @click="printUnitario(palet.id)"
                                                    color="orange"
                                                    text>
                                                    <i class="fas fa-print"></i>
                                                </v-btn>
                                            </td>
                                            <td v-if="sub_lote_seleccionado.estado=='Pendiente'">
                                                <v-btn v-if="palet.id!=index_edit" @click="edit(palet.id)"
                                                        color="orange"
                                                        text>
                                                    <i class="fas fa-pen"></i>
                                                </v-btn>
                                                <v-btn-toggle v-else
                                                    group
                                                >
                                                    <v-btn  @click="saveEdit(palet)"
                                                            color="info"
                                                            text>
                                                        <i class="far fa-save"></i>
                                                    </v-btn>
                                                    <v-btn  
                                                        @click="index_edit=0;listarPaletEntrada()"
                                                        color="info"
                                                        text>
                                                        <i class="fas fa-share"></i>
                                                    </v-btn>
                                                </v-btn-toggle>
                                                
                                            </td>
                                        </tr>
                                        <tr v-if="seleccionado_sub_lote==null">
                                            <td colspan="3"> Seleccione un Sub lote </td>
                                        </tr>
                                    </tbody>
                                </template>
                            </v-simple-table>
                            <v-btn  
                                v-if="sub_lote_seleccionado.estado=='Pendiente'"
                                class="mt-3"
                                color="primary"
                                block 
                                @click="pesado()">
                                Finalizar
                            </v-btn>
                        </div>
                </v-card-text>
            </v-card>
        </v-dialog>
        <v-dialog 
            max-width="600"
            v-model="open_nuevo" 
            persistent>
            <v-card>
                <v-card-title class="headline">Nuevo Sub Lote</v-card-title>
                <v-card-text>
                    <v-row>
                        <v-col cols=12>
                            <v-select
                                v-model="sub_lote.lote_id"
                                label="Lote:"
                                :items="lotes"
                                :item-text=" (item) => `${item.codigo} : ${item.descripcion} - ${item.nombre_materia}/${item.nombre_variedad}`"
                                item-value="id"
                                :error-messages="sub_lote_error.lote_id"
                                >
                                </v-select>
                        </v-col>
                        <v-col cols=4 lg="4">
                            <v-text-field 
                                label="Viaje:" 
                                type="number"
                                v-model="sub_lote.viaje"
                                :outlined="true"
                                dense
                                clearable
                            ></v-text-field>
                        </v-col>
                        <v-col cols=4 lg="4">
                            <v-text-field 
                                label="Guia:" 
                                v-model="sub_lote.guia"
                                :outlined="true"
                                clearable
                                :error-messages="sub_lote_error.guia"
                            ></v-text-field>
                        </v-col>
                        <v-col cols=4 lg="4">
                            <v-text-field 
                                label="Placa Camión:" 
                                v-model="sub_lote.placa"
                                :outlined="true"
                                clearable
                                :error-messages="sub_lote_error.placa"
                            ></v-text-field>
                        </v-col>
                        <v-col cols=12>
                            <v-text-field 
                                label="Peso Guia (Kg):" 
                                v-model="sub_lote.peso_guia"
                                :outlined="true"
                                dense
                                type="number"
                                clearable
                                :error-messages="sub_lote_error.peso_guia"
                            ></v-text-field>
                        </v-col>
                        <v-col cols=12 sm=12>
                            <v-text-field 
                                label="Fecha Recepcion:" 
                                v-model="sub_lote.fecha_recepcion"
                                outlined
                                dense
                                clearable
                                type="datetime-local"
                                :error-messages="sub_lote_error.fecha_recepcion"
                            ></v-text-field>
                        </v-col>
                    </v-row>
                    <div class="text-right mt-3">
                        <v-btn 
                            outlined 
                            color="secondary" 
                            @click="open_nuevo=false"
                            >Cancelar</v-btn>
                        <v-btn color="primary" @click="guardarSubLote()">
                            Guardar
                        </v-btn>
                    </div>
                </v-card-text>
            </v-card>               
        </v-dialog>
        <v-dialog 
            v-model="modal_editar"
            max-width="600"
            persistent>
            <v-card>
                <v-card-title class="headline">Editar Sub Lote</v-card-title>
                <v-card-text v-if="sub_lote_editar!=null">
                    <v-row>
                        <v-col cols=12>
                            <v-select
                                readonly
                                v-model="sub_lote_editar.lote_id"
                                label="Lote:"
                                :items="lotes"
                                :item-text=" (item) => `${item.codigo} : ${item.descripcion} - ${item.nombre_materia}/${item.nombre_variedad}`"
                                item-value="id"
                                >
                                </v-select>
                        </v-col>
                        <v-col cols=4 lg="4">
                            <v-text-field 
                                label="Viaje:" 
                                type="number"
                                v-model="sub_lote_editar.viaje"
                                dense
                                clearable
                            ></v-text-field>
                        </v-col>
                        <v-col cols=6 lg="4">
                            <v-text-field 
                                label="Guia:" 
                                v-model="sub_lote_editar.guia"
                                clearable
                            ></v-text-field>
                        </v-col>
                        <v-col cols=6 lg="4">
                            <v-text-field 
                                label="Placa Camión:" 
                                v-model="sub_lote_editar.placa"
                                clearable
                            ></v-text-field>
                        </v-col>
                        <v-col cols=12>
                            <v-text-field 
                                label="Peso Guia (Kg):" 
                                v-model="sub_lote_editar.peso_guia"
                                dense
                                type="number"
                                clearable
                            ></v-text-field>
                        </v-col>
                        <v-col cols=12 sm=12>
                            <v-text-field 
                                label="Fecha Recepcion:" 
                                v-model="sub_lote_editar.fecha_recepcion"
                                outlined
                                dense
                                clearable
                                type="datetime-local"
                            ></v-text-field>
                        </v-col>
                    </v-row>
                    <div class="text-right mt-3">
                        <v-btn 
                            outlined 
                            color="secondary" 
                            @click="modal_editar=false"
                            >Cancelar</v-btn>
                        <v-btn color="primary" @click="updateSubLote()">
                            Guardar
                        </v-btn>
                    </div>
                </v-card-text>
            </v-card>               
        </v-dialog>
        <v-row>
            <v-col cols="12">
                <v-card>
                    <v-card-text>
                        <v-row>
                            <v-col cols="6">
                                <h4>LISTA DE SUBLOTES</h4>             
                            </v-col>
                            <v-col cols="6" class="text-right">
                                <v-btn 
                                    @click="openNuevo" 
                                    small
                                    color="info">
                                    Nuevo Sub Lote
                                </v-btn>
                            </v-col>
                            <v-col cols="12">
                                <v-select
                                @change="listarSublote"
                                outlined
                                dense
                                v-model="s_cliente_id"
                                label="Cliente:"
                                :items="clientes"
                                :item-text=" (item) => `${item.ruc} : ${item.descripcion}`"
                                item-value="id">
                                </v-select>
                            </v-col>
                            <v-col 
                                cols=12 
                                sm="12"
                                lg="4"
                                v-for="(sub,index) in sub_lotes" 
                                :key="index">
                                <v-card 
                                    outlined 
                                    class="mb-3" 
                                    >
                                        <v-card-text>
                                            <v-row>
                                                <v-col cols="12" class="pb-0 pt-0">
                                                    <v-row>
                                                        <v-col class="pb-0 pt-0" cols="4"><b>Viaje:</b></v-col>
                                                        <v-col class="pb-0 pt-0" cols="8">{{ sub.viaje }}</v-col>
                                                        <v-col class="pb-0 pt-0" cols="4"><b>Código:</b></v-col>
                                                        <v-col class="pb-0 pt-0" cols="8">{{ sub.codigo }}</v-col>
                                                        <v-col class="pb-0 pt-0" cols="4"><b>Materia:</b></v-col>
                                                        <v-col class="pb-0 pt-0" cols="8">{{ sub.materia }} - {{ sub.variedad }} - {{ sub.tipo }}</v-col>
                                                        <v-col class="pb-0 pt-0" cols="4"><b>Guia:</b></v-col>
                                                        <v-col class="pb-0 pt-0" cols="8">{{ sub.guia }}</v-col>
                                                        <v-col class="pb-0 pt-0" cols="4"><b>Placa Camión:</b></v-col>
                                                        <v-col class="pb-0 pt-0" cols="8">{{ sub.placa }}</v-col>
                                                        <v-col class="pb-0 pt-0" cols="4"><b>Recepción:</b></v-col>
                                                        <v-col class="pb-0 pt-0" cols="8">{{ sub.fecha_recepcion }}</v-col>
                                                        <v-col class="pb-0 pt-0" cols="4"><b>Peso Guia:</b></v-col>
                                                        <v-col class="pb-0 pt-0" cols="8">{{ sub.peso_guia }} Kg</v-col>
                                                        <v-col class="pb-0 pt-0" cols="4"><b>Total Jabas:</b></v-col>
                                                        <v-col class="pb-0 pt-0" cols="8">{{ sub.total_jabas }}</v-col>
                                                        <v-col class="pb-0 pt-0" cols="4"><b>Peso Bruto:</b></v-col>
                                                        <v-col class="pb-0 pt-0" cols="8">{{ sub.peso_bruto }}</v-col>
                                                        <v-col class="pb-0 pt-0" cols="4"><b>Peso Neto:</b></v-col>
                                                        <v-col class="pb-0 pt-0" cols="8">{{ sub.peso_neto }}</v-col>
                                                        <v-col cols="12" class="text-center">
                                                            <v-btn 
                                                                small
                                                                color="info" 
                                                                @click="abrirPalet(sub.id,sub.estado)">
                                                                <i class="fas fa-boxes"></i>
                                                            </v-btn>
                                                            <v-btn 
                                                                small
                                                                color="amber" 
                                                                @click="openEditar(sub.id)">
                                                                <i class="far fa-edit"></i>
                                                            </v-btn>
                                                        </v-col>
                                                    </v-row>
                                                </v-col>
                                            </v-row>
                                        </v-card-text>
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
    .table-noborde,.table-noborde *{
        border: 0;
    }
    .card-no-select{
        background-color: #eee;
    }
    @font-face {
        font-family: "digital";
        src: url("/font/digital_display_tfb.ttf");
    }
    .digital{
        border: 1px solid #111;
        padding: 0.5rem;
        font-size: 2rem;
        text-align: right;
        font-family: 'digital';
    }
</style>
<script>
import { mapState,mapMutations } from 'vuex'

export default {
    data() {
        return {
            alert: {},
            //modal
            open_nuevo:false,
            modal_editar: false,
            open_palets: false,
            //operaciones
            num_jabas: 0,
            peso: 0,
            peso_palet: 1.2,
            peso_jaba: 0.2,
            lote: {},
            lotes: [],
            sub_lote: this.init(),
            sub_lote_error:{},
            s_cliente_id: null,
            //listas
            clientes:[],
            sub_lotes: [],
            palets: [],
            palets_entrada: [],
            sub_lote_seleccionado: {
                palets: []
            },
            palets_error:{},
            //selectores
            seleccionado_sub_lote: null,
            seleccionado_estado_sub_lote: null,
            getZPL: true,
            printAdd: true,
            index_edit: -1,
            sub_lote_editar: null
        }
    },
    computed: {
        jabas_totales(){
            // var jabas_totales
            for (let index = 0; index < this.sub_lote_seleccionado.palets.length; index++) {
                const palet = this.sub_lote_seleccionado.palets[index];
                
            }
        },
        ...mapState(['defaultPrinter']),
    },
    watch: {
        open_palets: function() {
            this.listarSublote()
        }
    },
    mounted() {
        this.$store.commit('getDefaultPrinter');
        axios.get(url_base+`/cliente?proceso`)
        .then(response => {
            this.clientes=response.data
        });
        axios.get(url_base+`/lote_ingreso?estado=Pendiente`)
        .then(response => {
            this.lotes=response.data
        });
    },
    methods: {
        openEditar(id){
            this.modal_editar=true;
            axios.get(`${url_base}/sub_lote/${id}`)
            .then(response => {
                this.sub_lote_editar=response.data
                this.sub_lote_editar.fecha_recepcion=moment(this.sub_lote_editar.fecha_recepcion,'YYYY-MM-DD HH:mm').format('YYYY-MM-DDTHH:mm')
            });
        },
        updateSubLote(){
            axios.post(url_base+`/sub_lote/${this.sub_lote_editar.id}?_method=PATCH`,this.sub_lote_editar)
            .then(response => {
                var res=response.data;
                switch (res.status) {
                    case 'OK':
                        this.modal_editar=false;
                        this.sub_lote_editar=this.init();
                        swal("Sub Lote Actualizado", { icon: "success", timer: 2000, buttons: false });
                        this.listarSublote();
                        break;
                
                    default:
                        break;
                }
            });
        },
        openNuevo(){
            this.open_nuevo=true;
            this.sub_lote=this.init();
        },
        abrirPalet(id,estado){
            this.seleccionar(id);
            this.seleccionado_estado_sub_lote=estado;
            this.open_palets=true;
        },
        momentFormat(fecha,format){
            return moment(fecha).format(format);
        },
        init(){
            return {
                lote_id: null,
                viaje: 1,
                fecha_recepcion: moment().format('YYYY-MM-DDTHH:mm')
            };
        },
        listarSublote(){
            axios.get(`${url_base}/sub_lote?estado=Pendiente,Lanzado&cliente_id=${this.s_cliente_id}`)
            .then(response => {
                this.sub_lotes=response.data
            });
        },
        guardarSubLote(){
            axios.post(url_base+'/sub_lote',this.sub_lote)
            .then(response => {
                 var respuesta=response.data;
                switch (respuesta.status) {
                    case "VALIDATION":
                        this.sub_lote_error=respuesta.data;
                        break;
                    case "OK":
                        swal("Sub Lote Creado", { icon: "success", timer: 2000, buttons: false });
                        this.listarSublote();
                        this.open_nuevo=false;
                        t.lote_error={};
                        break;
                    default:
                        t.lote_error={};
                        break;
                }
            });
        },
        seleccionar(id){
            this.seleccionado_sub_lote=id;
            this.listarPaletEntrada();
        },
        listarPaletEntrada(){
            this.index_edit=0;
            axios.get(url_base+`/sub_lote/${this.seleccionado_sub_lote}/palets`)
            .then(response => {
                this.sub_lote_seleccionado=response.data;
            });
        },
        add(){
            axios.post(url_base+`/sub_lote/${this.seleccionado_sub_lote}/palet_entrada`,{
                num_jabas: this.num_jabas,
                peso: this.peso,
                peso_palet: this.peso_palet,
                peso_jaba: this.peso_jaba
            })
            .then(response => {
                var res=response.data;
                switch (res.status) {
                    case 'OK':
                        this.peso=0;
                        this.num_jabas=0;
                        this.palets_error={};
                        if (this.printAdd) {
                            this.printUnitario(res.data.id);
                        }
                        this.listarPaletEntrada();
                        break;

                    case 'VALIDATION':
                        this.palets_error=res.data;
                        break;
                
                    default:
                        break;
                }
            });
        },
        edit(index){ this.index_edit=index },
        saveEdit(palet){
            console.log(palet);
            
            axios.post(url_base+`/palet_entrada/${this.index_edit}?_method=PATCH`,palet)
            .then(response => {
                var res=response.data;
                switch (res.status) {
                    case 'OK':
                        this.index_edit=0;
                        this.listarPaletEntrada();
                        break;
                
                    default:
                        break;
                }
            });
        },
        pesado(){
            var t=this;
            swal({ title: "¿Cerrar pesado de Sub Lote?", buttons: ['Cancelar',"Si"]})
            .then((res) => {
                if (res) {
                    axios.post(`${url_base}/sub_lote/${ t.seleccionado_sub_lote }/estado?_method=PUT`,{
                        estado: 'Lanzado'
                    }).then(response => {
                        var res=response.data;
                        switch (res.status) {
                            case 'OK':
                                t.open_palets=false;
                                t.listarSublote();
                                break;
                        
                            default:
                                break;
                        }
                    });
                }
            });
        },
        printUnitario(id){
            axios.get(`${url_base}/print/zpl/palet_entrada`,{
                params: {
                    palet_entrada_id: id
                }
            })
            .then(response => {
                var respuesta=response.data;
                switch (respuesta.status) {
                    case 'OK':
                        this.defaultPrinter.send(respuesta.data, undefined, function(errorMessage){
                            alert("Error: " + errorMessage);	
                        });
                        break;
                    case 'ERROR':
                        this.alert.status= 'warning';
                        this.alert.visible= true;
                        this.alert.message= respuesta.data;
                        break;
                }
                // this.timer=setTimeout(() => {
                //     this.alert=this.initAlert();
                // }, 10000);

            });
        },    
        print(id){
            console.log(this.getZPL);
            axios.get(`${url_base}/print/zpl/palet_entrada/all?getZPL`,{
                params: {
                    sub_lote_id: id,
                }
            })
            .then(response => {
                var respuesta=response.data;
                switch (respuesta.status) {
                    case 'OK':
                        this.alert.status = 'primary';
                        this.alert.visible= true;
                        this.alert.message= 'IMPRIMIENDO';
                        
                        this.defaultPrinter.send(respuesta.data, undefined, function(errorMessage){
                            alert("Error: " + errorMessage);	
                        });
                        break;
                    case 'ERROR':
                        this.alert.status= 'warning';
                        this.alert.visible= true;
                        this.alert.message= respuesta.data;
                        break;
                }
                // this.timer=setTimeout(() => {
                //     this.alert=this.initAlert();
                // }, 10000);

            });
        }    
    },
}
</script>