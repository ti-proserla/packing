<template>
    <v-container fluid>
        <v-card>
            <v-card-title>Reporte Consumo de Viaje</v-card-title>              
            <v-card-text>
                <apexcharts 
                    type="bar" 
                    :options="chartOptions" 
                    :series="series"
                    ></apexcharts>
            </v-card-text>
        </v-card>
    </v-container>
</template>
<script>
import VueApexCharts from 'vue-apexcharts'
export default {
    components: {
      apexcharts: VueApexCharts,
    },
    data() {
        return {
            series: [],

            chartOptions: {
                chart: {
                    id: 'vuechart-example'
                },
                colors: ['#77B6EA', '#545454'],
                xaxis: {
                    categories: ['V.1'],
                    title: {
                        text: 'Viaje'
                    }
                },
                yaxis: {
                    title: {
                        text: 'Kg.'
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
                params: this.consulta
            })
            .then(response => {
                var series_1=[];
                var series_2=[];
                var viajes=[];
                for (let i = 0; i < response.data.length; i++) {
                    const data = response.data[i];
                    series_1.push(data.peso_neto);
                    series_2.push(data.peso_lanzado);
                    viajes.push(`V.${data.viaje}`)
                }
                this.chartOptions={xaxis: {
                        categories: viajes,
                        title: {
                            text: 'Viaje'
                        }
                        },yaxis: {
                            title: {
                                text: 'Kg.'
                            }
                        },
                        dataLabels: {
                            enabled: false,
                        },
                };
                this.series=[
                    {name: 'Peso Neto', data: series_1 },
                    {name: 'Peso Lanzado', data: series_2 }
                ];
            });
        }
    }
}
</script>