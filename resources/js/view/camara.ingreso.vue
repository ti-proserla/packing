<template>
    <v-container fluid>
        <v-row>
            <v-col  cols="12" lg="4">
                <v-card class="mb-3" outlined>
                    <v-card-title>Datos de Pallet</v-card-title>
                    <v-card-text>
                        <v-form v-on:submit.prevent="searching">
                            <v-row>
                                <v-col cols="6">
                                    <v-text-field 
                                        label="Ingresar QR:" 
                                        v-model="palet_search"
                                    ></v-text-field>
                                </v-col>
                                <v-col cols="6">
                                    <v-btn
                                        type="submit" color="primary">
                                        Buscar
                                    </v-btn>
                                </v-col>
                                <v-col cols="12">
                                    <h5><b>Campaña:</b> {{ palet.campania_id }}</h5>
                                    <h5><b>Número:</b> {{ palet.numero }}</h5>
                                    <h5><b>Cliente:</b> {{ palet.cliente }}</h5>
                                    <h5><b>Cod. Operación:</b> {{ palet.codigo_operacion }}</h5>
                                    <h5><b>Operación:</b> {{ palet.operacion }}</h5>
                                    <!-- {{palet}} -->
                                </v-col>
                            </v-row>
                        </v-form>
                    </v-card-text>
                </v-card>
                <v-card class="mb-3" outlined>
                    <v-card-title>Revisión de Cámaras</v-card-title>
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
                        </v-row>
                    </v-card-text>
                </v-card>
            </v-col>
            <v-col cols="12" lg="8">
                <!-- <v-card outlined>
                    <v-card-title>Ingresar Palet</v-card-title>
                    <v-card-text>
                        <v-form v-on:submit.prevent="registrar">
                            <v-row>
                                <v-col cols="4">
                                    <v-text-field 
                                        label="Ingresar QR:" 
                                        v-model="codigo_palet"
                                        :disabled="seleccionado==0"
                                    ></v-text-field>
                                </v-col>
                                <v-col cols="2">
                                    <v-btn
                                        :disabled="seleccionado==0"
                                        type="submit" color="primary">
                                        Registrar
                                    </v-btn>
                                </v-col>
                            </v-row>
                        </v-form>
                    </v-card-text>
                </v-card> -->
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
                                            :class="(posicion.palet!=null ? 'posicion-ocupada' : 'posicion-seleccionable') + ' ' +(seleccionado==posicion.id ? 'posicion-seleccionada':'')" 
                                            class="posicion" 
                                            @click="posicion.palet==null ? seleccionar(posicion.id) : ''"
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
            </v-col>
        </v-row>
        <!-- <v-fab-transition> -->
        <v-btn
            color="primary"
            fab
            bottom
            right
            fixed
            class="v-btn--example"
            @click="registrar"
            :disabled="comprobar"
        >
            <i class="fas fa-save"></i>
        </v-btn>
    </v-container>
</template>
<style>
    .filas{
        display: flex;
        grid-template-columns: auto;
    }
    .piso{
        overflow-x: auto;
    }
    .posicion{
        user-select: none;
        border-color: #1867c0!important;
        color: #1867c0!important;
        border: 1px solid;
        font-size: 12px;
        border-radius: 14px;
        width: 28px;
        height: 28px;
        padding: 4px 0;
        margin: 2px;
        display: inline-block;
        text-align: center;
        font-weight: 600;
    }
    .posicion-nula{
        border-color: #fff!important;
    }

    .posicion-ocupada{
        color: #fff!important;
        background-color: #f44336!important;
        border-color: #f44336!important;
    }
    .posicion-seleccionada{
        color: #fff!important;
        background-color: #1867c0;
    }
    .posicion-seleccionable{
        cursor: pointer;
    }
    @media (min-width: 600px){
        .posicion{
            font-size: 12px;
            border-radius: 20px;
            width: 32px;
            height: 32px;
            padding: 6px 0;
        }
    }
    .posicion-ocupada.success{
        background-color: #87cb16!important;
        border-color: transparent!important;;
    }
    .posicion-ocupada.warning{
        background-color: #ffa534!important;
        border-color: transparent!important;;
    }
    .posicion-ocupada.error{
        background-color: #fb404b!important;
        border-color: transparent!important;;
    }
    .legend{
        display: flex;
        align-items: center;
    }
    .legend .posicion{
        width: 20px!important;
        height: 20px!important;
        margin: 4px;
    }
    .posicion-disabled{
        border-color: transparent!important;
        background-color: rgba(0,0,0,.25)!important;
        color: #fff!important;
    }
</style>
<script>
export default {
    data() {
        return {
            camara: [],
            panel: 0,
            seleccionado: 0,
            camaras: [],
            codigo_camara: null,
            codigo_palet: null,
            open_search: false,
            palet_search: null,
            palet: {},
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
    computed:{
        comprobar(){
            if (this.seleccionado>0) {
                return false;
            }else{
                return true;
            }
        }
    },
    mounted(){
        this.listarCamaras();
        // this.ver();
    },
    methods: {
        seleccionar(id){
            this.seleccionado=id;
        },
        searching(){
            axios.get(url_base+`/palet_salida/search/${this.palet_search}`)
            .then(response => {
                this.palet = response.data;
                axios.get(url_base+`/camara/operacion/${ this.palet.codigo_operacion }?min_data`)
                .then(response => {
                    console.log(response.data);
                })
            })
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
                codigo_palet: this.palet_search
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
            