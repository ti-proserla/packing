<template>
    <v-container fluid>
        <v-card outlined>
            <v-card-title>Programa de Presentacion x Linea</v-card-title>
            <v-card-text>
                 <v-row>
                    <v-col cols="12" sm=6 lg="3">
                        <v-text-field
                            label="Fecha Proceso:"
                            v-model="fecha_proceso"
                            type="date">
                        </v-text-field>
                    </v-col>
                    <v-col cols="12" sm=6 lg="3">
                        <v-btn @click="listar" outlined color="info">Actualizar</v-btn>
                    </v-col>
                    <v-col cols="12" sm=6 lg="6" class="text-right">
                        <v-btn @click="open_nuevo=true" outlined color="info">Nuevo Programa</v-btn>
                    </v-col>
                 </v-row>
                <v-data-table
                    :headers="header"
                    :items="table.data"
                    :page.sync="table.current_page"
                    hide-default-footer
                    >
                    <template v-slot:item.fin="{ item }">
                        <v-btn v-if="item.fin==null" text color="info" @click="cerrar(item.id)">
                            <i class="far fa-times-circle"></i>
                        </v-btn>
                        {{ item.fin }}
                    </template>
                    <!-- <template v-slot:item.editar="{ item }">
                        <v-btn text color="warning" @click="buscar(item.id)">
                            <i class="far fa-edit"></i>
                        </v-btn>
                    </template> -->
                </v-data-table>
                <v-pagination v-model="table.current_page" :length="table.last_page" circle @input="listar"></v-pagination>
            </v-card-text>

            <!-- Nuevo -->
            <v-dialog v-model="open_nuevo" persistent max-width="350">
                <v-card>
                    <v-card-title class="headline">Nueva Presentacion Linea</v-card-title>
                    <v-card-text>
                        <v-row>
                            <v-col cols="12" lg="12">
                                <v-text-field 
                                        label="Fecha"
                                        type="date" 
                                        v-model="presentacion_linea.fecha_ref"
                                        :error-messages="error.fecha_ref"
                                    ></v-text-field>
                            </v-col>
                            <v-col cols="12" lg="12">
                                <v-select
                                    label="Linea:"
                                    v-model="presentacion_linea.linea_id"
                                    :items="lineas"
                                    :item-text="(item) => item.numero"
                                    :error-messages="error.linea_id"
                                    item-value="numero">
                                    </v-select>
                            </v-col>
                            <v-col cols="12">
                                <v-select
                                    label="Presentacion:"
                                    v-model="presentacion_linea.presentacion_id"
                                    :error-messages="error.presentacion_id"
                                    :items="presentaciones"
                                    item-text="nombre_presentacion"
                                    item-value="id">
                                    </v-select>
                            </v-col>
                        </v-row>
                        <div class="text-right mt-3">
                            <v-btn 
                                outlined 
                                color="secondary" 
                                @click="open_nuevo=false"
                                >Cancelar</v-btn>
                            <v-btn 
                                outlined 
                                color="primary" 
                                @click="guardar()"
                                >Guardar</v-btn>
                        </div>
                    </v-card-text>
                </v-card>               
            </v-dialog>
            <!-- Editar -->
            <v-dialog v-model="open_editar" persistent max-width="350">
                <v-card>
                    <v-card-title class="headline">Editar presentacion_linea</v-card-title>
                    <v-card-text>
                        <v-row>
                            <v-col cols="12">
                                <v-text-field 
                                    label="Nombre" 
                                    v-model="presentacion_linea_editar.nombre_presentacion_linea"
                                    :error-messages="error_editar.nombre_presentacion_linea"
                                ></v-text-field>
                            </v-col>
                            <v-col cols="12">
                                <v-text-field 
                                    label="Peso Neto:" 
                                    v-model="presentacion_linea_editar.peso_neto"
                                    :error-messages="error_editar.peso_neto"
                                ></v-text-field>
                            </v-col>
                            <v-col cols="12">
                                <v-text-field 
                                    label="Tope Cajas por Palet:" 
                                    v-model="presentacion_linea_editar.tope_cajas"
                                    :error-messages="error_editar.tope_cajas"
                                ></v-text-field>
                            </v-col>
                        </v-row>
                        <div class="text-right mt-3">
                            <v-btn 
                                outlined 
                                color="secondary" 
                                @click="open_editar=false"
                                >Cancelar</v-btn>
                            <v-btn 
                                outlined 
                                color="primary" 
                                @click="actualizar()"
                                >Guardar</v-btn>
                        </div>
                    </v-card-text>
                </v-card>               
            </v-dialog>
        </v-card>
    </v-container>
