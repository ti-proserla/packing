<template>
    <v-container fluid>
        <v-card outlined>
            <v-card-title>Lista de Etiqueta Caja</v-card-title>
            <v-card-text>
                <v-btn @click="open_nuevo=true" outlined color="info">Nueva Etiqueta Caja</v-btn>
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
            <v-dialog v-model="open_nuevo" persistent max-width="650">
                <v-card>
                    <v-card-title class="headline">Nueva Etiqueta Caja</v-card-title>
                    <v-card-text>
                        <v-row>
                            <v-col 
                                cols="12"
                                lg="12">
                                <v-select
                                    label="Lote:"
                                    hide-details="auto"
                                    v-model="etiqueta_caja.lote_id"
                                    :error-messages="error.lote_id"
                                    :items="lotes"
                                    :item-text="item => `${item.codigo} - ${item.descripcion} - ${item.nombre_materia} - ${item.nombre_variedad}`"
                                    item-value="id">
                                </v-select>
                            </v-col>
                            <v-col 
                                cols="12"
                                lg="4">
                                <v-select
                                    label="Calibre:"
                                    hide-details="auto"
                                    v-model="etiqueta_caja.calibre_id"
                                    :error-messages="error.calibre_id"
                                    :items="calibresMateria"
                                    item-text="nombre_calibre"
                                    item-value="id">
                                </v-select>
                            </v-col>
                            <v-col 
                                cols="12"
                                lg="4">
                                <v-select
                                    label="Categoria:"
                                    hide-details="auto"
                                    v-model="etiqueta_caja.categoria_id"
                                    :error-messages="error.categoria_id"
                                    :items="categorias"
                                    item-text="nombre_categoria"
                                    item-value="id">
                                </v-select>
                            </v-col>
                            <v-col 
                                cols="12"
                                lg="4">
                                <v-select
                                    label="Presentacion:"
                                    hide-details="auto"
                                    v-model="etiqueta_caja.presentacion_id"
                                    :error-messages="error.presentacion_id"
                                    :items="presentaciones"
                                    item-text="nombre_presentacion"
                                    item-value="id">
                                </v-select>
                            </v-col>
                        </v-row>
                        <v-text-field 
                            hide-details="auto"
                            label="cod_cartilla" 
                            v-model="etiqueta_caja.cod_cartilla"
                            :error-messages="error.cod_cartilla"
                        ></v-text-field>
                        <v-text-field 
                            hide-details="auto"
                            label="Nombre" 
                            v-model="etiqueta_caja.nombre_etiqueta_caja"
                            :error-messages="error.nombre_etiqueta_caja"
                        ></v-text-field>
                        
                        <div>
                            
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
                    <v-card-title class="headline">Editar etiqueta_caja</v-card-title>
                    <v-card-text>
                        <v-text-field 
                            hide-details="auto"
                            label="cod_cartilla" 
                            v-model="etiqueta_caja_editar.cod_cartilla"
                            :error-messages="error_editar.cod_cartilla"
                        ></v-text-field>
                        <v-text-field 
                            required 
                            hide-details="auto"
                            label="Nombre" 
                            v-model="etiqueta_caja_editar.nombre_etiqueta_caja"
                            :error-messages="error_editar.nombre_etiqueta_caja"
                        ></v-text-field>
                        <v-select
                                label="Materia:"
                                hide-details="auto"
                                v-model="etiqueta_caja_editar.materia_id"
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
            lotes: [],
            calibres: [],
            categorias: [],
            presentaciones: [],
            materias: [],
            header:[
                { text: 'Descripción', value: 'nombre_etiqueta_caja' },
                { text: 'Materia', value: 'nombre_materia' },
                { text: 'Código Cartilla', value: 'cod_cartilla' },
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
            etiqueta_caja: this.initForm(),
            error: {},
            //Modal Editar
            open_editar: false,
            etiqueta_caja_editar: this.initForm(),
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
        this.listarLotes();
        this.listarCalibres();
        this.listarCategorias();
        this.listarPresentaciones();
    },
    computed:{
        getMateriaId(){
            var materia_id=0;
            for (let i = 0; i < this.lotes.length; i++) {
                const lote = this.lotes[i];
                if (lote.id==this.etiqueta_caja.lote_id) {
                    materia_id=lote.materia_id;
                }
            }
            return materia_id;
        },
        calibresMateria(){
            var calibres=[];
            for (let i = 0; i < this.calibres.length; i++) {
                const calibre = this.calibres[i];
                if (calibre.materia_id=this.getMateriaId) {
                    calibres.push(calibre);
                }
            }
            return calibres;
        }
    },
    methods: {
        initForm(){
            return {
                cod_cartilla: '',
                nombre_etiqueta_caja: '',
                materia_id: '',
            }
        },
        listarLotes(){
            axios.get(url_base+`/lote_ingreso`)
            .then(response => {
                this.lotes=response.data
            });
        },
        listarCalibres(){
            axios.get(url_base+`/calibre?all`)
            .then(response => {
                this.calibres=response.data
            });
        },
        listarPresentaciones(){
            axios.get(url_base+`/presentacion?all`)
            .then(response => {
                this.presentaciones=response.data
            });
        },
        listarCategorias(){
            axios.get(url_base+`/categoria?all`)
            .then(response => {
                this.categorias=response.data
            });
        },
        listar(n=this.table.current_page){
            axios.get(url_base+'/etiqueta-caja?page='+n+'&search='+this.search)
            .then(response => {
                this.table = response.data;
            })
        },
        guardar(){
            axios.post(url_base+'/etiqueta_caja',this.etiqueta_caja)
            .then(response => {
                var respuesta=response.data;
                switch (respuesta.status) {
                    case 'OK':
                        swal("etiqueta_caja Creado", { 
                            icon: "success", 
                            timer: 2000, 
                            buttons: false
                        });
                        this.etiqueta_caja=this.initForm();
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
            axios.get(url_base+'/etiqueta_caja/'+id)
            .then(response => {
                this.open_editar=true;
                this.etiqueta_caja_editar = response.data;
            })
        },
        actualizar(){
            axios.post(url_base+`/etiqueta_caja/${this.etiqueta_caja_editar.id}?_method=PUT`,this.etiqueta_caja_editar)
            .then(response => {
                var respuesta=response.data;
                switch (respuesta.status) {
                    case 'OK':
                        swal("etiqueta_caja Actualizado", { 
                            icon: "success", 
                            timer: 2000, 
                            buttons: false
                        });
                        this.etiqueta_caja_editar=this.initForm();
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