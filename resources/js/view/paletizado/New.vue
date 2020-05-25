<template>
    <div class="row">
        <div class="col-12">
            <h5>Nuevo Palet</h5>
        </div>
        <div class="col-sm-6 form-group">
            <label for="">Cliente - Lote</label>
            <select name="" id="" class="form-control" v-model="palet_salida.lote_id">
                <option value="1">Plantaciones del SOl - Lote RS</option>
            </select>
        </div>
        <div class="col-sm-3 form-group">
            <label for="">Producto</label>
            <select name="" id="" class="form-control" v-model="palet_salida.producto_id">
                <option v-for="producto in productos" :value="producto.id">{{ producto.nombre_producto }}</option>
            </select>
        </div>
        <div class="col-sm-3 form-group">
            <label>Proceso</label>
            <select class="form-control" name="" id="">
                <option value="">Empaque 3 Etapas</option>
                <option value="">Clanshell</option>
            </select>
        </div>
        <div class="col-sm-12 col-lg-3">
            <button class="form-control btn-danger" @click="crear()">Crear</button>
        </div>
    </div>
</template>
<script>
export default {
    data() {
        return {
            palet_salida: {
                lote_id:    null,
                producto_id: null
            },
            productos: [],
        }
    },
    mounted() {
        this.listarProducto()
    },
    methods: {
        listarProducto(){
            axios.get(url_base+'/producto')
            .then(response => {
                this.productos=response.data
            })
        },
        crear(){
            var t=this;
            swal({
                title: "¿Desea crear Palet?",
                buttons: ['Cancelar',"Crear"],
            })
            .then((res) => {
                if (res) {
                    /**
                     * Operacion
                     */
                    var request=BD_REQUEST.transaction(["PALET_SALIDA"], "readwrite")
                        .objectStore("PALET_SALIDA").add(t.palet_salida);
                    /**
                     * Registro Correcto
                     */
                    request.onsuccess = function(event) {
                        swal("Palet Creado", {
                            icon: "success",
                            timer: 2000,
                            buttons: false,
                        });
                        t.$router.push('/paletizado/'+event.target.result);
                    };
                }
            });
        }
    },
}

function errorCB(tx, err) {
  console.log(err);  
}
</script>