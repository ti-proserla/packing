<template>
    <v-container fluid>
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
                            <v-col sm=4 cols=12>
                                Nuevo Sub Lote
                                <v-row>
                                    <v-col cols=12>
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
                                </v-row>
                                <div class="text-center">
                                    <v-btn color="primary" @click="guardarSubLote()">
                                        Guardar
                                    </v-btn>
                                </div>
                            </v-col>
                            <v-col sm=4 cols=12>
                                Lista de Sub Lotes
                                <v-radio-group v-model="seleccionado_sub_lote">
                                    <v-card 
                                        outlined 
                                        class="mb-3" 
                                        v-for="(sub,index) in sub_lotes" 
                                        :key="index" 
                                        @click="seleccionar(sub.id)">
                                            <v-card-text>
                                                <v-row>
                                                    <v-col cols="2">
                                                        <v-radio :value="sub.id"></v-radio>
                                                    </v-col>
                                                    <v-col cols="10">
                                                        <h6>Transportista: {{ sub.transportista.nombre_transportista  }}</h6>
                                                        <p class="mb-0">Guia: {{ sub.guia }}</p>
                                                    </v-col>
                                                </v-row>
                                            </v-card-text>
                                    </v-card>
                                </v-radio-group>
                            </v-col>
                            <v-col sm=4 cols=12>
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
                                </v-row>
                                <div class="text-center" v-if="seleccionado_sub_lote!=null">
                                    <v-btn color="primary" @click="add()">Agregar</v-btn>
                                    <v-simple-table>
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
            //operaciones
            num_jabas: 0,
            peso: 0,
            lote: {},
            sub_lote: this.init(),
            sub_lote_error:{},
            //listas
            transportistas:[],
            sub_lotes: [],
            palets: [],
            palets_entrada: [],
            palets_error:{},
            //selectores
            seleccionado_sub_lote: null
        }
    },
    computed: {
        // ...mapState(['peso']),
    },
    mounted() {
        axios.get(url_base+`/lote_ingreso/${this.$route.params.id}`)
        .then(response => {
            this.lote=response.data
        });
        this.listarTransportistas();
        this.listarSublote();
    },
    methods: {
        init(){
            return {
                lote_id: this.$route.params.id
            };
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
                peso: this.peso
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