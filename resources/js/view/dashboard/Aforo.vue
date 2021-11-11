<template>
    <v-container fluid>
        <v-row>
            <v-col cols="12" lg="6">
                <v-card>
                    <v-card-text>
                        <v-row>
                            <v-col cols="12" sm="4">
                                <v-text-field
                                    v-model="consulta.fecha_produccion"
                                    type="date">
                                </v-text-field>
                            </v-col>
                            <v-col cols="12" sm="4">
                                <v-select
                                dense
                                v-model="consulta.turno"
                                label="Turno:"
                                :items="turno"
                                item-text="turno"
                                item-value="turno"
                                >
                                </v-select>
                            </v-col>
                            <v-col cols="12" sm="4" class="text-center">
                                <v-btn @click="actualizar" color="primary">
                                    Actualizar
                                </v-btn>
                            </v-col>
                        </v-row>
                        <apexcharts 
                            height="400"
                            type="bar" 
                            :options="chartOptions" 
                            :series="series"
                            ></apexcharts>
                    </v-card-text>
                </v-card>
            </v-col>
            <v-col cols="12" lg="6">
                <v-card>
                    <v-card-title>Listado de Labores</v-card-title>              
                    <v-card-text>
                            <v-col cols="12" lg="4">
                                <v-text-field
                                    v-model="search"
                                    append-icon="mdi-magnify"
                                    label="Search"
                                    single-line
                                    hide-details
                                ></v-text-field>
                            </v-col>
                            <v-data-table
                                :search="search"
                                class="table-lineal"
                                :headers="header"
                                :items="table"
                                >
                            </v-data-table>
                    </v-card-text>
                </v-card>
            </v-col>
        </v-row>
    </v-container>
</template>
<style>
.v-input--selection-controls {
    margin-top: 0!important;
}
</style>
<script>
import VueApexCharts from 'vue-apexcharts'
import Index from '../lanzado/index.vue';
export default {
    components: {
      apexcharts: VueApexCharts
    },
    data() {
        return {
            search: '',
            resultados: [],
            variedades: [],
            seleccionadas: [],
            series_bar:[],
            series: [],
            restantes: 0,
            total_linea_1: 0,
            total_linea_2: 0,
            total_linea_3: 0,
            total_linea_4: 0,
            total_linea_5: 0,
            total_linea_6: 0,
            chartOptions2: {
                chart: {
                    id: 'vuechart-example'
                },
                colors: ['#77B6EA', '#545454'],
                labels: ["Lanzado"],
                stroke: {
                    lineCap: "round",
                },
                plotOptions: {
                    radialBar: {
                        hollow: {
                            // margin: -15,
                            size: "60%"
                        },
                        
                        dataLabels: {
                            showOn: "always",
                            name: {
                                offsetY: -10,
                                show: true,
                                color: "#888",
                                fontSize: "12px"
                            },
                            value: {
                                offsetY: -5,
                                color: "#111",
                                fontSize: "14px",
                                show: true
                            }
                        }
                    }
                }
            },
            chartOptions: {
                chart: {
                    id: 'vuechart-example',
                    type: 'bar',
                    stacked: true,
                    toolbar: {
                        show: true
                    },
                    zoom: {
                        enabled: true
                    }
                },
                legend: {
                    position: 'right',
                    offsetY: 100,
                    fontSize: '10px',
                    fontWeight: 600,
                },
                xaxis: {
                    categories: [
                        'Linea 06',
                        'Linea 05',
                        'Linea 04',
                        'Linea 03',
                        'Linea 02',
                        'Linea 01',
                    ]
                },
                plotOptions: {
                    radialBar: {
                        hollow: {
                            // margin: -15,
                            size: "50%"
                        },
                        
                        dataLabels: {
                            showOn: "always",
                            name: {
                                offsetY: -10,
                                show: true,
                                color: "#888",
                                fontSize: "12px"
                            },
                            value: {
                                offsetY: -5,
                                color: "#111",
                                fontSize: "12px",
                                show: true
                            }
                        }
                    }
                }
            },
            turno: [
                {turno: '01'},
                {turno: '02'},
            ],
            consulta:{
                fecha_produccion: moment().format('YYYY-MM-DD'),
                turno: '01',
            },
            table: [],
            fecha_recepcion: moment().format('YYYY-MM-DD'),
            cliente_id: null,
            clientes: [],
            header_excel: [
                {
                    label: 'Productor',
                    field: 'nombre_productor',
                }
            ],
            header: [
                { text: 'Labor', value: 'nom_labor'},
                { text: 'Cantidad', value: 'cantidad'},
            ],

        }
    },
    mounted() {
        this.actualizar();
    },
    computed:{
        excel(){
            var query="";
            Object.entries(this.consulta).forEach(([key, value]) => {
                query+=`&${key}=${value}`;
            });
            return `${url_base}/reporte/consumo-viaje?excel${query}`
        }
    },
    methods:{
        actualizar(){
            this.buscar();
        this.labores();
        },
        labores(){
            axios.get(`${url_base}/reporte/cantidad-labor`,{
                params: this.consulta
            })
            .then(response => {
                this.table=response.data;
            })
        },
        buscar(){
            axios.get(`${url_base}/reporte/aforo`,{
                params: this.consulta
            })
            .then(response => {
                this.resultados=response.data;
                this.series=[];
                this.total_linea_1=0;
                this.total_linea_2=0;
                this.total_linea_3=0;
                this.total_linea_4=0;
                this.total_linea_5=0;
                this.total_linea_6=0;
                for (let i = 0; i < this.resultados.length; i++) {
                    const element = this.resultados[i];
                    this.series.push({
                        name: element.labor,
                        data: [
                            element.linea_6, 
                            element.linea_5, 
                            element.linea_4, 
                            element.linea_3, 
                            element.linea_2, 
                            element.linea_1, 
                        ]
                    })
                    this.total_linea_1+=element.linea_1;
                    this.total_linea_2+=element.linea_2;
                    this.total_linea_3+=element.linea_3;
                    this.total_linea_4+=element.linea_4;
                    this.total_linea_5+=element.linea_5;
                    this.total_linea_6+=element.linea_6;
                }
            });
        }
    }
}
</script>