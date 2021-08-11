<template>
    <v-container fluid>
        <v-card outlined>
            <v-card-title>Lista de Calibres</v-card-title>
            <v-card-text>
                <v-btn @click="open_nuevo=true" outlined color="info">Nuevo Calibre</v-btn>
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
                    <v-card-title class="headline">Nuevo calibre</v-card-title>
                    <v-card-text>
                        <v-text-field 
                            hide-details="auto"
                            label="Nombre" 
                            v-model="calibre.nombre_calibre"
                            :error-messages="error.nombre_calibre"
                        ></v-text-field>
                        <div>
                            <v-select
                                label="Materia:"
                                hide-details="auto"
                                v-model="calibre.materia_id"
                                :error-messages="error.materia_id"
                                :items="materias"
                                item-text="nombre_materia"
                                item-value="id">
                                </v-select>
                        </div>
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
                    <v-card-title class="headline">Editar calibre</v-card-title>
                    <v-card-text>
                        <v-text-field 
                            required 
                            hide-details="auto"
                            label="Nombre" 
                            v-model="calibre_editar.nombre_calibre"
                            :error-messages="error_editar.nombre_calibre"
                        ></v-text-field>
                        <v-select
                                label="Materia:"
                                hide-details="auto"
                                v-model="calibre_editar.materia_id"
                                :error-messages="error_editar.materia_id"
                                :items="materias"
                                item-text="nombre_materia"
                                item-value="id">
                                </v-select>
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
                { text: 'Descripción', value: 'nombre_calibre' },
                { text: 'Materia', value: 'nombre_materia' },
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
            calibre: this.initForm(),
            error: {},
            //Modal Editar
            open_editar: false,
            calibre_editar: this.initForm(),
            error_editar: {},
        }
    },
    mounted() {
        axios.get(url_base+'/materia?all')
        .then(response => {
            this.materias = response.data;
            this.materias.push({
                id: '',
                nombre_materia: 'Seleccione Materia'
            })
        })
        this.listar(1);
    },
    methods: {
        initForm(){
            return {
                nombre_calibre: '',
                materia_id: '',
            }
        },
        listar(n=this.table.current_page){
            axios.get(url_base+'/calibre?page='+n+'&search='+this.search)
            .then(response => {
                this.table = response.data;
            })
        },
        guardar(){
            axios.post(url_base+'/calibre',this.calibre)
            .then(response => {
                var respuesta=response.data;
                switch (respuesta.status) {
                    case 'OK':
                        swal("calibre Creado", { 
                            icon: "success", 
                            timer: 2000, 
                            buttons: false
                        });
                        this.calibre=this.initForm();
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
            axios.get(url_base+'/calibre/'+id)
            .then(response => {
                this.open_editar=true;
                this.calibre_editar = response.data;
            })
        },
        actualizar(){
            axios.post(url_base+`/calibre/${this.calibre_editar.id}?_method=PUT`,this.calibre_editar)
            .then(response => {
                var respuesta=response.data;
                switch (respuesta.status) {
                    case 'OK':
                        swal("calibre Actualizado", { 
                            icon: "success", 
                            timer: 2000, 
                            buttons: false
                        });
                        this.calibre_editar=this.initForm();
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