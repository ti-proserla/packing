<template>
    <v-container fluid>
        <v-row>
            <v-col cols="12" lg="4">
                <v-card outlined>
                    <v-card-title>Semaforizacion de Cámaras</v-card-title>
                    <v-card-text>
                        <v-row>
                            <v-col cols="12">
                                <v-select
                                label="Cámaras:"
                                v-model="codigo_camara"
                                :items="camaras"
                                item-text="nombre"
                                item-value="codigo">
                                </v-select>
                            </v-col>
                            <v-col cols="12">
                                <v-btn color="primary" @click="ver">
                                    Buscar
                                </v-btn>
                            </v-col>
                            <v-col cols="12">
                                <div class="legend">
                                    <span 
                                        class="posicion-ocupada success posicion" 
                                        >
                                    </span> 1 - 2 Días
                                    <span 
                                        class="posicion-ocupada warning posicion" 
                                        >
                                    </span> 3 - 4 Días
                                    <span 
                                        class="posicion-ocupada error posicion" 
                                        >
                                    </span> 5 a más Días
                                </div>
                            </v-col>
                        </v-row>
                    </v-card-text>
                </v-card>
            </v-col>
            <v-col cols="12" lg="8">
                <v-expansion-panels
                    v-model="panel"
                    >
                    <v-expansion-panel :key="index" v-for="(piso,index) in camara">
                        <v-expansion-panel-header>
                            <h4>Piso {{ index + 1 }}</h4>
                        </v-expansion-panel-header>
                        <v-expansion-panel-content>
                            <div class="piso">
                                <div  class="filas" :key="index1" v-for="(fila,index1) in piso">
                                    <div :key="index2" v-for="(posicion,index2) in fila">
                                        <span 
                                            v-if="posicion!=null"
                                            :class="(posicion.palet!=null ? 'posicion-ocupada' : 'posicion-disabled') + ' '+color(posicion.ingreso)" 
                                            class="posicion" 
                                            >
                                            {{ posicion.codigo }}
                                        </span>
                                        <span class="posicion posicion-nula" v-else></span>
                                    </div>
                                </div>
                            </div>
                        </v-expansion-panel-content>
                    </v-expansion-panel>
                </v-expansion-panels>
                    <!-- <v-btn
                        elevation="0"
                        color="primary"
                        outlined
                        fab>
                        B10
                    </v-btn> -->
            </v-col>
        </v-row>
    </v-container>
</template>
<script>
export default {
    data() {
        return {
            camara: [],
            panel: 0,
            seleccionado: 0,
            camaras: [],
            codigo_camara: null,
            codigo_palet: null
        }
    },
    components:{
        SheetFooter: {
        functional: true,

        render (h, { children }) {
          return h('v-sheet', {
            staticClass: 'mt-auto align-center justify-center d-flex px-2',
            props: {
              color: 'rgba(0, 0, 0, .36)',
              dark: true,
              height: 50,
            },
          }, children)
        },
      },
    },
    mounted(){
        this.listarCamaras();
        // this.ver();
    },
    methods: {
        color(fecha){
            var given = moment(fecha, "YYYY-MM-DD");
            var current = moment().startOf('day');
            var col='';
            var diffDay=moment.duration(current.diff(given)).asDays();
            switch (true) {
                case (diffDay<3):
                    col='success'
                    break;
                case (diffDay<5):
                    col='warning'
                    break;
                case (diffDay>4):
                    col='error'
                    break;
            }
            return col;
        },
        seleccionar(id){
            this.seleccionado=id;
        },
        ver(){
            axios.get(url_base+`/camara/${this.codigo_camara}`)
            .then(response => {
                this.camara = response.data;
            })
        },
        listarCamaras(){
            axios.get(url_base+'/camara')
            .then(response => {
                this.camaras = response.data;
            })

        },
        registrar(){
            axios.post(url_base+'/sku',{
                posicion_id: this.seleccionado,
                codigo_palet: this.codigo_palet
            })
            .then(response => {
                var respuesta=response.data;
                switch (respuesta.status) {
                    case 'OK':
                        swal("Palet en Camara.", { 
                            icon: "success", 
                            timer: 2000, 
                            buttons: false
                        });
                        this.codigo_palet=null;
                        this.seleccionado=0;
                        this.ver();
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
            