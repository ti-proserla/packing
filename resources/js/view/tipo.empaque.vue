<template>
    <v-container fluid>
        <v-card outlined>
            <v-card-title>Lista de tipo empaque</v-card-title>
            <v-card-text>
                <v-btn @click="open_nuevo=true" outlined color="info">Nueva tipo empaque</v-btn>
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
                    <v-card-title class="headline">Nueva tipo empaque</v-card-title>
                    <v-card-text>
                        <v-row>
                            <v-col cols="12" lg="12">
                                <v-text-field 
                                    label="Nombre:" 
                                    v-model="tipo_empaque.nombre_tipo_empaque"
                                    :error-messages="error.nombre_tipo_empaque"
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
                    <v-card-title class="headline">Editar tipo empaque</v-card-title>
                    <v-card-text>
                        <v-row>
                            <v-col cols="12">
                                <v-text-field 
                                    label="Nombre" 
                                    v-model="tipo_empaque_editar.nombre_tipo_empaque"
                                    :error-messages="error_editar.nombre_tipo_empaque"
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
                { text: 'Descripción', value: 'nombre_tipo_empaque' },
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
            tipo_empaque: this.initForm(),
            error: {},
            //Modal Editar
            open_editar: false,
            tipo_empaque_editar: this.initForm(),
            error_editar: {},
        }
    },
    mounted() {
        this.listar(1);
    },
    methods: {
        reset(){
            this.tipo_empaque=this.initForm();
        },
        initForm(){
            return {
                nombre_tipo_empaque: '',
            }
        },
        listar(n=this.table.current_page){
            axios.get(url_base+'/tipo-empaque?page='+n+'&search='+this.search)
            .then(response => {
                this.table = response.data;
            })
        },
        guardar(){
            axios.post(url_base+'/tipo-empaque',this.tipo_empaque)
            .then(response => {
                var respuesta=response.data;
                switch (respuesta.status) {
                    case 'OK':
                        swal("tipo_empaque Creado", { 
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
            axios.get(url_base+'/tipo-empaque/'+id)
            .then(response => {
                this.open_editar=true;
                this.tipo_empaque_editar = response.data;
            })
        },
        actualizar(){
            axios.post(url_base+`/tipo-empaque/${this.tipo_empaque_editar.id}?_method=PUT`,this.tipo_empaque_editar)
            .then(response => {
                var respuesta=response.data;
                switch (respuesta.status) {
                    case 'OK':
                        swal("tipo_empaque Actualizado", { 
                            icon: "success", 
                            timer: 2000, 
                            buttons: false
                        });
                        this.tipo_empaque_editar=this.initForm();
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