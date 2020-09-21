<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class PaletSalida extends Model
{
    protected $table="palet_salida";

    public function jabas()
    {
        return $this->hasMany('App\Model\JabaSalida')
                    ->select('palet_salida_id','numero',DB::raw("GROUP_CONCAT(codigo_barras separator '|') as codigos"))    
                    ->groupBy('palet_salida_id','numero');
    }
}
