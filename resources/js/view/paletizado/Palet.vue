<template>
    <v-container fluid>        
        <v-card>
            <v-card-text>
                <v-row>
                    <v-col cols="12" lg="4">
                        <v-select
                            v-model="palet_busqueda_1.cliente_id"
                            label="Cliente:"
                            :items="clientes"
                            :item-text="cliente => `${cliente.descripcion}`"
                            item-value="id"
                            >
                        </v-select>
                    </v-col>
                    <v-col cols="8" lg="4">
                        <v-select
                            v-model="palet_busqueda_1.tipo_palet_id"
                            label="Tipo de Palet:"
                            :items="tipos_palet"
                            :item-text="tipo => `${tipo.descripcion}`"
                            item-value="id">
                        </v-select>
                    </v-col>
                    <v-col cols="4" lg="2">
                        <v-text-field 
                            required 
                            label="Número" 
                            v-model="palet_busqueda_1.numero"
                        ></v-text-field>
                    </v-col>
                    <v-col cols="12" lg="2">
                        <v-btn @click="getPaletSalida">
                            Buscar
                        </v-btn>
                    </v-col>
                </v-row>
            </v-card-text>
        </v-card>
        <v-card v-if="palet!=null">
            <v-card-title>
                Palet {{ palet.tipo_palet_id }} - {{ palet.numero }}  
                <v-chip small
                        class="ma-2"
                        :color="colorGet(palet.estado)"
                        text-color="white"
                        >
                        {{palet.estado}}
                </v-chip>
            </v-card-title>
            <v-card-text >
                <v-btn @click="open_nuevo=true" color="primary" small>Transferir</v-btn>
                <v-btn @click="cambiarPendiente" color="warning" small>Cambiar a Pendiente</v-btn>
                <v-data-table
                    item-key="id"
                    show-select
                    :disable-sort="false"
                    :headers="header"
                    :items="palet.detalle"
                    >
                    <template v-slot:item.opciones="{ item }">
                        <v-btn text color="red" @click="eliminar(item.id)">
                            <i class="fas fa-trash-alt"></i>
                        </v-btn>
                    </template>
                </v-data-table>
            </v-card-text>
        </v-card>
        
        <v-dialog v-model="open_nuevo" persistent max-width="900" scrollable>
            <v-card>
                <v-card-title>
                    Transferencia de Cajas
                </v-card-title>
                <v-card-text>

                    <v-stepper v-model="e1">
                        <v-stepper-header>
                            <v-stepper-step :complete="e1 > 1" step="1">
                                Seleccionar Palet
                            </v-stepper-step>
                            <v-divider></v-divider>
                            <v-stepper-step :complete="e1 > 2" step="2">
                                Seleccionar Nueva Etiqueta
                            </v-stepper-step>
                            <v-divider></v-divider>
                            <v-stepper-step step="3">Confirmar</v-stepper-step>
                        </v-stepper-header>
                        <v-stepper-items>
                            <v-stepper-content step="1">
                                <v-row>
                                    <v-col cols="12" sm="6" lg="4">
                                        <v-select
                                            v-model="palet_busqueda.tipo_palet_id"
                                            label="Tipo de Palet:"
                                            :items="tipos_palet"
                                            :item-text="tipo => `${tipo.descripcion}`"
                                            item-value="id">
                                        </v-select>
                                    </v-col>
                                    <v-col cols="12" lg="2">
                                        <v-text-field 
                                            required 
                                            label="Número" 
                                            v-model="palet_busqueda.numero"
                                        ></v-text-field>
                                    </v-col>
                                    <v-col cols="12" lg="2">
                                        <v-btn @click="buscar">
                                            Buscar
                                        </v-btn>
                                    </v-col>
                                    <v-col cols="12" lg="4">
                                        <v-text-field
                                            v-model="search"
                                            append-icon="mdi-magnify"
                                            label="Search"
                                            single-line
                                            hide-details
                                        ></v-text-field>
                                    </v-col>
                                </v-row>
                                <v-data-table
                                    v-if="palet_transferir!=null"
                                    item-key="id"
                                    v-model="selected_transferir"
                                    show-select
                                    :disable-sort="false"
                                    :headers="header"
                                    :items="palet_transferir.detalle"
                                    disable-pagination
                                    :search="search"
                                    >
                                </v-data-table>
                            </v-stepper-content>
                            <v-stepper-content step="2">
                                <v-row>
                                    <v-col cols="12" sm=6 lg="4">
                                        <v-text-field
                                            readonly
                                            label="Fecha Empaque:"
                                            v-model="consulta_etiqueta.fecha_empaque"
                                            type="date">
                                        </v-text-field>
                                    </v-col>
                                    <v-col cols="12" sm=6 lg="2">
                                        <v-btn @click="listarEtiquetas">Listar</v-btn>
                                    </v-col>
                                    <v-col cols="12">
                                        <v-layout v-if="etiquetas!=null" column style="height: 400px"> 
                                            <v-flex style="overflow: auto"> 
                                                <v-data-table
                                                    item-key="id"
                                                    v-model="selected_etiqueta_id"
                                                    show-select
                                                    :disable-sort="false"
                                                    :headers="header"
                                                    :items="etiquetas"
                                                    :single-select="true"
                                                    disable-pagination
                                                    :search="search"
                                                    >
                                                </v-data-table>
                                            </v-flex>
                                        </v-layout>
                                    </v-col>
                                </v-row>

                                <v-btn
                                color="primary"
                                @click="e1 = 3"
                                >
                                Continue
                                </v-btn>

                                <v-btn @click="e1=1" text>
                                    Atras
                                </v-btn>
                            </v-stepper-content>

                            <v-stepper-content step="3">
                                <v-card
                                class="mb-12"
                                color="grey lighten-1"
                                height="200px"
                                ></v-card>

                                <v-btn
                                color="primary"
                                @click="transferir()"
                                >
                                    Transferir
                                </v-btn>

                                <v-btn text @click="e1=2">
                                    Atras
                                </v-btn>
                            </v-stepper-content>
                        </v-stepper-items>
                    </v-stepper>
                </v-card-text>
                <v-card-actions>
                    <v-container>
                        <v-row v-if="e1==1">
                            <v-col cols="12" sm="6" lg="4">
                                Seleccionadas: {{seleccionadas}} 
                            </v-col>
                            <v-col cols="12" sm="6" lg="4">
                                Empaque: {{fechaSelect}}
                            </v-col>
                            <v-col cols="12" lg="4" class="text-right" v-if="fechaSelect.length==1">
                                <v-btn color="primary" @click="e1 = 2">Continuar</v-btn>
                            </v-col>
                        </v-row>
                    </v-container>
                </v-card-actions>
            </v-card>
            <!--v-card>
                <v-card-title class="headline">
                    
                </v-card-title>
                <v-card-text style="height: 500px;">
                    
                </v-card-text>
                <v-card-actions>
                    <v-row>
                        <v-col cols="12" lg="4">
                            Seleccionadas: {{seleccionadas}}
                        </v-col>
                        
                    </v-row>
                    <div class="text-right mt-3" >
                        <v-btn 
                            outlined 
                            color="secondary" 
                            @click="open_nuevo=false"
                            >Cancelar</v-btn>
                        <v-btn 
                            outlined 
                            color="primary" 
                            @click="guardar()"
                            >Transferir</v-btn>
                    </div>
                </v-card-actions>
            </v-card>                -->
        </v-dialog>
    </v-container>
