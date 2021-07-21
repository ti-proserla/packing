<template>
    <v-container fluid>
        <v-card>
            <v-card-title>Nuevo Lote de Ingreso</v-card-title>              
            <v-card-text>
                <v-form @submit.prevent="guardar()">
                    <v-row>
                        <v-col cols=12 sm=6>
                            <v-select
                                outlined
                                dense
                                v-model="lote.cliente_id"
                                label="Cliente:"
                                :items="clientes"
                                item-text="descripcion"
                                item-value="id"
                                hide-details="auto"
                                :error-messages="lote_error.cliente_id"
                                >
                                </v-select>
                        </v-col>
                        <v-col cols=12 sm=6>
                            <v-select
                                outlined
                                dense
                                v-model="lote.materia_id"
                                label="Materia:"
                                :items="materias"
                                item-text="nombre_materia"
                                item-value="id"
                                hide-details="auto"
                                :error-messages="lote_error.materia_id"
                                >
                                </v-select>
                        </v-col>
                        <v-col cols=12 sm=6>
                            <v-select
                                outlined
                                dense
                                v-model="lote.variedad_id"
                                label="Variedad:"
                                :items="variedades"
                                item-text="nombre_variedad"
                                item-value="id"
                                hide-details="auto"
                                >
                                </v-select>
                        </v-col>
                        <v-col cols=12 sm=6>
                            <v-select
                                    outlined
                                    dense
                                    v-model="lote.tipo_id"
                                    label="Tipo:"
                                    :items="tipos"
                                    item-text="nombre_tipo"
                                    item-value="id"
                                    hide-details="auto"
                                    >
                                    </v-select>
                        </v-col>
                        <v-col cols=12 sm=6>
                            <v-select
                                outlined
                                dense
                                v-model="lote.fundo_id"
                                label="Fundo:"
                                :items="fundos"
                                :item-text="item => item.nombre_fundo + ' -'  + item.lugar_produccion"
                                item-value="id"
                                hide-details="auto"
                                :error-messages="lote_error.fundo_id"
                                >
                                </v-select>
                        </v-col>
                        <v-col cols=12 sm=6>
                            <v-text-field 
                                label="Fecha Cosecha:" 
                                v-model="lote.fecha_cosecha"
                                outlined
                                dense
                                clearable
                                type="date"
                                hide-details="auto"
                                :error-messages="lote_error.fecha_cosecha"
                            ></v-text-field>
                        </v-col>
                        <v-col cols=8 sm=4>
                            <v-text-field 
                                label="Código Lote:" 
                                v-model="lote.codigo"
                                outlined
                                dense
                                clearable
                                hide-details="auto"
                                :error-messages="lote_error.codigo"
                            ></v-text-field>
                        </v-col>
                        <v-col cols="4" sm="2">
                            <v-btn 
                                color="red"
                                @click="generar_codigo"
                                block>
                                Generar
                            </v-btn>
                        </v-col>
                    </v-row>
                    <div class="text-center my-3">
                        <v-btn type="submit" color="success">
                            Guardar
                        </v-btn>
                    </div>
                </v-form>
            </v-card-text>
        </v-card>
    </v-container>
</template>
<script>
export default {
    data() {
        return {
            /**
             * Listas
             */
            clientes: [],
            materias: [],
            fundos: [],
            /**
             * Modificadores
             */
            lote: {
                codigo: "",
                cliente_id: "",
                materia_id: "",
                variedad_id: "",
                tipo_id: "",
                fundo_id: "",
                parcela_id: "",
                fecha_cosecha: moment().format('YYYY-MM-DD')
            },
            lote_error: {}
        }
    },
    mounted() {
        this.listarProductos();
        this.listarClientes();
        this.listarFundos();
    },
    computed: {
        variedades() {
            for (let i = 0; i < this.materias.length; i++) {
                const materia = this.materias[i];
                if (materia.id==this.lote.materia_id) {
                    return materia.variedad;
                }
            }
            return []
        },
        tipos() {
            for (let i = 0; i < this.materias.length; i++) {
                const materia = this.materias[i];
                if (materia.id==this.lote.materia_id) {
                    return materia.tipo;
                }
            }
            return []
        },
        parcelas() {
            for (let i = 0; i < this.fundos.length; i++) {
                const fundo = this.fundos[i];
                if (fundo.id==this.lote.fundo_id) {
                    return fundo.parcelas;
                }
            }
            return []
        }
    },
    methods: {
        listarProductos(){
            axios.get(url_base+'/materia/detallado')
            .then(response => {
                this.materias=response.data
            });
        },
        listarFundos(){
            axios.get(url_base+'/fundo/detallado')
            .then(response => {
                this.fundos=response.data
            });
        },
        listarClientes(){
            axios.get(url_base+'/cliente?all')
            .then(response => {
                this.clientes=response.data
            });
        },
        generar_codigo(){
            axios.get(url_base+'/lote_ingreso/generar_codigo',{
                params: this.lote 
            })
            .then(response => {
                this.lote.codigo=response.data.codigo;
            });
        },
        guardar(){
            var t=this;
            t.lote_error={};
            swal({ title: "¿Desea crear Lote?", buttons: ['Cancelar',"Crear"]})
            .then((res) => {
                if (res) {
                    /**
                     * Guardado
                     */
                    axios.post(url_base+'/lote_ingreso',t.lote)
                    .then(response => {
                        var respuesta=response.data;
                        switch (respuesta.status) {
                            case "VALIDATION":
                                t.lote_error=respuesta.data;
                                break;
                            case "OK":
                                swal("Lote Creado", { icon: "success", timer: 2000, buttons: false });
                                t.$router.push('/acopio/lote');
                                t.lote_error={};
                                break;
                            default:
                                t.lote_error={};
                                break;
                        }
                    });
                }
            });
        }
    },
}
</script>