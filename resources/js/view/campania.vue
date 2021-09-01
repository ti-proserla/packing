<template>
    <v-container fluid>
        <v-card outlined>
            <v-card-title>Lista de campañas</v-card-title>
            <v-card-text>
                <v-btn @click="open_nuevo=true" outlined color="info">Nuevo campaña</v-btn>
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
                    <v-card-title class="headline">Nuevo campania</v-card-title>
                    <v-card-text>
                        <v-row>
                            <v-col cols="12">
                                <v-text-field 
                                    label="ID:" 
                                    v-model="campania.id"
                                    :error-messages="error.id"
                                ></v-text-field>
                            </v-col>
                            <v-col cols="12">
                                <v-text-field 
                                    label="Año:" 
                                    v-model="campania.anio"
                                    :error-messages="error.anio"
                                ></v-text-field>
                            </v-col>
                            <v-col>
                                <v-select
                                    label="Materia:"
                                    v-model="campania.materia_id"
                                    :error-messages="error.materia_id"
                                    :items="materias"
                                    item-text="nombre_materia"
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
                    <v-card-title class="headline">Editar campania</v-card-title>
                    <v-card-text>
                        <v-text-field 
                            label="anio" 
                            v-model="campania_editar.anio"
                            :error-messages="error_editar.anio"
                        ></v-text-field>
                        <v-text-field 
                            required 
                            label="RUC" 
                            v-model="campania_editar.ruc"
                            :error-messages="error_editar.ruc"
                        ></v-text-field>
                        <v-text-field 
                            required 
                            label="Nombre" 
                            v-model="campania_editar.descripcion"
                            :error-messages="error_editar.descripcion"
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
                { text: 'ID', value: 'id' },
                { text: 'Año', value: 'anio' },
                { text: 'Materia', value: 'nombre_materia' },
                { text: 'Estado', value: 'estado' },
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
            campania: this.initForm(),
            error: {},
            //Modal Editar
            open_editar: false,
            campania_editar: this.initForm(),
            error_editar: {},
            materias: [],
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
                descripcion: '',
                ruc: '',
            }
        },
        listar(n=this.table.current_page){
            axios.get(url_base+'/campania?page='+n+'&search='+this.search)
            .then(response => {
                this.table = response.data;
            })
        },
        guardar(){
            axios.post(url_base+'/campania',this.campania)
            .then(response => {
                var respuesta=response.data;
                switch (respuesta.status) {
                    case 'OK':
                        swal("campania Creado", { 
                            icon: "success", 
                            timer: 2000, 
                            buttons: false
                        });
                        this.campania=this.initForm();
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
            axios.get(url_base+'/campania/'+id)
            .then(response => {
                this.open_editar=true;
                this.campania_editar = response.data;
            })
        },
        actualizar(){
            axios.post(url_base+`/campania/${this.campania_editar.id}?_method=PUT`,this.campania_editar)
            .then(response => {
                var respuesta=response.data;
                switch (respuesta.status) {
                    case 'OK':
                        swal("campania Actualizado", { 
                            icon: "success", 
                            timer: 2000, 
                            buttons: false
                        });
                        this.campania_editar=this.initForm();
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