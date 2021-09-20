<template>
    <v-container fluid>
        <v-card outlined>
            <v-card-title>Lista de presentaciones</v-card-title>
            <v-card-text>
                <v-btn @click="open_nuevo=true" outlined color="info">Nueva presentacion</v-btn>
                <v-data-table
                    :headers="header"
                    :items="table.data"
                    :page.sync="table.current_page"
                    hide-default-footer
                    >
                    <template v-slot:item.editar="{ item }">
                        <v-btn text color="warning" @click="buscar(item.id)">
                            <i class="far fa-edit"></i>
                        </v-btn>
                    </template>
                </v-data-table>
                <v-pagination v-model="table.current_page" :length="table.last_page" circle @input="listar"></v-pagination>
            </v-card-text>

            <!-- Nuevo -->
            <v-dialog v-model="open_nuevo" persistent max-width="350">
                <v-card>
                    <v-card-title class="headline">Nueva presentacion</v-card-title>
                    <v-card-text>
                        <v-row>
                            <v-col cols="12" lg="12">
                                <v-text-field 
                                    label="Nombre:" 
                                    v-model="presentacion.nombre_presentacion"
                                    :error-messages="error.nombre_presentacion"
                                ></v-text-field>
                            </v-col>
                            <v-col cols="12">
                                <v-text-field 
                                    label="Peso Neto:" 
                                    v-model="presentacion.peso_neto"
                                    :error-messages="error.peso_neto"
                                ></v-text-field>
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
                    <v-card-title class="headline">Editar presentacion</v-card-title>
                    <v-card-text>
                        <v-row>
                            <v-col cols="12">
                                <v-text-field 
                                    label="Nombre" 
                                    v-model="presentacion_editar.nombre_presentacion"
                                    :error-messages="error_editar.nombre_presentacion"
                                ></v-text-field>
                            </v-col>
                            <v-col cols="12">
                                <v-text-field 
                                    label="Peso Neto:" 
                                    v-model="presentacion_editar.peso_neto"
                                    :error-messages="error_editar.peso_neto"
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
            materias: [],
            header:[
                { text: 'Descripción', value: 'nombre_presentacion' },
                { text: 'Editar', value: 'editar' },
            ],
            table: {
                current_page: 1,
                last_page: 1,
                data: []
            },
            search: '',
            //Modal Nuevo
            open_nuevo: false,
            presentacion: this.initForm(),
            error: {},
            //Modal Editar
            open_editar: false,
            presentacion_editar: this.initForm(),
            error_editar: {},
        }
    },
    mounted() {
        this.listar(1);
    },
    methods: {
        reset(){
            this.presentacion=this.initForm();
        },
        initForm(){
            return {
                nombre_presentacion: '',
            }
        },
        listar(n=this.table.current_page){
            axios.get(url_base+'/presentacion?page='+n+'&search='+this.search)
            .then(response => {
                this.table = response.data;
            })
        },
        guardar(){
            axios.post(url_base+'/presentacion',this.presentacion)
            .then(response => {
                var respuesta=response.data;
                switch (respuesta.status) {
                    case 'OK':
                        swal("presentacion Creado", { 
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
            axios.get(url_base+'/presentacion/'+id)
            .then(response => {
                this.open_editar=true;
                this.presentacion_editar = response.data;
            })
        },
        actualizar(){
            axios.post(url_base+`/presentacion/${this.presentacion_editar.id}?_method=PUT`,this.presentacion_editar)
            .then(response => {
                var respuesta=response.data;
                switch (respuesta.status) {
                    case 'OK':
                        swal("presentacion Actualizado", { 
                            icon: "success", 
                            timer: 2000, 
                            buttons: false
                        });
                        this.presentacion_editar=this.initForm();
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