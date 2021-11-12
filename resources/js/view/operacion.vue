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
                        <v-btn color="info" @click="buscar">
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
                    <template v-slot:item.ver="{ item }">
                        <v-btn text color="info" :to="`/operacion/${item.id}`">
                            <i class="far fa-clipboard"></i>
                        </v-btn>
                    </template>
                </v-data-table>
                <v-pagination v-model="table.current_page" :length="table.last_page" circle @input="listar"></v-pagination>
            </v-card-text>
        </v-card>
    </v-container>
</template>
<script>
export default {
    data() {
        return {
            data_post:{
                desde: moment().format('YYYY-MM-DD'),
                hasta: moment().format('YYYY-MM-DD')
            },
            header:[
                { text: 'Código', value: 'descripcion' },
                { text: 'Fecha', value: 'fecha_operacion' },
                { text: 'Cliente', value: 'cliente' },
                { text: 'Estado', value: 'estado' },
                { text: 'Ver', value: 'ver' },
                // { text: 'Editar', value: 'editar' },
            ],
            table: {
                current_page: 1,
                last_page: 1,
                data: []
            },
        }
    },
    mounted(){
        this.buscar();
    },
    methods: {
        buscar(){
            axios.get(`${url_base}/operacion?desde=${this.data_post.desde}&hasta=${this.data_post.hasta}`)
            .then(response => {
                this.table=response.data
                // console.log(response.data)
            });
        }
    }
}
</script>