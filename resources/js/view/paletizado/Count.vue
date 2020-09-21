<template>
    <v-container fluid>
        <v-row>
            <v-col cols=12 sm=6>
                PALETIZADO
            </v-col>
            <v-col cols=12 sm=6 class="text-right">
                <v-btn @click="$router.push('/paletizado')" color="error">Continuar Despues</v-btn>
                <v-btn @click="terminar()" color="success">Cerrar Palet</v-btn>
            </v-col>
        </v-row>
        <v-row>
            <v-col cols=12 sm=6>
                <v-card>
                    <v-card-text>
                        <label>Ingresar Código:</label>
                        <v-row>
                            <v-col cols=12>
                                <v-form @submit.prevent="agregar()">
                                    <v-text-field dense outlined label="Código de Barras" autofocus v-model="codigo_barras">

                                    </v-text-field>
                                    <button type="submit" hidden>Submin</button>
                                </v-form>
                            </v-col>
                        </v-row>
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
                                    <td v-for="cell in fila_codigos">{{ cell }}</td>
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
export default {
    data() {
        return {
            codigo_barras: null,
            lista_codigos: [],
            fila_codigos: [],
            matriz_codigos: [],
            indice_matriz: 1,
        }
    },
    mounted() {
        // setTimeout(() => {
            // var objectStore=BD_REQUEST.transaction(["PALET_SALIDA"])
            //                 .objectStore("PALET_SALIDA");
            // var request=objectStore.get(3);
    
            // request.onsuccess = function(event) {
            //     var data = request.result;
            //     console.log(data);
            // };
            
        // }, 100);

        
    },
    methods: {
        agregar(){
            var repetido=0;
            for (let i = 0; i < this.matriz_codigos.length; i++) {
                var fila_codigos = this.matriz_codigos[i];
                for (let k = 0; k < fila_codigos.length; k++) {
                    const element = fila_codigos[k];
                    console.log(element);
                    
                    if (this.codigo_barras==element) {
                        repetido=1;
                        break;
                    }
                }
            }
            console.info("comprobacion de repeticion");
            if (repetido==1) {
                swal("Código ya registrado en este Palet.", {
                    icon: "error",
                    timer: 3500
                });
            }else{
                for (let j = 0; j < this.fila_codigos.length; j++) {
                    const element2 = this.fila_codigos[j];
                    if (element2.substring(0,2)==this.codigo_barras.substring(0,2)) {
                        swal("Labor ya registrada para esta jaba.", {
                            icon: "error",
                            timer: 3500
                        });
                        repetido=1;
                        break;
                    }
                }
                console.info("comprobacion de repeticion2");
                
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
                    swal("Palet Terminado", {
                        icon: "success",
                        timer: 2000,
                        buttons: false
                    });
                    this.$router.push('/paletizado'); 
                }
            });
        }
    },
}
</script>