<template>
    <v-container fluid>
        <v-card outlined>
            <v-card-title>Lista de Materias</v-card-title>
            <v-card-text>
                <v-btn @click="open_nuevo=true" outlined color="info">Nueva Materia</v-btn>
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
                    <v-card-title class="headline">Nueva Materia</v-card-title>
                    <v-card-text>
                        <v-text-field 
                            required 
                            hide-details="auto"
                            label="Nombre" 
                            v-model="materia.nombre_materia"
                            :error-messages="error.nombre_materia"
                        ></v-text-field>
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
                    <v-card-title class="headline">Editar Materia</v-card-title>
                    <v-card-text>
                        <v-text-field 
                            required 
                            hide-details="auto"
                            label="Nombre" 
                            v-model="materia_editar.nombre_materia"
                            :error-messages="error_editar.nombre_materia"
                        ></v-text-field>
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
            header:[
                { text: 'Nombre', value: 'nombre_materia' },
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
            materia: this.initForm(),
            error: {},
            //Modal Editar
            open_editar: false,
            materia_editar: this.initForm(),
            error_editar: {},
        }
    },
    mounted() {
        this.listar(1);
    },
    methods: {
        initForm(){
            return {
                nombre_materia: ''
            }
        },
        listar(n=this.table.current_page){
            axios.get(url_base+'/materia?page='+n+'&search='+this.search)
            .then(response => {
                this.table = response.data;
            })
        },
        guardar(){
            axios.post(url_base+'/materia',this.materia)
            .then(response => {
                var respuesta=response.data;
                switch (respuesta.status) {
                    case 'OK':
                        swal("Materia Creada", { 
                            icon: "success", 
                            timer: 2000, 
                            buttons: false
                        });
                        this.materia=this.initForm();
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
            axios.get(url_base+'/materia/'+id)
            .then(response => {
                this.open_editar=true;
                this.materia_editar = response.data;
            })
        },
        actualizar(){
            axios.post(url_base+`/materia/${this.materia_editar.id}?_method=PUT`,this.materia_editar)
            .then(response => {
                var respuesta=response.data;
                switch (respuesta.status) {
                    case 'OK':
                        swal("Materia Actualizada", { 
                            icon: "success", 
                            timer: 2000, 
                            buttons: false
                        });
                        this.materia_editar=this.initForm();
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