<template>
    <v-container fluid>
        <v-row>
            <v-col cols="12" lg="4">
                <!-- <v-card class="mb-3" outlined>
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
                </v-card> -->
                <v-card outlined>
                    <v-card-title>Seleccionar Operacion</v-card-title>
                    <v-card-text>
                        <v-form v-on:submit.prevent="listarCamaras">
                            <v-row>
                                <v-col cols="6">
                                    <v-text-field 
                                        label="Ingresar Código Operación:" 
                                        v-model="operacion_id"
                                    ></v-text-field>
                                </v-col>
                                <v-col cols="6">
                                    <v-btn
                                        type="submit" color="primary">
                                        Buscar
                                    </v-btn>
                                </v-col>
                                <v-col cols="12">
                                    {{palet}}
                                </v-col>
                            </v-row>
                        </v-form>
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
                    <v-expansion-panel :key="index" v-for="(piso,index) in camaras">
                        <v-expansion-panel-header>
                            <h4>{{ piso.titulo }}</h4>
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
                    <!-- <v-btn
                        elevation="0"
                        color="primary"
                        outlined
                        fab>
                        B10
                    </v-btn> -->
            </v-col>
        </v-row>
        <!-- <v-fab-transition> -->
        <v-btn
            color="primary"
            fab
            dark
            bottom
            right
            fixed
            class="v-btn--example"
            @click="registrar"
        >
            <i class="fas fa-search-plus"></i>
        </v-btn>
    </v-container>
</template>
<style>
    
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
            operacion_id: null,
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
            })
        },
        ver(){
            axios.get(url_base+`/camara/${this.codigo_camara}`)
            .then(response => {
                this.camara = response.data;
            })
        },
        listarCamaras(){
            axios.get(url_base+`/camara/operacion/${ this.operacion_id }`)
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
            