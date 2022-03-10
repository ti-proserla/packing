<template>
    <v-container fluid>
        <v-row>
            <v-col cols="12" lg="4">
                <v-card outlined>
                    <v-card-title>Seleccionar Operacion</v-card-title>
                    <v-card-text>
                        <v-form v-on:submit.prevent="listarCamaras">
                            <v-row>
                                <v-col cols="7">
                                    <v-text-field 
                                        label="Código Operación:" 
                                        v-model="operacion_id"
                                    ></v-text-field>
                                </v-col>
                                <v-col cols="5">
                                    <v-btn
                                        type="submit" color="primary">
                                        Buscar
                                    </v-btn>
                                </v-col>
                                <v-col cols="12">
                                    <h5><b>Código:</b> {{ operacion.codigo_operacion }}</h5>
                                    <h5><b>Descripción:</b> {{ operacion.descripcion }}</h5>
                                    <h5><b>Cliente:</b> {{ operacion.cliente }}</h5>
                                </v-col>
                            </v-row>
                        </v-form>
                    </v-card-text>
                </v-card>
            </v-col>
            <v-col cols="12" lg="8">
                <v-expansion-panels
                    v-model="panel"
                    multiple
                    >
                    <v-expansion-panel :key="index" v-for="(piso,index) in camaras">
                        <v-expansion-panel-header>
                            <h5>{{ piso.titulo }}</h5>
                        </v-expansion-panel-header>
                        <v-expansion-panel-content>
                            <div class="piso">
                                <div  class="filas" :key="index1" v-for="(fila,index1) in piso.datos">
                                    <div :key="index2" v-for="(posicion,index2) in fila">
                                        <v-tooltip v-if="posicion!=null" top>
                                            <template  v-slot:activator="{ on, attrs }">
                                                <span 
                                                    v-bind="attrs" 
                                                    v-on="on"
                                                    :class="(posicion.palet!=null ? 'posicion-ocupada' : 'posicion-disabled') + ' ' +(seleccionado==posicion.id ? 'posicion-seleccionada':'')" 
                                                    class="posicion" 
                                                    >
                                                    {{ posicion.codigo }}
                                                </span>
                                            </template>
                                            <span>{{ posicion.palet }}</span>
                                        </v-tooltip>
                                        <span class="posicion posicion-nula" v-else></span>
                                    </div>
                                </div>
                            </div>
                        </v-expansion-panel-content>
                    </v-expansion-panel>
                </v-expansion-panels>
            </v-col>
        </v-row>
    </v-container>
</template>
<script>
export default {
    data() {
        return {
            camara: [],
            panel: [],
            seleccionado: 0,
            camaras: [],
            codigo_camara: null,
            codigo_palet: null,
            open_search: false,
            operacion_id: null,
            palet: {},
            operacion: {}
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
    },
    methods: {
        seleccionar(id){
            this.seleccionado=id;
        },
        searching(){
            axios.get(url_base+`/palet_salida/search/${this.palet_search}`)
            .then(response => {
                this.palet = response.data;
            })
        },
        listarCamaras(){
            axios.get(url_base+`/camara/operacion/${ this.operacion_id }`)
            .then(response => {
                this.getOperacion();
                this.camaras = response.data;
                this.panel=[];
                for (let i = 0; i < this.camaras.length; i++) {
                    this.panel.push(i);                    
                }
            })
        },
        getOperacion(){
            axios.get(url_base+`/operacion/codigo/${ this.operacion_id }`)
            .then(response => {
                this.operacion = response.data;
            });
        }
    }
}
</script>
            