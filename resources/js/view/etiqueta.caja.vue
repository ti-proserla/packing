<template>
    <v-container fluid>
        <v-card outlined>
            <v-card-title>Lista de Etiqueta Caja</v-card-title>
            <v-card-text>
                <v-row>
                    
                    <v-col cols="12" sm=6 lg="3">
                        <v-text-field
                            @keydown="listar()"
                            label="Fecha Empaque:"
                            v-model="consulta.fecha_empaque"
                            type="date">
                        </v-text-field>
                    </v-col>
                    <v-col cols="12" sm=6 lg="3">
                        <v-btn 
                            color="primary"
                            @click="listar()">
                            Listar
                        </v-btn>
                    </v-col>
                    <v-col class="text-right" cols="12" sm=6 lg="6">
                        <v-btn  @click="open_nuevo=true" outlined color="info">Nueva Etiqueta Caja</v-btn>
                    </v-col>
                </v-row>
                <v-data-table
                    :sort-desc="false"
                    :disable-sort="false"
                    :headers="header"
                    :items="table.data"
                    :page.sync="table.current_page"
                    hide-default-footer
                    >
                    <template v-slot:item.ver="{ item }">
                        <v-btn 
                            text 
                            color="info" 
                            @click="buscar(item.id)">
                            <i class="fas fa-search"></i>
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
                                <v-text-field 
                                    label="Fecha Empaque:" 
                                    v-model="etiqueta_caja.fecha_empaque"
                                    clearable
                                    type="date"
                                    :error-messages="error.fecha_empaque"
                                ></v-text-field>
                            </v-col>
                            <v-col 
                                cols="12"
                                lg="4">
                                <v-select
                                    label="Calibre:"
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
                                    v-model="etiqueta_caja.presentacion_id"
                                    :error-messages="error.presentacion_id"
                                    :items="presentaciones"
                                    item-text="nombre_presentacion"
                                    item-value="id">
                                </v-select>
                            </v-col>
                            <v-col 
                                cols="12"
                                lg="4">
                                <v-select
                                    label="Marca Caja:"
                                    v-model="etiqueta_caja.marca_caja_id"
                                    :error-messages="error.marca_caja_id"
                                    :items="marca_cajas"
                                    item-text="nombre_marca_caja"
                                    item-value="id">
                                </v-select>
                            </v-col>
                            <v-col 
                                cols="12"
                                lg="4">
                                <v-select
                                    label="Tipo Empaque:"
                                    v-model="etiqueta_caja.tipo_empaque_id"
                                    :error-messages="error.tipo_empaque_id"
                                    :items="tipo_empaques"
                                    item-text="nombre_tipo_empaque"
                                    item-value="id">
                                </v-select>
                            </v-col>
                            <v-col 
                                cols="12"
                                lg="4">
                                <v-select
                                    label="Marca Empaque:"
                                    v-model="etiqueta_caja.marca_empaque_id"
                                    :error-messages="error.marca_empaque_id"
                                    :items="marca_empaques"
                                    item-text="nombre_marca_empaque"
                                    item-value="id">
                                </v-select>
                            </v-col>
                            <v-col 
                                cols="12"
                                lg="4">
                                <v-select
                                    label="PLU:"
                                    v-model="etiqueta_caja.plu_id"
                                    :error-messages="error.plu_id"
                                    :items="plus"
                                    item-text="nombre_plu"
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
            <v-dialog v-model="open_ver" persistent max-width="550" scrollable>
                <v-card>
                    <v-card-title class="headline">
                        IMPRIMIR ETIQUETA

                        <v-row>
                            <v-col 
                                cols="12"
                                sm="4">
                                <v-text-field 
                                    label="Cantidad" 
                                    type="number"
                                    v-model="print_count"
                                ></v-text-field>
                            </v-col>
                            <v-col
                                cols="12"
                                sm=6>
                                <v-select
                                    label="Diseño Etiqueta:"
                                    v-model="zpl_id"
                                    :items="zpls"
                                    item-text="nombre_zpl"
                                    item-value="id">
                                </v-select>
                            </v-col>
                            <v-col cols="12" sm=2>
                                 <v-btn @click="getZpl" color="primary">
                                     Ver
                                    </v-btn>  
                            </v-col>
                            <v-col cols="12">
                            </v-col>
                        </v-row>
                    </v-card-title>
                    <v-card-text style="height: 400px;">
                                <v-img :src="preview"></v-img>
                        
                    </v-card-text>
                    <v-card-actions>
                        <div class="text-right mt-3">
                            <v-btn 
                                outlined 
                                color="secondary" 
                                @click="open_ver=false"
                                >Cancelar</v-btn>
                            <v-btn 
                                outlined 
                                color="primary" 
                                @click="print()"
                                >IMPRIMIR</v-btn>
                        </div>
                    </v-card-actions>
                </v-card>               
            </v-dialog>
        </v-card>
    </v-container>
