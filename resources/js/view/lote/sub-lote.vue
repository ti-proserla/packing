<template>
    <v-container fluid>
        <v-dialog v-model="open_nuevo" persistent max-width="350">
            <v-card>
                <v-card-title class="headline">Nuevo Sub Lote</v-card-title>
                <v-card-text>
                    <v-row>
                        <v-col cols=4>
                            <v-text-field 
                                label="Viaje:" 
                                type="number"
                                v-model="sub_lote.viaje"
                                :outlined="true"
                                hide-details="auto"
                                dense
                                clearable
                            ></v-text-field>
                        </v-col>
                        <v-col cols=8>
                            <v-text-field 
                                label="Guia:" 
                                v-model="sub_lote.guia"
                                :outlined="true"
                                hide-details="auto"
                                dense
                                clearable
                            ></v-text-field>
                        </v-col>
                        <v-col cols=12>
                            <v-select
                                :outlined="true"
                                dense
                                v-model="sub_lote.transportista_id"
                                label="Transportista"
                                :items="transportistas"
                                item-text="nombre_transportista"
                                item-value="id"
                                hide-details="auto"
                                ></v-select>
                        </v-col>
                        <v-col cols=12>
                            <v-text-field 
                                label="Peso Guia (Kg):" 
                                v-model="sub_lote.peso_guia"
                                :outlined="true"
                                hide-details="auto"
                                dense
                                type="number"
                                clearable
                            ></v-text-field>
                        </v-col>
                        <v-col cols=12 sm=6>
                            <v-select
                                outlined
                                dense
                                v-model="sub_lote.materia_id"
                                label="Materia:"
                                :items="materias"
                                item-text="nombre_materia"
                                item-value="id"
                                hide-details="auto"
                                >
                                </v-select>
                        </v-col>
                        <v-col cols=12 sm=6>
                            <v-select
                                outlined
                                dense
                                v-model="sub_lote.variedad_id"
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
                                v-model="sub_lote.tipo_id"
                                label="Tipo:"
                                :items="tipos"
                                item-text="nombre_tipo"
                                item-value="id"
                                hide-details="auto"
                                >
                                </v-select>
                        </v-col>
                        <v-col cols=12 sm=12>
                            <v-text-field 
                                label="Fecha Recepcion:" 
                                v-model="sub_lote.fecha_recepcion"
                                outlined
                                dense
                                clearable
                                type="datetime-local"
                                hide-details="auto"
                            ></v-text-field>
                        </v-col>
                        
                    </v-row>
                    <div class="text-right mt-3">
                        <v-btn 
                            outlined 
                            color="secondary" 
                            @click="open_nuevo=false"
                            >Cancelar</v-btn>
                        <v-btn color="primary" @click="guardarSubLote()">
                            Guardar
                        </v-btn>
                    </div>
                </v-card-text>
            </v-card>               
        </v-dialog>
        <v-card>
            <v-card-text>
                <v-row>
                    <v-col cols="12" sm=6>
                        Registro de Palets - Lote: {{lote.codigo}}
                    </v-col>
                    <v-col cols=12 sm=6 class="text-right">
                        <v-btn v-if="lote.estado=='Registrado'" color="orange" @click="finalizar">FINALIZAR</v-btn>
                    </v-col>
                </v-row>
            </v-card-text>
        </v-card>
        <v-row>
            <v-col cols="12">
                <v-card>
                    <v-card-text>
                        <v-row>
                            <v-col sm=7 cols=12>
                                <h4>Lista de Sub Lotes</h4>             
                                <v-btn @click="open_nuevo=true" 
                                        outlined 
                                        color="info">
                                        Nuevo Sub Lote
                                </v-btn>
                                <v-radio-group v-model="seleccionado_sub_lote">
                                    <v-card 
                                        outlined 
                                        class="mb-3" 
                                        v-for="(sub,index) in sub_lotes" 
                                        :key="index" 
                                        @click="seleccionar(sub.id)">
                                            <v-card-text>
                                                <v-row>
                                                    <v-col cols="1"  class="pb-0 pt-0">
                                                        <v-radio :value="sub.id"></v-radio>
                                                    </v-col>
                                                    <v-col cols="11" class="pb-0 pt-0">
                                                        <v-row>
                                                            <v-col class="pb-0 pt-0" cols="2"><b>Viaje:</b> {{ sub.viaje }}</v-col>
                                                            <v-col class="pb-0 pt-0" cols="3"><b>Guia:</b> {{ sub.guia }}</v-col>
                                                            <v-col class="pb-0 pt-0" cols="5">{{ sub.nombre_materia }} / {{ sub.nombre_variedad }} / {{ sub.nombre_tipo }}</v-col>
                                                            <v-col class="pb-0 pt-0" cols="2">
                                                                <v-btn 
                                                                    color="primary"
                                                                    elevation="2"
                                                                    small>
                                                                    E
                                                                </v-btn>
                                                                <v-btn icon
                                                                    outlined
                                                                    color="red">
                                                                    X
                                                                </v-btn>
                                                            </v-col>
                                                        </v-row>
                                                    </v-col>
                                                </v-row>
                                            </v-card-text>
                                    </v-card>
                                </v-radio-group>
                            </v-col>
                            <v-col sm=5 cols=12>
                                Registro de Palets
                                <v-row>
                                    <v-col cols="12" sm="6">
                                        <v-text-field 
                                            label="N° de Jabas:" 
                                            v-model="num_jabas"
                                            outlined
                                            dense
                                            clearable
                                            hide-details="auto"
                                            type="number"
                                            :error-messages="palets_error.num_jabas"
                                        ></v-text-field>
                                    </v-col>
                                    <v-col cols="12" sm="6">
                                        <v-text-field 
                                            label="Peso Total:" 
                                            v-model="peso"
                                            outlined
                                            dense
                                            clearable
                                            type="number"
                                            hide-details="auto"
                                            :error-messages="palets_error.peso"
                                        ></v-text-field>
                                    </v-col>
                                    <v-col cols="12" sm="6">
                                        <v-text-field 
                                            label="Peso Palet:" 
                                            v-model="peso_palet"
                                            outlined
                                            dense
                                            clearable
                                            type="number"
                                            hide-details="auto"
                                            :error-messages="palets_error.peso_palet"
                                        ></v-text-field>
                                    </v-col>
                                    <v-col cols="12" sm="6">
                                        <v-btn color="primary" @click="add()">Agregar</v-btn>
                                    </v-col>
                                </v-row>
                                <div class="text-center" v-if="seleccionado_sub_lote!=null">
                                    <v-simple-table dense>
                                        <template v-slot:default>
                                            <thead>
                                                <tr>
                                                    <th>N°</th>
                                                    <th>N° Jabas</th>
                                                    <th>Peso Total</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr v-for="(palet,index) in palets_entrada.slice().reverse()">
                                                    <td>{{ palets_entrada.length - index}}</td>
                                                    <td>{{ palet.num_jabas }}</td>
                                                    <td>{{ palet.peso }}</td>
                                                </tr>
                                                <tr v-if="seleccionado_sub_lote==null">
                                                    <td colspan="3"> Seleccione un Sub lote </td>
                                                </tr>
                                            </tbody>
                                        </template>
                                    </v-simple-table>
                                </div>
                            </v-col>
                        </v-row>
                    </v-card-text>
                </v-card>
            </v-col>
        </v-row>
    </v-container>
