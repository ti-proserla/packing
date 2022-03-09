<template>
    <v-container fluid>
        <v-card outlined>
            <v-card-title>Operaciones</v-card-title>
            <v-card-text>
                <v-row>
                    <v-col cols="12" lg=4>
                        <v-btn :to="`/operacion/new`" 
                            outlined 
                            color="info">
                            Nueva Operación
                        </v-btn>
                    </v-col>
                    <v-col cols="12" sm=5 lg=3>
                        <v-text-field
                            outlined
                            dense
                            label="Desde:"
                            v-model="data_post.desde"
                            type="date">
                        </v-text-field>
                    </v-col>
                    <v-col cols="12" sm="5" lg=3>
                        <v-text-field
                            outlined
                            dense
                            label="Hasta:"
                            v-model="data_post.hasta"
                            type="date">
                        </v-text-field>
                    </v-col>
                    <v-col cols="12" sm="2" lg=2>
                        <v-btn color="info" @click="listar(1)">
                            Buscar
                        </v-btn>
                    </v-col>
                </v-row>
                <v-data-table
                    :headers="header"
                    :items="table.data"
                    :page.sync="table.current_page"
                    hide-default-footer
                    >
                    <template v-slot:item.estado="{ item }">
                        <v-chip v-if="item.estado=='Pendiente'"
                            small
                            :color="colorGet(item.estado)"
                            @click="abrir_actualizar(item.id)">
                            {{item.estado}}
                        </v-chip>
                        <v-chip
                            v-else
                            small
                            class="ma-2"
                            :color="colorGet(item.estado)"
                            text-color="white"
                            >
                            {{item.estado}}
                        </v-chip>
                    </template>
                    <!-- <template v-slot:item.ver="{ item }">
                        <v-btn text color="info" :to="`/operacion/${item.id}`">
                            <i class="far fa-clipboard"></i>
                        </v-btn>
                    </template> -->
                </v-data-table>
                <v-pagination v-model="table.current_page" :length="table.last_page" circle @input="listar"></v-pagination>
            </v-card-text>
        </v-card>

        <v-dialog v-model="open_editar" persistent max-width="350">
            <v-card>
                <v-card-title class="headline">Fecha Despacho</v-card-title>
                <v-card-text>
                    <v-text-field 
                        label="Fecha" 
                        type="date"
                        v-model="operacion.fecha_despachado"
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
    </v-container>
</template>
<script>
export default {
    data() {
        return {
            data_post:{
                desde: moment().startOf('month').format('YYYY-MM-DD'),
                hasta: moment().endOf('month').format('YYYY-MM-DD')
            },
            header:[
                { text: 'Código', value: 'codigo_operacion' },
                { text: 'Descripción', value: 'descripcion' },
                { text: 'Fecha', value: 'fecha_operacion' },
                { text: 'Cliente', value: 'cliente' },
                { text: 'Cantidad Cajas', value: 'cantidad_cajas' },
                { text: 'Estado', value: 'estado' },
                // { text: 'Editar', value: 'editar' },
            ],
            table: {
                current_page: 1,
                last_page: 1,
                data: []
            },
            estados: [
                {color: 'success', estado: 'Pendiente'},
                // {color: 'warning', estado: 'Despachado'},
                {color: 'primary', estado: 'Cerrado'},
                {color: 'error', estado: 'Despachado'}
            ],
            open_editar: false,
            operacion: {
                estado: 'Despachado',
                fecha_despachado: moment().format('YYYY-MM-DD')
            }
        }
    },
    mounted(){
        this.listar(1);
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
        listar(n=this.table.current_page){
            axios.get(`${url_base}/operacion?page=${n}&desde=${this.data_post.desde}&hasta=${this.data_post.hasta}`)
            .then(response => {
                this.table=response.data
                // console.log(response.data)
            });
        },
        abrir_actualizar(id){
            this.operacion.id=id;
            this.open_editar=true;
        },
        actualizar(){
            axios.post(url_base+`/operacion/${this.operacion.id}?_method=PUT`,this.operacion)
            .then(response => {
                var respuesta=response.data;
                switch (respuesta.status) {
                    case 'OK':
                        swal("Operacion Actualizada.", { 
                            icon: "success", 
                            timer: 2000, 
                            buttons: false
                        });
                        this.open_editar=false;
                        this.listar();
                        break;
                    case 'VALIDATION':
                        this.error_editar=respuesta.data;
                        break;
                    case 'ERROR':
                        break;
                }
            });
        }
    }
}
</script>