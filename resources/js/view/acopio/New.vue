<template>
    <div class="row">
        <div class="col-12">
            <h5>Nuevo Lote Ingreso</h5>
        </div>
        <div class="col-12">
            <div class="row">
                <div class="col-sm-6 form-group">
                    <label for="">Código:</label>
                    <input type="text" class="form-control" v-model="lote.codigo">
                </div>
                <div class="col-sm-6 form-group">
                    <label for="">Fecha Cosecha:</label>
                    <input type="date" class="form-control" v-model="lote.fecha_cosecha">
                </div>
                <div class="col-sm-6 form-group">
                    <label for="">Cliente:</label>
                    <select name="" id="" class="form-control" v-model="lote.cliente_id">
                        <option v-for="cliente in clientes" :value="cliente.id">{{ `${cliente.ruc} - ${cliente.descripcion}` }}</option>
                    </select>
                </div>
                <div class="col-sm-6 form-group">
                    <label for="">Materia:</label>
                    <select name="" id="" class="form-control" v-model="lote.materia_id">
                        <option v-for="materia in materias" :value="materia.id">{{materia.nombre_materia}}</option>
                    </select>
                </div>
                <div class="col-sm-6 form-group">
                    <label for="">Variedad:</label>
                    <select name="" id="" class="form-control" v-model="lote.variedad_id">
                        <option v-for="variedad in variedades" :value="variedad.id">{{ `${variedad.nombre_variedad}` }}</option>
                    </select>
                </div>
                <div class="col-sm-12">
                    <button class="form-control btn-danger" @click="crear()">Crear</button>
                </div>
            </div>
        </div>
        <div class="col-12">
            {{peso}}
        </div>
    </div>
</template>
<script>
import { mapState,mapMutations } from 'vuex'

export default {
    data() {
        return {
            //Listas
            clientes: [],
            materias: [],
            variedades: [],
            //Guardar
            lote: {}
        }
    },
    computed: {
        ...mapState(['peso']),
    },
    mounted() {
        this.listarClientes();
        this.listarMaterias();
        this.listarVariedades();
    },
    methods: {
        listarClientes(){
            axios.get(url_base+'/cliente')
            .then(response => {
                this.clientes=response.data
            })
        },    
        listarMaterias(){
            axios.get(url_base+'/materia')
            .then(response => {
                this.materias=response.data
            })
        }, 
        listarVariedades(){
            axios.get(url_base+'/variedad')
            .then(response => {
                this.variedades=response.data
            })
        },
        crear(){
            var t=this;
            swal({
                title: "¿Desea crear Lote?",
                buttons: ['Cancelar',"Crear"],
            })
            .then((res) => {
                if (res) {
                    /**
                     * Registro Correcto
                     */
                    axios.post(url_base+'/lote-ingreso',t.lote).then(res=>{
                        var data=res.data;
                        if (data.status=="OK") {
                            swal("Lote Creado", {
                                icon: "success",
                                timer: 2000,
                                buttons: false,
                            });
                            t.$router.push('/lote/'+data.data.id+'/palets');
                        }
                    });
                }
            });
        } 
    },
}
</script>