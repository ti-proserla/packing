<template>
    <v-container fluid>
        <v-card outlined>
            <v-card-title>Nueva Operación</v-card-title>
            <v-card-text>
                    <v-col cols="12" lg="6">
                        <v-text-field 
                            outlined
                            dense
                            type="date"
                            label="Fecha produccion" 
                            v-model="produccion.fecha_produccion"
                        ></v-text-field>
                    </v-col>
                    <v-col cols=12>
                        <v-select
                            outlined
                            dense
                            v-model="produccion.productor_id"
                            label="productor:"
                            :items="productores"
                            item-text="descripcion"
                            item-value="id"
                            >
                            </v-select>
                    </v-col>
                    <div class="text-right mt-3">
                        <v-btn 
                            outlined 
                            color="secondary" 
                            @click="open_nuevo=false"
                            >Cancelar</v-btn>
                        <v-btn 
                            outlined 
                            color="primary" 
                            @click="guardar()"
                            >Guardar</v-btn>
                    </div>
            </v-card-text>
        </v-card>
    </v-container>
</template>
<script>
export default {
    data() {
        return {
            produccion: {
                fecha_produccion: moment().format('YYYY-MM-DD')
            },
            productores: [],
        }
    },
    mounted(){
        this.listarproductores();
    },
    methods:{
        listarproductores(){
            axios.get(url_base+'/cliente?all')
            .then(response => {
                this.productores=response.data
            });
        },
        guardar(){
            axios.post(`${url_base}/produccion`,this.produccion)
            .then(response => {
                var respuesta=response.data;
                switch (respuesta.status) {
                    case 'OK':
                        swal(respuesta.message, { 
                            icon: "success", 
                            timer: 2000, 
                            buttons: false
                        });
                        this.$router.push(`/produccion/${respuesta.data.id}`);
                        break;
                    case 'VALIDATION':
                        this.error=respuesta.data;
                        break;
                    case 'ERROR':
                        break;
                    default:

                        break;
                }
            });
        }
    }
}
</script>