<template>
    <div>
        <h5>Lista de Palets</h5>
        <div class="card" v-for="lote in lotes_ingreso">
            <div class="card-body">
                <div class="row">
                    <div class="col-9">
                        <h6>{{ lote.descripcion}}</h6>
                        <h6><b>Lote:</b> {{ lote.codigo }}</h6>
                        <p><b>Materia:</b> {{ lote.nombre_materia}} - {{ lote.nombre_variedad }}</p>
                    </div>
                    <div class="col-3 text-right">
                        <span class="btn btn-sm btn-info">{{ lote.estado }}</span>
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