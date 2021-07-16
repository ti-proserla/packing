<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class PaletSalida extends Model
{
    protected $table="palet_salida";

    public function cajas()
    {
        return $this->hasMany('App\Model\Caja')
                    ->leftJoin('rendimiento_personal','rendimiento_personal.caja_id','=','caja.id')
                    ->select(
                        'caja.id',
                        'caja.calibre',
                        'caja.categoria',
                        'caja.presentacion',
                        'caja.marca_caja',
                        'palet_salida_id',
                        DB::raw("GROUP_CONCAT(codigo_barras separator '|') as codigos"))    
                    ->groupBy('caja.id');
    }
}
