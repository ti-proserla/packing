<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class LoteIngreso extends Model
{
    protected $table='lote_ingreso';

    public function subLote()
    {
        return $this->hasMany('App\Model\SubLote');
    }

    public function palets_salida()
    {
        return $this->hasMany('App\Model\PaletSalida','lote_id')
                    ->join('producto','palet_salida.producto_id','=','producto.id')
                    ->select('palet_salida.id','lote_id','producto.nombre_producto as producto','numero','palet_salida.estado')
                    ->orderBy('palet_salida.id','DESC');
    }
}