</template>
<style>
    .scroll {
        overflow-y: scroll
    }
</style>
<script>
export default {
    data(){
        return {
            selected_etiqueta_id: [],
            e1: 1,
            search: '',
            palet_busqueda_1:{
                tipo_palet_id: null,
                numero: null,
                cliente_id: null
            },
            palet_busqueda: {
                tipo_palet_id: ''
            },
            palet_transferir: null,
            open_nuevo: false,
            palet: null,
            selected_transferir: [],
            header:[
                { text: 'Fecha Empaque', value: 'fecha_empaque' },
                { text: 'Lote', value: 'codigo_lote' },
                { text: 'Presentación', value: 'nombre_presentacion' },
                { text: 'Calibre', value: 'nombre_calibre' },
                { text: 'Categoria', value: 'nombre_categoria' },
                { text: 'Tipo Empaque', value: 'nombre_tipo_empaque' },
                { text: 'Marca Empaque', value: 'nombre_marca_empaque' },
                { text: 'Marca Caja', value: 'nombre_marca_caja' },
                { text: 'PLU', value: 'nombre_plu' },
                { text: 'Opciones', value: 'opciones' },
            ],
            tipos_palet: [],
            consulta_etiqueta:{
                estado: 'Pendiente'
            },
            etiquetas: [],
            fecha_empaque: '',
            clientes: [],
            estados: [
                {color: 'success', estado: 'Pendiente'},
                {color: 'warning', estado: 'Lanzado'},
                {color: 'primary', estado: 'Cerrado'},
                {color: 'danger', estado: 'Pendiente'}
            ]
        }
    },
    mounted(){
        // this.getPaletSalida();
        this.listarTiposPalet();
        this.listarClientes();
    },
    computed:{
        seleccionadas(){
            return this.selected_transferir.length
        },
        fechaSelect(){
            var fecha="";
            var fechas=this.selected_transferir.map((fila)=>fila.fecha_empaque);
            var newFechas = fechas.filter((valor, indice) => {
                return fechas.indexOf(valor) === indice;
            });
            if (newFechas.length>0) {
                this.consulta_etiqueta.fecha_empaque=newFechas[0];
            }
            return newFechas;
        }
    },
    methods:{
        colorGet(estado){
            for (let i = 0; i < this.estados.length; i++) {
                const element = this.estados[i];
                if (element.estado==estado) {
                    return element.color;
                }
            }
            return '';
        },
        listarClientes(){
            axios.get(url_base+'/cliente?all')
            .then(response => {
                this.clientes=response.data
            })
        },
        getPaletSalida(){
            axios.get(url_base+`/palet_salida/search`,{
                params: this.palet_busqueda_1
            }).then(response => {
                this.palet=response.data;
                this.palet_busqueda.cliente_id=this.palet.cliente_id;
                this.consulta_etiqueta.productor_id=this.palet.cliente_id;
            });
        },
        listarTiposPalet(){
            axios.get(url_base+`/tipo-palet`)
            .then(response => {
                this.tipos_palet=response.data
                this.palet_busqueda.tipo_palet_id=this.tipos_palet[0].id
            });
        },
        buscar(){
            axios.get(url_base+`/palet_salida/search`,{
                params: this.palet_busqueda
            })
            .then(response => {
                this.palet_transferir=response.data;
            });
        },
        transferir(){
            axios.post(url_base+`/palet_salida/transferencia`,{
                cajas_id: this.selected_transferir.map((fila)=>fila.id).join(','),
                palet_destino_id: this.palet.id,
                etiqueta_caja_id: (this.selected_etiqueta_id.length>0) ? this.selected_etiqueta_id[0].id: null
            })
            .then(response => {
                var respuesta=response.data;
                switch (respuesta.status) {
                    case 'OK':
                        this.getPaletSalida();
                        this.buscar();
                        swal("Transferencia Realizada", {
                                    icon: "success",
                                    timer: 2000,
                                    buttons: false
                                });
                        break;
                
                    default:
                        break;
                }
            });
        },
        listarEtiquetas(){
            axios.get(url_base+'/etiqueta-caja?all',{
                params: this.consulta_etiqueta
            })
            .then(response => {
                this.etiquetas = response.data;
            })
        },
        eliminar(id){
            swal({
                title: "¿Desea Eliminar Caja?",
                buttons: ['Cancelar',"OK"],
            })
            .then((res) => {
                if (res) {
                    axios.post(url_base+`/caja/${ id }?_method=DELETE`)
                    .then(response => {
                        var res=response.data;
                        switch (res.status) {
                            case 'OK':
                                swal(res.message, {
                                    icon: "success",
                                    timer: 2000,
                                    buttons: false
                                });
                                this.getPaletSalida();
                                break;
                        }
                    });
                }
            });
        },
        cambiarPendiente(){
            swal({
                title: "¿Desea cambiar a Pendiente?",
                buttons: ['Cancelar',"OK"],
            })
            .then((res) => {
                if (res) {
                    axios.post(url_base+`/palet_salida/${ this.palet.id }?_method=patch`,{
                        estado: 'Pendiente'
                    })
                    .then(response => {
                        var res=response.data;
                        switch (res.status) {
                            case 'OK':
                                swal(res.message, {
                                    icon: "success",
                                    timer: 2000,
                                    buttons: false
                                });
                                this.getPaletSalida();
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