<template>
    <div class="card">
        <div class="card-head">
            <h4>Nuevo Lote de Ingreso</h4>
        </div>
        <div class="card-body">
            <form v-on:submit.prevent="guardar()" class="row">
                <div class="col-md-6">
                    <label for="">Codigo</label>
                    <input type="text" class="form-control" v-model="lote.codigo">
                    <span>{{ lote_error.codigo }}</span>
                </div>
                <div class="col-md-6">
                    <label for="">Cliente</label>
                    <select class="form-control" v-model="lote.cliente_id">
                        <option value="">-Seleccionar Cliente-</option>
                        <option v-for="cliente in clientes" :value="cliente.id">{{ cliente.descripcion }}</option>
                    </select>
                    <span>{{ lote_error.cliente_id }}</span>
                </div>
                <div class="col-md-6">
                    <label for="">Materia</label>
                    <select class="form-control" v-model="lote.materia_id">
                        <option value="">-Seleccionar Materia-</option>
                        <option v-for="materia in materias" :value="materia.id">{{ materia.nombre_materia }}</option>
                    </select>
                    <span>{{ lote_error.materia_id }}</span>
                </div>
                <div class="col-md-6">
                    <label for="">Variedad</label>
                    <select class="form-control" v-model="lote.variedad_id">
                        <option value="">-Seleccionar Variedad-</option>
                        <option v-for="variedad in variedades" :value="variedad.id">{{ variedad.nombre_variedad }}</option>
                    </select>
                    <span>{{ lote_error.variedad_id }}</span>
                </div>
                <div class="col-sm-6">
                    <label for="">Fecha cosecha</label>
                    <input type="date" class="form-control" v-model="lote.fecha_cosecha">
                    <span>{{ lote_error.fecha_cosecha }}</span>
                </div>
                <div class="col-12 text-center">
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </div>
            </form>
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
                codigo: "",
                cliente_id: "",
                materia_id: "",
                variedad_id: "",
                fecha_cosecha: moment().format('YYYY-MM-DD')
            },
            lote_error: {}
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
        },
        guardar(){
            var t=this;
            t.lote_error={};
            // swal({ title: "¿Desea crear Lote?", buttons: ['Cancelar',"Crear"]})
            // .then((res) => {
            //     if (res) {
                    /**
                     * Guardado
                     */
                    axios.post(url_base+'/lote-ingreso',t.lote)
                    .then(response => {
                        var respuesta=response.data;
                        switch (respuesta.status) {
                            case "VALIDATION":
                                t.lote_error=respuesta.data;
                                break;
                            case "OK":
                                swal("Lote Creado", { icon: "success", timer: 2000, buttons: false });
                                t.$router.push('/lote');
                                t.lote_error={};
                                break;
                            default:
                                t.lote_error={};
                                break;
                        }
                    });
            //     }
            // });
        }
    },
}
</script>