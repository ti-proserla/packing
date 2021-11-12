<template>
    <v-container fluid>
        <v-row>
            <v-col cols="12" lg="4">
                <v-card>
                    <v-card-text>
                        <v-row>
                            <v-col cols="12" lg="12">
                                <v-select
                                dense
                                v-model="consulta.cliente_id"
                                label="Productor:"
                                :items="clientes"
                                item-text="descripcion"
                                item-value="id"
                                >
                                </v-select>
                            </v-col>
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
                                <h4>Variedades</h4>
                                <div v-for="data in variedades">
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
            <v-col cols="12" lg="3">
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
            </v-col>
            <v-col cols="12" lg="5">
                <v-card>
                    <!-- <v-card-title>Consumo por Fecha</v-card-title>               -->
                    <v-card-text>
                        <apexcharts 
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
            variedades: [],
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
                colors: ['#77B6EA', '#545454'],
                xaxis: {
                },
                yaxis: {
                    title: {
                        text: 'Jabas'
                    }
                },
            },
            consulta:{
                desde: moment().format('YYYY-MM-DD'),
                hasta: moment().format('YYYY-MM-DD'),
                cliente_id: 1
            },
            table: [],
            header: [
                { text: 'Productor', value: 'nombre_productor'},
                { text: 'Fundo', value: 'nombre_fundo'},
                { text: 'Viaje', value: 'viaje'},
                { text: 'Guia', value: 'guia'},
                { text: 'Placa', value: 'placa'},
                { text: 'Semana', value: 'semana'},
                { text: 'Recepción', value: 'fecha_recepcion'},
                { text: 'Hora', value: 'hora_ingreso'},
                { text: 'Lanzado', value: 'fecha_lanzado'},
                { text: 'Hora', value: 'hora_lanzado'},
                { text: 'Materia', value: 'nombre_materia'},
                { text: 'Variedad', value: 'nombre_variedad'},
                { text: 'Lote Materia', value: 'lote_materia' },
                { text: 'Número de Jabas', value: 'numero_jabas' },
                { text: 'Peso Promedio Jabas', value: 'peso_promedio_jaba' },
                { text: 'Peso Neto', value: 'peso_neto' },
                { text: 'Jabas Lanzadas', value: 'jabas_lanzadas'},
                { text: 'Peso Lanzado', value: 'peso_lanzado'},
                { text: '% Lanzado', value: '%_lanzado'},
            ],
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
        this.listarClientes();
        this.buscar();
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
        listarClientes(){
            axios.get(url_base+'/cliente?all')
            .then(response => {
                this.clientes = response.data;
                this.clientes.push({
                    descripcion: 'Todos los Clientes',
                    id: ''
                })
            })
        },
        buscar(){
            axios.get(`${url_base}/reporte/consumo-viaje`,{
                params: {
                    desde: this.consulta.desde,
                    hasta: this.consulta.desde,
                    cliente_id: this.consulta.cliente_id
                }
            })
            .then(response => {
                this.resultados=response.data;
                this.variedades = this.resultados.map(item => item.nombre_variedad)
                .filter((value, index, self) => self.indexOf(value) === index);
                this.seleccionadas = this.variedades;
                this.graficar();
            });
        },
        graficar(){
            var series_1=0;
            var series_2=0;
            var viajes='0000-00-00';
            for (let i = 0; i < this.resultados.length; i++) {
                const data = this.resultados[i];
                if (this.seleccionadas.indexOf(data.nombre_variedad)>-1) {
                    series_1+=Number(data.numero_jabas);
                    series_2+=Number(data.jabas_lanzadas);
                }
                viajes=data.fecha_recepcion;
            }
            
            this.variedades = this.resultados.map(item => item.nombre_variedad)
                .filter((value, index, self) => self.indexOf(value) === index);
            this.chartOptions={
                xaxis: {
                    categories: [viajes],
                },
            };
            this.series=[
                {name: 'Jabas Ingresadas', data: [series_1] },
                {name: 'Jabas Lanzadas', data: [series_2] }
            ];
            this.series_bar=[(series_2*100/series_1).toFixed(2)]
            this.restantes=series_1-series_2;
        }

    }
}
</script>