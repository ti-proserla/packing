<template>
    <v-container fluid>
                <v-card>
                    <v-card-title>Palet {{ palet.tipo_palet_id }} - {{ palet.numero }}</v-card-title>
                    <v-card-text>
                        <v-row>
                            <v-col cols="12" sm="6" class="text-right">
                                <v-btn @click="$router.push('/paletizado')" small color="error">Salir</v-btn>
                                <v-btn @click="terminar()" small color="success" v-if="palet.estado=='Pendiente'">Cerrar</v-btn>
                            </v-col>
                            <v-col  cols=12 v-if="palet.estado=='Pendiente'">
                                <v-form v-if="!tope" autocomplete="off" @submit.prevent="agregar()">
                                    <v-text-field 
                                        dense 
                                        outlined 
                                        label="Lectura" 
                                        autofocus
                                        ref="codigo_barras"
                                        :disabled="tope"
                                        @focus="OpenFocus()" 
                                        :readonly="readonlyFocusInit"
                                        v-model="codigo_barras">
                                        </v-text-field>
                                    <button type="submit" hidden>Submin</button>
                                    <br>
                                    
                                    <v-alert 
                                        dense
                                        v-model="alert.visible" 
                                        :color="alert.status" 
                                        dark 
                                        transition="scale-transition"
                                    >{{ alert.message }}</v-alert>
                                </v-form>
                                <v-simple-table dense v-if="!tope">
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
                                <h2 v-if="tope">Palet completado ...</h2>
                            </v-col>
                            <v-col cols="12">
                                <v-data-table
                                    dense
                                    :disable-sort="false"
                                    :headers="header"
                                    :items="palet.cajas"
                                    hide-default-footer
                                    >
                                </v-data-table>
                            </v-col>
                        </v-row>
                    </v-card-text>
                </v-card>
    </v-container>
</template>
<style>
      .v-data-table-header-mobile{
        display: none !important;
      }
</style>
<script>
    // extension: linea(2)autonumerico(8)
export default {
    data() {
        return {
            statusFocus: true,
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
                { text: 'Fecha Empaque', value: 'fecha_empaque' },
                { text: 'Cantidad', value: 'cantidad' },
                { text: 'Presentación', value: 'nombre_presentacion' },
                { text: 'Calibre', value: 'nombre_calibre' },
                { text: 'Categoria', value: 'nombre_categoria' },
                { text: 'Tipo Empaque', value: 'nombre_tipo_empaque' },
                { text: 'Marca Empaque', value: 'nombre_marca_empaque' },
                { text: 'Marca Caja', value: 'nombre_marca_caja' },
                { text: 'PLU', value: 'nombre_plu' },
            ],
        }
    },
    mounted() {
        this.getPaletSalida();
    },
    computed:{
        tope(){
            var cantidad=0;
            if (this.palet.cajas.length!=0) {
                for (let i = 0; i < this.palet.cajas.length; i++) {
                    cantidad+= this.palet.cajas[i].cantidad;
                }
            }
            return (cantidad>=this.palet.tope_cajas);
        },
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
            if (this.fila_codigos.length!=0) {
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
            }else{
                this.alerta("Leer al menos una etiqueta de trabajador.");
            }
            this.$refs.codigo_barras.focus();
        },
        getPaletSalida(){
            axios.get(url_base+`/palet_salida/${this.$route.params.id}`)
            .then(response => {
                this.palet=response.data

                switch (this.palet.etapas) {
                    case 1:
                        this.labores=[{codigo: '04',descripcion: 'CLANSHELL'}]
                        break;

                    case 2:
                        this.labores=[
                            {codigo: '03',descripcion: 'SELECCION'},
                            {codigo: '04',descripcion: 'CLANSHELL'}
                        ]
                        break;
                    case 3:
                        this.labores=[
                            {codigo: '01',descripcion: 'EMPAQUE'},
                            {codigo: '02',descripcion: 'PESADO'},
                            {codigo: '03',descripcion: 'SELECCION'},
                        ];
                        break;
                
                    default:
                        break;
                }

                var cajas=this.palet.cajas;
                for (let i = 0; i < cajas.length; i++) {
                    const caja = cajas[i];
                    if (caja.codigos!=null) {
                        var codigos=caja.codigos.split('|');
                        this.matriz_codigos.push(codigos);
                    }
                }
                if (this.tope&&this.palet.estado=='Pendiente') {
                    this.alertaTerminado("Cajas Completas.");
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
            return (sCodigo.length==16||sCodigo.length==17) ? true : false;
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
        alertaTerminado(sMensaje){
            var x = document.getElementById("myAudio2");
            x.play();
            window.navigator.vibrate([500,500,500]);
            this.alert.status= 'info';
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
                        this.fila_codigos=[];
                       break;
                    case "ERROR":
                        this.alerta(data.message);
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
                    for (let i = 0; i < this.palet.cajas.length; i++) {
                        var codigos = (this.palet.cajas[i].codigos ===null) ? '' : this.palet.cajas[i].codigos ;
                        if (codigos!='') {
                            if (codigos.includes(this.codigo_barras)) {
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
                        this.alerta("Código repetido.");
                    }else{

                        for (let j = 0; j < this.fila_codigos.length; j++) {
                            const element2 = this.fila_codigos[j];
                            if (element2.substring(2,4)==this.codigo_barras.substring(2,4)) {
                                this.alerta("Labor ya registrada para esta caja.");
                                repetido=1;
                                break;
                            }
                        }

                        var encontrado=0;
                        for (let i = 0; i < this.labores.length; i++) {
                            const labor = this.labores[i];
                            if (labor.codigo==this.codigo_barras.substring(2,4)) {
                                encontrado=1;
                                break;
                            }
                        }
                        if (encontrado==0) {
                            repetido=1;
                            this.alerta("Labor no admitida.");
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
