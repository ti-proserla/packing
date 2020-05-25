<template>
    <div class="card">
        <div class="card-head">
            <h4>Nuevo Lote de Ingreso</h4>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <label for="">Codigo</label>
                    <input type="text" class="form-control" v-model="lote.codigo">
                </div>
                <div class="col-md-6">
                    <label for="">Cliente</label>
                    <select class="form-control" v-model="lote.cliente_id">
                        <option value="">-Seleccionar Cliente-</option>
                        <option v-for="cliente in clientes" :value="cliente.id">{{ cliente.descripcion }}</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="">Materia</label>
                    <select class="form-control" v-model="lote.materia_id">
                        <option value="">-Seleccionar Materia-</option>
                        <option v-for="materia in materias" :value="materia.id">{{ materia.nombre_materia }}</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="">Variedad</label>
                    <select class="form-control" v-model="lote.variedad_id">
                        <option value="">-Seleccionar Variedad-</option>
                        <option v-for="variedad in variedades" :value="variedad.id">{{ variedad.nombre_variedad }}</option>
                    </select>
                </div>
                <div class="col-sm-6">
                    <label for="">Fecha cosecha</label>
                    <input type="date" class="form-control" v-model="lote.fecha_cosecha">
                </div>
                <div class="col-12 text-center">
                    <button class="btn btn-primary">Guardar</button>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    data() {
        return {
            /**
             * Listas
             */
            materias: [],
            clientes: [],
            /**
             * Modificadores
             */
            lote: {
                cliente_id: "",
                materia_id: "",
                variedad_id: "",
                fecha_cosecha: moment().format('YYYY-MM-DD')
            }
        }
    },
    mounted() {
        this.listarProductos();
        this.listarClientes();
    },
    computed: {
        variedades(){
            var variedades = [];
            for (let i = 0; i < this.materias.length; i++) {
                const materia = this.materias[i];
                if (materia.id==this.lote.materia_id) {
                    variedades=materia.variedad;
                }
            }
            return variedades
        }
    },
    methods: {
        listarProductos(){
            axios.get(url_base+'/materia/variedad')
            .then(response => {
                this.materias=response.data
            });
        },
        listarClientes(){
            axios.get(url_base+'/cliente')
            .then(response => {
                this.clientes=response.data
            });
        }
    },
}
</script>