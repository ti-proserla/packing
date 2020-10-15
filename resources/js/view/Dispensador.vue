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
                            <v-text-field 
                                dense 
                                outlined 
                                label="Código de Barras" 
                                autofocus 
                                v-model="form.codigo_operador">
                                </v-text-field>
                            <v-select
                                outlined
                                dense
                                v-model="form.ip_print"
                                label="Printers:"
                                :items="prints"
                                :item-text="print => `${print.ip} = ${print.nombre}`"
                                item-value="ip"
                                hide-details="auto"
                                v-on:change="changePrint"
                                >
                                </v-select>
                            <v-alert class="mt-3" v-model="alert.visible" :color="alert.status" dark transition="scale-transition">{{ alert.message }}</v-alert>
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
            prints: [
                {nombre: 'ZT410 Linea 06', ip: '192.168.1.164'}
            ],
            form: {
                codigo_operador: null,
                ip_print: localStorage.getItem('ip_print') || null,
            }
        }
    },
    methods: {
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
            axios.get(`${url_base}/print/zpl/cajas/`,{
                params: this.form
            })
            .then(response => {
                var respuesta=response.data;
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