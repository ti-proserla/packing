<template>
    <v-container fluid>
        <v-card outlined>
            <v-card-title>Lista de Descartes</v-card-title>
            <v-card-text>
                <v-btn @click="open_nuevo=true" outlined color="info">Nueva descarte</v-btn>
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
                    <v-card-title class="headline">Nueva descarte</v-card-title>
                    <v-card-text>
                        <v-row>
                            <v-col cols="12">
                                <v-select
                                    label="Lote:"
                                    v-model="descarte.lote_id"
                                    :error-messages="descarte_error.lote_id"
                                    :items="lotes"
                                    item-text="codigo"
                                    item-value="id">
                                </v-select>
                            </v-col>
                            <v-col cols="12">
                                <v-text-field 
                                    label="Descarte Racimos (Kg):" 
                                    v-model="descarte.descarte_racimos"
                                    type="number"
                                    clearable
                                    :error-messages="descarte_error.descarte_racimos"
                                ></v-text-field>
                            </v-col>
                            <v-col cols="12">
                                <v-text-field 
                                    label="Descarte Granos (Kg):" 
                                    v-model="descarte.descarte_granos"
                                    type="number"
                                    clearable
                                    :error-messages="descarte_error.descarte_granos"
                                ></v-text-field>
                            </v-col>
                            <v-col cols="12">
                                <v-text-field 
                                    label="Cantidad de Jabas Descarte:" 
                                    v-model="descarte.cantidad_jabas_descarte"
                                    type="number"
                                    clearable
                                    :error-messages="descarte_error.cantidad_jabas_descarte"
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
                    <v-card-title class="headline">Editar descarte</v-card-title>
                    <v-card-text>
                        <v-text-field 
                            label="cod_cartilla" 
                            v-model="descarte_editar.cod_cartilla"
                            :error-messages="error_editar.cod_cartilla"
                        ></v-text-field>
                        <v-text-field 
                            required 
                            label="Nombre" 
                            v-model="descarte_editar.nombre_descarte"
                            :error-messages="error_editar.nombre_descarte"
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
            lotes: [],
            header:[
                { text: 'Lote', value: 'codigo' },
                { text: 'Descarte Racimos (Kg)', value: 'descarte_racimos' },
                { text: 'Descarte Granos (Kg)', value: 'descarte_granos' },
                { text: 'Cantidad Jabas', value: 'cantidad_jabas_descarte' },
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
            descarte: this.initForm(),
            error: {},
            //Modal Editar
            open_editar: false,
            descarte_editar: this.initForm(),
            error_editar: {},
            descarte_error: {}
        }
    },
    mounted() {
        axios.get(url_base+'/lote_ingreso?descarte')
        .then(response => {
            this.lotes = response.data;
            this.lotes.push({
                id: '',
                codigo: 'Seleccione Lote'
            })
        })
        this.listar(1);
    },
    methods: {
        initForm(){
            return {
                cod_cartilla: '',
                nombre_descarte: '',
                materia_id: '',
            }
        },
        listar(n=this.table.current_page){
            axios.get(url_base+'/descarte?page='+n+'&search='+this.search)
            .then(response => {
                this.table = response.data;
            })
        },
        guardar(){
            axios.post(url_base+'/descarte',this.descarte)
            .then(response => {
                var respuesta=response.data;
                switch (respuesta.status) {
                    case 'OK':
                        swal("descarte Creado", { 
                            icon: "success", 
                            timer: 2000, 
                            buttons: false
                        });
                        this.descarte=this.initForm();
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
            axios.get(url_base+'/descarte/'+id)
            .then(response => {
                this.open_editar=true;
                this.descarte_editar = response.data;
            })
        },
        actualizar(){
            axios.post(url_base+`/descarte/${this.descarte_editar.id}?_method=PUT`,this.descarte_editar)
            .then(response => {
                var respuesta=response.data;
                switch (respuesta.status) {
                    case 'OK':
                        swal("descarte Actualizado", { 
                            icon: "success", 
                            timer: 2000, 
                            buttons: false
                        });
                        this.descarte_editar=this.initForm();
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