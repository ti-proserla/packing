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
}
