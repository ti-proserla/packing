<template>
    <div>
        <h5 class="mb-3">Scanner de Palet</h5>
        <div class="row">
            <div class="col-sm-6 mb-3">
                <button class="btn btn-secondary">Salir</button>
                <button class="btn btn-success" @click="terminar()">Terminar</button>
            </div>
            <div class="col-sm-12">
            </div>
            <div class="form-group col-sm-6 col-12">
                <label>Ingresar Código:</label>
                <form v-on:submit.prevent="agregar()" class="input-group">
                    <input type="text" class="form-control" v-model="codigo_barras" placeholder="(2)LAB-(8)COD-(4)NUM">
                    <div class="input-group-prepend">
                        <button type="submit" class="btn btn-primary"> > </button>
                    </div>
                </form>
            </div>
        </div>
        <h6>JABAS ESCANEADAS</h6>
        <table class="table">
            <tbody>
                <td v-for="cell in fila_codigos">{{ cell }}</td>
                <tr v-for="(fila,index) in matriz_codigos">
                    <td>{{ matriz_codigos.length - index }}</td>
                    <td v-for="row in fila">{{row}}</td>
                </tr>
            </tbody>
        </table>
    </div>
</template>
<script>
export default {
    data() {
        return {
            codigo_barras: null,
            lista_codigos: [],
            fila_codigos: [],
            matriz_codigos: [],
            indice_matriz: 2,
        }
    },
    mounted() {
        // setTimeout(() => {
            var objectStore=BD_REQUEST.transaction(["PALET_SALIDA"])
                            .objectStore("PALET_SALIDA");
            var request=objectStore.get(3);
    
            request.onsuccess = function(event) {
                var data = request.result;
                console.log(data);
            };
            
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
                if (repetido==0) {
                    this.fila_codigos.push(this.codigo_barras);
                    if (this.fila_codigos.length==this.indice_matriz) {
                        this.matriz_codigos.push(this.fila_codigos);
                        this.fila_codigos=[];
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