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
                    ->join('marca_caja as MC','MC.id','=','EC.marca_caja_id')
                    ->join('marca_empaque as ME','ME.id','=','EC.marca_empaque_id')
                    ->join('tipo_empaque as TE','TE.id','=','EC.tipo_empaque_id')
                    ->join('plu as PLU','PLU.id','=','EC.plu_id')
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
                        'EC.fecha_empaque',
                        'caja.palet_salida_id',
                        DB::raw('count(caja.id) cantidad'),
                        'CL.nombre_calibre',
                        'CT.nombre_categoria',
                        'PE.nombre_presentacion',
                        'TE.nombre_tipo_empaque',
                        'ME.nombre_marca_empaque',
                        'MC.nombre_marca_caja',
                        'PLU.nombre_plu',
                        DB::raw('GROUP_CONCAT(RP.codigo_barras) codigos')
                    )
                    ->groupBy('caja.etiqueta_caja_id');
    }
}
