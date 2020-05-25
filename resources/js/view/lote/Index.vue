<template>
    <div>
        <h5>Lista de Palets</h5>{{ lotes_ingreso }}
        <div class="card" v-for="lote in lotes_ingreso">
            <div class="card-body">
                <div class="row">
                    <div class="col-10">
                        <h6>{{ lote.codigo }} - Plantaciones del Sol</h6>
                        <p><b>Materia:</b> UVA - RED GLOBE</p>
                        <p></p>
                    </div>
                    <div class="col-2 text-right">
                        <span class="btn btn-sm btn-info">Registrado</span>
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
            lotes_ingreso: []
        }
    },
    mounted() {
        this.listarLotes()
    },
    methods: {
        listarLotes(){
            axios.get(url_base+'/lote-ingreso')
            .then(response => {
                this.lotes_ingreso=response.data
            })
        },
    },
}
</script>