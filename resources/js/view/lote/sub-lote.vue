<template>
    <div class="row">
        <div class="col-sm-6">
            <div class="card">
                <div class="card-head">
                    <h5 class="mb-0">Sub Lote</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <label for="">Guia: </label>
                            <input type="text" class="form-control" v-model="sub_lote.guia">
                            <span>{{ sub_lote_error.guia }}</span>
                        </div>
                        <div class="col-md-6">
                            <select class="form-control" v-model="sub_lote.trasnportista_id">
                                <option value="">-Seleccionar Transportista-</option>
                                <option v-for="trasnportista in transportistas" :value="transportista.id">{{ transportista.nombre_transportista }}</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    data() {
        return {
            sub_lote: {},
            sub_lote_error:{},
            //listas
            transportistas:[],
        }
    },
    mounted() {
        this.listarTransportistas();
    },
    methods: {
        listarTransportistas(){
            axios.get(url_base+'/tranportista')
            .then(response => {
                this.transportistas=response.data
            });
        },    
    },
}
</script>