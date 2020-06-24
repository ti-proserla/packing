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
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-2">
                                    1
                                </div>
                                <div class="col-10">
                                    <h6>Transportista: Dieojhsandk</h6>
                                    <p>Guia: 23151351351531</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card card-no-select">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-2">
                                    2
                                </div>
                                <div class="col-10">
                                    <h6>Transportista: Dieojhsandk</h6>
                                    <p>Guia: 23151351351531</p>
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
                        <div class="col-12">
                            Balanza: {{ peso }}
                            Número de Jabas:
                            <input class="form-control" v-model="num_jabas">
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
                                    <tr>
                                        <td>1</td>
                                        <td>{{ 51 }}</td>
                                        <td>{{ 20 }}</td>
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

            palets: []
        }
    },
    computed: {
        ...mapState(['peso']),
    },
    mounted() {
        this.listarTransportistas();
    },
    methods: {
        init(){
            return {
                lote_id: this.$route.params.id
            };
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
                
            });
        },
        add(){
            this.palets.push({
                num_jabas: this.num_jabas,
                peso: this.peso
            });
        }    
    },
}
</script>