<template>
    <v-container fluid>
        <v-card outlined>
            <v-card-title>Lista de zpls</v-card-title>
            <v-card-text>
                <v-btn @click="open_nuevo=true" outlined color="info">Nuevo zpl</v-btn>
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
            <v-dialog v-model="open_nuevo" persistent max-width="400">
                <v-card>
                    <v-card-title class="headline">Nuevo zpl</v-card-title>
                    <v-card-text>
                        <v-row>
                            <v-col cols="12">
                                <v-text-field 
                                    label="Nombre" 
                                    v-model="zpl.nombre_zpl"
                                    :error-messages="error.nombre_zpl"
                                ></v-text-field>
                            </v-col>
                            <v-col cols="12">
                                <v-textarea
                                outlined
                                label="Contenido"
                                v-model="zpl.contenido"
                                ></v-textarea>
                            </v-col>
                            <v-col cols="12">
                                <v-select
                                    v-model="zpl.tipo"
                                    label="Tipo de Label:"
                                    :items="tipos"
                                    :item-text="tipo => `${tipo.descripcion}`"
                                    item-value="tipo">
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
                    <v-card-title class="headline">Editar zpl</v-card-title>
                    <v-card-text>
                        <v-row>
                            <v-col cols="12">
                                <v-text-field 
                                    label="Nombre" 
                                    v-model="zpl_editar.nombre_zpl"
                                    :error-messages="error.nombre_zpl"
                                ></v-text-field>
                            </v-col>
                            <v-col cols="12">
                                <v-textarea
                                outlined
                                label="Contenido"
                                v-model="zpl_editar.contenido"
                                ></v-textarea>
                            </v-col>
                            <v-col cols="12">
                                <v-select
                                    v-model="zpl_editar.tipo"
                                    label="Tipo de Label:"
                                    :items="tipos"
                                    :item-text="tipo => `${tipo.descripcion}`"
                                    item-value="tipo">
                                </v-select>
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
            header:[
                { text: 'Nombre', value: 'nombre_zpl' },
                { text: 'Tipo', value: 'tipo' },
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
            zpl: this.initForm(),
            error: {},
            //Modal Editar
            open_editar: false,
            zpl_editar: this.initForm(),
            error_editar: {},
            tipos: [
                {'tipo' : 'ACO' , "descripcion" : "Acopio"},
                {'tipo' : 'TRA' , "descripcion" : "Trazabilidad"},
                {'tipo' : 'PAL' , "descripcion" : "Paletizado"},
            ],
        }
    },
    mounted() {
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
            axios.get(url_base+'/zpl?page='+n+'&search='+this.search)
            .then(response => {
                this.table = response.data;
            })
        },
        guardar(){
            axios.post(url_base+'/zpl',this.zpl)
            .then(response => {
                var respuesta=response.data;
                switch (respuesta.status) {
                    case 'OK':
                        swal("zpl Creado", { 
                            icon: "success", 
                            timer: 2000, 
                            buttons: false
                        });
                        this.zpl=this.initForm();
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
            axios.get(url_base+'/zpl/'+id)
            .then(response => {
                this.open_editar=true;
                this.zpl_editar = response.data;
            })
        },
        actualizar(){
            axios.post(url_base+`/zpl/${this.zpl_editar.id}?_method=PUT`,this.zpl_editar)
            .then(response => {
                var respuesta=response.data;
                switch (respuesta.status) {
                    case 'OK':
                        swal("zpl Actualizado", { 
                            icon: "success", 
                            timer: 2000, 
                            buttons: false
                        });
                        this.zpl_editar=this.initForm();
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