<template>
    <v-container fluid>
        <v-row class="justify-center align-center">
            <v-col
                 cols="12" 
                 lg=6>
                <v-card outlined>
                    <v-card-title>Producción Code Bar</v-card-title>
                    <v-card-text>
                        <v-form autocomplete="off" @submit.prevent="print()">
                            <v-row>
                                <v-col cols="12">
                                    <v-select
                                        v-model="printing_local"
                                        label="Printers:"
                                        :items="[
                                            { value: true, descripcion: 'Impresión Local'},
                                            { value: false, descripcion: 'Impresión Red'},
                                        ]"
                                        item-text="descripcion"
                                        item-value="value"
                                    ></v-select>
                                </v-col>
                                <v-col cols="12" v-if="printing_local">
                                    <v-select
                                        outlined
                                        dense
                                        v-model="printer_uid"
                                        label="Printers:"
                                        :items="printers_local"
                                        :item-text="print => `${print.name}`"
                                        item-value="uid"
                                        >
                                        </v-select>
                                </v-col>
                                <v-col cols="12" v-else>
                                    <v-select
                                        outlined
                                        dense
                                        v-model="form.ip_print"
                                        label="Printers:"
                                        :items="prints"
                                        :item-text="print => `${print.ip} = ${print.nombre}`"
                                        item-value="ip"
                                        v-on:change="changePrint"
                                        >
                                        </v-select>
                                </v-col>
                                <v-col cols="12">
                                    <v-text-field 
                                        dense 
                                        outlined 
                                        label="Código de Barras" 
                                        autofocus 
                                        v-model="form.codigo_operador">
                                        </v-text-field>
                                </v-col>
                            </v-row>
                            <v-alert v-model="alert.visible" :color="alert.status" dark transition="scale-transition">{{ alert.message }}</v-alert>
                            <button type="submit" hidden>Submin</button>
                        </v-form>
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
            alert: this.initAlert(),
            timer: null,
            printing_local: true,
            prints: [
                {nombre: 'ZT410 Linea 06', ip: '192.168.1.164'}
            ],
            printers_local: [],
            printer_select: null,
            printer_uid: null,
            form: {
                codigo_operador: null,
                ip_print: localStorage.getItem('ip_print') || null,
            }
        }
    },
    mounted() {
        axios.get(url_base+'/impresora?all')
        .then(response => {
            this.prints = response.data;
        })
        this.getLocalDevices();
    },
    watch:{

    },
    methods: {
        getLocalDevices(){
            var t=this;
            BrowserPrint.getDefaultDevice("printer", function(device){
                t.printer_select=device;
                t.printer_uid=t.printer_select.uid;
            });
            BrowserPrint.getLocalDevices(function(device_list){
                for(var i = 0; i < device_list.length; i++)
                {
                    t.printers_local.push(device_list[i]);
                }                
            }, function(){},"printer");
        },
        initAlert(){
            return {
                status: '',
                visible: false,
                message: ''
            }
        },
        print(){
            if (this.timer) {
                clearTimeout(this.timer);
            }
            var str_return = (this.printing_local) ? 'return': '';
            axios.get(`${url_base}/print/zpl/cajas?${str_return}`,{
                params: this.form
            })
            .then(response => {
                var respuesta=response.data;
                if (this.printing_local) {
                    switch (respuesta.status) {
                        case 'OK':
                            this.printer_select.send(respuesta.data, undefined, function(errorMessage){
                                alert("Error: " + errorMessage);	
                            });
                            this.alert.status= 'primary';
                            this.alert.visible= true;
                            this.alert.message= 'Imprimiendo.';
                            break;
                    }
                }else{
                    switch (respuesta.status) {
                        case 'OK':
                            this.alert.status= 'primary';
                            this.alert.visible= true;
                            this.alert.message= respuesta.data;
                            break;
                        case 'ERROR':
                            this.alert.status= 'warning';
                            this.alert.visible= true;
                            this.alert.message= respuesta.data;
                            break;
                    }
                }
                this.form.codigo_operador='';
                this.timer=setTimeout(() => {
                    this.alert=this.initAlert();
                }, 10000);
            });
        },
        changePrint(){
            localStorage.setItem('ip_print',this.form.ip_print)
        }
    },
}
</script>