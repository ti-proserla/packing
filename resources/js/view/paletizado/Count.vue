<template>
    <v-container fluid>
        <v-row>
            <v-col cols=12 sm=4>
                PALETIZADO
            </v-col>
            <v-col cols=12 sm=8 class="text-right">
                <v-btn @click="$router.push('/paletizado')" color="error">Continuar Despues</v-btn>
                <v-btn @click="terminar()" color="success" v-if="palet.estado=='Abierto'">Cerrar</v-btn>
            </v-col>
        </v-row>
        <v-row>
            <v-col cols=12 sm=6 v-if="palet.estado=='Abierto'">
                <v-card>
                    <v-card-text>
                        <label>Ingresar Código:</label>
                        <v-row>
                            <v-col cols=12>
                                <v-form autocomplete="off" @submit.prevent="agregar()">
                                    <v-text-field 
                                        type="number"
                                        dense 
                                        outlined 
                                        label="Código de Barras" 
                                        autofocus
                                        @focus="OpenFocus()" 
                                        :readonly="readonlyFocusInit"
                                        v-model="codigo_barras">
                                        </v-text-field>
                                    <button type="submit" hidden>Submin</button>
                                </v-form>
                            </v-col>
                        </v-row>
                        <audio id="myAudio">
                            <!-- <source src="horse.ogg" type="audio/ogg"> -->
                            <source src="/mp3/error.mp3" type="audio/mpeg">
                            Your browser does not support the audio element.
                        </audio>
                    </v-card-text>
                </v-card>
            </v-col>
            <v-col cols=12 sm=6>
                <v-card>
                    <v-card-text>
                        <h6>JABAS ESCANEADAS</h6>
                        <v-simple-table>
                            <template v-slot:default>
                                <tbody>
                                    <tr>
                                        <td v-for="cell in fila_codigos">{{ cell }}</td>
                                    </tr>
                                    <tr v-for="(fila,index) in matriz_codigos">
                                        <td>{{ matriz_codigos.length - index }}</td>
                                        <td v-for="row in fila">{{row}}</td>
                                    </tr>
                                </tbody>
                            </template>
                        </v-simple-table>
                    </v-card-text>
                </v-card>
            </v-col>
        </v-row>
    </v-container>
</template>
<script>
    // extension: linea(2)autonumerico(8)
export default {
    data() {
        return {
            readonlyFocusInit: false,
            palet: {},
            codigo_barras: null,
            lista_codigos: [],
            fila_codigos: [],
            matriz_codigos: [],
            indice_matriz: 1,
            extension: 14 
        }
    },
    mounted() {
        axios.get(url_base+`/palet_salida/${this.$route.params.id}`)
        .then(response => {
            this.palet=response.data
            
            for (let i = 0; i < this.palet.jabas.length; i++) {
                const jaba = this.palet.jabas[i];
                var codigos=jaba.codigos.split('|');
                this.matriz_codigos.push(codigos);
            }
        });
    },
    methods: {
        OpenFocus(){
            // console.log("HOLA");
            // if (this.focusSelect){
                this.readonlyFocusInit=true;
                setTimeout(() => {
                    this.readonlyFocusInit=false;
                },300 );
            // }
        },
        agregar(){
            var repetido=0;

            if (this.codigo_barras.length==this.extension) {
                for (let i = 0; i < this.matriz_codigos.length; i++) {
                    var fila_codigos = this.matriz_codigos[i];
                    for (let k = 0; k < fila_codigos.length; k++) {
                        const element = fila_codigos[k];
                        if (this.codigo_barras==element) {
                            repetido=1;
                            break;
                        }
                    }
                }
                for (let i = 0; i < this.fila_codigos.length; i++) {
                    const element = this.fila_codigos[i];
                    if (this.codigo_barras==element) {
                        repetido=1;
                        break;
                    }
                }
                
                if (repetido==1) {
                    var x = document.getElementById("myAudio");
                    x.play();
                    window.navigator.vibrate([500,100,500]);
                    // swal("Código ya registrado en este Palet.", {
                    //     icon: "error",
                    //     timer: 3500
                    // });
                }else{
                    // for (let j = 0; j < this.fila_codigos.length; j++) {
                    //     const element2 = this.fila_codigos[j];
                    //     if (element2.substring(0,2)==this.codigo_barras.substring(0,2)) {
                    //         swal("Labor ya registrada para esta jaba.", {
                    //             icon: "error",
                    //             timer: 3500
                    //         });
                    //         repetido=1;
                    //         break;
                    //     }
                    // }
                    
                    if (repetido==0) {
                        this.fila_codigos.push(this.codigo_barras);
                        if (this.fila_codigos.length==this.indice_matriz) {
                            axios.post(url_base+`/palet_salida/${this.$route.params.id}/jaba`,{
                                codigos_barras: this.fila_codigos
                            }).then(res=>{
                                var data=res.data;
                                switch (data.status) {
                                    case "OK":
                                        this.matriz_codigos.push(this.fila_codigos);
                                        this.fila_codigos=[];
                                        break;
                                
                                    default:
                                        break;
                                }
                                this.codigo_barras="";       
                            });
                        }    
                    }
                    
                }
                
            }else{
                swal("Código no cumple con la estructura.", {
                    icon: "error",
                    timer: 3500
                });
            }
            this.codigo_barras=null;
        },
        terminar(){
            swal({
                title: "Terminar Palet",
                // text: "Once deleted, you will not be able to recover this imaginary file!",
                // icon: "warning",
                // buttons: true,
                buttons: ['Cancelar',"Finalizar"],
                // dangerMode: true,
            })
            .then((res) => {
                if (res) {
                    axios.post(url_base+`/palet_salida/${ this.$route.params.id }?_method=patch`)
                    .then(response => {
                        var res=response.data;
                        switch (res.status) {
                            case 'OK':
                                swal("Palet Terminado", {
                                    icon: "success",
                                    timer: 2000,
                                    buttons: false
                                });
                                this.$router.push('/paletizado'); 
                                break;
                        
                            default:
                                break;
                        }
                    });

                }
            });
        }
    },
}
</script>