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
        print(){
            axios.get(`${url_base}/print/zpl/cajas/`,{
                params: this.form
            })
            .then(response => {
                
            });
        },
        changePrint(){
            localStorage.setItem('ip_print',this.form.ip_print)
        }
    },
}
</script>