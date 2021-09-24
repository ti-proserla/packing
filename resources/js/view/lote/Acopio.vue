<template>
    <v-container fluid>
        <v-card>
            <v-card-title>
                Lotes Productor
            </v-card-title>
            <v-card-text>
                <v-simple-table dense>
                    <template v-slot:default>
                        <thead>
                            <tr>
                                <th>Productor</th>
                                <th>Lote</th>
                                <th>Materia</th>
                                <th>Variedad</th>
                                <th>Cosecha</th>
                                <th>Fecha Proceso</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr cols="12" v-for="(lote,i) in lotes_ingreso" :key="i">
                                <td>{{ lote.descripcion}}</td>
                                <td>{{ lote.codigo}}</td>
                                <td>{{ lote.nombre_materia}}</td>
                                <td>{{ lote.nombre_variedad }}</td>
                                <td>{{ lote.fecha_cosecha }}</td>
                                <td>{{ lote.fecha_proceso }}
                                    <v-btn v-if="lote.estado!='Cerrado'"
                                        small
                                        text
                                        @click="abrir_actualizar(lote.id)">
                                        <i class="fas fa-pen"></i>
                                    </v-btn>
                                </td>
                                <td>
                                    <v-chip
                                    @click="finalizar(lote.id,lote.estado)"
                                    small
                                    class="ma-2"
                                    :color="colorGet(lote.estado)"
                                    text-color="white"
                                    >
                                        {{lote.estado}}
                                    </v-chip>
                                    <!-- <v-btn 
                                        v-if="lote.estado!='Cerrado'"
                                        text>
                                        Cerrar
                                    </v-btn> -->
                                </td>
                            </tr>
                        </tbody>
                    </template>
                </v-simple-table>
            </v-card-text>
        </v-card>
        <!-- Editar -->
        <v-dialog v-model="open_editar" persistent max-width="350">
            <v-card>
                <v-card-title class="headline">Fecha Producción</v-card-title>
                <v-card-text>
                    <v-text-field 
                        label="Fecha" 
                        v-model="lote_editar.fecha_proceso"
                    ></v-text-field>
                    <div class="text-right mt-3">
                        <v-btn 
                            outlined 
                            color="secondary" 
                            @click="open_editar=false"
                            >Cancelar</v-btn>
                        <v-btn 
                            outlined 
                            color="primary" 
                            @click="actualizar()"
                            >Guardar</v-btn>
                    </div>
                </v-card-text>
            </v-card>               
        </v-dialog>
        <v-btn
            dark
            fab
            bottom
            :fixed="true"
            right
            color="primary"
            @click="nuevo">
              <v-icon>+</v-icon>
        </v-btn>
    </v-container>
</template>
<style>
    b.detalles{
        width: 100px;
        display: inline-block;
    }
</style>
<script>
export default {
    data() {
        return {
            lotes_ingreso: [],
            lote_editar: {
                lote_ingreso_id: 0,
                fecha_proceso: moment().format('YYYY-MM-DD')
            },
            open_editar: false,
            estados: [
                {color: 'success', estado: 'Pendiente'},
                {color: 'warning', estado: 'Lanzado'},
                {color: 'primary', estado: 'Cerrado'},
                {color: 'danger', estado: 'Pendiente'}
            ]
        }
    },
    mounted() {
        this.listarLotes()
    },
    methods: {
        colorGet(estado){
            for (let i = 0; i < this.estados.length; i++) {
                const element = this.estados[i];
                if (element.estado==estado) {
                    return element.color;
                }
            }
            return '';
        },
        listarLotes(){
            axios.get(url_base+'/lote_ingreso')
            .then(response => {
                this.lotes_ingreso=response.data
            })
        },
        nuevo(){
            this.$router.push('/acopio/lote/new');
        },
        abrir_actualizar(id){
            this.lote_editar.lote_ingreso_id=id;
            this.open_editar=true;
        },
        finalizar(id,estado){
            if (estado=='Pendiente') {
                var t=this;
                swal({ title: "¿Desea Cerrar Lote?", buttons: ['Cancelar',"Si"]})
                .then((res) => {
                    if (res) {
                        axios.post(url_base+`/lote_ingreso/${ id }?_method=patch`,{
                            estado: 'Cerrado'
                        }).then(response => {
                            var res=response.data;
                            switch (res.status) {
                                case 'OK':
                                    t.listarLotes();
                                    break;
                            
                                default:
                                    break;
                            }
                        });
                    }
                });
            }
        },
        actualizar(){
            axios.post(url_base+`/lote_ingreso/${this.lote_editar.lote_ingreso_id}?_method=PUT`,this.lote_editar)
            .then(response => {
                var respuesta=response.data;
                switch (respuesta.status) {
                    case 'OK':
                        swal("Lote Actualizado", { 
                            icon: "success", 
                            timer: 2000, 
                            buttons: false
                        });
                        this.open_editar=false;
                        this.listarLotes();
                        break;
                    case 'VALIDATION':
                        this.error_editar=respuesta.data;
                        break;
                    case 'ERROR':
                        break;
                }
            });
        }
    },
}
</script>