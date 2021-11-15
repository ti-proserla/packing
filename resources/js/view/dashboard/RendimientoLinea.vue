<template>
    <v-container fluid>
        <v-row>
            <v-col cols="12" lg="4">
                <v-card>
                    <v-card-text>
                        <v-row>
                            <!-- <v-col cols="12" lg="12">
                                <v-select
                                dense
                                v-model="consulta.cliente_id"
                                label="Productor:"
                                :items="clientes"
                                item-text="descripcion"
                                item-value="id"
                                >
                                </v-select>
                            </v-col> -->
                            <v-col cols="12" lg="12">
                                <v-text-field
                                    v-model="consulta.desde"
                                    type="date">
                                </v-text-field>

                            </v-col>
                            <v-col cols="12" lg="12" class="text-center">
                                <v-btn @click="buscar" color="primary">
                                    Actualizar
                                </v-btn>
                            </v-col>
                            <v-col cols="12" lg="12">
                                <h4>Presentaciones</h4>
                                <div v-for="data in presentaciones">
                                    <v-checkbox 
                                        dense
                                        v-model="seleccionadas"
                                        :label="data"
                                        :value="data"
                                        hide-details
                                        mt="0"
                                        @change="graficar"
                                    ></v-checkbox>
                                </div>
                            </v-col>
                        </v-row>
                    </v-card-text>
                </v-card>
            </v-col>
            <!-- <v-col cols="12" lg="3">
                <v-card>
                    <v-card-text>
                        <v-row>
                            <v-col cols="5" lg="12" align-self="center">
                                <h2 class="text-center">{{restantes}}</h2>
                                <h5 class="text-center">Jabas Pendientes</h5>
                            </v-col>
                            <v-col cols="7" lg="12">
                                <apexcharts 
                                    height="200"
                                    type="radialBar" 
                                    :options="chartOptions2"
                                    :series="series_bar">
                                </apexcharts>
                            </v-col>
                        </v-row>
                    </v-card-text>
                </v-card>
            </v-col> -->
            <v-col cols="12" lg="8">
                <v-card>
                    <!-- <v-card-title>Consumo por Fecha</v-card-title>               -->
                    <v-card-text>
                        <apexcharts 
                            height="500"
                            type="bar" 
                            :options="chartOptions" 
                            :series="series"
                            ></apexcharts>
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
            resultados: [],
            presentaciones: [],
            seleccionadas: [],
            series_bar:[],
            series: [],
            restantes: 0,
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
                            enabled:true ,
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
                    id: 'vuechart-example'
                },
                // dataLabels:{
                    
                    //     // enabled: false,
                //     style: {
                    //         fontSize: '12px',
                //         colors: ['#333']
                //     },
                // },
                dataLabels:{
                    offsetX: 30,
                    textAnchor: 'start',
                    formatter: function (val, opts) {
                        return val.toFixed(0)
                    },
                    style: {
                        fontSize: '12px',
                        colors:['#333' ]
        
                    },

                },
                plotOptions: {
                    bar: {
                        // borderRadius: 4,
                        horizontal: true,
                        dataLabels: {
                            position: 'top',
                            // offsetX: 100,

                        },
                    },
                    // dataLabels: {
                    //     position: 'top',
                    //     offsetX: 100,
                    // },
                },
                // colors: ['#77B6EA'],
                xaxis: {
                },
                yaxis: {
                    title: {
                        text: ''
                    }
                },
            },
            consulta:{
                desde: moment().format('YYYY-MM-DD'),
                hasta: moment().format('YYYY-MM-DD'),
                cliente_id: 1
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
            ]
        }
    },
    mounted() {
        this.buscar();
    },
    methods:{
        buscar(){
            axios.get(`${url_base}/reporte/rendimiento_linea`,{
                params: {
                    desde: this.consulta.desde,
                    hasta: this.consulta.desde,
                    cliente_id: this.consulta.cliente_id
                }
            })
            .then(response => {
                this.resultados=response.data;
                this.presentaciones = this.resultados.map(item => item.nombre_presentacion)
                .filter((value, index, self) => self.indexOf(value) === index);
                this.seleccionadas = this.presentaciones;
                this.graficar();
            });
        },
        graficar(){
            var series=[];
            var categorias=[];
            for (let i = 0; i < 24; i++) {
                series.push(0); 
                categorias.push(`${i}:00 `);
            }

            for (let j = 0; j < this.resultados.length; j++) {
                const data = this.resultados[j];
                if (this.seleccionadas.indexOf(data.nombre_presentacion)>-1) {
                    series[data.hora_inicio.split(':')[0]]+=Number(data.salida)
                }
            }
                console.log(series);
            this.series=[{data: series}];
            this.chartOptions={
                xaxis: {
                    categories: categorias,
                },
            };
        }

    }
}
</script>