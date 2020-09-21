<template>
    <div>
        <v-simple-table>
            <template v-slot:default>
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Cliente</th>
                        <th>Codigo</th>
                        <th>Linea</th>
                        <th>Cantidad</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(row,i) in table" :key="i">
                        <td>{{row.fecha_produccion}}</td>
                        <td>{{row.cliente}}</td>
                        <td>{{row.codigo}}</td>
                        <td>{{row.linea}}</td>
                        <td>{{row.cantidad}}</td>
                    </tr>
                </tbody>
            </template>
        </v-simple-table>
    </div>
</template>
<script>
export default {
    data() {
        return {
            table: []
        }
    },
    methods: {
    },
    mounted() {
        axios.get(url_base+`/cantidad-por-linea`)
        .then(response => {
            this.table=response.data;
        });
    },
}
</script>