<template>
    <v-container fluid>
        <v-card outlined>
            <v-card-title>Lista de Impresoras</v-card-title>
            <v-card-text>
                <v-btn @click="open_nuevo=true" outlined color="info" class="mb-3">Nueva Impresora</v-btn>
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
                    <v-card-title class="headline">Nueva impresora</v-card-title>
                    <v-card-text>
                        <v-text-field 
                            required 
                            hide-details="auto"
                            label="IP" 
                            v-model="impresora.ip"
                            :error-messages="error.ip"
                        ></v-text-field>
                        <v-text-field 
                            required 
                            hide-details="auto"
                            label="Nombre" 
                            v-model="impresora.nombre"
                            :error-messages="error.nombre"
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
                    <v-card-title class="headline">Editar impresora</v-card-title>
                    <v-card-text>
                        <v-text-field 
                            required 
                            hide-details="auto"
                            label="Nombre" 
                            v-model="impresora_editar.nombre_impresora"
                            :error-messages="error_editar.nombre_impresora"
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
                { text: 'Nombre', value: 'nombre_impresora' },
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
            impresora: this.initForm(),
            error: {},
            //Modal Editar
            open_editar: false,
            impresora_editar: this.initForm(),
            error_editar: {},
        }
    },
    mounted() {
        this.listar(1);
    },
    methods: {
        initForm(){
            return {
                ip: '',
                nombre_impresora: ''
            }
        },
        listar(n=this.table.current_page){
            axios.get(url_base+'/impresora?page='+n+'&search='+this.search)
            .then(response => {
                this.table = response.data;
            })
        },
        guardar(){
            axios.post(url_base+'/impresora',this.impresora)
            .then(response => {
                var respuesta=response.data;
                switch (respuesta.status) {
                    case 'OK':
                        swal("impresora Creada", { 
                            icon: "success", 
                            timer: 2000, 
                            buttons: false
                        });
                        this.impresora=this.initForm();
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
            axios.get(url_base+'/impresora/'+id)
            .then(response => {
                this.open_editar=true;
                this.impresora_editar = response.data;
            })
        },
        actualizar(){
            axios.post(url_base+`/impresora/${this.impresora_editar.id}?_method=PUT`,this.impresora_editar)
            .then(response => {
                var respuesta=response.data;
                switch (respuesta.status) {
                    case 'OK':
                        swal("impresora Actualizada", { 
                            icon: "success", 
                            timer: 2000, 
                            buttons: false
                        });
                        this.impresora_editar=this.initForm();
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