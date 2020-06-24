<template>
    <div class="row">
        <div class="col-sm-6">
            <div class="card">
                <div class="card-head">
                    <h5 class="mb-0">Nuevo Sub Lote</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <label for="">Guia: </label>
                            <input type="text" class="form-control" v-model="sub_lote.guia">
                            <span>{{ sub_lote_error.guia }}</span>
                        </div>
                        <div class="col-md-12">
                            <label for="">Transportista:</label>
                            <select class="form-control" v-model="sub_lote.transportista_id">
                                <option value="">-Seleccionar Transportista-</option>
                                <option v-for="transportista in transportistas" :value="transportista.id">{{ transportista.nombre_transportista }}</option>
                            </select>
                        </div>
                        <div class="col-12 text-center">
                            <button @click="guardarSubLote()" class="btn btn-primary">Guardar</button>
                        </div>
                    </div>
                </div>
            </div>
            <br>
            <div class="card">
                <div class="card-head">
                    <h5 class="mb-0">Lista Sub Lote</h5>
                </div>
                <div class="card-body">
                    <div v-for="(sub,index) in sub_lotes" class="card" :class="(seleccionado_sub_lote==sub.id ? '' : 'card-no-select')" @click="seleccionar(sub.id)">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-2">
                                    {{ index+1 }}
                                </div>
                                <div class="col-10">
                                    <h6>Transportista: {{ sub.transportista.nombre_transportista  }}</h6>
                                    <p>Guia: {{ sub.guia }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="card">
                <div class="card-head">
                    <h5 class="mb-0">Lista Palets - Sub Lote 1</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-6"></div>
                        <div class="col-sm-6"></div>
                    </div>

                    <div class="row">
                        <div class="col-6 form-group">
                            <label for="">Número de Jabas:</label>
                            <input class="form-control" v-model="num_jabas">
                        </div>
                        <div class="col-6 form-group">
                            <label for="">Balanza:</label>
                            <div class="digital">{{ peso }} Kg</div>
                        </div>
                        <div class="col-12 form-group text-center">
                            <button @click="add()" class="btn btn-info">Agregar</button>
                        </div>
                        <div class="col-12">
                            <table class="table-responsive table-bordered">
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
                            </table>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
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
            sub_lote: this.init(),
            sub_lote_error:{},
            //listas
            transportistas:[],
            sub_lotes: [],
            palets: [],
            palets_entrada: [],
            //selectores
            seleccionado_sub_lote: null
        }
    },
    computed: {
        ...mapState(['peso']),
    },
    mounted() {
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
                this.sub_lote=this.init()
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
                this.listarPaletEntrada();
            });
        }    
    },
}
</script>