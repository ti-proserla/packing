<template>
    <div>
        <v-btn
            @click="open_nuevo=true"
            color="primary">
            Agregar
        </v-btn>

        <v-card>
            <v-card-text>
                <h6>CAJAS ESCANEADAS</h6>
                <v-simple-table>
                    <template v-slot:default>
                        <tbody>
                            <tr v-for="dato in datos">
                                <td>{{dato.codigo}}</td>
                                <td>{{dato.linea_lanzado}}</td>
                                <td>{{dato.palets}}</td>
                            </tr>
                        </tbody>
                    </template>
                </v-simple-table>
            </v-card-text>
        </v-card>
        <v-dialog v-model="open_nuevo" persistent max-width="350">
            <v-card>
                <v-card-title class="headline">Nueva variedad</v-card-title>
                <v-card-text>
                    <form
                        id="app"
                        v-on:submit.prevent="guardar">
                        <v-select
                            label="Linea:"
                            v-model="lanzado.linea"
                            :items="lineas"
                            :item-text="(item) => item.numero"
                            item-value="numero">
                            </v-select>
                        <v-text-field 
                            label="Código de Palet:" 
                            v-model="lanzado.codigo"
                        ></v-text-field>
                        <div class="text-right mt-3">
                            <v-btn 
                                outlined 
                                color="secondary" 
                                @click="cerrar()"
                                >Cerrar</v-btn>
                            <v-btn 
                                outlined 
                                color="primary" 
                                type="submit"
                                >Lanzar</v-btn>
                        </div>
                    </form>
                </v-card-text>
            </v-card>               
        </v-dialog>
    </div>
</template>
<script>
export default {
    data() {
        return {
            datos: [],
            lanzado: this.initLanzado(),
            open_nuevo: false,
            lineas: [
                {numero: 1},
                {numero: 2},
                {numero: 3},
                {numero: 4},
                {numero: 5},
                {numero: 6}
            ],

            // fecha_produccion: moment().format('YYYY-MM-DD')
        }
    },
    mounted() {
        this.listar();
    },
    methods:{
        initLanzado(){
            return {
                codigo: null,
                linea: null
            }
        },
        listar(){
            axios.get(`${url_base}/lanzado`)
            .then(response=>{
                this.datos=response.data
            });
        },
        guardar(){
            var t=this;
            axios.post(url_base+`/lanzado?_method=patch`,this.lanzado)
            .then(response => {
                var res=response.data;
                switch (res.status) {
                    case 'OK':
                        swal(res.data, {
                            icon: "success",
                            timer: 2000,
                            buttons: false,
                        });
                        t.listar();
                        t.cerrar();
                        break;
                    case 'ERROR':
                        var x = document.getElementById("myAudio");
                        x.play();
                        window.navigator.vibrate([500,100,500]);
                        swal(res.data, {
                            icon: "error",
                            timer: 3000,
                            buttons: false,
                        });
                        t.lanzado.codigo='';
                        break;
                }
            });
        },
        cerrar(){
            this.open_nuevo=false;
            this.lanzado=this.initLanzado();
        }
    }
}
</script>