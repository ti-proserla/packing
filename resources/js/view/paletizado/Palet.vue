<template>
    <v-container fluid>
        <v-card v-if="palet!=null">
            <v-card-title>Palet {{ palet.tipo_palet_id }} - {{ palet.numero }}</v-card-title>
            <v-card-text>
                <v-btn @click="">Reetiquetar</v-btn>
                <v-data-table
                    :disable-sort="false"
                    :headers="header"
                    :items="palet.cajas"
                    hide-default-footer
                    >
                </v-data-table>
            </v-card-text>
        </v-card>
    </v-container>
</template>
<script>
export default {
    data(){
        return {
            palet: null,
            header:[
                { text: 'Fecha Empaque', value: 'fecha_empaque' },
                { text: 'Cantidad', value: 'cantidad' },
                { text: 'Presentación', value: 'nombre_presentacion' },
                { text: 'Calibre', value: 'nombre_calibre' },
                { text: 'Categoria', value: 'nombre_categoria' },
                { text: 'Tipo Empaque', value: 'nombre_tipo_empaque' },
                { text: 'Marca Empaque', value: 'nombre_marca_empaque' },
                { text: 'Marca Caja', value: 'nombre_marca_caja' },
                { text: 'PLU', value: 'nombre_plu' },
            ],
        }
    },
    mounted(){
        this.getPaletSalida();
    },
    methods:{
        getPaletSalida(){
            axios.get(url_base+`/palet_salida/${this.$route.params.id}`)
            .then(response => {
                this.palet=response.data;
            });
        }
    }
}
</script>