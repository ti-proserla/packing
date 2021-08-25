<template>
    <v-container fluid>
        <v-row class="">
            <v-col cols=12 sm=4>
                PALETIZADO
            </v-col>
            <v-col cols=12 sm=8 class="text-right">
                <v-btn @click="$router.push('/paletizado')" color="error">Continuar Despues</v-btn>
                <v-btn @click="terminar()" color="success" v-if="palet.estado=='Pendiente'">Cerrar</v-btn>
                
            </v-col>
        </v-row>
        <v-row>
            <v-col cols=12 sm=6>
                <v-card>
                    <v-card-text>
                        <v-row v-if="palet.estado=='Pendiente'">
                            <v-col cols=12>
                                <v-form autocomplete="off" @submit.prevent="agregar()">
                                    <v-text-field 
                                        dense 
                                        outlined 
                                        label="Lectura" 
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
                            <v-col cols="12">
                                <v-simple-table>
                                    <template v-slot:default>
                                        <tbody>
                                            <tr>
                                                <td :key="i" v-for="(cell,i) in escaneadosLabores">{{ cell.labor }}</td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <td :key="i" v-for="(cell,i) in escaneadosLabores">
                                                    <i class="fas fa-check" v-if="cell.estado"></i>
                                                    <i class="fas fa-times" v-else></i>
                                                </td>
                                                <td>
                                                    <v-btn
                                                        small
                                                        @click="completar"
                                                        color="primary">
                                                        LLenar
                                                    </v-btn>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </template>
                                </v-simple-table>
                            </v-col>
                        </v-row>
                        <v-data-table
                            :disable-sort="false"
                            :headers="header"
                            :items="palet.cajas"
                            hide-default-footer
                            >
                        </v-data-table>
                        <!-- <v-simple-table>
                            <template v-slot:default>
                                <tbody>
                                    <tr>
                                        <td><b>Calibre</b></td>
                                        <td><b>Categoria</b></td>
                                        <td><b>Presentación</b></td>
                                        <td><b>Cantidad</b></td>
                                    </tr>
                                    <tr :key="index" v-for="(caja,index) in palet.cajas">
                                        <td>{{ caja.nombre_calibre }}</td>
                                        <td>{{caja.nombre_categoria}}</td>
                                        <td>{{caja.nombre_presentacion}}</td>
                                        <td>{{caja.cantidad}}</td>
                                    </tr>
                                </tbody>
                            </template>
                        </v-simple-table> -->
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
            extension: 16,
            labores: [
                {codigo: '01',descripcion: 'EMPAQUE'},
                {codigo: '02',descripcion: 'PESADO'},
                {codigo: '03',descripcion: 'SELECCION'},
            ],
            header:[
                { text: 'Calibre', value: 'nombre_calibre' },
                { text: 'Categoria', value: 'nombre_categoria' },
                { text: 'Presentación', value: 'nombre_presentacion' },
                { text: 'Cantidad', value: 'cantidad' },
            ],
        }
    },
    mounted() {
        this.getPaletSalida();
    },
    computed:{
        escaneadosLabores(){
            var avance= [];
            for (let i = 0; i < this.labores.length; i++) {
                const labor = this.labores[i];
                var encontrado=0;
                for (let j = 0; j < this.fila_codigos.length; j++) {
                    
                    const codigo = this.fila_codigos[j];
                    
                    if (labor.codigo==codigo.substring(2,4)) {
                        avance.push({labor: labor.descripcion.substring(0,1), estado: true});
                        encontrado=1;
                        break;
                    }
                }
                if (encontrado==0) {
                    avance.push({labor: labor.descripcion.substring(0,1), estado: false});
                }
            }
            return avance;
        }
    },
    methods: {
        completar(){
            for (let i = 0; i < this.labores.length; i++) {
                const labor = this.labores[i];
                var encontrado=0;
                for (let j = 0; j < this.fila_codigos.length; j++) {
                    
                    const codigo = this.fila_codigos[j];
                    
                    if (labor.codigo==codigo.substring(2,4)) {
                        encontrado=1;
                        break;
                    }
                }
                if (encontrado==0) {
                    this.fila_codigos.push('00XX000000000000'.replace('XX',labor.codigo));
                }
            }
        },
        getPaletSalida(){
            axios.get(url_base+`/palet_salida/${this.$route.params.id}`)
            .then(response => {
                this.palet=response.data
this.labores=this.palet.etapas==1 ? 
[{codigo: '01',descripcion: 'EMPAQUE'}] : 
[
                {codigo: '01',descripcion: 'EMPAQUE'},
                {codigo: '02',descripcion: 'PESADO'},
                {codigo: '03',descripcion: 'SELECCION'},
            ];
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
            return (sCodigo.indexOf('C-')>-1) ? true : false;
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
                        this.getPaletSalida();
                        // this.palet.cajas.push(data.data);
                        // this.matriz_codigos.push(this.fila_codigos);
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
                if (this.fila_codigos.length==this.palet.etapas) {
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
                            console.log(element2.toString().substring(2,4));
                            console.log(this.codigo_barras.substring(2,4));
                            if (element2.substring(2,4)==this.codigo_barras.substring(2,4)) {
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
                if (this.fila_codigos.length==this.palet.etapas) {
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
