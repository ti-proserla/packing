<template>
    <div>
        {{datos}}
    </div>
</template>
<script>
export default {
    data() {
        return {
            datos: []
        }
    },
    mounted() {
        axios.get(`${url_base}/reporte/lote`)
        .then(response => {
            this.datos=response.data;
        });
    },
}
</script>