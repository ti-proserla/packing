<template>
    <v-container fluid>
        <v-row class="justify-center align-center">
            <v-col
                 cols="12" 
                 lg=6>
                <v-card outlined>
                    <v-card-title>Etiquetas Cajas</v-card-title>
                    <v-card-text>
                        <v-form autocomplete="off" @submit.prevent="print()">
                            <v-row>
                                <v-col cols="12">
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
                                </v-col>
                                <v-col cols="12">
                                    <v-select
                                        outlined
                                        dense
                                        v-model="form.codigo"
                                        label="Lotes:"
                                        :items="lotes_ingreso"
                                        :item-text="lote => `${lote.codigo} - ${lote.nombre_materia} -${lote.nombre_variedad}`"
                                        item-value="codigo"
                                        hide-details="auto"
                                        >
                                        </v-select>
                                </v-col>
                                <v-col cols="12" lg=6>
                                    <v-select
                                        outlined
                                        dense
                                        v-model="form.calibre"
                                        label="Calibre:"
                                        :items="calibres"
                                        item-text="nombre_calibre"
                                        item-value="nombre_calibre"
                                        hide-details="auto"
                                        >
                                        </v-select>
                                </v-col>
                                <v-col cols="12" lg=6>
                                    <v-select
                                        outlined
                                        dense
                                        v-model="form.categoria"
                                        label="Categoria:"
                                        :items="categorias"
                                        item-text="nombre_categoria"
                                        item-value="nombre_categoria"
                                        hide-details="auto"
                                        >
                                        </v-select>
                                </v-col>
                                <v-col cols="12">
                                    <v-btn 
                                        type="submit"
                                        color="primary">
                                        imprimir
                                    </v-btn>
                                </v-col>
                            </v-row>
                            {{ codigo }}
                            <v-alert v-model="alert.visible" :color="alert.status" dark transition="scale-transition">{{ alert.message }}</v-alert>
                            <!-- <button type="submit" hidden>Submin</button> -->
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
            materias: [],
            categorias: [
                {nombre_categoria: 'I'},
                {nombre_categoria: 'II'},
                {nombre_categoria: 'III'}
            ],
            lotes_ingreso: [],
            prints: [],
            alert: this.initAlert(),
            timer: null,
            form: {
                materia_id: null,
                lote_ingreso_id: null,
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
        this.listarLotes();
        this.listarMaterias();
    },
    computed:{
        calibres() {
            var lote_seleccionado={};

            for (let i = 0; i < this.lotes_ingreso.length; i++) {
                const lote = this.lotes_ingreso[i];
                if (lote.codigo==this.form.codigo) {
                    lote_seleccionado=lote;
                    break;
                }
            }
            for (let i = 0; i < this.materias.length; i++) {
                const materia = this.materias[i];
                if (materia.id==lote_seleccionado.materia_id) {
                    return materia.calibre;
                }
            }
            return []
        },
        codigo(){
            return this.form.codigo+'-'+this.form.calibre+'-'+this.form.categoria;
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
        listarMaterias(){
            axios.get(url_base+'/materia/detallado')
            .then(response => {
                this.materias=response.data
            });
        },
        listarLotes(){
            axios.get(url_base+'/lote_ingreso?estado=Pendiente')
            .then(response => {
                this.lotes_ingreso=response.data
            })
        },
        print(){
            if (this.timer) {
                clearTimeout(this.timer);
            }
            axios.get(`${url_base}/print/cajas`,{
                params: {
                    ip_print: ip_print,
                    codigo: this.codigo()
                }
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