</template>
<script>
export default {
    data() {
        return {
            consulta:{
                fecha_empaque: moment().format('YYYY-MM-DD')
            },
            print_count: 1,
            zpl: '',
            printer_select: {},
            lotes: [],
            zpls: [],
            calibres: [],
            categorias: [],
            presentaciones: [],
            marca_cajas: [],
            tipo_empaques: [],
            marca_empaques: [],
            plus: [],
            materias: [],
            header:[
                { text: 'Cod. Caja', value: 'codigo_caja' },
                { text: 'Registrado', value: 'created_at' },
                { text: 'Fecha Empaque', value: 'fecha_empaque' },
                { text: 'Lote', value: 'codigo' },
                { text: 'Calibre', value: 'nombre_calibre' },
                { text: 'Categoria', value: 'nombre_categoria' },
                { text: 'Materia', value: 'nombre_materia' },
                { text: 'Variedad', value: 'nombre_variedad' },
                { text: 'presentacion', value: 'nombre_presentacion' },
                { text: 'Marca Caja', value: 'nombre_marca_caja' },
                { text: 'Tipo Empaque', value: 'nombre_tipo_empaque' },
                { text: 'Marca Empaque', value: 'nombre_marca_empaque' },
                { text: 'PLU', value: 'nombre_plu' },
                { text: 'Estado', value: 'estado' },
                { text: 'Ver', value: 'ver' },
            ],
            table: {
                current_page: 1,
                last_page: 1,
                data: []
            },
            url_label: '',
            search: '',
            zpl_id: '',
            etiqueta_caja_id: '',
            //Modal Nuevo
            open_nuevo: false,
            etiqueta_caja: this.initForm(),
            error: {},
            //Modal Editar
            open_ver: false,
            etiqueta_caja_editar: this.initForm(),
            error_editar: {},
            preview: ""
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
        this.listarPLUs();
        this.listarMarcaCajas();
        this.listarCategorias();
        this.listarPresentaciones();
        this.listarTipoEmpaque();
        this.listarMarcaEmpaque();
        this.listarZpls();
        var t = this;
        BrowserPrint.getDefaultDevice("printer", function(device){
            t.printer_select=device;
        });
        
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
        print(){
            var zplPrint='';
            for (let i = 0; i < this.print_count; i++) {
                zplPrint+=this.zpl;
            }
            this.printer_select.send(zplPrint, undefined, function(errorMessage){
                alert("Error: " + errorMessage);	
            });
        },
        listarLotes(){
            axios.get(url_base+`/lote_ingreso?estado=Pendiente,Lanzado`)
            .then(response => {
                this.lotes=response.data
            });
        },
        listarZpls(){
            axios.get(url_base+`/zpl?all`)
            .then(response => {
                this.zpls=response.data
            });
        },
        listarCalibres(){
            axios.get(url_base+`/calibre?all`)
            .then(response => {
                this.calibres=response.data
            });
        },
        listarMarcaCajas(){
            axios.get(url_base+`/marca-caja?all`)
            .then(response => {
                this.marca_cajas=response.data
            });
        },
        listarPresentaciones(){
            axios.get(url_base+`/presentacion?all`)
            .then(response => {
                this.presentaciones=response.data
            });
        },
        listarPLUs(){
            axios.get(url_base+`/plu?all`)
            .then(response => {
                this.plus=response.data
            });
        },
        listarCategorias(){
            axios.get(url_base+`/categoria?all`)
            .then(response => {
                this.categorias=response.data
            });
        },
        listarMarcaEmpaque(){
            axios.get(url_base+`/marca-empaque?all`)
            .then(response => {
                this.marca_empaques=response.data
            });
        },
        listarTipoEmpaque(){
            axios.get(url_base+`/tipo-empaque?all`)
            .then(response => {
                this.tipo_empaques=response.data
            });
        },
        listar(n=this.table.current_page){
            axios.get(url_base+'/etiqueta-caja?page='+n+'&search='+this.search,{
                params: this.consulta
            })
            .then(response => {
                this.table = response.data;
            })
        },
        getZpl(id){
            
            axios.get(url_base+'/print/muestra_etiqueta_caja?zpl_id='+this.zpl_id+'&etiqueta_caja_id='+this.etiqueta_caja_id)
            .then(response => {

                this.url_label="http://api.labelary.com/v1/printers/8dpmm/labels/4x3/0/"+response.data;
                this.zpl=response.data;

                axios.post(url_base+'/zpl/preview',{
                    zpl: this.zpl
                },
                { responseType: 'arraybuffer' }
                )
                .then(response => {
                    this.preview=`data:${response.headers['content-type']};base64,${btoa(String.fromCharCode(...new Uint8Array(response.data)))}`;
                });
                    
            })
        },
        guardar(){
            axios.post(url_base+'/etiqueta-caja',this.etiqueta_caja)
            .then(response => {
                var respuesta=response.data;
                switch (respuesta.status) {
                    case 'OK':
                        swal("Etiqueta Caja Creada", { 
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
            this.etiqueta_caja_id=id;
            this.open_ver=true;
            // axios.get(url_base+'/etiqueta_caja/'+id)
            // .then(response => {
            //     this.etiqueta_caja_editar = response.data;
            // })
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
                        this.open_ver=false;
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