</template>
<style>
    .card-no-select{
        background-color: #eee;
    }
    @font-face {
        font-family: "digital";
        src: url("/font/digital_display_tfb.ttf");
    }
    .digital{
        border: 1px solid #111;
        padding: 0.5rem;
        font-size: 2rem;
        text-align: right;
        font-family: 'digital';
    }
</style>
<script>
import { mapState,mapMutations } from 'vuex'

export default {
    data() {
        return {
            //modal
            open_nuevo:false,
            //operaciones
            num_jabas: 0,
            peso: 0,
            peso_palet: 20.00,
            lote: {},
            sub_lote: this.init(),
            sub_lote_error:{},
            //listas
            transportistas:[],
            sub_lotes: [],
            palets: [],
            palets_entrada: [],
            palets_error:{},
            materias: [],
            variedades: [],
            tipos: [],
            //selectores
            seleccionado_sub_lote: null
        }
    },
    // computed: {
    //     materias()
    // },
    watch: {
        sub_lote: function (newQuestion, oldQuestion) {
            for (let i = 0; i < this.materias.length; i++) {
                const materia = this.materias[i];
                if (materia.id==this.sub_lote.materia_id) {
                    this.variedades = materia.variedad;
                    this.tipos= materia.tipo;
                }
            }
        }
    },
    mounted() {
        axios.get(url_base+`/lote_ingreso/${this.$route.params.id}`)
        .then(response => {
            this.lote=response.data
        });
        this.listarTransportistas();
        this.listarSublote();
        this.listarMaterias();
        // this.listarVariedades();
    },
    methods: {
        init(){
            return {
                lote_id: this.$route.params.id,
                viaje: 1,
                fecha_recepcion: moment().format('YYYY-MM-DDTHH:mm')
            };
        },
        listarMaterias(){
            axios.get(url_base+'/materia/detallado')
            .then(response => {
                this.materias=response.data
            })
        }, 
        listarVariedades(){
            axios.get(url_base+'/variedad?all')
            .then(response => {
                this.variedades=response.data
            })
        },
        listarSublote(){
            axios.get(url_base+`/lote_ingreso/${this.$route.params.id}/sub_lote`)
            .then(response => {
                this.sub_lotes=response.data
            });
        },
        listarTransportistas(){
            axios.get(url_base+'/transportista')
            .then(response => {
                this.transportistas=response.data
            });
        },
        guardarSubLote(){
            axios.post(url_base+'/sub_lote',this.sub_lote)
            .then(response => {
                this.listarSublote();
                this.open_nuevo=false;
                // this.sub_lote=this.init()
            });
        },
        seleccionar(id){
            this.seleccionado_sub_lote=id;
            this.listarPaletEntrada();
        },
        listarPaletEntrada(){
            axios.get(url_base+`/sub_lote/${this.seleccionado_sub_lote}/palet_entrada`)
            .then(response => {
                this.palets_entrada=response.data
            });
        },
        add(){
            axios.post(url_base+`/sub_lote/${this.seleccionado_sub_lote}/palet_entrada`,{
                num_jabas: this.num_jabas,
                peso: this.peso,
                peso_palet: this.peso_palet
            })
            .then(response => {
                var res=response.data;
                switch (res.status) {
                    case 'OK':
                        this.peso=0;
                        this.num_jabas=0;
                        this.palets_error={};
                        this.listarPaletEntrada();
                        break;

                    case 'VALIDATION':
                        this.palets_error=res.data;
                        break;
                
                    default:
                        break;
                }
            });
        },
        finalizar(){
            axios.post(url_base+`/lote_ingreso/${ this.$route.params.id }?_method=patch`,{
                estado: 'Lanzado'
            }).then(response => {
                var res=response.data;
                switch (res.status) {
                    case 'OK':
                        this.$router.push('/acopio/lote');
                        break;
                
                    default:
                        break;
                }
            });
        }    
    },
}
</script>