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
                    ->join('etiqueta_caja as EC','EC.id','=','caja.etiqueta_caja_id')
                    ->join('calibre as CL','CL.id','=','EC.calibre_id')
                    ->join('categoria as CT','CT.id','=','EC.categoria_id')    
                    ->join('presentacion as PE','PE.id','=','EC.presentacion_id')
                    ->leftJoin(DB::raw("(SELECT caja_id,GROUP_CONCAT(rendimiento_personal.codigo_barras) codigo_barras 
                                    FROM rendimiento_personal 
                                    GROUP BY caja_id) RP
                            "
                    ),
                    function($join)
                    {
                        $join->on('RP.caja_id', '=', 'caja.id');
                    })
                    ->select(
                        'caja.palet_salida_id',
                        DB::raw('count(caja.id) cantidad'),
                        'CL.nombre_calibre',
                        'CT.nombre_categoria',
                        'PE.nombre_presentacion',
                        DB::raw('GROUP_CONCAT(RP.codigo_barras) codigos')
                    )
                    ->groupBy('caja.etiqueta_caja_id');
    }
}
