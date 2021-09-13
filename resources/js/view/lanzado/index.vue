<template>
    <div>
        <v-card>
            <v-card-title class="headline">
                Lanzado de Materia
            </v-card-title>
            <v-card-text>
                <v-row>
                    
                    <v-col cols="12" sm=6 lg="3">
                        <v-text-field
                            @change="listar()"
                            label="Fecha Proceso:"
                            v-model="consulta.fecha_proceso"
                            type="date">
                        </v-text-field>
                    </v-col>
                    <v-col cols="12" lg="2">
                        <v-btn
                            @click="open_nuevo=true"
                            color="primary">
                            Lanzar un nuevo Palet
                        </v-btn>
                    </v-col>
                </v-row>
                <v-data-table
                    :headers="header"
                    :items="datos"
                    hide-default-footer
                    >
                    <template v-slot:item.fin="{ item }">
                        {{item.fin}}
                        <v-btn v-if="item.fin==null" text color="warning" @click="openCerrar(item.id)">
                            <i class="far fa-stop-circle"></i>
                        </v-btn>
                    </template>
                </v-data-table>
            </v-card-text>
        </v-card>
        <v-dialog v-model="open_cerrar" persistent max-width="350">
            <v-card>
                <v-card-title class="headline">Cerrar Palet Lanzado</v-card-title>
                <v-card-text>
                    <form
                        autocomplete="off"
                        id="app"
                        v-on:submit.prevent="cerrarPalet(palet_id)">
                        <v-text-field 
                            label="Adicionar Minutos:"
                            type="number"
                            v-model="timer"
                        ></v-text-field>
                        <div class="text-right mt-3">
                            <v-btn 
                                outlined 
                                color="secondary" 
                                @click="open_cerrar=false"
                                >Cerrar</v-btn>
                            <v-btn 
                                outlined 
                                color="primary" 
                                type="submit"
                                >Lanzar</v-btn>
                        </div>
                    </form>
                </v-card-text>
            </v-card>               
        </v-dialog>
        <v-dialog v-model="open_nuevo" persistent max-width="350">
            <v-card>
                <v-card-title class="headline">Registrar Lanzado</v-card-title>
                <v-card-text>
                    <form
                        id="app"
                        v-on:submit.prevent="guardar">
                        <v-select
                            label="Linea:"
                            v-model="lanzado.linea"
                            :items="lineas"
                            :item-text="(item) => item.numero"
                            item-value="numero">
                            </v-select>
                        <v-text-field 
                            label="Código de Palet:" 
                            v-model="lanzado.codigo"
                        ></v-text-field>
                        <div class="text-right mt-3">
                            <v-btn 
                                outlined 
                                color="secondary" 
                                @click="cerrar()"
                                >Cerrar</v-btn>
                            <v-btn 
                                outlined 
                                color="primary" 
                                type="submit"
                                >Lanzar</v-btn>
                        </div>
                    </form>
                </v-card-text>
            </v-card>               
        </v-dialog>
    </div>
</template>
<script>
export default {
    data() {
        return {
            open_cerrar: false,
            palet_id: 0,
            timer: '',
            datos: [],
            lanzado: this.initLanzado(),
            open_nuevo: false,
            lineas: [
                {numero: 1},
                {numero: 2},
                {numero: 3},
                {numero: 4},
                {numero: 5},
                {numero: 6}
            ],
            consulta: {
                fecha_proceso: moment().format('YYYY-MM-DD'),
                linea: null
            },
            header:[
                { text: 'Linea', value: 'linea_lanzado' },
                { text: 'Cosecha', value: 'fecha_cosecha' },
                { text: 'Productor', value: 'productor' },
                { text: 'Materia', value: 'nombre_materia' },
                { text: 'Variedad', value: 'nombre_variedad' },
                { text: 'Inicio', value: 'inicio' },
                { text: 'Fin', value: 'fin' },
                { text: 'Diferencia (min)', value: 'diferencia' }
            ],
        }
    },
    mounted() {
        this.listar();
    },
    methods:{
        initLanzado(){
            return {
                codigo: null,
                linea: null
            }
        },
        listar(){
            axios.get(`${url_base}/lanzado`,{params: this.consulta})
            .then(response=>{
                this.datos=response.data
            });
        },
        guardar(){
            var t=this;
            axios.post(url_base+`/lanzado?_method=patch`,this.lanzado)
            .then(response => {
                var res=response.data;
                switch (res.status) {
                    case 'OK':
                        swal(res.data, {
                            icon: "success",
                            timer: 2000,
                            buttons: false,
                        });
                        t.listar();
                        t.cerrar();
                        break;
                    case 'ERROR':
                        var x = document.getElementById("myAudio");
                        x.play();
                        window.navigator.vibrate([500,100,500]);
                        swal(res.data, {
                            icon: "error",
                            timer: 3000,
                            buttons: false,
                        });
                        t.lanzado.codigo='';
                        break;
                }
            });
        },
        cerrar(){
            this.open_nuevo=false;
            this.lanzado=this.initLanzado();
        },
        openCerrar(id){
            this.palet_id=id;
            this.open_cerrar=true;
        },
        cerrarPalet(id){
            var t=this;
            axios.post(url_base+`/lanzado/${id}/cerrar?_method=patch`,{
                timer: this.timer
            })
            .then(response => {
                var res=response.data;
                switch (res.status) {
                    case 'OK':
                        swal(res.message, {
                            icon: "success",
                            timer: 2000,
                            buttons: false,
                        });
                        t.listar();
                        t.open_cerrar=false;
                        break;
                    case 'ERROR':
                        var x = document.getElementById("myAudio");
                        x.play();
                        window.navigator.vibrate([500,100,500]);
                        swal(res.data, {
                            icon: "error",
                            timer: 3000,
                            buttons: false,
                        });
                        t.lanzado.codigo='';
                        break;
                }
            });
        }
    }
}
</script>