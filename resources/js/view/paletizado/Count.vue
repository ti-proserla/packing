<template>
    <v-container fluid>
        <v-row class="">
            <v-col cols=12 sm=4>
                PALETIZADO
            </v-col>
            <v-col cols=12 sm=8 class="text-right">
                <v-btn @click="$router.push('/paletizado')" color="error">Continuar Despues</v-btn>
                <v-btn @click="terminar()" color="success" v-if="palet.estado=='Pendiente'">Cerrar</v-btn>
                <v-menu offset-y>
                    <template v-slot:activator="{ on }">
                        <v-btn
                        icon
                        dark
                        v-on="on"
                        >
                        <v-icon>more_vert</v-icon>
                        </v-btn>
                    </template>
                    <!-- <v-list>
                        <v-list-tile
                        v-for="(item, index) in items"
                        :key="index"
                        >
                        <v-list-tile-title>{{ item.title }}</v-list-tile-title>
                        </v-list-tile>
                    </v-list> -->
                </v-menu>
            </v-col>
        </v-row>
        <v-row>
            <v-col cols=12 sm=6 v-if="palet.estado=='Pendiente'">
                <v-card>
                    <v-card-text>
                        <label>Ingresar Código:</label>
                        <v-row>
                            <v-col cols=12>
                                <v-form autocomplete="off" @submit.prevent="agregar()">
                                    <v-text-field 
                                        dense 
                                        outlined 
                                        label="Código de Barras" 
                                        autofocus
                                        @focus="OpenFocus()" 
                                        :readonly="readonlyFocusInit"
                                        v-model="codigo_barras">
                                        </v-text-field>
                                    <button type="submit" hidden>Submin</button>
                                    <v-alert v-model="alert.visible" 
                                        :color="alert.status" 
                                        dark 
                                        transition="scale-transition"
                                    >{{ alert.message }}</v-alert>
                                </v-form>
                            </v-col>
                        </v-row>
                    </v-card-text>
                </v-card>
            </v-col>
            <v-col cols=12 sm=6>
                <v-card>
                    <v-card-text>
                        <h6>CAJAS ESCANEADAS</h6>
                        <v-simple-table>
                            <template v-slot:default>
                                <tbody>
                                    <tr>
                                        <td :key="i" v-for="(cell,i) in fila_codigos">{{ cell }}</td>
                                    </tr>
                                </tbody>
                            </template>
                        </v-simple-table>
                        <v-simple-table>
                            <template v-slot:default>
                                <tbody>
                                    <tr>
                                        <td><b>N°</b></td>
                                        <td><b>Cal</b></td>
                                        <td><b>Cat</b></td>
                                        <td><b>Pre</b></td>
                                    </tr>
                                    <tr :key="index" v-for="(caja,index) in palet.cajas">
                                        <td>{{ index+1 }}</td>
                                        <td>{{ caja.calibre }}</td>
                                        <!-- <td>
                                            <label for=""><b>Cal:</b> {{caja.calibre}}</label><br>
                                            <label for=""><b>Cat:</b> </label><br>
                                            <label for=""><b>Pre:</b> {{caja.presentacion}}</label><br>
                                        </td> -->
                                        <td>{{caja.categoria}}</td>
                                        <td>{{caja.presentacion}}</td>
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
            palet: {
                cajas: []
            },
            alert: this.initAlert(),
            codigo_barras: null,
            
            // lista_codigos: [],
            codigo_palet: '', 
            fila_codigos: [],
            matriz_codigos: [],
            extension: 16 
        }
    },
    mounted() {
        this.getPaletSalida();
    },
    methods: {
        getPaletSalida(){
            axios.get(url_base+`/palet_salida/${this.$route.params.id}`)
            .then(response => {
                this.palet=response.data
                var cajas=this.palet.cajas;
                for (let i = 0; i < cajas.length; i++) {
                    const caja = cajas[i];
                    if (caja.codigos!=null) {
                        var codigos=caja.codigos.split('|');
                        this.matriz_codigos.push(codigos);
                    }
                }
            });
        },
        initAlert(){
            return {
                status: '',
                visible: false,
                message: ''
            }
        },
        OpenFocus(){            
            this.readonlyFocusInit=true;
            setTimeout(() => {
                this.readonlyFocusInit=false;
            },300 );
        },
        isCodigoTrabajador(sCodigo){
            return (sCodigo.length==16) ? true : false;
        },
        isCodigoPalet(sCodigo){
            return (sCodigo.indexOf('P-')>-1) ? true : false;
        },
        alerta(sMensaje){
            var x = document.getElementById("myAudio");
            x.play();
            window.navigator.vibrate([500,100,500]);
            this.alert.status= 'danger';
            this.alert.visible= true;
            this.alert.message= sMensaje;
            this.timer=setTimeout(() => {
                this.alert=this.initAlert();
            }, 2000);
        },
        addCaja(){
            axios.post(url_base+`/palet_salida/${this.$route.params.id}/caja`,{
                codigos_trabajador: this.fila_codigos,
                codigo_palet: this.codigo_palet,
            }).then(res=>{
                var data=res.data;
                switch (data.status) {
                    case "OK":
                        this.palet.cajas.push(data.data);
                        this.matriz_codigos.push(this.fila_codigos);
                        this.fila_codigos=[];
                       break;
                    case "ERROR":
                        this.alerta("Código de caja no existe o Lote ya esta cerrado.");
                        break;
                    default:
                        break;
                }
            });
        },
        agregar(){
            if (this.isCodigoTrabajador(this.codigo_barras)) {
                if (this.fila_codigos.length==1) {
                    this.alerta("Escanear codigo de palet.");
                }else{

                    var repetido=0;

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
                    console.log("hola",repetido);

                    if (repetido==1) {
                        this.alerta("Código repetido.");
                    }else{
                        console.log("hola 01:",repetido);

                        for (let j = 0; j < this.fila_codigos.length; j++) {
                            const element2 = this.fila_codigos[j];
                            if (element2.substring(0,2)==this.codigo_barras.substring(0,2)) {
                                this.alerta("Labor ya registrada para esta caja.");
                                repetido=1;
                                break;
                            }
                        }
                    
                        if (repetido==0) {
                            if (this.fila_codigos.length<this.palet.etapas) {
                                this.fila_codigos.push(this.codigo_barras);
                                this.codigo_barras="";
                            }  
                        }
                    }
                }
            }else if (this.isCodigoPalet(this.codigo_barras)) {
                if (this.fila_codigos.length==1) {
                    this.codigo_palet=this.codigo_barras
                    this.addCaja();
                }else{
                    this.alerta("Terminar de escanear codigo trabajador.");
                }
            }else{
                this.alerta("Código no cumple con la estructura.");
            }
            this.codigo_barras='';
        },
        terminar(){
            swal({
                title: "Terminar Palet",
                buttons: ['Cancelar',"Finalizar"],
            })
            .then((res) => {
                if (res) {
                    axios.post(url_base+`/palet_salida/${ this.$route.params.id }?_method=patch`,{
                        estado: 'Cerrado'
                    })
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