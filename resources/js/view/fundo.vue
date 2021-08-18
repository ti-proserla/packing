<template>
    <v-container fluid>
        <v-card outlined>
            <v-card-title>Lista de Fundos</v-card-title>
            <v-card-text>
                <v-btn @click="open_nuevo=true" outlined color="info">Nueva Fundo</v-btn>
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
                    <v-card-title class="headline">Nueva fundo</v-card-title>
                    <v-card-text>
                        <v-row>

                            <v-col cols="12">
                                <v-text-field 
                                    label="cod_cartilla" 
                                    v-model="fundo.cod_cartilla"
                                    :error-messages="error.cod_cartilla"
                                ></v-text-field>
                            </v-col>
                            <v-col cols="12">
                                <v-text-field 
                                    label="Nombre" 
                                    v-model="fundo.nombre_fundo"
                                    :error-messages="error.nombre_fundo"
                                ></v-text-field>
                            </v-col>
                            <v-col cols="12">
                                <v-text-field 
                                    label="Lugar Producción:" 
                                    v-model="fundo.lugar_produccion"
                                    :error-messages="error.lugar_produccion"
                                ></v-text-field>
                            </v-col>
                            <v-col cols="12">
                                <v-text-field 
                                    label="COd Lugar Producción" 
                                    v-model="fundo.cod_lugar_produccion"
                                    :error-messages="error.cod_lugar_produccion"
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
                    <v-card-title class="headline">Editar Fundo</v-card-title>
                    <v-card-text>
                        <v-row>
                            <v-col cols="12">
                                <v-text-field 
                                    label="cod_cartilla" 
                                    v-model="fundo_editar.cod_cartilla"
                                    :error-messages="error_editar.cod_cartilla"
                                ></v-text-field>
                            </v-col>
                            <v-col cols="12">
                                <v-text-field 
                                    required 
                                    label="Lugar Producción:" 
                                    v-model="fundo_editar.lugar_produccion"
                                    :error-messages="error_editar.lugar_produccion"
                                ></v-text-field>
                            </v-col>
                            <v-col cols="12">
                                <v-text-field 
                                    required 
                                    label="Código Lugar Producción:" 
                                    v-model="fundo_editar.cod_lugar_produccion"
                                    :error-messages="error_editar.cod_lugar_produccion"
                                ></v-text-field>
                            </v-col>
                            <v-col cols="12">
                                <v-text-field 
                                    required 
                                    label="Nombre" 
                                    v-model="fundo_editar.nombre_fundo"
                                    :error-messages="error_editar.nombre_fundo"
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
                { text: 'Código Cartilla', value: 'cod_cartilla' },
                { text: 'Descripción', value: 'nombre_fundo' },
                { text: 'Lugar de Producción', value: 'lugar_produccion' },
                { text: 'Cod Lugar Producción', value: 'cod_lugar_produccion' },
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
            fundo: this.initForm(),
            error: {},
            //Modal Editar
            open_editar: false,
            fundo_editar: this.initForm(),
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
                cod_cartilla: '',
                nombre_fundo: '',
                lugar_produccion: '',
                cod_lugar_produccion: ''
            }
        },
        listar(n=this.table.current_page){
            axios.get(url_base+'/fundo?page='+n+'&search='+this.search)
            .then(response => {
                this.table = response.data;
            })
        },
        guardar(){
            axios.post(url_base+'/fundo',this.fundo)
            .then(response => {
                var respuesta=response.data;
                switch (respuesta.status) {
                    case 'OK':
                        swal("fundo Creado", { 
                            icon: "success", 
                            timer: 2000, 
                            buttons: false
                        });
                        this.fundo=this.initForm();
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
            axios.get(url_base+'/fundo/'+id)
            .then(response => {
                this.open_editar=true;
                this.fundo_editar = response.data;
            })
        },
        actualizar(){
            axios.post(url_base+`/fundo/${this.fundo_editar.id}?_method=PUT`,this.fundo_editar)
            .then(response => {
                var respuesta=response.data;
                switch (respuesta.status) {
                    case 'OK':
                        swal("fundo Actualizado", { 
                            icon: "success", 
                            timer: 2000, 
                            buttons: false
                        });
                        this.fundo_editar=this.initForm();
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