</template>
<script>
export default {
    data() {
        return {
            presentaciones: [],
            lineas: [
                {numero: 1},
                {numero: 2},
                {numero: 3},
                {numero: 4},
                {numero: 5},
                {numero: 6}
            ],
            materias: [],
            header:[
                { text: 'Linea', value: 'linea_id' },
                { text: 'Presentacion', value: 'nombre_presentacion' },
                { text: 'Inicio', value: 'inicio' },
                { text: 'Fin', value: 'fin' },
                // { text: 'Editar', value: 'editar' },
            ],
            table: {
                current_page: 1,
                last_page: 1,
                data: []
            },
            search: '',
            //Modal Nuevo
            open_nuevo: false,
            presentacion_linea: this.initForm(),
            error: {},
            //Modal Editar
            open_editar: false,
            presentacion_linea_editar: this.initForm(),
            error_editar: {},
            fecha_proceso: moment().format('YYYY-MM-DD')
        }
    },
    mounted() {
        this.listar(1);
        axios.get(`${url_base}/presentacion?all`)
        .then(response => {
            this.presentaciones = response.data;
        })
    },
    methods: {
        reset(){
            this.presentacion_linea=this.initForm();
        },
        initForm(){
            return {
                presentacion_id: '',
                linea_id: '',
                fecha_ref: moment().format('YYYY-MM-DD')
            }
        },
        listar(n=this.table.current_page){
            axios.get(url_base+'/presentacion_linea?page='+n+'&fecha_proceso='+this.fecha_proceso)
            .then(response => {
                this.table = response.data;
            })
        },
        guardar(){
            axios.post(url_base+'/presentacion_linea',this.presentacion_linea)
            .then(response => {
                var respuesta=response.data;
                switch (respuesta.status) {
                    case 'OK':
                        swal("presentacion_linea Creado", { 
                            icon: "success", 
                            timer: 2000, 
                            buttons: false
                        });
                        this.reset()
                        this.open_nuevo=false;
                        this.listar();
                        break;
                    case 'VALIDATION':
                        this.error=respuesta.data;
                        break;
                    case 'ERROR':
                        break;
                    default:

                        break;
                }
            });
        },
        buscar(id){
            axios.get(url_base+'/presentacion_linea/'+id)
            .then(response => {
                this.open_editar=true;
                this.presentacion_linea_editar = response.data;
            })
        },
        cerrar(id){
            var t=this;
            swal({ title: "¿Desea Cerrar Programa?", buttons: ['Cancelar',"Si"]})
            .then((res) => {
                if (res) {
                    axios.post(url_base+`/presentacion_linea/${id}?_method=PUT`)
                    .then(response => {
                        var respuesta=response.data;
                        switch (respuesta.status) {
                            case 'OK':
                                swal("Programa Cerrado", { 
                                    icon: "success", 
                                    timer: 2000, 
                                    buttons: false
                                });
                                t.listar();
                                break;
                        }
                    });
                }
            });
        },
        actualizar(){
            axios.post(url_base+`/presentacion_linea/${this.presentacion_linea_editar.id}?_method=PUT`,this.presentacion_linea_editar)
            .then(response => {
                var respuesta=response.data;
                switch (respuesta.status) {
                    case 'OK':
                        swal("presentacion_linea Actualizado", { 
                            icon: "success", 
                            timer: 2000, 
                            buttons: false
                        });
                        this.presentacion_linea_editar=this.initForm();
                        this.open_editar=false;
                        this.listar();
                        break;
                    case 'VALIDATION':
                        this.error_editar=respuesta.data;
                        break;
                    case 'ERROR':
                        break;
                    default:

                        break;
                }
            });
        }
    },
}
</script>