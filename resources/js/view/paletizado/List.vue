<template>
    <div>
        <h5>Lista de Palets x Lote</h5>
        <div class="card" v-for="lote in lotes">
            <div class="card-body">
                <div class="row">
                    <div class="col-12">
                        <h6>COD LOTE: {{ lote.codigo }}</h6>
                        <h6>CLIENTE: {{ lote.cliente }} </h6>
                        <p>MATERIA {{ lote.materia }} </p>
                    </div>
                    <div v-for="palet in lote.palets_salida" class="col-sm-3">
                        <button class="btn btn-primary">{{ palet.producto }}</button>
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
            lotes: []
        }
    },
    methods: {
        
    },
    mounted() {
        axios.get(url_base+`/lote_ingreso/palets_salida?estado=lanzado`)
        .then(response => {
            this.lotes=response.data
        });
    },
}
</script>