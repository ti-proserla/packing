<template>
    <v-container fluid>
        <v-card>
            <v-card-title>LISTA DE PALETS</v-card-title>
            <v-card-text>
                <v-row>
                    <v-col cols=12 sm=6>
                        <v-text-field 
                            label="Fecha Empaque:" 
                            v-model="rendimiento.fecha_empaque"
                            outlined
                            dense
                            clearable
                            type="date"
                        ></v-text-field>
                    </v-col>
                    <v-col cols="12" sm="6">
                        <v-select
                            label="Presentacion:"
                            v-model="rendimiento.presentacion_id"
                            :items="presentaciones"
                            item-text="nombre_presentacion"
                            item-value="id">
                        </v-select>
                    </v-col>
                </v-row>
                <v-form autocomplete="off" @submit.prevent="agregar()">
                    <v-text-field 
                        dense 
                        outlined 
                        label="Lectura" 
                        autofocus
                        ref="codigo_barras"
                        @focus="OpenFocus()" 
                        :readonly="readonlyFocusInit"
                        v-model="rendimiento.codigo">
                        </v-text-field>
                    <button type="submit" hidden>Submin</button>
                </v-form>
                <v-alert v-model="alert.visible" 
                    :color="alert.status" 
                    dark 
                    transition="scale-transition"
                >{{ alert.message }}</v-alert>
            </v-card-text>
        </v-card>
    </v-container>
</template>
<script>
export default {
    data() {
        return {
            alert: this.initAlert(),
            readonlyFocusInit:true,
            rendimiento: {
                presentacion_id: '',
                fecha_empaque: moment().format('YYYY-MM-DD'),
                codigo: ''
            },
            presentaciones: []
        }
    },
    mounted() {
        this.listarPresentaciones();
    },
    methods: {
        initAlert(){
            return {
                status: '',
                visible: false,
                message: ''
            }
        },
        listarPresentaciones(){
            axios.get(url_base+`/presentacion?all`)
            .then(response => {
                this.presentaciones=response.data
            });
        },
        OpenFocus(){            
            this.readonlyFocusInit=true;
            setTimeout(() => {
                this.readonlyFocusInit=false;
            },300 );
        },
        agregar(){
            axios.post(url_base+`/rendimiento-personal`,this.rendimiento)
            .then(res=>{
                this.rendimiento.codigo='';
                var data=res.data;
                switch (data.status) {
                    case "OK":

                        break;
                    case "ERROR":
                        this.alerta(data.message);
                        break;
                }
            });
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
    }
}
</script>
