<template>
    <v-container fluid>
        <v-card outlined>
            <v-card-title>Lista de productos</v-card-title>
            <v-card-text>
                <v-btn @click="open_nuevo=true" outlined color="info">Nuevo producto</v-btn>
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
                    <v-card-title class="headline">Nuevo producto</v-card-title>
                    <v-card-text>
                        <v-text-field 
                            required 
                            hide-details="auto"
                            label="Nombre:" 
                            v-model="producto.nombre_producto"
                            :error-messages="error.nombre_producto"
                        ></v-text-field>
                        <v-text-field 
                            required 
                            hide-details="auto"
                            label="Peso Neto:" 
                            v-model="producto.peso_neto"
                            :error-messages="error.peso_neto"
                        ></v-text-field>
                        <v-text-field 
                            required 
                            hide-details="auto"
                            label="Peso Pote:" 
                            v-model="producto.peso_pote"
                            :error-messages="error.peso_pote"
                        ></v-text-field>
                        <v-text-field 
                            required 
                            hide-details="auto"
                            label="Potes:"
                            type="number" 
                            v-model="producto.potes"
                            :error-messages="error.potes"
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
                    <v-card-title class="headline">Editar producto</v-card-title>
                    <v-card-text>
                        <v-text-field 
                            required 
                            hide-details="auto"
                            label="Nombre" 
                            v-model="producto_editar.nombre_producto"
                            :error-messages="error_editar.nombre_producto"
                        ></v-text-field>
                        <v-text-field 
                            required 
                            hide-details="auto"
                            label="Peso Neto:" 
                            v-model="producto_editar.peso_neto"
                            :error-messages="error_editar.peso_neto"
                        ></v-text-field>
                        <v-text-field 
                            required 
                            hide-details="auto"
                            label="Peso Pote:" 
                            v-model="producto_editar.peso_pote"
                            :error-messages="error_editar.peso_pote"
                        ></v-text-field>
                        <v-text-field 
                            required 
                            hide-details="auto"
                            label="Potes:"
                            type="number" 
                            v-model="producto_editar.potes"
                            :error-messages="error_editar.potes"
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
                { text: 'Descripción', value: 'nombre_producto' },
                { text: 'Peso Neto', value: 'peso_neto' },
                { text: 'Peso Pote', value: 'peso_pote' },
                { text: 'Potes', value: 'potes' },
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
            producto: this.initForm(),
            error: {},
            //Modal Editar
            open_editar: false,
            producto_editar: this.initForm(),
            error_editar: {},
        }
    },
    mounted() {
        this.listar(1);
    },
    methods: {
        initForm(){
            return {
                nombre_producto: '',
                peso_neto: '0.00',
                peso_pote: '0.00',
                potes: '0',
            }
        },
        listar(n=this.table.current_page){
            axios.get(url_base+'/producto?page='+n+'&search='+this.search)
            .then(response => {
                this.table = response.data;
            })
        },
        guardar(){
            axios.post(url_base+'/producto',this.producto)
            .then(response => {
                var respuesta=response.data;
                switch (respuesta.status) {
                    case 'OK':
                        swal("producto Creado", { 
                            icon: "success", 
                            timer: 2000, 
                            buttons: false
                        });
                        this.producto=this.initForm();
                        this.error={};
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
            axios.get(url_base+'/producto/'+id)
            .then(response => {
                this.open_editar=true;
                this.producto_editar = response.data;
            })
        },
        actualizar(){
            axios.post(url_base+`/producto/${this.producto_editar.id}?_method=PUT`,this.producto_editar)
            .then(response => {
                var respuesta=response.data;
                switch (respuesta.status) {
                    case 'OK':
                        swal("producto Actualizado", { 
                            icon: "success", 
                            timer: 2000, 
                            buttons: false
                        });
                        this.producto_editar=this.initForm();
                        this.error_editar={};
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